<?php
/**
 * Created by PhpStorm.
 * User: jurif
 * Date: 06/04/2018
 * Time: 16:55
 */

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\email\CEmail;
use bamboo\core\facebook\CFacebookCookieSession;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanDetail;
use bamboo\domain\entities\CEditorialPlanSocial;
use \bamboo\utils\time\STimeToolbox;
use Facebook\Facebook;
use DateTime;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdCreative;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\Ad;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Values\AdSetBillingEventValues;
use FacebookAds\Object\Values\AdSetOptimizationGoalValues;
use FacebookAds\Http\RequestInterface;
use FacebookAds\Object\Values\AdCreativeCallToActionTypeValues;
use FacebookAds\Object\Fields\AdCreativeFields;
use FacebookAds\Object\Fields\AdFields;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\Fields\AdsInsightsFields;
use FFMpeg;

class CEditorialPlanDetailAddAjaxController extends AAjaxController
{

    /**
     * @return bool|string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $title = $data['title'];
        if ($title == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> titolo non compilato</i>';
        }
        $isEventVisible = $data['isEventVisible'];
        $startEventDate = $data['start'];
        $endEventDate = $data['end'];
        $argument = $data['argument'];
        $linkDestination = $data['linkDestination'];
        $facebookCampaignId = $data['facebookCampaignId'];
        $lifetime_budget = isset($data['lifetime_budget'])? $data['lifetime_budget']: '';
        $campaignId = isset($data['campaignId'])?str_replace('\n','',$data['campaignId']):'';
        $groupAdsName = isset($data['campaignId'])?str_replace('\n','',$data['groupAdsName']):'';
        $selecterCampaign = isset($data['selecterCampaign'])?$data['selecterCampaign']:'';
        $isNewAdSet = isset($data['isNewAdSet'])?$data['isNewAdSet']:'';
        if ($argument == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Argomento non selezionnato</i>';
        }
        $isVisibleEditorialPlanArgument = $data['isVisibleEditorialPlanArgument'];
        $isVisiblePhotoUrl = $data['isVisiblePhotoUrl'];
        $bodyEvent = $data['bodyEvent'];
        $isVisibleBodyEvent = $data['isVisibleBodyEvent'];
        $note = $data['note'];
        $isVisibleNote = $data['isVisibleNote'];
        $description = $data['description'];
        $isVisibleDescription = $data['isVisibleDescription'];
        $buying_type = isset($data['buying_type'])?$data['buying_type']:'';
        $objective = isset($data['objective'])?$data['objective']:'';
        $photoUrl = (array_key_exists('photoUrl',$data)) ? $data['photoUrl'] : '';
        $unlinkphoto = [];
        $status = $data['status'];
        if ($status == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Non hai selezionato lo stato</i>';
        }

        $this->app->vendorLibraries->load("videoEditing");
        $this->app->vendorLibraries->load("facebook");
        \Monkey::app()->vendorLibraries->load("amazon2723");
        $config = $this->app->cfg()->fetch('miscellaneous','amazonConfiguration');
        $tempFolder = $this->app->rootPath() . $this->app->cfg()->fetch('paths','tempFolder') . '-plandetail' . "/";



        $c = new CFacebookCookieSession($this->app);
        $fbConfig = $this->app->cfg()->fetch('miscellaneous','facebook');
        $fb = new Facebook([
            'app_id' => $fbConfig['app_id'], // Replace {app-id} with your app id
            'app_secret' => $fbConfig['app_secret'],
            'default_graph_version' => 'v7.0',
            'persistent_data_handler' => &$c
        ]);

        $this->app->vendorLibraries->load("facebookBusiness");
        $fbConfig = $this->app->cfg()->fetch('miscellaneous','facebook');
        $socialId = $data['socialId'];
        $editorialPlanId = $data['editorialPlanId'];
        $editorialPlan = \Monkey::app()->repoFactory->create('EditorialPlan')->findOneBy(['id' => $data['editorialPlanId']]);
        $facebookMarketAccountId = $editorialPlan->facebookMarketAccountId;
        $editorialPlanShopAsSocial = \Monkey::app()->repoFactory->create('EditorialPlanShopAsSocial')->findOneBy(['shopId' => $editorialPlan->shopId]);
        $pageAccessToken = $editorialPlanShopAsSocial->access_token;
        $adAccountId = 'act_' . $facebookMarketAccountId;
        $_SESSION['facebook_access_token'] = $pageAccessToken;
        Api::init(
            $fbConfig['app_id'], // App ID
            $fbConfig['app_secret'],
            $_SESSION['facebook_access_token']);
        // Your user access token
        $startEventForFacebook = STimeToolbox::FormatDateFromDBValue($startEventDate,DateTime::ISO8601);
        $endEventForFacebook = STimeToolbox::FormatDateFromDBValue($endEventDate,DateTime::ISO8601);
        $startEventDate = STimeToolbox::FormatDateFromDBValue($startEventDate,'Y-m-d H:i:s');
        $endEventDate = STimeToolbox::FormatDateFromDBValue($endEventDate,'Y-m-d H:i:s');

        $notifyEmail = $data['notifyEmail'];
        $tempFolder = $this->app->rootPath() . $this->app->cfg()->fetch('paths','tempFolder') . "-plandetail/";
        $files = glob($tempFolder . "*.jpg");
        $url = "https://iwes-editorial.s3-eu-west-1.amazonaws.com/plandetail-images/";

        if ($photoUrl != '') {
            foreach ($photoUrl as &$jpg) {

                $jpg = $jpg;

            }
            $isCarousel = '0';
            if (count($photoUrl) > 1) {
                $isCarousel = 1;
            }
            /** @var array $groupimage */
            $groupimage = implode(",",$photoUrl);
        } else {
            $groupimage = '';
        }
        /** @var CRepo $editorialPlanDetailRepo */
        $editorialPlanDetailRepo = \Monkey::app()->repoFactory->create('EditorialPlanDetail');

        /** @var CEditorialPlanDetail $editorialPlanDetail */
        $editorialPlanDetail = $editorialPlanDetailRepo->findOneBy(['title' => $title]);


        if (empty($editorialPlanDetail)) {

            /** @var CEditorialPlanDetail $editorialPlanDetailInsert */
            $editorialPlanDetailInsert = \Monkey::app()->repoFactory->create('EditorialPlanDetail')->getEmptyEntity();

            $editorialPlanDetailInsert->title = $title;
            $editorialPlanDetailInsert->isEventVisible = $isEventVisible;
            $editorialPlanDetailInsert->startEventDate = $startEventDate;
            $editorialPlanDetailInsert->endEventDate = $endEventDate;
            $editorialPlanDetailInsert->editorialPlanArgumentId = $argument;
            $editorialPlanDetailInsert->isVisibleEditorialPlanArgument = $isVisibleEditorialPlanArgument;
            $editorialPlanDetailInsert->description = $description;
            $editorialPlanDetailInsert->isVisibleDescription = $isVisibleDescription;
            $editorialPlanDetailInsert->photoUrl = $groupimage;
            $editorialPlanDetailInsert->isVisiblePhotoUrl = $isVisiblePhotoUrl;
            $editorialPlanDetailInsert->status = $status;
            $editorialPlanDetailInsert->note = $note;
            $editorialPlanDetailInsert->linkDestination = $linkDestination;
            $editorialPlanDetailInsert->isVisibleNote = $isVisibleNote;
            $editorialPlanDetailInsert->socialId = $socialId;
            $editorialPlanDetailInsert->bodyEvent = $bodyEvent;
            $editorialPlanDetailInsert->lifetime_budget = $lifetime_budget;
            $editorialPlanDetailInsert->buying_type = $buying_type;
            $editorialPlanDetailInsert->objective = $objective;
            if ($facebookCampaignId != 'notExist') {
                $editorialPlanDetailInsert->facebookCampaignId = $facebookCampaignId;
            }

            $editorialPlanDetailInsert->isVisibleBodyEvent = $isVisibleBodyEvent;
            $editorialPlanDetailInsert->editorialPlanId = $editorialPlanId;
            $insertionId = '';


            switch ($argument) {
                case '4':
                case '11':
                case '12':
                case '13':
                case '14':
                case '15':
                case '16':
                case '17':
                case '18':
                case '19':
                case '20':

                    break;
                case '6':
                case  '5':


                if($data['type']=='formInsert') {
                    $linkData = [
                        'message' => $editorialPlan->name,
                        'name' => $data['postImageTitle'],
                        'link' => $linkDestination,
                        'description' => $data['postImageDescription'],
                        'picture' => $photoUrl[0]
                    ];
                    $editorialPlanDetailInsert->postImageTitle=$data['postImageTitle'];
                    $editorialPlanDetailInsert->postImageUrl=$photoUrl[0];
                    $editorialPlanDetailInsert->postDescriptionImage=$data['postDescriptionImage'];
                    try {
                        $response = $fb->post('/me/feed',$linkData,$pageAccessToken);
                    } catch (Facebook\Exceptions\FacebookResponseException $e) {
                        \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');

                    } catch (Facebook\Exceptions\FacebookSDKException $e) {
                        \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error Sdk: ' . $e->getMessage(),$e->getLine(),'');

                    }
                    $graphNode = $response->getGraphNode();
                    $insertionId = $graphNode['id'];
                    $editorialPlanDetailInsert->insertionId = $graphNode['id'];
                }

                    break;
                case '8':
                    if ($isNewAdSet == '2') {


                        $account = new AdAccount($adAccountId);

                        $cursor = $account->getCampaigns(['id','name','objective','buying_type','effective_status']);
                        $campaignList = [];
// Loop over objects

                        foreach ($cursor as $campaign) {
                            $nameCampaign = $campaign->{CampaignFields::NAME};
                            $idCampaign = $campaign->{CampaignFields::ID};

                            $objective = $campaign->{CampaignFields::OBJECTIVE};
                            $buying_type = $campaign->{CampaignFields::BUYING_TYPE};
                            $effective_status = $campaign->{CampaignFields::EFFECTIVE_STATUS};
                            if ($idCampaign == $campaignId)
                                $campaignList[] = ['idCampaign' => $idCampaign,'nameCampaign' => $nameCampaign,'objective' => $objective,'buying_type' => $buying_type,'effective_status' => $effective_status];
                            $editorialPlanDetailInsert->facebookCampaignId=$idCampaign;
                            break;
                        }

                    }
                    if ($selecterCampaign == 1) {
                        $adSetList = [];
                        $adsets = $account->getAdSets(array(
                            AdSetFields::NAME,
                            AdSetFields::CONFIGURED_STATUS,
                            AdSetFields::EFFECTIVE_STATUS,
                            AdSetFields::ID,
                            AdSetFields::CAMPAIGN_ID,
                        ));
// Loop over objects

                        foreach ($adsets as $adset) {
                            $nameAdSet = $adset->{AdSetFields::NAME};
                            $idAdSet = $adset->{AdSetFields::ID};
                            $status = $adset->{CampaignFields::STATUS};
                            if ($idAdSet == $groupAdsName) {
                                $adSetList[] = ['idAdSet' => $idAdSet,'nameAdSet' => $nameAdSet,'status' => $status,'error' => '0'];
                                $editorialPlanDetailInsert->groupInsertionId=$idAdSet;
                            }
                        }


                        try {
                            // Returns a `Facebook\FacebookResponse` object
                            $response = $fb->post(
                                '/' . $adAccountId . '/adsets',
                                array(
                                    'name' => $adSetList[0]['nameAdSet'],
                                    'start_time' => $startEventForFacebook,
                                    'end_time' => $endEventForFacebook,
                                    'campaign_id' => $campaignId,
                                    'billing_event' => 'IMPRESSIONS',
                                    'bid_amount' => '500',
                                    'optimization_goal' => $campaignList[0]['objective'],
                                    'targeting' => '{"geo_locations":{"countries":["IT"]},"facebook_positions":["feed"],"publisher_platforms":["facebook","audience_network"]}',
                                    'status' => $campaignList[0]['effective_status'],
                                ),
                                $pageAccessToken
                            );
                        } catch
                        (Facebook\Exceptions\FacebookResponseException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'set Response adSet');
                            return 'Graph returned an error: ' . $e->getMessage();
                        } catch (Facebook\Exceptions\FacebookSDKException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'set Response adSet sdk');
                            return 'Facebook SDK returned an error: ' . $e->getMessage();

                        }
                        $graphNode = $response->getGraphNode();

                        $groupAdsName = $graphNode['id'];
                    }

                    if($data['type']=='formInsert') {
                        $arrayPhotoHash = [];
                        $i = 0;

                        $downloadedFileContents = file_get_contents($groupimage);
                        $img = base64_encode($downloadedFileContents);
                        try {
                            // Returns a `Facebook\FacebookResponse` object
                            $response = $fb->post(
                                '/' . $adAccountId . '/adimages/',
                                array(
                                    'bytes' => $img
                                ),
                                $pageAccessToken
                            );
                        } catch (Facebook\Exceptions\FacebookResponseException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');
                            return 'Graph returned an error: ' . $e->getMessage();
                        } catch (Facebook\Exceptions\FacebookSDKException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');
                            return 'Graph returned an error: ' . $e->getMessage();
                        }
                        $graphNode = $response->getGraphNode();
                        $imagehash = $graphNode['images']['bytes']['hash'];
                        $editorialPlanDetailInsert->postImageTitle=$data['postImageTitle'];
                        $editorialPlanDetailInsert->postImageUrl=$photoUrl[0];
                        $editorialPlanDetailInsert->postImageDescription=$data['postImageDescription'];
                        $editorialPlanDetailInsert->postImageHash=$imagehash;


                        try {
                            $response = $fb->post(
                                '/' . $adAccountId . '/adcreatives',
                                array(
                                    'name' => $title,
                                    'object_story_spec' => array(
                                        'page_id' => $editorialPlanShopAsSocial->page_id,
                                        'photo_data' => array(
                                            'image_hash' => $imagehash
                                        ),
                                        'title' => $data['postImageTitle'],
                                        'body' => $data['postImageDescription'],
                                        'link_url' => $data['postImageUrl'],
                                        'object_url' => $data['postImageUrl']
                                    ),
                                ),
                                $pageAccessToken
                            );
                        } catch (Facebook\Exceptions\FacebookResponseException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');
                            return 'Graph returned an error: ' . $e->getMessage();
                        } catch (Facebook\Exceptions\FacebookSDKException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');
                            return 'Graph returned an error: ' . $e->getMessage();
                        }
                        $graphNode = $response->getGraphNode();
                        $creativeId = $graphNode['id'];
                        $editorialPlanDetailInsert->creativeId = $creativeId;
                        try {
                            $response = $fb->post(
                                '/' . $adAccountId . '/ads',
                                array(
                                    'name' => $title,
                                    'adset_id' => $groupAdsName,
                                    'creative' => array('creative_id' => $creativeId),
                                    'status' => $campaignList[0]['effective_status'],
                                ),
                                $pageAccessToken
                            );

                        } catch (Facebook\Exceptions\FacebookResponseException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');
                            return 'Graph returned an error: ' . $e->getMessage();
                        } catch (Facebook\Exceptions\FacebookSDKException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error Sdk: ' . $e->getMessage(),$e->getLine(),'');
                            return 'Graph returned an error: ' . $e->getMessage();
                        }
                        $graphNode = $response->getGraphNode();
                        $editorialPlanDetailInsert->insertionId = $graphNode['id'];
                    }
                    break;
                case 9:
                    if ($isNewAdSet == '2') {


                        $account = new AdAccount($adAccountId);

                        $cursor = $account->getCampaigns(['id','name','objective','buying_type','effective_status']);
                        $campaignList = [];


                        foreach ($cursor as $campaign) {
                            $nameCampaign = $campaign->{CampaignFields::NAME};
                            $idCampaign = $campaign->{CampaignFields::ID};

                            $objective = $campaign->{CampaignFields::OBJECTIVE};
                            $buying_type = $campaign->{CampaignFields::BUYING_TYPE};
                            $effective_status = $campaign->{CampaignFields::EFFECTIVE_STATUS};
                            if ($idCampaign == $campaignId)
                                $campaignList[] = ['idCampaign' => $idCampaign,'nameCampaign' => $nameCampaign,'objective' => $objective,'buying_type' => $buying_type,'effective_status' => $effective_status];
                            $editorialPlanDetailInsert->facebookCampaignId=$idCampaign;
                            break;
                        }

                    }
                    if ($selecterCampaign == 1) {
                        $adSetList = [];
                        $adsets = $account->getAdSets(array(
                            AdSetFields::NAME,
                            AdSetFields::CONFIGURED_STATUS,
                            AdSetFields::EFFECTIVE_STATUS,
                            AdSetFields::ID,
                            AdSetFields::CAMPAIGN_ID,
                        ));

                        foreach ($adsets as $adset) {
                            $nameAdSet = $adset->{AdSetFields::NAME};
                            $idAdSet = $adset->{AdSetFields::ID};
                            $status = $adset->{CampaignFields::STATUS};
                            if ($idAdSet == $groupAdsName) {
                                $adSetList[] = ['idAdSet' => $idAdSet,'nameAdSet' => $nameAdSet,'status' => $status,'error' => '0'];
                                $editorialPlanDetailInsert->groupInsertionId=$idAdSet;
                            }
                        }


                        try {
                            // Returns a `Facebook\FacebookResponse` object
                            $response = $fb->post(
                                '/' . $adAccountId . '/adsets',
                                array(
                                    'name' => $adSetList[0]['nameAdSet'],
                                    'start_time' => $startEventForFacebook,
                                    'end_time' => $endEventForFacebook,
                                    'campaign_id' => $campaignId,
                                    'billing_event' => 'IMPRESSIONS',
                                    'bid_amount' => '500',
                                    'optimization_goal' => $campaignList[0]['objective'],
                                    'targeting' => '{"geo_locations":{"countries":["IT"]},"facebook_positions":["feed"],"publisher_platforms":["facebook","audience_network"]}',
                                    'status' => $campaignList[0]['effective_status'],
                                ),
                                $pageAccessToken
                            );
                        } catch
                        (Facebook\Exceptions\FacebookResponseException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'set Response adSet');
                            return 'Graph returned an error: ' . $e->getMessage();
                        } catch (Facebook\Exceptions\FacebookSDKException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'set Response adSet sdk');
                            return 'Facebook SDK returned an error: ' . $e->getMessage();

                        }
                        $graphNode = $response->getGraphNode();

                        $groupAdsName = $graphNode['id'];
                    }
                    if($data['type']=='formInsert') {
                        $arrayPhotoHash = [];
                        $i = 0;

                        $arrayLink = [];
                        $arrayDescription = [];
                        $arrayTitle = [];
                        if ($data['imageUrl1'] != '') {
                            array_push($arrayLink,$data['imageUrl1']);
                            array_push($arrayDescription,$data['descriptionImage1']);
                            array_push($arrayTitle,$data['imageTitle1']);
                            $editorialPlanDetailInsert->imageUrl1 = $data['imageUrl1'];
                            $editorialPlanDetailInsert->imageTitle1 = $data['imageTitle1'];
                            $editorialPlanDetailInsert->descriptionImage1 = $data['descriptionImage1'];

                        }
                        if ($data['imageUrl2'] != '') {
                            array_push($arrayLink,$data['imageUrl2']);
                            array_push($arrayDescription,$data['descriptionImage2']);
                            array_push($arrayTitle,$data['imageTitle2']);
                            $editorialPlanDetailInsert->imageUrl2 = $data['imageUrl2'];
                            $editorialPlanDetailInsert->imageTitle2 = $data['imageTitle2'];
                            $editorialPlanDetailInsert->descriptionImage2 = $data['descriptionImage2'];
                        }
                        if ($data['imageUrl3'] != '') {
                            array_push($arrayLink,$data['imageUrl3']);
                            array_push($arrayDescription,$data['descriptionImage3']);
                            array_push($arrayTitle,$data['imageTitle3']);
                            $editorialPlanDetailInsert->imageUrl3 = $data['imageUrl3'];
                            $editorialPlanDetailInsert->imageTitle3 = $data['imageTitle3'];
                            $editorialPlanDetailInsert->descriptionImage3 = $data['descriptionImage3'];

                        }
                        if ($data['imageUrl4'] != '') {
                            array_push($arrayLink,$data['imageUrl4']);
                            array_push($arrayDescription,$data['descriptionImage4']);
                            array_push($arrayTitle,$data['imageTitle4']);
                            $editorialPlanDetailInsert->imageUrl4 = $data['imageUrl4'];
                            $editorialPlanDetailInsert->imageTitle4 = $data['imageTitle4'];
                            $editorialPlanDetailInsert->descriptionImage4 = $data['descriptionImage4'];
                        }
                        if ($data['imageUrl5'] != '') {
                            array_push($arrayLink,$data['imageUrl5']);
                            array_push($arrayDescription,$data['descriptionImage5']);
                            array_push($arrayTitle,$data['imageTitle5']);
                            $editorialPlanDetailInsert->imageUrl5 = $data['imageUrl5'];
                            $editorialPlanDetailInsert->imageTitle5 = $data['imageTitle5'];
                            $editorialPlanDetailInsert->descriptionImage5 = $data['descriptionImage5'];
                        }
                        if ($data['imageUrl6'] != '') {
                            array_push($arrayLink,$data['imageUrl6']);
                            array_push($arrayDescription,$data['descriptionImage6']);
                            array_push($arrayTitle,$data['imageTitle6']);
                            $editorialPlanDetailInsert->imageUrl6 = $data['imageUrl6'];
                            $editorialPlanDetailInsert->imageTitle6 = $data['imageTitle6'];
                            $editorialPlanDetailInsert->descriptionImage6 = $data['descriptionImage6'];
                        }
                        if ($data['imageUrl7'] != '') {
                            array_push($arrayLink,$data['imageUrl7']);
                            array_push($arrayDescription,$data['descriptionImage7']);
                            array_push($arrayTitle,$data['imageTitle7']);
                            $editorialPlanDetailInsert->imageUrl7 = $data['imageUrl7'];
                            $editorialPlanDetailInsert->imageTitle7 = $data['imageTitle7'];
                            $editorialPlanDetailInsert->descriptionImage7 = $data['descriptionImage7'];
                        }
                        if ($data['imageUrl8'] != '') {
                            array_push($arrayLink,$data['imageUrl8']);
                            array_push($arrayDescription,$data['descriptionImage8']);
                            array_push($arrayTitle,$data['imageTitle8']);
                            $editorialPlanDetailInsert->imageUrl8 = $data['imageUrl8'];
                            $editorialPlanDetailInsert->imageTitle8 = $data['imageTitle8'];
                            $editorialPlanDetailInsert->descriptionImage8 = $data['descriptionImage8'];
                        }
                        if ($data['imageUrl9'] != '') {
                            array_push($arrayLink,$data['imageUrl9']);
                            array_push($arrayDescription,$data['descriptionImage9']);
                            array_push($arrayTitle,$data['imageTitle9']);
                            $editorialPlanDetailInsert->imageUrl9 = $data['imageUrl9'];
                            $editorialPlanDetailInsert->imageTitle9 = $data['imageTitle9'];
                            $editorialPlanDetailInsert->descriptionImage9 = $data['descriptionImage9'];
                        }
                        if ($data['imageUrl10'] != '') {
                            array_push($arrayLink,$data['imageUrl10']);
                            array_push($arrayDescription,$data['descriptionImage10']);
                            array_push($arrayTitle,$data['imageTitle10']);
                            $editorialPlanDetailInsert->imageUrl10 = $data['imageUrl10'];
                            $editorialPlanDetailInsert->imageTitle10 = $data['imageTitle10'];
                            $editorialPlanDetailInsert->descriptionImage10 = $data['descriptionImage10'];
                        }

                        foreach ($photoUrl as $photoHash) {

                            $downloadedFileContents = file_get_contents($photoHash);
                            $img = base64_encode($downloadedFileContents);

                            try {
                                $response = $fb->post(
                                    '/' . $adAccountId . '/adimages/',
                                    array(
                                        'bytes' => $img
                                    ),
                                    $pageAccessToken
                                );
                            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                                \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');
                                return 'Graph returned an error: ' . $e->getMessage();
                            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                                \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');
                                return 'Graph returned an error: ' . $e->getMessage();
                            }
                            $graphNode = $response->getGraphNode();
                            $arrayPhotoHash[] = ['description' => $arrayDescription[$i],'image_hash' => $graphNode['images']['bytes']['hash'],'link' => $arrayLink[$i],'name' => $arrayTitle[$i]];
                            switch (key($arrayLink[$i])) {
                                case 'imageUrl1':
                                    $editorialPlanDetailInsert->imageHash1 = $graphNode['images']['bytes']['hash'];
                                    break;
                                case 'imageUrl2':
                                    $editorialPlanDetailInsert->imageHash2 = $graphNode['images']['bytes']['hash'];
                                    break;
                                case 'imageUrl3':
                                    $editorialPlanDetailInsert->imageHash3 = $graphNode['images']['bytes']['hash'];
                                    break;
                                case 'imageUrl4':
                                    $editorialPlanDetailInsert->imageHash4 = $graphNode['images']['bytes']['hash'];
                                    break;
                                case 'imageUrl5':
                                    $editorialPlanDetailInsert->imageHash5 = $graphNode['images']['bytes']['hash'];
                                    break;
                                case 'imageUrl6':
                                    $editorialPlanDetailInsert->imageHash6 = $graphNode['images']['bytes']['hash'];
                                    break;
                                case 'imageUrl7':
                                    $editorialPlanDetailInsert->imageHash7 = $graphNode['images']['bytes']['hash'];
                                    break;
                                case 'imageUrl8':
                                    $editorialPlanDetailInsert->imageHash8 = $graphNode['images']['bytes']['hash'];
                                    break;
                                case 'imageUrl9':
                                    $editorialPlanDetailInsert->imageHash9 = $graphNode['images']['bytes']['hash'];
                                    break;
                                case 'imageUrl10':
                                    $editorialPlanDetailInsert->imageHash10 = $graphNode['images']['bytes']['hash'];
                                    break;

                            }
                            $i++;

                        }


                        try {
                            // Returns a `Facebook\FacebookResponse` object
                            $response = $fb->post(
                                '/' . $adAccountId . '/adcreatives',
                                array(
                                    'name' => $title,
                                    'object_story_spec' => array(
                                        'page_id' => $editorialPlanShopAsSocial->page_id,
                                        'link_data' => array(
                                            'child_attachments' => $arrayPhotoHash,
                                            'link' => $linkDestination),
                                    ),
                                ),
                                $pageAccessToken

                            );
                        } catch (Facebook\Exceptions\FacebookResponseException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');
                            return 'Graph returned an error: ' . $e->getMessage();
                        } catch (Facebook\Exceptions\FacebookSDKException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');
                            return 'Graph returned an error: ' . $e->getMessage();
                        }
                        $graphNode = $response->getGraphNode();
                        $creativeId = $graphNode['id'];


                        $editorialPlanDetailInsert->creativeId = $creativeId;
                        try {
                            $response = $fb->post(
                                '/' . $adAccountId . '/ads',
                                array(
                                    'name' => $title,
                                    'adset_id' => $groupAdsName,
                                    'creative' => array('creative_id' => $creativeId),
                                    'status' => $campaignList[0]['effective_status'],
                                ),
                                $pageAccessToken
                            );

                        } catch (Facebook\Exceptions\FacebookResponseException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');
                            return 'Graph returned an error: ' . $e->getMessage();
                        } catch (Facebook\Exceptions\FacebookSDKException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error Sdk: ' . $e->getMessage(),$e->getLine(),'');
                            return 'Graph returned an error: ' . $e->getMessage();
                        }
                        $graphNode = $response->getGraphNode();
                        $editorialPlanDetailInsert->insertionId = $graphNode['id'];
                    }
                    break;
                case 10:
                    if ($data['video1'] != '') {
                        $postVideoTitle = $data['postVideoTitle'];
                        $namePath = $tempFolder . trim($postVideoTitle) . '1.jpg';
                        $ffmpeg = FFMpeg\FFMpeg::create();
                        $video = $ffmpeg->open($data['video1']);
                        $video
                            ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(1))
                            ->save($namePath);
                        $image = new ImageManager(new S3Manager($config['credential']),$this->app,$tempFolder);
                        //$fileName=$tempFolder.$title.'1.jpg';
                        $fileName['name'] = trim($postVideoTitle) . '1.jpg';
                        $res = $image->processImageEditorialUploadPhoto(trim($postVideoTitle) . '1.jpg',$fileName,$config['bucket'] . '-editorial','plandetail-images');
                        $imageThumbVideo1 = "https://iwes-editorial.s3-eu-west-1.amazonaws.com/plandetail-images/" . $fileName['name'];

                    }
                    if ($isNewAdSet == '2') {


                        $account = new AdAccount($adAccountId);

                        $cursor = $account->getCampaigns(['id','name','objective','buying_type','effective_status']);
                        $campaignList = [];
// Loop over objects

                        foreach ($cursor as $campaign) {
                            $nameCampaign = $campaign->{CampaignFields::NAME};
                            $idCampaign = $campaign->{CampaignFields::ID};

                            $objective = $campaign->{CampaignFields::OBJECTIVE};
                            $buying_type = $campaign->{CampaignFields::BUYING_TYPE};
                            $effective_status = $campaign->{CampaignFields::EFFECTIVE_STATUS};
                            if ($idCampaign == $campaignId)
                                $campaignList[] = ['idCampaign' => $idCampaign,'nameCampaign' => $nameCampaign,'objective' => $objective,'buying_type' => $buying_type,'effective_status' => $effective_status];
                            $editorialPlanDetailInsert->facebookCampaignId=$idCampaign;
                            break;
                        }
                    }
                    if ($selecterCampaign == 1) {
                        $adSetList = [];
                        $adsets = $account->getAdSets(array(
                            AdSetFields::NAME,
                            AdSetFields::CONFIGURED_STATUS,
                            AdSetFields::EFFECTIVE_STATUS,
                            AdSetFields::ID,
                            AdSetFields::CAMPAIGN_ID,
                        ));
// Loop over objects

                        foreach ($adsets as $adset) {
                            $nameAdSet = $adset->{AdSetFields::NAME};
                            $idAdSet = $adset->{AdSetFields::ID};
                            $status = $adset->{CampaignFields::STATUS};
                            if ($idAdSet == $groupAdsName) {
                                $adSetList[] = ['idAdSet' => $idAdSet,'nameAdSet' => $nameAdSet,'status' => $status,'error' => '0'];
                                $editorialPlanDetailInsert->groupInsertionId=$idAdSet;
                            }
                        }


                        try {
                            // Returns a `Facebook\FacebookResponse` object
                            $response = $fb->post(
                                '/' . $adAccountId . '/adsets',
                                array(
                                    'name' => $adSetList[0]['nameAdSet'],
                                    'start_time' => $startEventForFacebook,
                                    'end_time' => $endEventForFacebook,
                                    'campaign_id' => $campaignId,
                                    'billing_event' => 'IMPRESSIONS',
                                    'bid_amount' => '500',
                                    'optimization_goal' => $campaignList[0]['objective'],
                                    'targeting' => '{"geo_locations":{"countries":["IT"]},"facebook_positions":["feed"],"publisher_platforms":["facebook","audience_network"]}',
                                    'status' => $campaignList[0]['effective_status'],
                                ),
                                $pageAccessToken
                            );
                        } catch
                        (Facebook\Exceptions\FacebookResponseException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'set Response adSet');
                            return 'Graph returned an error: ' . $e->getMessage();
                        } catch (Facebook\Exceptions\FacebookSDKException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'set Response adSet sdk');
                            return 'Facebook SDK returned an error: ' . $e->getMessage();

                        }
                        $graphNode = $response->getGraphNode();

                        $groupAdsName = $graphNode['id'];
                    }
                    if($data['type']=='') {
                        try {
                            // Returns a `Facebook\FacebookResponse` object
                            $response = $fb->post(
                                '/' . $adAccountId . '/advideos',
                                array(
                                    'title' => $data['postVideoTitle'],
                                    'description' => $data['postDescriptionVideo'],
                                    'file_url' => $data['video1'],
                                    'name' => $data['postVideoTitle']
                                ),
                                $pageAccessToken
                            );
                        } catch
                        (Facebook\Exceptions\FacebookResponseException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'set Response adSet');
                            return 'Graph returned an error: ' . $e->getMessage();
                        } catch (Facebook\Exceptions\FacebookSDKException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'set Response adSet sdk');
                            return 'Facebook SDK returned an error: ' . $e->getMessage();

                        }
                        $graphNode = $response->getGraphNode();

                        $videoFacebookId = $graphNode['id'];
                        sleep(10);
                        try {
                            // Returns a `Facebook\FacebookResponse` object
                            $response = $fb->post(
                                '/' . $adAccountId . '/adcreatives',
                                array(
                                    'name' => $title,
                                    'object_story_spec' => array(
                                        'page_id' => $editorialPlanShopAsSocial->page_id,
                                        'video_data' => array(
                                            'image_url' => $imageThumbVideo1,
                                            'video_id' => $videoFacebookId
                                        ),
                                    ),
                                ),
                                $pageAccessToken

                            );
                        } catch (Facebook\Exceptions\FacebookResponseException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');
                            return 'Graph returned an error: ' . $e->getMessage();
                        } catch (Facebook\Exceptions\FacebookSDKException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');
                            return 'Graph returned an error: ' . $e->getMessage();
                        }
                        $graphNode = $response->getGraphNode();
                        $creativeId = $graphNode['id'];


                        $editorialPlanDetailInsert->creativeId = $creativeId;
                        $editorialPlanDetailInsert->postVideoCallToAction = $data['postVideoCallToAction'];
                        $editorialPlanDetailInsert->videoFacebookId = $videoFacebookId;
                        $editorialPlanDetailInsert->postVideoTitle = $data['postVideoTitle'];
                        $editorialPlanDetailInsert->postDescriptionVideo = $data['postDescriptionVideo'];
                        $editorialPlanDetailInsert->video1 = $data['video1'];
                        try {
                            $response = $fb->post(
                                '/' . $adAccountId . '/ads',
                                array(
                                    'name' => $title,
                                    'adset_id' => $groupAdsName,
                                    'creative' => array('creative_id' => $creativeId),
                                    'status' => $campaignList[0]['effective_status'],
                                ),
                                $pageAccessToken
                            );

                        } catch (Facebook\Exceptions\FacebookResponseException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');
                            return 'Graph returned an error: ' . $e->getMessage();
                        } catch (Facebook\Exceptions\FacebookSDKException $e) {
                            \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error Sdk: ' . $e->getMessage(),$e->getLine(),'');
                            return 'Graph returned an error: ' . $e->getMessage();
                        }
                        $graphNode = $response->getGraphNode();
                        $editorialPlanDetailInsert->insertionId = $graphNode['id'];
                    }


                    break;


            }
            if ($isNewAdSet != '0') {
                $editorialPlanDetailInsert->groupInsertionId = $groupAdsName;
            }
            $findFoison=\Monkey::app()->repoFactory->create('Foison')->findOneBy(['id'=>$data['foisonId']]);
            $userId=$findFoison->userId;
            $editorialPlanDetailInsert->userId=$userId;
            $editorialPlanDetailInsert->smartInsert();



            /*  foreach ($photoUrl as $file) {
                  unlink($tempFolder . $file);
              }*/
            $res = "Dettaglio Piano Editoriale inserito con successo!";
            /** @var ARepo $shopRepo */
            $ePlanRepo = \Monkey::app()->repoFactory->create('EditorialPlan');

            /** @var CEditorialPlan $editorialPlan */
            $editorialPlan = $ePlanRepo->findOneBy(['id' => $editorialPlanId]);

            $shopId = $editorialPlan->shop->id;
            $shopEmail = $editorialPlan->shop->referrerEmails;
            /** var ARepo $editorialPlanArgumentRepo */
            $editorialPlanArgumentRepo = \Monkey::app()->repoFactory->create('EditorialPlanArgument');

            /** @var CEditorialPlanArgument $editorialPlanArgument */
            $editorialPlanArgument = $editorialPlanArgumentRepo->findOneBy(['id' => $argument]);
            $argumentName = $editorialPlanArgument->titleArgument;
            /** @var Ceditorial $to */
            $to = $shopEmail;
            $userFind=\Monkey::app()->repoFactory->create('User')->findOneBy(['id'=>$userId]);
            $userEditor=[$userFind->email];
            $editorialPlanName = $editorialPlan->name;
            $subject = "Creazione Nuovo Dettaglio Piano Editoriale";
            $message = "Creazione Nuovo dettaglio Piano Editoriale<p>";
            $message .= "Title:" . $title . "<p>";
            $message .= "Data di Inizio:" . $startEventDate . "<p>";
            $message .= "Data di Fine:" . $endEventDate . "<p>";
            $message .= "Argomento:" . $argumentName . "<p>";
            $message .= "Descrizione:" . $description . "<p>";
            $message .= "Stato:" . $status . "<p>";
            $message .= "Note:" . $note . "<p>";
            /** @var ARepo $ePlanSocialRepo */
            $ePlanSocialRepo = \Monkey::app()->repoFactory->create('EditorialPlanSocial');
            /** @var CEditorialPlanSocial $editorialPlanSocial */
            $editorialPlanSocial = $ePlanSocialRepo->findOneBy(['id' => $socialId]);

            /** @var CObjectCollection $editorialPlanSocialName */
            $editorialPlanSocialName = $editorialPlanSocial->name . "<p>";
            $message .= "Media utilizzato:" . $editorialPlanSocialName . "<p>";
            $message .= "Piano Editoriale:" . $editorialPlanName . "<p>";


            if ($notifyEmail === "yesNotify") {

                if (ENV == 'dev') return false;
                /** @var \bamboo\domain\repositories\CEmailRepo $emailRepo */
                $emailRepo = \Monkey::app()->repoFactory->create('Email');
                if (!is_array($to)) {
                    $to = [$to];
                }
                $emailRepo->newMail('Iwes IT Department <it@iwes.it>',$to,$userEditor,[],$subject,$message,null,null,null,'mailGun',false,null);
            }

        } else {
            //Se hai trovato qualcosa allora restituitsci messaggio di errore
            $res = "Esiste già un Evento Azione per il  piano Editoriale con lo stesso nome";
        }


        return $res;
    }


}
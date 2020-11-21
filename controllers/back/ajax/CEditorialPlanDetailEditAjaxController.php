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
use bamboo\core\facebook\CFacebookCookieSession;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanArgument;
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

class CEditorialPlanDetailEditAjaxController extends AAjaxController
{

    public function post()
      {
          $data = \Monkey::app()->router->request()->getRequestData();
          $title = $data['title'];
          if ($title == '') {
              return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> titolo non compilato</i>';
          }
          $isEventVisible = $data['isEventVisible'];
          $creativeId=$data['creativeId'];
          $insertionId=$data['insertionId'];
          $editorialPlanDetailId=$data['editorialPlanDetailId'];
          $groupInsertionId=$data['groupInsertionId'];
          $startEventDate = $data['start'];
          $endEventDate = $data['end'];
          $argument = $data['argument'];
          $fason=$data['fason'];
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
          $array_video = [];
          


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





              /** @var CEditorialPlanDetail $editorialPlanDetailUpdate */
              $editorialPlanDetailUpdate = \Monkey::app()->repoFactory->create('EditorialPlanDetail')->findOneBy(['id'=>$editorialPlanDetailId]);

              $editorialPlanDetailUpdate->title = $title;
              $editorialPlanDetailUpdate->isEventVisible = $isEventVisible;
              $editorialPlanDetailUpdate->startEventDate = $startEventDate;
              $editorialPlanDetailUpdate->endEventDate = $endEventDate;
              $editorialPlanDetailUpdate->editorialPlanArgumentId = $argument;
              $editorialPlanDetailUpdate->isVisibleEditorialPlanArgument = $isVisibleEditorialPlanArgument;
              $editorialPlanDetailUpdate->description = $description;
              $editorialPlanDetailUpdate->isVisibleDescription = $isVisibleDescription;
              $editorialPlanDetailUpdate->photoUrl = $groupimage;
              $editorialPlanDetailUpdate->isVisiblePhotoUrl = $isVisiblePhotoUrl;
              $editorialPlanDetailUpdate->status = $status;
              $editorialPlanDetailUpdate->note = $note;
              $editorialPlanDetailUpdate->linkDestination = $linkDestination;
              $editorialPlanDetailUpdate->isVisibleNote = $isVisibleNote;
              $editorialPlanDetailUpdate->socialId = $socialId;
              $editorialPlanDetailUpdate->bodyEvent = $bodyEvent;
              $editorialPlanDetailUpdate->lifetime_budget = $lifetime_budget;
              $editorialPlanDetailUpdate->buying_type = $buying_type;
              $editorialPlanDetailUpdate->objective = $objective;
              if ($facebookCampaignId != 'notExist') {
                  $editorialPlanDetailUpdate->facebookCampaignId = $facebookCampaignId;
              }

              $editorialPlanDetailUpdate->isVisibleBodyEvent = $isVisibleBodyEvent;
              $editorialPlanDetailUpdate->editorialPlanId = $editorialPlanId;


          $ad = new Ad($insertionId);
          $ad->deleteSelf();
          $creative = new AdCreative($creativeId);
          $creative->deleteSelf();


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


                      $linkData = [
                          'message' => $editorialPlan->name,
                          'name' => $data['postImageTitle'],
                          'link' => $linkDestination,
                          'description' => $data['postImageDescription'],
                          'picture' => $photoUrl[0]
                      ];
                  $editorialPlanDetailUpdate->postImageTitle=$data['postImageTitle'];
                  $editorialPlanDetailUpdate->postImageUrl=$photoUrl[0];
                  $editorialPlanDetailUpdate->postImageDescription=$data['postImageDescription'];
                      try {
                          $response = $fb->post('/me/feed',$linkData,$pageAccessToken);
                      } catch (Facebook\Exceptions\FacebookResponseException $e) {
                          \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');

                      } catch (Facebook\Exceptions\FacebookSDKException $e) {
                          \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController','Error','Graph returned an error Sdk: ' . $e->getMessage(),$e->getLine(),'');

                      }
                      $graphNode = $response->getGraphNode();
                      $insertionId = $graphNode['id'];
                      $editorialPlanDetailUpdate->insertionId = $graphNode['id'];

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
                              $editorialPlanDetailUpdate->facebookCampaignId=$idCampaign;
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
                                  $editorialPlanDetailUpdate->groupInsertionId=$idAdSet;
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
                      $editorialPlanDetailUpdate->postImageTitle=$data['postImageTitle'];
                      $editorialPlanDetailUpdate->postImageUrl=$photoUrl[0];
                      $editorialPlanDetailUpdate->postImageDescription=$data['postImageDescription'];
                      $editorialPlanDetailUpdate->postImageHash=$imagehash;


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
                      $editorialPlanDetailUpdate->creativeId = $creativeId;
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
                      $editorialPlanDetailUpdate->insertionId = $graphNode['id'];
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
                              $editorialPlanDetailUpdate->facebookCampaignId=$idCampaign;
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
                                  $editorialPlanDetailUpdate->groupInsertionId=$idAdSet;
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

                      $arrayPhotoHash = [];
                      $i = 0;

                      $arrayLink = [];
                      $arrayDescription = [];
                      $arrayTitle = [];
                      if ($data['imageUrl1'] != '') {
                          array_push($arrayLink,$data['imageUrl1']);
                          array_push($arrayDescription,$data['descriptionImage1']);
                          array_push($arrayTitle,$data['imageTitle1']);
                          $editorialPlanDetailUpdate->imageUrl1 = $data['imageUrl1'];
                          $editorialPlanDetailUpdate->imageTitle1 = $data['imageTitle1'];
                          $editorialPlanDetailUpdate->descriptionImage1 = $data['descriptionImage1'];

                      }
                      if ($data['imageUrl2'] != '') {
                          array_push($arrayLink,$data['imageUrl2']);
                          array_push($arrayDescription,$data['descriptionImage2']);
                          array_push($arrayTitle,$data['imageTitle2']);
                          $editorialPlanDetailUpdate->imageUrl2 = $data['imageUrl2'];
                          $editorialPlanDetailUpdate->imageTitle2 = $data['imageTitle2'];
                          $editorialPlanDetailUpdate->descriptionImage2 = $data['descriptionImage2'];
                      }
                      if ($data['imageUrl3'] != '') {
                          array_push($arrayLink,$data['imageUrl3']);
                          array_push($arrayDescription,$data['descriptionImage3']);
                          array_push($arrayTitle,$data['imageTitle3']);
                          $editorialPlanDetailUpdate->imageUrl3 = $data['imageUrl3'];
                          $editorialPlanDetailUpdate->imageTitle3 = $data['imageTitle3'];
                          $editorialPlanDetailUpdate->descriptionImage3 = $data['descriptionImage3'];

                      }
                      if ($data['imageUrl4'] != '') {
                          array_push($arrayLink,$data['imageUrl4']);
                          array_push($arrayDescription,$data['descriptionImage4']);
                          array_push($arrayTitle,$data['imageTitle4']);
                          $editorialPlanDetailUpdate->imageUrl4 = $data['imageUrl4'];
                          $editorialPlanDetailUpdate->imageTitle4 = $data['imageTitle4'];
                          $editorialPlanDetailUpdate->descriptionImage4 = $data['descriptionImage4'];
                      }
                      if ($data['imageUrl5'] != '') {
                          array_push($arrayLink,$data['imageUrl5']);
                          array_push($arrayDescription,$data['descriptionImage5']);
                          array_push($arrayTitle,$data['imageTitle5']);
                          $editorialPlanDetailUpdate->imageUrl5 = $data['imageUrl5'];
                          $editorialPlanDetailUpdate->imageTitle5 = $data['imageTitle5'];
                          $editorialPlanDetailUpdate->descriptionImage5 = $data['descriptionImage5'];
                      }
                      if ($data['imageUrl6'] != '') {
                          array_push($arrayLink,$data['imageUrl6']);
                          array_push($arrayDescription,$data['descriptionImage6']);
                          array_push($arrayTitle,$data['imageTitle6']);
                          $editorialPlanDetailUpdate->imageUrl6 = $data['imageUrl6'];
                          $editorialPlanDetailUpdate->imageTitle6 = $data['imageTitle6'];
                          $editorialPlanDetailUpdate->descriptionImage6 = $data['descriptionImage6'];
                      }
                      if ($data['imageUrl7'] != '') {
                          array_push($arrayLink,$data['imageUrl7']);
                          array_push($arrayDescription,$data['descriptionImage7']);
                          array_push($arrayTitle,$data['imageTitle7']);
                          $editorialPlanDetailUpdate->imageUrl7 = $data['imageUrl7'];
                          $editorialPlanDetailUpdate->imageTitle7 = $data['imageTitle7'];
                          $editorialPlanDetailUpdate->descriptionImage7 = $data['descriptionImage7'];
                      }
                      if ($data['imageUrl8'] != '') {
                          array_push($arrayLink,$data['imageUrl8']);
                          array_push($arrayDescription,$data['descriptionImage8']);
                          array_push($arrayTitle,$data['imageTitle8']);
                          $editorialPlanDetailUpdate->imageUrl8 = $data['imageUrl8'];
                          $editorialPlanDetailUpdate->imageTitle8 = $data['imageTitle8'];
                          $editorialPlanDetailUpdate->descriptionImage8 = $data['descriptionImage8'];
                      }
                      if ($data['imageUrl9'] != '') {
                          array_push($arrayLink,$data['imageUrl9']);
                          array_push($arrayDescription,$data['descriptionImage9']);
                          array_push($arrayTitle,$data['imageTitle9']);
                          $editorialPlanDetailUpdate->imageUrl9 = $data['imageUrl9'];
                          $editorialPlanDetailUpdate->imageTitle9 = $data['imageTitle9'];
                          $editorialPlanDetailUpdate->descriptionImage9 = $data['descriptionImage9'];
                      }
                      if ($data['imageUrl10'] != '') {
                          array_push($arrayLink,$data['imageUrl10']);
                          array_push($arrayDescription,$data['descriptionImage10']);
                          array_push($arrayTitle,$data['imageTitle10']);
                          $editorialPlanDetailUpdate->imageUrl10 = $data['imageUrl10'];
                          $editorialPlanDetailUpdate->imageTitle10 = $data['imageTitle10'];
                          $editorialPlanDetailUpdate->descriptionImage10 = $data['descriptionImage10'];
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
                                  $editorialPlanDetailUpdate->imageHash1 = $graphNode['images']['bytes']['hash'];
                                  break;
                              case 'imageUrl2':
                                  $editorialPlanDetailUpdate->imageHash2 = $graphNode['images']['bytes']['hash'];
                                  break;
                              case 'imageUrl3':
                                  $editorialPlanDetailUpdate->imageHash3 = $graphNode['images']['bytes']['hash'];
                                  break;
                              case 'imageUrl4':
                                  $editorialPlanDetailUpdate->imageHash4 = $graphNode['images']['bytes']['hash'];
                                  break;
                              case 'imageUrl5':
                                  $editorialPlanDetailUpdate->imageHash5 = $graphNode['images']['bytes']['hash'];
                                  break;
                              case 'imageUrl6':
                                  $editorialPlanDetailUpdate->imageHash6 = $graphNode['images']['bytes']['hash'];
                                  break;
                              case 'imageUrl7':
                                  $editorialPlanDetailUpdate->imageHash7 = $graphNode['images']['bytes']['hash'];
                                  break;
                              case 'imageUrl8':
                                  $editorialPlanDetailUpdate->imageHash8 = $graphNode['images']['bytes']['hash'];
                                  break;
                              case 'imageUrl9':
                                  $editorialPlanDetailUpdate->imageHash9 = $graphNode['images']['bytes']['hash'];
                                  break;
                              case 'imageUrl10':
                                  $editorialPlanDetailUpdate->imageHash10 = $graphNode['images']['bytes']['hash'];
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


                      $editorialPlanDetailUpdate->creativeId = $creativeId;
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
                      $editorialPlanDetailUpdate->insertionId = $graphNode['id'];
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
                              $editorialPlanDetailUpdate->facebookCampaignId=$idCampaign;
                              break;
                          }

                          $adSetList = [];
                          $account = new AdAccount($adAccountId);
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
                                  $editorialPlanDetailUpdate->groupInsertionId=$idAdSet;
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


                      $editorialPlanDetailUpdate->creativeId = $creativeId;
                      $editorialPlanDetailUpdate->postVideoCallToAction = $data['postVideoCallToAction'];
                      $editorialPlanDetailUpdate->videoFacebookId = $videoFacebookId;
                      $editorialPlanDetailUpdate->postVideoTitle = $data['postVideoTitle'];
                      $editorialPlanDetailUpdate->postDescriptionVideo = $data['postDescriptionVideo'];
                      $editorialPlanDetailUpdate->video1 = $data['video1'];
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
                      $editorialPlanDetailUpdate->insertionId = $graphNode['id'];


                      break;


              }
              if ($isNewAdSet != '0') {
                  $editorialPlanDetailUpdate->groupInsertionId = $groupAdsName;
              }
          $findFoison=\Monkey::app()->repoFactory->create('Foison')->findOneBy(['id'=>$data['fason']]);
          $userId=$findFoison->userId;
              $editorialPlanDetailUpdate->userId=$userId;
              $editorialPlanDetailUpdate->update();



              /*  foreach ($photoUrl as $file) {
                    unlink($tempFolder . $file);
                }*/
              $res = "Dettaglio Piano Editoriale modifica con successo!";
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
              $subject = "Modifica  Dettaglio Piano Editoriale";
              $message = "Modifica Nuovo dettaglio Piano Editoriale<p>";
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




          return $res;
      }
      public function delete(){

          $data = $this->app->router->request()->getRequestData();

          $editorialPlanDetailId = $data['editorialPlanDetailId'];
          /** @var CRepo $editorialPlanDetail */
          $editorialPlanDetail = \Monkey::app()->repoFactory->create('editorialPlanDetail');

          /** @var CEditorialPlanDetail $editorial */
          $editorial = $editorialPlanDetail->findOneBy(['id' => $editorialPlanDetailId]);

          $editorial->delete();


          $res = "  Dettaglio Piano Editoriale Cancellato con Successo";
          return $res;
      }

    
}
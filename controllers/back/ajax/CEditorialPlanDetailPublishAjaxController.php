<?php


namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\facebook\CFacebookCookieSession;
use FacebookAds\Http\Exception\RequestException;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanArgument;
use bamboo\domain\entities\CEditorialPlanDetail;
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
use FacebookAds\Object\AdCreativeLinkData;
use FacebookAds\Object\Fields\AdCreativeLinkDataFields;
use FacebookAds\Object\AdCreativeObjectStorySpec;
use FacebookAds\Object\Fields\AdCreativeObjectStorySpecFields;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\Fields\AdsInsightsFields;
use FacebookAds\Object\AdImage;
use FacebookAds\Object\Fields\AdImageFields;


/**
 * Class CEditorialPlanDetailPublishAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 31/07/2020
 * @since 1.0
 */
class CEditorialPlanDetailPublishAjaxController extends AAjaxController
{


    public function post()
    {
        if (ENV == 'dev') {
            require '/media/sf_sites/vendor/mailgun/vendor/autoload.php';
        } else {
            require '/home/shared/vendor/mailgun/vendor/autoload.php';
        }
        $this->app->vendorLibraries->load("facebook");
        $c = new CFacebookCookieSession($this->app);
        $fbConfig = $this->app->cfg()->fetch('miscellaneous','facebook');
        $fb = new Facebook([
            'app_id' => $fbConfig['app_id'], // Replace {app-id} with your app id
            'app_secret' => $fbConfig['app_secret'],
            'default_graph_version' => 'v7.0',
            'persistent_data_handler' => &$c
        ]);

        $res = '';
        $data = $this->app->router->request()->getRequestData();

        $editorialPlanDetailId = $data['editorialPlanDetailId'];
        /** @var CRepo $editorialPlanDetail */
        $editorialPlanDetail = \Monkey::app()->repoFactory->create('EditorialPlanDetail')->findOneBy(['id' => $editorialPlanDetailId]);
        /** @var CRepo $editorialPlan */
        $editorialPlan = \Monkey::app()->repoFactory->create('EditorialPlan')->findOneBy(['id' => $editorialPlanDetail->editorialPlanId]);
        $facebookMarketAccountId=$editorialPlan->facebookMarketAccountId;
        $adAccountId='act_'.$facebookMarketAccountId;

        $editorialPlanShopAsSocial = \Monkey::app()->repoFactory->create('EditorialPlanShopAsSocial')->findOneBy(['shopId' => $editorialPlan->shopId]);
        $pageAccessToken = 'EAALxfLD2ZAZCoBAGWoLZAfszPwLN4WnPehwiHyym7tZAOZAsZAVVHMQkT3ZCIsZAmkXK3hQZCKlvS66tjPyEVtCaDwQzUoZCyh5rusYHYt0oeunHzZAbwaBUwMRGhKet2BORvAiypkvu21XJWh7pkAZCGiKRXpN2EHgZBwmxHyKcsd7w1KQZDZD';
        $this->app->vendorLibraries->load("facebookBusiness");
        $_SESSION['facebook_access_token']=$pageAccessToken;
        Api::init(
            $fbConfig['app_id'], // App ID
            $fbConfig['app_secret'],
            $_SESSION['facebook_access_token'] // Your user access token
        );

        /*

        $linkData = [
            'message' => $editorialPlan->name,
            'name' => $editorialPlanDetail->title,
            'link' => $editorialPlanDetail->linkDestination,
            'description' => $editorialPlanDetail->description,
            'picture' => $editorialPlanDetail->photoUrl
        ];
        try {
            $response = $fb->post('/me/feed',$linkData,$pageAccessToken);
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            return $res = 'Graph returned an error: ' . $e->getMessage();

        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            return $res = 'Facebook SDK returned an error: ' . $e->getMessage();

        }
        $graphNode = $response->getGraphNode();
        $id=$graphNode['id'];*/






        $account = new AdAccount($adAccountId);


        $adSetList=[];

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->post(
                '/'.$adAccountId.'/adsets',
                array (
                    'name' => 'prova2',
                    'start_time' => '2020-08-14T12:46:42-0700',
                    'end_time' => '2020-08-21T12:46:42-0700',
                    'campaign_id' => '6186226299176',
                    'billing_event' => 'IMPRESSIONS',
                    'bid_amount'=>'500',
                    'optimization_goal' => 'REACH',
                    'targeting' => '{"geo_locations":{"countries":["IT"]},"facebook_positions":["feed"],"publisher_platforms":["facebook","audience_network"]}',
                    'status' => 'PAUSED',
                ),
                $pageAccessToken
            );
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        $graphNode = $response->getGraphNode();
        /* handle the result */
        $groupAdsName=$graphNode['id'];
        $adsets = $account->getAdSets(array(
            AdSetFields::NAME,
            AdSetFields::CONFIGURED_STATUS,
            AdSetFields::EFFECTIVE_STATUS,
            AdSetFields::ID,
        ));
// Loop over objects
        if($adsets!=null) {
            foreach ($adsets as $adset) {
                $nameAdSet = $adset->{CampaignFields::NAME} . PHP_EOL;
                $idAdSet = $adset->{CampaignFields::ID};
                $status=$adset->{CampaignFields::EFFECTIVE_STATUS};
                if($idAdSet==$groupAdsName) {
                    $adSetList[] = ['idAdSet' => $idAdSet,'nameAdSet' => $nameAdSet,'status' => $status,'error' => '0'];
                    break;
                }
            }
        }
        $downloadedFileContents = file_get_contents($editorialPlanDetail->photoUrl);
        if($downloadedFileContents === false){
            throw new Exception('Failed to download file at: ' . $url);
        }
        //The path and filename that you want to save the file to.
        $tempFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'tempFolder')."/";
        $fileName = 'temp.jpg';
        $save=file_put_contents($tempFolder.$fileName,$downloadedFileContents);
        $newFile=$tempFolder.$fileName;
        $hashFile=hash_hmac('sha256', $newFile);
        $base64 = base64_encode($newFile);



        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get(
                '/'.$adAccountId.'/adimages',
                array (
                    'filename' => $editorialPlanDetail->photoUrl

                ),
                $pageAccessToken
            );
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        $graphNode = $response->getGraphNode();


        try {



            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->post(
                '/'.$adAccountId.'/adcreatives',
                array (
                    'title' => $editorialPlanDetail->title,
                    'name' => $editorialPlanDetail->title,
                    'body' => $editorialPlanDetail->description,
                    'object_url'=>$editorialPlanDetail->linkDestination,
                    'link_url'=>$editorialPlanDetail->linkDestination,
                    'image_file'=>$editorialPlanDetail->photoUrl
                ),
                $pageAccessToken
            );
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        $graphNode = $response->getGraphNode();
        /* handle the result */
       $creativeId=$graphNode['id'];
        $response = $fb->post(
            '/' . $adAccountId . '/ads',
            array(
                'name' => $editorialPlanDetail->title,
                'adset_id' =>$groupAdsName,
                'creative'=>array('creative_id'=>$creativeId),
                'status' => $adSetList[0]['status'],
            ),
            $pageAccessToken
        );
        $graphNode=$response->getGraphNode();
        $insertionId=$graphNode['id'];

        $res = "  Post pubblicato con successo";
        return $res;
    }
}
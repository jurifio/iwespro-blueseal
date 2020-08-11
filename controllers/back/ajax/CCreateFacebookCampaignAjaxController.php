<?php


namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\facebook\CFacebookCookieSession;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanArgument;
use bamboo\domain\entities\CEditorialPlanDetail;
use \bamboo\utils\time\STimeToolbox;
use Facebook\Facebook;
use DateTime;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Values\AdSetBillingEventValues;
use FacebookAds\Object\Values\AdSetOptimizationGoalValues;

/**
 * Class CCreateFacebookCampaignAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 05/08/2020
 * @since 1.0
 */
class CCreateFacebookCampaignAjaxController extends AAjaxController
{


    public function post()
    {
        if (ENV == 'dev') {
            require '/media/sf_sites/vendor/mailgun/vendor/autoload.php';
        } else {
            require '/home/shared/vendor/mailgun/vendor/autoload.php';
        }
        $this->app->vendorLibraries->load("facebook");
        $this->app->vendorLibraries->load("facebookBusiness");
        $c = new CFacebookCookieSession($this->app);
        $fbConfig = $this->app->cfg()->fetch('miscellaneous','facebook');
        $fb = new Facebook([
            'app_id' => $fbConfig['app_id'], // Replace {app-id} with your app id
            'app_secret' => $fbConfig['app_secret'],
            'default_graph_version' => 'v7.0',
            'persistent_data_handler' => &$c
        ]);
        $special_ad_categories=[];



        $res = '';
        $data = $this->app->router->request()->getRequestData();
        $editorialPlan=\Monkey::app()->repoFactory->create('EditorialPlan')->findOneBy(['id'=>$data['editorialPlanId']]);
        $facebookMarketAccountId=$editorialPlan->facebookMarketAccountId;
        $editorialPlanShopAsSocial = \Monkey::app()->repoFactory->create('EditorialPlanShopAsSocial')->findOneBy(['shopId' => $editorialPlan->shopId]);
        $pageAccessToken = $editorialPlanShopAsSocial->access_token;
        $adAccountId='act_'.$facebookMarketAccountId;
        $groupAdsName=$data['groupAdsName'];


    $linkData = array(
        'name' => $data['campaignName'],
        'buying_type' => $data['buying_type'],
        'objective' => 'REACH',
        'lifetime_budget' => str_replace('.','',$data['lifetime_budget']),
        'status' => 'PAUSED',
        'special_ad_categories' =>'NONE',
    );


            try {
                $response = $fb->post('/'.$adAccountId.'/campaigns',
                    $linkData,$pageAccessToken);
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                return $res = 'Graph returned an error: ' . $e->getMessage();

            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                return $res = 'Facebook SDK returned an error: ' . $e->getMessage();

            }
            $graphNode = $response->getGraphNode();

        $_SESSION['facebook_access_token']=$pageAccessToken;
         Api::init(
            $fbConfig['app_id'], // App ID
            $fbConfig['app_secret'],
            $_SESSION['facebook_access_token'] // Your user access token
        );


        $start_time = (new \DateTime("+1 week"))->format(DateTime::ISO8601);
        $end_time = (new \DateTime("+2 week"))->format(DateTime::ISO8601);


        $linkData1 = array(
            'name' => $groupAdsName,
            'optimization_goal'=> $data['objective'],
            'billing_event' => 'IMPRESSIONS',
            'objective' => 'REACH',
            'campaign_id'=> $graphNode['id'],
            'start_time'=>$start_time,
            'end_time'=>$end_time,
            'status' => 'PAUSED',
            'bid_amount'=>'500',
            'targeting' => '{"geo_locations":{"countries":["IT"]},"facebook_positions":["feed"],"publisher_platforms":["facebook","audience_network"]}',

        );
        $response1 = $fb->post('/' . $adAccountId . '/adsets',
            $linkData1,$pageAccessToken);
        $graphNode2=$response1->getGraphNode();

        $res = $graphNode['id'];
        return $res;
    }
    public function put()
    {
        if (ENV == 'dev') {
            require '/media/sf_sites/vendor/mailgun/vendor/autoload.php';
        } else {
            require '/home/shared/vendor/mailgun/vendor/autoload.php';
        }
        $this->app->vendorLibraries->load("facebookBusiness");
        $c = new CFacebookCookieSession($this->app);
        $fbConfig = $this->app->cfg()->fetch('miscellaneous','facebook');
        $fb = new Facebook([
            'app_id' => $fbConfig['app_id'], // Replace {app-id} with your app id
            'app_secret' => $fbConfig['app_secret'],
            'default_graph_version' => 'v7.0',
            'persistent_data_handler' => &$c
        ]);
        $special_ad_categories=[];



        $res = '';
        $data = $this->app->router->request()->getRequestData();
        $editorialPlan=\Monkey::app()->repoFactory->create('EditorialPlan')->findOneBy(['id'=>$data['editorialPlanId']]);
        $facebookMarketAccountId=$editorialPlan->facebookMarketAccountId;
        $editorialPlanShopAsSocial = \Monkey::app()->repoFactory->create('EditorialPlanShopAsSocial')->findOneBy(['shopId' => $editorialPlan->shopId]);
        $pageAccessToken = $editorialPlanShopAsSocial->access_token;
        $adAccountId='act_'.$facebookMarketAccountId;


        $linkData = array(
            'buying_type' => $data['buying_type'],
            'objective' => $data['objective'],
            'lifetime_budget' => str_replace('.','',$data['lifetime_budget']),
            'status' => 'PAUSED',
            'special_ad_categories' =>'NONE',
        );


        try {
            $response = $fb->post('/'.$data['campaignId'],
                $linkData,$pageAccessToken);
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            return $res = 'Graph returned an error: ' . $e->getMessage();

        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            return $res = 'Facebook SDK returned an error: ' . $e->getMessage();

        }
        $graphNode = $response->getGraphNode();

         $_SESSION['facebook_access_token']=$pageAccessToken;
          Api::init(
             $fbConfig['app_id'], // App ID
             $fbConfig['app_secret'],
             $_SESSION['facebook_access_token'] // Your user access token
         );


         $start_time = (new \DateTime("+1 week"))->format(DateTime::ISO8601);
         $end_time = (new \DateTime("+2 week"))->format(DateTime::ISO8601);


       /*  $linkData1 = array(
             'name' => $data['groupAdsName'],
             'optimization_goal'=> $data['objective'],
             'billing_event' => 'IMPRESSIONS',
             'objective' => $data['objective'],
             'campaign_id'=> $graphNode['id'],
             'start_time'=>$start_time,
             'end_time'=>$end_time,
             'status' => 'PAUSED',
             'bid_amount'=>'500',
             'targeting' => '{"geo_locations":{"countries":["IT"]},"facebook_positions":["feed"],"publisher_platforms":["facebook","audience_network"]}',

         );
         $response = $fb->post('https://graph.facebook.com/v2.11/'.$adAccountId.'/adsets',
             $linkData1,$pageAccessToken);
        $graphNode1 = $response->getGraphNode();*/


        $res = $graphNode['id'];
        return $res;
    }
}
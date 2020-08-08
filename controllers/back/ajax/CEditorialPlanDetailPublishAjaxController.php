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







        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->post(
                '/'.$adAccountId.'/adsets',
                array (
                    'name' => 'My First AdSet',
                    'start_time' => '2020-08-14T12:46:42-0700',
                    'end_time' => '2020-08-21T12:46:42-0700',
                    'campaign_id' => '6186226299176',
                    'billing_event' => 'IMPRESSIONS',
                    'bid_amount'=>'500',
                    'optimization_goal' => 'REACH',
                    'targeting' => '{"age_min":20,"age_max":24,"behaviors":[{"id":6002714895372,"name":"All travelers"}],"genders":[1],"geo_locations":{"countries":["US"],"regions":[{"key":"4081"}],"cities":[{"key":"777934","radius":10,"distance_unit":"mile"}]},"life_events":[{"id":6002714398172,"name":"Newlywed (1 year)"}],"facebook_positions":["feed"],"publisher_platforms":["facebook","audience_network"]}',
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



        $res = "  Post pubblicato con successo";
        return $res;
    }
}
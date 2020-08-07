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
        $this->app->vendorLibraries->load("facebookBusiness");
      /*  $_SESSION['facebook_access_token']=$pageAccessToken;
        Api::init(
            $fbConfig['app_id'], // App ID
            $fbConfig['app_secret'],
            $_SESSION['facebook_access_token']);*/


        $adset = new AdSet(null, $adAccountId);
        $adset->setData(array(
            AdSetFields::NAME => 'Peter//MyAdSet//PromotedObject//13112017-2',
            AdSetFields::PROMOTED_OBJECT => array(
                'page_id' => 126120924732396
            ),
            AdSetFields::OPTIMIZATION_GOAL => AdSetOptimizationGoalValues::REACH,
            AdSetFields::BILLING_EVENT => AdSetBillingEventValues::IMPRESSIONS,
            AdSetFields::BID_AMOUNT => 2,
            AdSetFields::DAILY_BUDGET => 1000,
            AdSetFields::CAMPAIGN_ID => '6186226299176',
            AdSetFields::TARGETING => (new Targeting())->setData(array(
                TargetingFields::GEO_LOCATIONS => array(
                    'countries' => array(
                        'IT',
                    ),
                )
            )),
            AdSetFields::START_TIME =>
                (new \DateTime("+1 week"))->format(\DateTime::ISO8601),
            AdSetFields::END_TIME =>
                (new \DateTime("+2 week"))->format(\DateTime::ISO8601),
        ));
        $adset->validate()->create(array(
            AdSet::STATUS_PARAM_NAME => AdSet::STATUS_ACTIVE,
        ));

$creative = new AdCreative(null, $adAccountId);
$creative->setData(array(
    AdCreativeFields::NAME => 'Prova nuova',
    AdCreativeFields::TITLE => 'Welcome to our app',
    AdCreativeFields::BODY => "We've got fun 'n' games",
    AdCreativeFields::IMAGE_HASH => $editorialPlanDetail->photoUrl,
    AdCreativeFields::OBJECT_URL => 'https://www.pickyshop.com',
));
$ad = new Ad(null, $adAccountId);
$ad->setData(array(
    AdFields::CREATIVE => $creative,
    AdFields::NAME => 'prova juri',
    AdFields::ADSET_ID => $adset->id,
));
$ad->create(array(
    Ad::STATUS_PARAM_NAME => Ad::STATUS_PAUSED,

));



        $res = "  Post pubblicato con successo";
        return $res;
    }
}
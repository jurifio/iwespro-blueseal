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
class CEditorialPlanDetailPreviewAjaxController extends AAjaxController
{


    public function get()
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
        $pageAccessToken = $editorialPlanShopAsSocial->access_token;
        $this->app->vendorLibraries->load("facebookBusiness");
        $_SESSION['facebook_access_token']=$pageAccessToken;
        Api::init(
            $fbConfig['app_id'], // App ID
            $fbConfig['app_secret'],
            $_SESSION['facebook_access_token'] // Your user access token
        );
            $res='';
            $parameter='ad_format=DESKTOP_FEED_STANDARD';
        try {
            $response = $fb->get(
                '/' . $editorialPlanDetail->insertionId . '/previews?'.$parameter,
                $pageAccessToken
            );

        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            \Monkey::app()->applicationLog('CEditorialPlanDetailPreviewAjaxController','Error','Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');
            return 'Graph returned an error: ' . $e->getMessage();
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            \Monkey::app()->applicationLog('CEditorialPlanDetailPreviewAjaxController','Error','Graph returned an error Sdk: ' . $e->getMessage(),$e->getLine(),'');
            return 'Graph returned an error: ' . $e->getMessage();
        }
        $graphNode = $response->getGraphEdge();
        $res=$graphNode[0]['body'];



        return $res;
    }
}
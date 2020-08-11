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
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Ad;
use FacebookAds\Object\Fields\AdFields;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\AdUser;
use FacebookAds\Object\Fields\AdAccountFields;
use FacebookAds\Object\Campaign;
use FacebookAds\Object\AdCampaign;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\Fields\AdsInsightsFields;

use FacebookAds\Object\Fields\AdSetFields;


class CSelectFacebookAdSetAjaxController extends AAjaxController
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
            'default_graph_version' => 'v5.0',
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
        $_SESSION['facebook_access_token']=$pageAccessToken;
        Api::init(
            $fbConfig['app_id'], // App ID
            $fbConfig['app_secret'],
            $_SESSION['facebook_access_token'] // Your user access token
        );
        $account = new AdAccount($adAccountId);


        $adSetList=[];
        $adsets = $account->getAdSets(array(
            AdSetFields::NAME,
            AdSetFields::CONFIGURED_STATUS,
            AdSetFields::EFFECTIVE_STATUS,
            AdSetFields::CAMPAIGN_ID,
            AdSetFields::ID,
        ));
// Loop over objects
      if($adsets!=null) {
          foreach ($adsets as $adset) {
              $nameAdSet = $adset->{AdSetFields::NAME};
              $idAdSet = $adset->{AdSetFields::ID};
              $status=$adset->{AdSetFields::EFFECTIVE_STATUS};
              $campaignId=$adset->{AdSetFields::CAMPAIGN_ID};
            if($campaignId==$data['campaignId']) {
                $adSetList[] = ['idAdSet' => $idAdSet,'nameAdSet' => $nameAdSet,'status' => $status,'error' => '0'];
            }
          }
      }else{
          $adSetList[] = ['idAdSet' => '0','nameAdSet' => '0','status'=>'0','error' => '1'];
      }
        return json_encode($adSetList);
    }
}
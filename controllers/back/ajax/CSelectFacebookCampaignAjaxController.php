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
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;


class CSelectFacebookCampaignAjaxController extends AAjaxController
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
        $params = array(
            'limit' => 500,
        );
        $fields = array(
            CampaignFields::NAME, /* <--- this is the error */
            CampaignFields::OBJECTIVE,
            CampaignFields::STATUS,
            CampaignFields::BUYING_TYPE,
            CampaignFields::LIFETIME_BUDGET,


        );
        $cursor = $account->getCampaigns(['id','name','objective','buying_type','effective_status','buying_type','lifetime_budget']);
        $campaignList=[];
// Loop over objects
      if($cursor!=null) {
          foreach ($cursor as $campaign) {
              $nameCampaign = $campaign->{CampaignFields::NAME};
              $idCampaign = $campaign->{CampaignFields::ID};
              $objective = $campaign->{CampaignFields::OBJECTIVE};
              $buying_type = $campaign->{CampaignFields::BUYING_TYPE};
              $lifetime_budget=$campaign->{CampaignFields::LIFETIME_BUDGET};
              $effective_status = $campaign->{CampaignFields::EFFECTIVE_STATUS};
              $campaignList[] = ['idCampaign' => $idCampaign,'nameCampaign' => $nameCampaign,'objective' => $objective,'buying_type' => $buying_type, 'effective_status' => $effective_status,'lifetime_budget'=>$lifetime_budget, 'error' => '0'];
          }
      }else{
          $campaignList[] = ['idCampaign' => '0','nameCampaign' => '0','lifetime_budget' => '0','objective' => '0','buying_type' => '0','effective_status' => '0','error' => '1'];
      }
        return json_encode($campaignList);
    }
}
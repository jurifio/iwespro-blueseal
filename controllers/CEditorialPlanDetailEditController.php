<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\facebook\CFacebookCookieSession;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanDetail;
use bamboo\domain\entities\CEditorialPlanSocial;
use bamboo\ecommerce\views\VBase;
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

/**
 * Class CEditorialPlanDetailAddController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 13/08/2020
 * @since 1.0
 */
class CEditorialPlanDetailEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "editorialplandetail_edit";

    public function get()
    {


        //$editorialPlanDetId = \Monkey::app()->router->request()->getRequestData('id');

        //trovi il piano editoriale
        /** @var ARepo $ePlanRepo */
        $ePlanRepo = \Monkey::app()->repoFactory->create('EditorialPlan');
        /** @var ARepo $editorialPlanDetailRepo */
        $editorialPlanDetailRepo=\Monkey::app()->repoFactory->create('EditorialPlanDetail');
        $editorialPlanDetailId =  \Monkey::app()->router->getMatchedRoute()->getComputedFilter('id');
        $editorialPlanDetail=$editorialPlanDetailRepo->findOneBy(['id'=>$editorialPlanDetailId]);
        $controlForApp=preg_match('/scatto Social/',$editorialPlanDetail->title);
        $allShops = \Monkey::app()->getUser()->hasPermission('allShops');
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




        $editorialPlan=\Monkey::app()->repoFactory->create('EditorialPlan')->findOneBy(['id'=>$editorialPlanDetail->editorialPlanId]);

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
        if($controlForApp==0) {
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
            $campaignList = [];
// Loop over objects
            $campaignSelected = '';
            $nameCampaignSelected = '';
            if ($cursor != null) {
                foreach ($cursor as $campaign) {
                    $nameCampaign = $campaign->{CampaignFields::NAME};
                    $idCampaign = $campaign->{CampaignFields::ID};
                    $objective = $campaign->{CampaignFields::OBJECTIVE};
                    $buying_type = $campaign->{CampaignFields::BUYING_TYPE};
                    $lifetime_budget = $campaign->{CampaignFields::LIFETIME_BUDGET};
                    $effective_status = $campaign->{CampaignFields::EFFECTIVE_STATUS};
                    if ($idCampaign == $editorialPlanDetail->facebookCampaignId) {
                        $campaignSelected = $idCampaign;
                        $nameCampaignSelected = $nameCampaign;
                    }
                }
            }
            $adSetSelected = '';
            $nameAdSetSelected = '';
            $adSetList = [];
            $adsets = $account->getAdSets(array(
                AdSetFields::NAME,
                AdSetFields::CONFIGURED_STATUS,
                AdSetFields::EFFECTIVE_STATUS,
                AdSetFields::CAMPAIGN_ID,
                AdSetFields::ID,
            ));
// Loop over objects
            if ($adsets != null) {
                foreach ($adsets as $adset) {
                    $nameAdSet = $adset->{AdSetFields::NAME};
                    $idAdSet = $adset->{AdSetFields::ID};
                    $status = $adset->{AdSetFields::EFFECTIVE_STATUS};
                    $campaignId = $adset->{AdSetFields::CAMPAIGN_ID};
                    if ($idAdSet == $editorialPlanDetail->groupInsertionId) {
                        $adSetSelected = $idAdSet;
                        $nameAdSetSelected = $nameAdSet;
                    }
                }
            } else {
                $adSetList[] = ['idAdSet' => '0','nameAdSet' => '0','status' => '0','error' => '1'];
            }
        }else{
            $campaignSelected = '';
            $nameCampaignSelected = '';
            $adSetSelected='';
            $nameAdSetSelected='';
        }

        /** @var aRepo $ePlanSocialRepo */
        $ePlanSocialRepo = \Monkey::app()->repoFactory->create('EditorialPlanSocial');
         /** @var CEditorialPlanSocial $editorialPlanSocial */
         $editorialPlanSocial=$ePlanSocialRepo->findAll();
        $contractId=$editorialPlan->contractId;
        $contractsRepo=\Monkey::app()->repoFactory->create('Contracts');
        $contracts=$contractsRepo->findOneBy(['id'=>$editorialPlan->contractId]);
        if(count($contracts)>0){

            $foisonId=$contracts->foisonId;

        }else{
            $contractId='';
            $foisonId='';
        }

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/editorialplandetail_edit.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'editorialPlanDetail'=>$editorialPlanDetail,
            'campaignSelected'=>$campaignSelected,
            'nameCampaignSelected'=>$nameCampaignSelected,
            'adSetSelected'=>$adSetSelected,
            'nameAdSetSelected'=>$nameAdSetSelected,
            'allShops'=>$allShops,
            'contractId'=>$contractId,
            'foisonId'=>$foisonId,
            'sidebar' => $this->sidebar->build()

        ]);
    }
}
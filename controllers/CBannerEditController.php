<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use PDO;
use PDOException;

/**
 * Class CBannerEditController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/03/2021
 * @since 1.0
 */
class CBannerEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "banner_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/banner_edit.php');

        $bannerId = $this->app->router->getMatchedRoute()->getComputedFilter('id');
        $bannerRepo = \Monkey::app()->repoFactory->create('Banner');
        $banner = $bannerRepo->findOneBy(['id'=>$bannerId]);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'banner' => $banner,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $bannerId = $this->app->router->getMatchedRoute()->getComputedFilter('id');

        $bannerRepo = \Monkey::app()->repoFactory->create('Banner');
        $banner = $bannerRepo->findOneBy(['id'=>$bannerId]);

        foreach ($data as $k => $v) {
            $banner->{$k} = $v;
            if($k=='campaignId'){
                $campaign=\Monkey::app()->repoFactory->create('Campaign')->findOneBy(['id'=>$v]);
                $remoteCampaignId=$campaign->remoteCampaignId;
            }
            if($k=='position'){
                switch(true){
                    case ($data['position'] %4 ==1):
                        $position=1;
                        break;
                    case ($data['position'] % 4 ==2):
                        $position=($data['position']-1);
                        break;
                    case ($data['position'] % 4 ==3):
                        $position=($data['position']-2);
                        break;
                    case ($data['position'] % 4 ==0):
                        $position=($data['position']-3);
                        break;

                }

            }

        }

        $coupon->position=$position;
        $couponRepo->update($coupon);
        $shopRepo = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $remoteShopId]);
        $db_host = $shopRepo->dbHost;
        $db_name = $shopRepo->dbName;
        $db_user = $shopRepo->dbUsername;
        $db_pass = $shopRepo->dbPassword;
        try {

            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
            $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $res = ' connessione ok <br>';
        } catch (PDOException $e) {
            $res = $e->getMessage();
        }
        $resCampaign = \Monkey::app()->dbAdapter->query('select b.id as bannerId, 
                                                           `b`.`name` as `bannerName`, 
                                                             b.textHtml as textHtml,
                                                            b.position as position,
                                                             b.link as link
                                                             b.click as click,
                                                              b.remoteId as remoteId,
                                                              b.remoteShopId as remoteShopId,
                                                              c.remoteCampaignId as remoteCampaignId,
                                                              b.isActive as isActive  
                                                                
       from Banner b
                                                              join Campaign c on b.campaignId = c.id where b.id=' . $bannerId,[])->fetchAll();
        foreach ($resCampaign as $remoteCampaign) {
            $bannerId = $remoteCampaign['bannerId'];
            $name = $remoteCampaign['bannerName'];
            $remoteId = $remoteCampaign['remoteId'];
            $textHtml = $remoteCampaign['textHtml'];
            $position = $remoteCampaign['position'];
            $link = $remoteCampaign['link'];
            $remoteCampaignId=$remoteCampaign['remoteCampaignId'];
            $isActive=$remoteCampaign['isActive'];
        }

        $stmtUpdateBanner = $db_con->prepare("Update Banner set
                       campaignId='" . $remoteCampaignId . "',
                      `name`='" . $name . "',
                      `textHtml`='" . $textHtml . "',
                      `position`='" . $position . "',
                      `link`='" . $link . "',
                      isActive='" . $isActive . "',
                      valid='" . $valid . "' 
                      where id=" . $remoteId);
        $stmtUpdateBanner->execute();


    }
}
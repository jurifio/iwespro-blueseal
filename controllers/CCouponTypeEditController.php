<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CCouponTypeEditController
 * @package bamboo\app\controllers
 */
class CCouponTypeEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "coupontype_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/coupontype_edit.php');

        $couponId = $this->app->router->getMatchedRoute()->getComputedFilter('id');
        $couponRepo = \Monkey::app()->repoFactory->create('CouponType');
        $coupon = $couponRepo->findOneBy(['id'=>$couponId]);

        $possValids =[];
        $possValids[0] = '1 anno';
        $possValids[1] = '1 mese';
        $possValids[2] = '3 giorni';
        $possValids[3] = '7 giorni';
        $possValids[4] = '14 giorni';
        $possValids[5] = '21 giorni';

        $possValidity = [];
        $possValidity[0] = 'P1Y';
        $possValidity[1] = 'P1M';
        $possValidity[2] = 'P3D';
        $possValidity[3] = 'P7D';
        $possValidity[4] = 'P14D';
        $possValidity[5] = 'P21D';

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'coupon' => $coupon,
            'possValids' => $possValids,
            'possValidity' => $possValidity,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $couponId = $this->app->router->getMatchedRoute()->getComputedFilter('id');

        $couponType = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id'=>$couponId]);
        $couponTypeRemoteId=$couponType->remoteId;
        $remoteShopId=$couponType->remoteShopId;
        foreach ($data as $k => $v) {
            $couponType->{$k} = $v;
        }
        $couponType->update();
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


        foreach ($couponType->couponTypeHasTag as $couponTypeHasTag) {
            $couponTypeHasTag->delete();
            /*$stmtRemoteCouponTypeHasTag=$db_con->prepare('delete from CouponTypeHasTag where couponTypeId='.$couponTypeRemoteId);
            $stmtRemoteCouponTypeHasTag->execute();*/
        }

        foreach ($data['tags'] as $tag) {
            $couponTypeHasTag = \Monkey::app()->repoFactory->create('CouponTypeHasTag')->getEmptyEntity();
            $couponTypeHasTag->tagId = $tag;
            $couponTypeHasTag->couponTypeId = $couponType->id;
            $couponTypeHasTag->insert();
        }


        $resCoupon=\Monkey::app()->dbAdapter->query('select ct.id as couponTypeId, 
                                                           `ct`.`name` as couponName, 
                                                             ct.amount as amount,
                                                             ct.amountType as amountType,
                                                             ct.`validity`,
                                                             ct.validForCartTotal,
                                                             ct.hasFreeShipping,
                                                              ct.hasFreeReturn,
                                                              ct.remoteId,
                                                              ct.remoteShopId,
                                                              ct.campaignId as campaignId,
                                                              ct.remoteCampaignId as remoteCampaignId      
       from CouponType ct
                                                              join Campaign c on ct.campaignId = c.campaignId where ct.id='.$couponId,[])->fetchAll();
        foreach ($resCoupon as $remoteCouponType){
            $couponTypeId=$remoteCouponType['couponTypeId'];
            $couponName=$remoteCouponType['couponName'];
            $amount=$remoteCouponType['amount'];
            $amountType=$remoteCouponType['amountType'];
            $validity=$remoteCouponType['validity'];
            $validForCartTotal=$remoteCouponType['validForCartTotal'];
            $hasFreeShipping=$remoteCouponType['hasFreeShipping'];
            $hasFreeReturn=$remoteCouponType['hasFreeReturn'];
            $campaignId=$remoteCouponType['campaignId'];
            $remoteCampaignId=$remoteCouponType['remoteCampaignId'];
        }

        $stmtUpdateCouponType=$db_con->prepare("Update CouponType set 
                      `name`='".$couponName."',
                      amount='".$amount."',
                      amountType='".$amountType."',
                      validity='".$validity."',
                      validForCartTotal='".$validForCartTotal."',
                      hasFreeShipping='".$hasFreeShipping."',
                      hasFreeReturn='".$hasFreeReturn."',
                      campaignId='".$remoteCampaignId."'
                    
                      where id=".$couponTypeId);
        $stmtUpdateCouponType->execute();

    }
}
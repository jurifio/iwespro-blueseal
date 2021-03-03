<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use PDO;
use PDOException;

/**
 * Class CCouponEditController
 * @package bamboo\app\controllers
 */
class CCouponEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "coupon_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/coupon_edit.php');

        $couponId = $this->app->router->getMatchedRoute()->getComputedFilter('id');
        $couponRepo = \Monkey::app()->repoFactory->create('Coupon');
        $coupon = $couponRepo->findOneBy(['id'=>$couponId]);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'coupon' => $coupon,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $couponId = $this->app->router->getMatchedRoute()->getComputedFilter('id');

        $couponRepo = \Monkey::app()->repoFactory->create('Coupon');
        $coupon = $couponRepo->findOneBy(['id'=>$couponId]);

        foreach ($data as $k => $v) {
            $coupon->{$k} = $v;
            if($k=='couponTypeId'){
                $couponType=\Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id'=>$v]);
                $amountType=$couponType->amountType;
                $coupon->amountType=$amountType;
            }
            if($k=='remoteShopId'){
                $remoteShopId=$v;
            }
        }


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
        $resCoupon = \Monkey::app()->dbAdapter->query('select c.id as couponId, 
                                                           `c`.`code` as codeCoupon, 
                                                             c.issueDate as issueDate,
                                                            c.validThru as validThru,
                                                             c.amountType as amountType,
                                                             c.amount as amount,
                                                              c.valid as valid,
                                                              c.remoteId as remoteId,
                                                              ct.remoteId as remoteCouponTypeId,
                                                              ct.remoteShopId as remoteShopId
                                                                
       from CouponEvent ce
                                                              join Coupon c on c.couponTypeId = ct.id where c.id=' . $couponId,[])->fetchAll();
        foreach ($resCoupon as $remoteCoupon) {
            $couponId = $remoteCoupon['couponId'];
            $codeCoupon = $remoteCoupon['codeCoupon'];
            $remoteCouponId = $remoteCoupon['remoteId'];
            $validThru = $remoteCoupon['validhThru'];
            $issueDate = $remoteCoupon['issueDate'];
            $valid = $remoteCoupon['valid'];
            $amount=$remoteCoupon['amount'];
            $amountType=$remoteCoupon['amountType'];
            $remoteCouponTypeId = $remoteCoupon['remoteCouponTypeId'];
        }

        $stmtUpdateCoupon = $db_con->prepare("Update Coupon set
                       couponTypeId='" . $remoteCouponTypeId . "',
                      `code`='" . $codeCoupon . "',
                      `issueDate`='" . $issueDate . "',
                      validThru='" . $validThru . "',
                      amount='" . $amount . "',
                      amountType='" . $amountType . "',
                      valid='" . $valid . "' 
                      where id=" . $remoteCouponId);
        $stmtUpdateCouponEvent->execute();


    }
}
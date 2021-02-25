<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CCouponEventEditController
 * @package bamboo\app\controllers
 */
class CCouponEventEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "couponevent_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths','blueseal') . '/template/couponevent_edit.php');

        $couponId = $this->app->router->getMatchedRoute()->getComputedFilter('id');
        $couponRepo = \Monkey::app()->repoFactory->create('CouponEvent');
        $coupon = $couponRepo->findOneBy(['id' => $couponId]);

        $em = $this->app->entityManagerFactory->create('CouponType');
        $couponTypes = $em->findAll('limit 9999');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'coupon' => $coupon,
            'couponTypes' => $couponTypes,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $couponId = $this->app->router->getMatchedRoute()->getComputedFilter('id');

        $couponRepo = \Monkey::app()->repoFactory->create('CouponEvent');
        $coupon = $couponRepo->findOneBy(['id' => $couponId]);

        foreach ($data as $k => $v) {
            $coupon->{$k} = $v;
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
        $resCouponEvent = \Monkey::app()->dbAdapter->query('select ce.id as couponEventId, 
                                                           `ce`.`name` as couponEventName, 
                                                             ce.description as description,
                                                            ce.couponTypeId as couponTypeId,
                                                             ce.startDate as startDate
                                                             ce.endDate as endDate,
                                                              ce.isAnnounce,
                                                              ce.couponText as couponText,
                                                              ct.remoteId as remoteCouponTypeId,
                                                              ct.remoteShopId as remoteShopId,
                                                                
       from CouponEvent ce
                                                              join CouponTypeId ct on ce.couponTypeId = ct.id where ce.id=' . $couponId,[])->fetchAll();
        foreach ($resCouponEvent as $remoteCouponEvent) {
            $couponEventId = $remoteCouponEvent['couponEventId'];
            $couponEventName = $remoteCouponEvent['couponEventName'];
            $description = $remoteCouponEvent['description'];
            $startDate = $remoteCouponEvent['startDate'];
            $endDate = $remoteCouponEvent['endDate'];
            $isAnnounce = $remoteCouponEvent['isAnnounce'];
            $couponText = $remoteCouponEvent['couponText'];
            $remoteCouponTypeId = $remoteCouponEvent['remoteCouponTypeId'];
        }

        $stmtUpdateCouponEvent = $db_con->prepare("Update CouponEvent set
                       couponTypeId='" . $remoteCouponTypeId . "',
                      `name`='" . $couponEventName . "',
                      `description`='" . $description . "',
                      startDate='" . $startDate . "',
                      endDate='" . $endDate . "',
                      isAnnounce='" . $isAnnounce . "',
                      couponText='" . $couponText . "'
                    
                      where id=" . $couponEventId);
        $stmtUpdateCouponEvent->execute();
    }
}
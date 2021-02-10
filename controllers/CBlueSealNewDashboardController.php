<?php

namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CBlueSealDashboardController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CBlueSealNewDashboardController extends ARestrictedAccessRootController
{
    protected $fallBack = "home";
    protected $logFallBack = "blueseal";
    protected $pageSlug = "newdashboard";

    public function get()
    {
        if(isset($_GET['typePeriod'])){
            $typePeriod = $_GET['typePeriod'];
        }else{
            $typePeriod='month';
        }
        switch ($typePeriod){
            case "year":
                $title = "Anno";
                $timeStartMask = (new \DateTime("first day of this year midnight"))->format('Y-m-d H:i:s');
                $timeEndMasks = (new \DateTime("last day of this year midnight"))->format('Y-m-d H:i:s');
                $timeStartMaskCompare=(new \DateTime("first day of last year midnight"))->format('Y-m-d H:i:s');
                $timeEndMaskCompare=(new \DateTime("last day of last year midnight"))->format('Y-m-d H:i:s');
                break;
            case "month":
                $title = "Mese";
                $timeStartMask = strtotime("first day of this month midnight");
                $timeEndMasks = strtotime("last day of this month midnight");
                $timeStartMaskCompare=(new \DateTime("first day of this month last  year midnight"))->format('Y-m-d H:i:s');
                $timeEndMaskCompare=(new \DateTime("last day of this month last year midnight"))->format('Y-m-d H:i:s');

                break;
            case "week":
                $title = "Settimana";
                $timeStartMask = strtotime("last monday midnight");
                $timeEndMasks = strtotime("next monday midnight");
                $timeStartMaskCompare=(new \DateTime("last monday of this month last  year midnight"))->format('Y-m-d H:i:s');
                $timeEndMaskCompare=(new \DateTime("next monday of this month last year midnight"))->format('Y-m-d H:i:s');

                break;
            case "day":
                $title = "Giorno";
                $timeStartMask = strtotime("midnight");
                $timeEndMasks = strtotime("tomorrow midnight");
                $timeStartMaskCompare=(new \DateTime("this day of this month last  year midnight"))->format('Y-m-d H:i:s');
                $timeEndMaskCompare=(new \DateTime("next day of this month last year midnight"))->format('Y-m-d H:i:s');

                break;
            case "hour":
                $title = "Ora";
                $timeStartMask = strToTime("Y-m-d H:00:00");
                $timeEndMasks = strToTime("Y-m-d H:00:00");
                break;
        }

        $data = \Monkey::app()->router->request()->getRequestData();
        $today = (new \DateTime())->format('Y-m-d H:i:s');
        $currentMonth = (new \DateTime())->format('m');
        $currentyear = (new \DateTime())->format('Y');
        $yesterday = (new \DateTime())->modify("-1 day")->format('Y-m-d H:i:s');
        $lastMonth = (new \DateTime())->modify("-1 month")->format('m');
        $lastYear = (new \DateTime())->modify("-1 year")->format('Y');


        $stats = [];
        $sqlOrder = 'select sum(o.netTotal) as totalOrder,count(o.id) as countOrder from `Order` `o`
WHERE `o`.`status` LIKE \'ORD_SHIPPED\' OR `o`.`status` LIKE \'ORD_DELIVERED\' OR  `o`.`status` LIKE \'ORD_CLOSED\'   and o.paymentDate is not null';
        $resOrder = \Monkey::app()->dbAdapter->query($sqlOrder,[])->fetchAll();
        foreach ($resOrder as $orderData) {
            $totalOrder = $orderData['totalOrder'];
            $quantityOrder = $orderData['countOrder'];

        }
        $sqlOrderReturn = 'select sum(o.netTotal) as totalOrder,count(o.id) as countOrder from `Order` `o`
 WHERE `o`.`status` LIKE \'ORD_RETURNED\'';
        $resOrderReturn = \Monkey::app()->dbAdapter->query($sqlOrderReturn,[])->fetchAll();
        foreach ($resOrderReturn as $orderDataReturn) {
            $totalOrderReturn = $orderDataReturn['totalOrder'];
            $quantityOrderReturn = $orderDataReturn['countOrder'];
        }
        $cartTotalNumber = 'select count(*) AS totalCart from UserSessionHasCart us join  `Cart` c on  `us`.cartId=c.id';
        $resCartTotalNumber = \Monkey::app()->dbAdapter->query($cartTotalNumber,[])->fetchAll();
        foreach ($resCartTotalNumber as $cartTotalNumber) {
            $cartTotalNumber = $cartTotalNumber['totalCart'];
        }


        $cartAbandonedTotalNumber = 'select count(c.id) AS totalCart from Cart c left join UserSessionHasCart us on c.id=us.cartId where us.cartId is null';
        $resCartAbandonedTotalNumber = \Monkey::app()->dbAdapter->query($cartAbandonedTotalNumber,[])->fetchAll();
        foreach ($resCartAbandonedTotalNumber as $resCartAbandoned) {
            $cartAbandonedTotal = $resCartAbandoned['totalCart'];
        }

        $sqlTotalUser = 'select count(*) as countUser from `User` where isActive=1';
        $resCountUser = \Monkey::app()->dbAdapter->query($sqlTotalUser,[])->fetchAll();
        foreach ($resCountUser as $countUser) {
            $totalUser = $countUser['countUser'];
        }
        $sqlTotalUserOnline = "select count(*) as countUser from `UserSession`  where creationDate between '" . $today . "' and '" . $yesterday . "'";
        $resCountUser = \Monkey::app()->dbAdapter->query($sqlTotalUserOnline,[])->fetchAll();
        foreach ($resCountUser as $countUser) {
            $totalUserOnline = $countUser['countUser'];
        }
        $sqlTotalProduct = 'select count(*) as totalProduct from Product where productStatusId=6 and qty>0';
        $resTotalProduct = \Monkey::app()->dbAdapter->query($sqlTotalProduct,[])->fetchAll();
        foreach ($resTotalProduct as $product) {
            $totalProduct = $product['totalProduct'];
        }

        $stats[] = ['totalOrder' => $totalOrder,
            'quantityOrder' => $quantityOrder,
            'totalOrderReturn' => $totalOrderReturn,
            'quantityOrderReturn' => $quantityOrderReturn,
            'cartTotalNumber' => $cartTotalNumber,
            'cartAbandonedTotal'=>$cartAbandonedTotal,
            'totalUser'=>$totalUser,
            'totalUserOnline'=>$totalUserOnline,
            'totalProduct'=>$totalProduct
        ];




        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths','blueseal') . '/template/newdashboard.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'data' => $this->request->getUrlPath(),
            'page' => $this->page,
            'stats' => $stats,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}
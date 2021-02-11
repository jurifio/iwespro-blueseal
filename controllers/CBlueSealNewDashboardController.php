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
        $data = \Monkey::app()->router->request()->getRequestData();
        $today = (new \DateTime())->format('Y-m-d H:i:s');
        $currentMonth = (new \DateTime())->format('m');
        $year = (new \DateTime())->format('Y');
        $yesterday = (new \DateTime())->modify("-1 day")->format('Y-m-d H:i:s');
        $lastMonth = (new \DateTime())->modify("-1 month")->format('m');
        $lastYear = (new \DateTime())->modify("-1 year")->format('Y');
        if(isset($_GET['typePeriod'])){
            $typePeriod = $_GET['typePeriod'];
        }else{
            $typePeriod='month';
        }
        switch ($typePeriod){
            case "year":
                $title = "Anno Corrente";
                $timeStartMask = (new \DateTime("first day of this year midnight"))->format('Y-m-d H:i:s');
                $timeEndMasks = (new \DateTime("last day of this year midnight"))->format('Y-m-d H:i:s');
                $timeStartMaskCompare=(new \DateTime("first day of last year midnight"))->format('Y-m-d H:i:s');
                $timeEndMaskCompare=(new \DateTime("last day of last year midnight"))->format('Y-m-d H:i:s');
                $sqlgraphOrder="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, concat(date_format(o.creationDate,'%M'),'/',YEAR(o.creationDate)) as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMask."' and '".$timeEndMasks."' and   `o`.`status` IN ('ORD_SHIPPED','ORD_DELIVERED','ORD_CLOSED','ORD_WORK')    and o.paymentDate is not null group by concat(date_format(o.creationDate,'%M'),'/',YEAR(o.creationDate)) ORDER BY concat(date_format(o.creationDate,'%M'),'/',YEAR(o.creationDate)) asc";
                $sqlGroupOrderCompare="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, concat(date_format(o.creationDate,'%M'),'/',YEAR(o.creationDate)) as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."'and   `o`.`status` IN ('ORD_SHIPPED','ORD_DELIVERED','ORD_CLOSED','ORD_WORK')    and o.paymentDate is not null  group by concat(date_format(o.creationDate,'%M'),'/',YEAR(o.creationDate)) ORDER BY concat(date_format(o.creationDate,'%M'),'/',YEAR(o.creationDate)) asc";
                $sqlgraphOrderReturn="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, concat(date_format(creationDate,'%M'),'/',YEAR(creationDate)) as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMask."' and '".$timeEndMasks."' and   `o`.`status` LIKE 'ORD_RETURNED' group by concat(date_format(creationDate,'%M'),'/',YEAR(creationDate)) ORDER BY concat(date_format(creationDate,'%M'),'/',YEAR(creationDate)) asc";
                $sqlGroupOrderReturnCompare="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, concat(date_format(creationDate,'%M'),'/',YEAR(creationDate)) as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."' and   `o`.`status` LIKE 'ORD_RETURNED'  group by concat(date_format(creationDate,'%M'),'/',YEAR(creationDate)) ORDER BY concat(date_format(creationDate,'%M'),'/',YEAR(creationDate)) asc";
                $cartTotalNumber = 'select count(us.cartId) AS totalCart, concat(date_format(c.creationDate,\'%M\'),\'/\',YEAR(c.creationDate)) as creationDate from UserSessionHasCart us join  `Cart` c on  `us`.cartId=c.id 
WHERE c.creationDate between \''.$timeStartMask.'\' and \''.$timeEndMasks.'\' group by concat(date_format(c.creationDate,\'%M\'),\'/\',YEAR(c.creationDate)) ORDER BY concat(date_format(c.creationDate,\'%M\'),\'/\',YEAR(c.creationDate)) asc';
                $cartTotalNumberCompare = 'select count(us.cartId) AS totalCart, concat(date_format(c.creationDate,\'%M\'),\'/\',YEAR(creationDate)) as creationDate from UserSessionHasCart us join  `Cart` c on  `us`.cartId=c.id 
WHERE c.creationDate between \''.$timeStartMaskCompare.'\' and \''.$timeEndMaskCompare.'\' group by concat(date_format(c.creationDate,\'%M\'),\'/\',YEAR(c.creationDate)) ORDER BY concat(date_format(c.creationDate,\'%M\'),\'/\',YEAR(c.creationDate)) asc';
                $cartAbandonedTotalNumber = 'select count(c.id) AS totalCart, YEAR(c.creationDate) as creationDate from Cart c left join UserSessionHasCart us on c.id=us.cartId where us.cartId is null and YEAR(c.creationDate)=\''.$year.'\' group by Year(c.creationDate) ORDER BY year(c.creationDate) asc';
                $cartAbandonedTotalNumberCompare = 'select count(c.id) AS totalCart, YEAR(c.creationDate) as creationDate from Cart c left join UserSessionHasCart us on c.id=us.cartId where us.cartId is null and YEAR(c.creationDate)=\''.$lastYear.'\' group by Year(c.creationDate) ORDER BY year(c.creationDate) asc';
                $sqlTotalUser = "select count(*) as countUser, concat(date_format(creationDate,'%M'),'/',YEAR(creationDate))  as creationDate from `User` where creationDate between '".$timeStartMask."' and '".$timeEndMasks."' and isActive=1 group by concat(date_format(creationDate,'%M'),'/',YEAR(creationDate)) ORDER BY concat(date_format(creationDate,'%M'),'/',YEAR(creationDate)) asc";
                $sqlTotalUserCompare = "select count(*) as countUser, concat(date_format(creationDate,'%M') as creationDate from `User`  WHERE creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."' and isActive=1 group by concat(date_format(creationDate,'%M'),'/',YEAR(creationDate)) ORDER BY concat(date_format(creationDate,'%M'),'/',YEAR(creationDate)) asc";
                $sqlTotalUserOnline = "select count(*) as countUser,  concat(date_format(creationDate,'%M'),'/',YEAR(creationDate)) as creationDate  from `UserSession`  where creationDate  between '".$timeStartMask."' and '".$timeEndMasks."' group by concat(date_format(creationDate,'%M'),'/',YEAR(creationDate)) ORDER BY concat(date_format(creationDate,'%M'),'/',YEAR(creationDate)) asc";
                $sqlTotalUserOnlineCompare = "select count(*) as countUser,  concat(date_format(creationDate,'%M'),'/',YEAR(creationDate)) as creationDate  from `UserSession`  where creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."' group by concat(date_format(creationDate,'%M'),'/',YEAR(creationDate)) ORDER BY concat(date_format(creationDate,'%M'),'/',YEAR(creationDate)) asc";
                break;

            case "month":
                $title = "Mese Corrente";
                $timeStartMask = (new \DateTime("first day of this month midnight"))->format('Y-m-d H:i:s');
                $timeEndMasks = (new \DateTime("last day of this month midnight"))->format('Y-m-d H:i:s');
                $timeStartMaskCompare=(new \DateTime("first day of this month last  year midnight"))->format('Y-m-d H:i:s');
                $timeEndMaskCompare=(new \DateTime("last day of this month last year midnight"))->format('Y-m-d H:i:s');
                $sqlgraphOrder="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, date_format(o.creationDate,'%d') as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMask."' and '".$timeEndMasks."'  and   `o`.`status` IN ('ORD_SHIPPED','ORD_DELIVERED','ORD_CLOSED','ORD_WORK')   and o.paymentDate is not null group by date_format(o.creationDate,'%d') ORDER BY date_format(o.creationDate,'%d') asc";
                $sqlGroupOrderCompare="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, date_format(creationDate,'%d') as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."'  and   `o`.`status` IN ('ORD_SHIPPED','ORD_DELIVERED','ORD_CLOSED','ORD_WORK')   and o.paymentDate is not null group by date_format(o.creationDate,'%d') ORDER BY date_format(o.creationDate,'%d') asc";
                $sqlgraphOrderReturn="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, date_format(o.creationDate,'%d') as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMask."' and '".$timeEndMasks."' and   `o`.`status` LIKE 'ORD_RETURNED' group by date_format(o.creationDate,'%d') ORDER BY date_format(o.creationDate,'%d') asc";
                $sqlGroupOrderReturnCompare="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, date_format(o.creationDate,'%d') as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."' and   `o`.`status` LIKE 'ORD_RETURNED' group by date_format(o.creationDate,'%d') ORDER BY date_format(o.creationDate,'%d') asc";
                $cartTotalNumber = 'select count(us.cartId) AS totalCart, date_format(c.creationDate,\'%d\') as creationDate from UserSessionHasCart us join  `Cart` c on  `us`.cartId=c.id 
WHERE c.creationDate between \''.$timeStartMask.'\' and \''.$timeEndMasks.'\' group by date_format(c.creationDate,\'%d\') ORDER BY date_format(c.creationDate,\'%d\') asc';
                $cartTotalNumberCompare = 'select count(us.cartId) AS totalCart, date_format(c.creationDate,\'%d\') as creationDate from UserSessionHasCart us join  `Cart` c on  `us`.cartId=c.id 
WHERE c.creationDate between \''.$timeStartMaskCompare.'\' and \''.$timeEndMaskCompare.'\' group by date_format(c.creationDate,\'%d\') ORDER BY date_format(c.creationDate,\'%d\') asc';
                $cartAbandonedTotalNumber = 'select count(c.id) AS totalCart,  date_format(c.creationDate,\'%d\') as creationDate from Cart c left join UserSessionHasCart us on c.id=us.cartId where us.cartId is null and  creationDate between \''.$timeStartMask.'\' and \''.$timeEndMasks.'\' group by  date_format(c.creationDate,\'%d\') ORDER BY  date_format(c.creationDate,\'%d\') asc';
                $cartAbandonedTotalNumberCompare = 'select count(c.id) AS totalCart,  date_format(c.creationDate,\'%d\') as creationDate from Cart c left join UserSessionHasCart us on c.id=us.cartId where us.cartId is null and  creationDate between \''.$timeStartMaskCompare.'\' and \''.$timeEndMaskCompare.'\'group by  date_format(c.creationDate,\'%d\') ORDER BY  date_format(c.creationDate,\'%d\') asc';
                $sqlTotalUser = "select count(*) as countUser, date_format(creationDate,'%d')  as creationDate from `User` where creationDate between '".$timeStartMask."' and '".$timeEndMasks."' and isActive=1 group by date_format(creationDate,'%d') ORDER BY date_format(creationDate,'%d') asc";
                $sqlTotalUserCompare = "select count(*) as countUser, date_format(creationDate,'%d') as creationDate from `User`  WHERE creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."' and isActive=1 group by date_format(creationDate,'%d') ORDER BY date_format(creationDate,'%d') asc";
                $sqlTotalUserOnline = "select count(*) as countUser,  date_format(creationDate,'%d') as creationDate  from `UserSession`  where creationDate  between '".$timeStartMask."' and '".$timeEndMasks."' group by date_format(creationDate,'%d') ORDER BY date_format(creationDate,'%d') asc";
                $sqlTotalUserOnlineCompare = "select count(*) as countUser,  date_format(creationDate,'%d') as creationDate  from `UserSession`  where creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."' group by date_format(creationDate,'%d') ORDER BY date_format(creationDate,'%d') asc";
                break;
            case "week":
                $title = "Settimana Corrente";
                $timeStartMask = (new \DateTime("midnight - 7 day"))->format('Y-m-d H:i:s');
                $timeEndMasks = (new \DateTime("midnight"))->format('Y-m-d H:i:s');
                $timeStartMaskCompare=(new \DateTime("this month last year midnight -7 day"))->format('Y-m-d H:i:s');
                $timeEndMaskCompare=(new \DateTime("this month last year midnight"))->format('Y-m-d H:i:s');
                $sqlgraphOrder="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, date_format(o.creationDate,'%d-%W') as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMask."' and '".$timeEndMasks."'  and   `o`.`status` IN ('ORD_SHIPPED','ORD_DELIVERED','ORD_CLOSED','ORD_WORK')   and o.paymentDate is not null group by date_format(o.creationDate,'%d-%W') ORDER BY date_format(o.creationDate,'%d-%W') asc";
                $sqlGroupOrderCompare="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, date_format(creationDate,'%d-%W') as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."'  and   `o`.`status` IN ('ORD_SHIPPED','ORD_DELIVERED','ORD_CLOSED','ORD_WORK')   and o.paymentDate is not null group by date_format(o.creationDate,'%d-%W') ORDER BY date_format(o.creationDate,'%d-%W') asc";
                $sqlgraphOrderReturn="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, date_format(o.creationDate,'%d-%W') as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMask."' and '".$timeEndMasks."' and   `o`.`status` LIKE 'ORD_RETURNED' group by date_format(o.creationDate,'%d-%W') ORDER BY date_format(o.creationDate,'%d-%W') asc";
                $sqlGroupOrderReturnCompare="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, date_format(o.creationDate,'%d-%W') as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."' and   `o`.`status` LIKE 'ORD_RETURNED' group by date_format(o.creationDate,'%d-%W') ORDER BY date_format(o.creationDate,'%d-%W') asc";
                $cartTotalNumber = 'select count(us.cartId) AS totalCart, date_format(c.creationDate,\'%d-%W\') as creationDate from UserSessionHasCart us join  `Cart` c on  `us`.cartId=c.id 
WHERE c.creationDate between \''.$timeStartMask.'\' and \''.$timeEndMasks.'\' group by date_format(c.creationDate,\'%d-%W\') ORDER BY date_format(c.creationDate,\'%d-%W\') asc';
                $cartTotalNumberCompare = 'select count(us.cartId) AS totalCart, date_format(c.creationDate,\'%d-%W\') as creationDate from UserSessionHasCart us join  `Cart` c on  `us`.cartId=c.id 
WHERE c.creationDate between \''.$timeStartMaskCompare.'\' and \''.$timeEndMaskCompare.'\' group by date_format(c.creationDate,\'%d-%W\') ORDER BY date_format(c.creationDate,\'%d-%W\') asc';
                $cartAbandonedTotalNumber = 'select count(c.id) AS totalCart,  date_format(c.creationDate,\'%d-%W\') as creationDate from Cart c left join UserSessionHasCart us on c.id=us.cartId where us.cartId is null and  creationDate between \''.$timeStartMask.'\' and \''.$timeEndMasks.'\' group by  date_format(c.creationDate,\'%d-%W\') ORDER BY  date_format(c.creationDate,\'%d-%W\') asc';
                $cartAbandonedTotalNumberCompare = 'select count(c.id) AS totalCart,  date_format(c.creationDate,\'%d-%W\') as creationDate from Cart c left join UserSessionHasCart us on c.id=us.cartId where us.cartId is null and  creationDate between \''.$timeStartMaskCompare.'\' and \''.$timeEndMaskCompare.'\'group by  date_format(c.creationDate,\'%d-%W\') ORDER BY  date_format(c.creationDate,\'%d-%W\') asc';
                $sqlTotalUser = "select count(*) as countUser, date_format(creationDate,'%d-%W')  as creationDate from `User` where creationDate between '".$timeStartMask."' and '".$timeEndMasks."' and isActive=1 group by date_format(creationDate,'%d-%W') ORDER BY date_format(creationDate,'%d-%W') asc";
                $sqlTotalUserCompare = "select count(*) as countUser, date_format(creationDate,'%d-%W') as creationDate from `User`  WHERE creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."' and isActive=1 group by date_format(creationDate,'%d-%W') ORDER BY date_format(creationDate,'%d-%W') asc";
                $sqlTotalUserOnline = "select count(*) as countUser,  date_format(creationDate,'%d-%W') as creationDate  from `UserSession`  where creationDate  between '".$timeStartMask."' and '".$timeEndMasks."' group by date_format(creationDate,'%d-%W') ORDER BY date_format(creationDate,'%d-%W') asc";
                $sqlTotalUserOnlineCompare = "select count(*) as countUser,  date_format(creationDate,'%d-%W') as creationDate  from `UserSession`  where creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."' group by date_format(creationDate,'%d-%W') ORDER BY date_format(creationDate,'%d-%W') asc";
                break;
            case "day":
                $title = "Giorno Corrente";
                $timeStartMask = (new \DateTime("midnight"))->format('Y-m-d H:i:s');
                $timeEndMasks = (new \DateTime("tomorrow midnight"))->format('Y-m-d H:i:s');
                $timeStartMaskCompare=(new \DateTime("this month last  year midnight"))->format('Y-m-d H:i:s');
                $timeEndMaskCompare=(new \DateTime("this month last year midnight + 1 day"))->format('Y-m-d H:i:s');
                $sqlgraphOrder="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, date_format(o.creationDate,'%H:00') as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMask."' and '".$timeEndMasks."'  and   `o`.`status` IN ('ORD_SHIPPED','ORD_DELIVERED','ORD_CLOSED','ORD_WORK')   and o.paymentDate is not null group by date_format(o.creationDate,'%H:00') ORDER BY date_format(o.creationDate,'%H:00') asc";
                $sqlGroupOrderCompare="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, date_format(o.creationDate,'%H:00') as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."'  and   `o`.`status` IN ('ORD_SHIPPED','ORD_DELIVERED','ORD_CLOSED','ORD_WORK')   and o.paymentDate is not null group by date_format(o.creationDate,'%H:00') ORDER BY date_format(o.creationDate,'%H:00') asc";
                $sqlgraphOrderReturn="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, date_format(o.creationDate,'%H:00') as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMask."' and '".$timeEndMasks."' and   `o`.`status` LIKE 'ORD_RETURNED'  group by date_format(o.creationDate,'%H:00') ORDER BY date_format(o.creationDate,'%H:00') asc";
                $sqlGroupOrderReturnCompare="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, date_format(o.creationDate,'%H:00') as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."' and   `o`.`status` LIKE 'ORD_RETURNED' group by date_format(o.creationDate,'%H:00') ORDER BY date_format(o.creationDate,'%H:00') asc";
                $cartTotalNumber = 'select count(us.cartId) AS totalCart, date_format(c.creationDate,\'%H:\') as creationDate from UserSessionHasCart us join  `Cart` c on  `us`.cartId=c.id 
WHERE c.creationDate between \''.$timeStartMask.'\' and \''.$timeEndMasks.'\' group by date_format(c.creationDate,\'%H:00\') ORDER BY date_format(c.creationDate,\'%H:00\') asc';
                $cartTotalNumberCompare = 'select count(us.cartId) AS totalCart, date_format(c.creationDate,\'%H:00\') as creationDate from UserSessionHasCart us join  `Cart` c on  `us`.cartId=c.id 
WHERE c.creationDate between \''.$timeStartMaskCompare.'\' and \''.$timeEndMaskCompare.'\' group by date_format(c.creationDate,\'%H:00\') ORDER BY date_format(c.creationDate,\'%H:00\') asc';
                $cartAbandonedTotalNumber = 'select count(c.id) AS totalCart,  date_format(c.creationDate,\'%H:00\') as creationDate from Cart c left join UserSessionHasCart us on c.id=us.cartId where us.cartId is null and  creationDate between \''.$timeStartMask.'\' and \''.$timeEndMasks.'\' group by  date_format(c.creationDate,\'%H:00\') ORDER BY  date_format(c.creationDate,\'%H:00\') asc';
                $cartAbandonedTotalNumberCompare = 'select count(c.id) AS totalCart,  date_format(c.creationDate,\'%H:00\') as creationDate from Cart c left join UserSessionHasCart us on c.id=us.cartId where us.cartId is null and  creationDate between \''.$timeStartMaskCompare.'\' and \''.$timeEndMaskCompare.'\' group by  date_format(c.creationDate,\'%H:00\') ORDER BY  date_format(c.creationDate,\'%H:00\') asc';
                $sqlTotalUser = "select count(*) as countUser, date_format(creationDate,'%H:00')  as creationDate from `User` where creationDate between '".$timeStartMask."' and '".$timeEndMasks."' and isActive=1 group by date_format(creationDate,'%H:00') ORDER BY date_format(creationDate,'%H:00') asc";
                $sqlTotalUserCompare = "select count(*) as countUser, date_format(creationDate,'%H:00') as creationDate from `User`  WHERE creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."' and isActive=1 group by date_format(creationDate,'%H:00') ORDER BY date_format(creationDate,'%H:00') asc";
                $sqlTotalUserOnline = "select count(*) as countUser,  date_format(creationDate,'%H:00') as creationDate  from `UserSession`  where creationDate  between '".$timeStartMask."' and '".$timeEndMasks."' group by date_format(creationDate,'%H:00') ORDER BY date_format(creationDate,'%H:00') asc";
                $sqlTotalUserOnlineCompare = "select count(*) as countUser,  date_format(creationDate,'%H:00') as creationDate  from `UserSession`  where creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."' group by date_format(creationDate,'%H:00') ORDER BY date_format(creationDate,'%H:00') asc";
                break;
            case "custom":
                $title = "Intervallo Personalizzato";
                if(isset($_GET['startDateWork'])) {
                    $timeStartMask = $_GET['startDateWork'];
                    $timeStartMaskCompare=(new \DateTime($_GET['startDateWork'] .'-1 year'))->format('Y-m-d H:i:s');
                }else{
                    $timeStartMask = (new \DateTime("midnight"))->format('Y-m-d H:i:s');
                }
                if(isset($_GET['endDateWork'])) {
                    $timeEndMasks = $_GET['endDateWork'];
                    $timeStartMaskCompare=(new \DateTime($_GET['endDateWork'] .'-1 year'))->format('Y-m-d H:i:s');
                }else{
                    $timeEndMaskCompare=(new \DateTime("this month last year midnight + 1 day"))->format('Y-m-d H:i:s');
                }
                $sqlgraphOrder="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, date_format(o.creationDate,'%d-%m-%Y') as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMask."' and '".$timeEndMasks."'  and   `o`.`status` IN ('ORD_SHIPPED','ORD_DELIVERED','ORD_CLOSED','ORD_WORK')   and o.paymentDate is not null group by date_format(o.creationDate,'%d-%m-%Y') ORDER BY date_format(o.creationDate,'%d-%m-%Y') asc";
                $sqlGroupOrderCompare="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, date_format(o.creationDate,'%d-%m-%Y') as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."'  and   `o`.`status` IN ('ORD_SHIPPED','ORD_DELIVERED','ORD_CLOSED','ORD_WORK')   and o.paymentDate is not null group by date_format(o.creationDate,'%d-%m-%Y') ORDER BY date_format(o.creationDate,'%d-%m-%Y') asc";
                $sqlgraphOrderReturn="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, date_format(o.creationDate,'%d-%m-%Y') as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMask."' and '".$timeEndMasks."' and   `o`.`status` LIKE 'ORD_RETURNED' group by date_format(o.creationDate,'%d-%m-%Y') ORDER BY date_format(o.creationDate,'%d-%m-%Y') asc";
                $sqlGroupOrderReturnCompare="select sum(o.netTotal) as totalOrder,count(o.id) as countOrder, date_format(o.creationDate,'%d-%m-%Y') as creationDate from `Order` `o`
WHERE creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."' and   `o`.`status` LIKE 'ORD_RETURNED'  group by date_format(o.creationDate,'%d-%m-%Y') ORDER BY date_format(o.creationDate,'%d-%m-%Y') asc";
                $cartTotalNumber = 'select count(us.cartId) AS totalCart, date_format(c.creationDate,\'%d-%m-%Y\') as creationDate from UserSessionHasCart us join  `Cart` c on  `us`.cartId=c.id 
WHERE c.creationDate between \''.$timeStartMask.'\' and \''.$timeEndMasks.'\' group by date_format(c.creationDate,\'%d-%m-%Y\') ORDER BY date_format(c.creationDate,\'%d-%m-%Y\') asc';
                $cartTotalNumberCompare = 'select count(us.cartId) AS totalCart, date_format(c.creationDate,\'%d-%m-%Y\') as creationDate from UserSessionHasCart us join  `Cart` c on  `us`.cartId=c.id 
WHERE c.creationDate between \''.$timeStartMaskCompare.'\' and \''.$timeEndMaskCompare.'\' group by date_format(creationDate,\'%d-%m-%Y\') ORDER BY date_format(c.creationDate,\'%d-%m-%Y\') asc';
                $cartAbandonedTotalNumber = 'select count(c.id) AS totalCart,  date_format(c.creationDate,\'%d-%m-%Y\') as creationDate from Cart c left join UserSessionHasCart us on c.id=us.cartId where us.cartId is null and  creationDate between \''.$timeStartMask.'\' and \''.$timeEndMasks.'\' group by  date_format(c.creationDate,\'%d-%m-%Y\') ORDER BY  date_format(c.creationDate,\'%d-%m-%Y\') asc';
                $cartAbandonedTotalNumberCompare = 'select count(c.id) AS totalCart,  date_format(c.creationDate,\'%d-%m-%Y\') as creationDate from Cart c left join UserSessionHasCart us on c.id=us.cartId where us.cartId is null and  creationDate between \''.$timeStartMaskCompare.'\' and \''.$timeEndMaskCompare.'\' group by  date_format(c.creationDate,\'%d-%m-%Y\') ORDER BY  date_format(c.creationDate,\'%d-%m-%Y\') asc';
                $sqlTotalUser = "select count(*) as countUser, date_format(creationDate,'%d-%m-%Y')  as creationDate from `User` where creationDate between '".$timeStartMask."' and '".$timeEndMasks."' and isActive=1 group by date_format(creationDate,'%d-%m-%Y') ORDER BY date_format(creationDate,'%d-%m-%Y') asc";
                $sqlTotalUserCompare = "select count(*) as countUser, date_format(creationDate,'%d-%m-%Y') as creationDate from `User`  WHERE creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."' and isActive=1 group by date_format(creationDate,'%d-%m-%Y') ORDER BY date_format(creationDate,'%d-%m-%Y') asc";
                $sqlTotalUserOnline = "select count(*) as countUser,  date_format(creationDate,'%d-%m-%Y') as creationDate  from `UserSession`  where creationDate  between '".$timeStartMask."' and '".$timeEndMasks."' group by date_format(creationDate,'%d-%m-%Y') ORDER BY date_format(creationDate,'%d-%m-%Y') asc";
                $sqlTotalUserOnlineCompare = "select count(*) as countUser,  date_format(creationDate,'%d-%m-%Y') as creationDate  from `UserSession`  where creationDate between '".$timeStartMaskCompare."' and '".$timeEndMaskCompare."' group by date_format(creationDate,'%d-%m-%Y') ORDER BY date_format(creationDate,'%d-%m-%Y') asc";
                break;
        }




        $stats = [];
        $totalOrder=0;
        $quantityOrder=0;
        $arrayOrder='';
        $arrayLabelOrder='';
        $arrayCountOrder='';

        $resOrder = \Monkey::app()->dbAdapter->query($sqlgraphOrder,[])->fetchAll();
        if(count($resOrder) > 0) {
            foreach ($resOrder as $orderData) {
                $totalOrder += $orderData['totalOrder'];
                $quantityOrder += $orderData['countOrder'];
                $arrayOrder .= "'".number_format($orderData['totalOrder'],2,'.','')."'".',';
                $arrayLabelOrder.= $orderData['creationDate'].',';
                $arrayCountOrder.= $orderData['countOrder'].',';


            }
        }else{
            $totalOrder='0.00';
            $quantityOrder='';
            $arrayLabelOrder='0';
            $arrayCountOrder='0';
            $arrayOrder='0.00';
        }
        $arrayOrderReturn='';
        $arrayCountOrderReturn='';
        $arrayLabelOrderReturn='';

        $resOrderReturn = \Monkey::app()->dbAdapter->query($sqlgraphOrderReturn,[])->fetchAll();
        if(count($resOrderReturn) > 0) {
            foreach ($resOrderReturn as $orderDataReturn) {
                $totalOrderReturn += $orderDataReturn['totalOrder'];
                $quantityOrderReturn += $orderDataReturn['countOrder'];
                $arrayOrderReturn .= number_format($orderDataReturn['totalOrder'],2,'.','').',';
                $arrayLabelOrderReturn.= $orderDataReturn['creationDate'].',';
                $arrayCountOrderReturn.= $orderDataReturn['countOrder'].',';
            }
        }else{
            $totalOrderReturn = '0.00';
            $quantityOrderReturn = '0';
            $arrayOrderReturn='0.00';
            $arrayCountOrderReturn='0';
            $arrayLabelOrderReturn='0';
        }
        $cartTotal=0;
    $arrayCartTotalNumber='';
    $arrayLabelCartTotalNumber='';
        $resCartTotalNumber = \Monkey::app()->dbAdapter->query($cartTotalNumber,[])->fetchAll();
        if(count($resCartTotalNumber) > 0) {
            foreach ($resCartTotalNumber as $cartTotalNumber) {
                $cartTotal += $cartTotalNumber['totalCart'];
                $arrayCartTotalNumber .= $cartTotalNumber['totalCart'].',';
                $arrayLabelCartTotalNumber.=$cartTotalNumber['creationDate'].',';
            }
        }else{
            $cartTotal='0';
            $arrayCartTotalNumber ='0';
            $arrayLabelCartTotalNumber='0';
        }
        $cartAbandonedTotal=0;
        $arrayCartAbandonedTotalNumber='';
        $arrayLabelCartAbandonedTotalNumber='';


        $resCartAbandonedTotalNumber = \Monkey::app()->dbAdapter->query($cartAbandonedTotalNumber,[])->fetchAll();
        if(count($resCartAbandonedTotalNumber) > 0) {
            foreach ($resCartAbandonedTotalNumber as $resCartAbandoned) {
                $cartAbandonedTotal += $resCartAbandoned['totalCart'];
                $arrayLabelCartAbandonedTotalNumber.=$resCartAbandoned['creationDate'].',';
                $arrayCartAbandonedTotalNumber .= $resCartAbandoned['totalCart'].',';
            }
        }else{
            $cartAbandonedTotal=0;
            $arrayLabelCartAbandonedTotalNumber='0';
            $arrayCartAbandonedTotalNumber = '0';
        }
        $totalUser=0;
        $arrayTotalUser='';
        $arrayLabelTotalUser='';
        //$sqlTotalUser = 'select count(*) as countUser from `User` where isActive=1';
        $resCountUser = \Monkey::app()->dbAdapter->query($sqlTotalUser,[])->fetchAll();
        if(count($resCartAbandonedTotalNumber) > 0) {
            foreach ($resCountUser as $countUser) {
                $totalUser += $countUser['countUser'];
                $arrayTotalUser.= $countUser['countUser'].',';
                $arrayLabelTotalUser.=$countUser['creationDate'].',';
            }
        }else{
            $totalUser="0";
            $arrayTotalUser= '0';
            $arrayLabelTotalUser='0';
        }
        $totalUserOnline=0;
        $arrayTotalUserOnLine='';
        $arrayLabelTotalUserOnLine='';
       // $sqlTotalUserOnline = "select count(*) as countUser from `UserSession`  where creationDate between '" . $today . "' and '" . $yesterday . "'";
        $resCountUser = \Monkey::app()->dbAdapter->query($sqlTotalUserOnline,[])->fetchAll();
        if(count($resCountUser) > 0) {
            foreach ($resCountUser as $countUser) {
                $totalUserOnline += $countUser['countUser'];
                $arrayTotalUserOnLine.=$countUser['countUser'].',';
                $arrayLabelTotalUserOnLine.=$countUser['creationDate'].',';
            }
        }else{
            $totalUserOnline='0';
            $arrayTotalUserOnLine='0';
            $arrayLabelTotalUserOnLine='0';
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
            'cartTotal' => $cartTotal,
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
            'arrayOrder'=>substr($arrayOrder,0,-1),
            'arrayLabelOrder'=>substr($arrayLabelOrder,0,-1),
            'arrayOrderReturn'=>substr($arrayOrderReturn,0,-1),
            'arrayLabelOrderReturn'=>substr($arrayLabelOrderReturn,0,-1),
            'arrayCountOrderReturn'=>substr($arrayCountOrderReturn,0,-1),
            'arrayCountOrder'=>substr($arrayCountOrder,0,-1),
            'arrayTotalUser'=>substr($arrayTotalUser,0,-1),
            'arrayLabelTotalUser'=>substr($arrayLabelTotalUser,0,-1),
            'arrayTotalUserOnLine'=>substr($arrayTotalUserOnLine,0,-1),
            'arrayLabelTotalUserOnLine'=>substr($arrayLabelTotalUserOnLine,0,-1),
            'arrayLabelCartTotalNumber'=>substr($arrayLabelCartTotalNumber,0,-1),
            'arrayLabelCartAbandonedTotalNumber'=>substr($arrayLabelCartAbandonedTotalNumber,0,-1),
            'arrayCartTotalNumber'=>substr($arrayCartTotalNumber,0,-1),
            'arrayCartAbandonedTotalNumber'=>substr($arrayCartAbandonedTotalNumber,0,-1),
            'title'=>$title,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}
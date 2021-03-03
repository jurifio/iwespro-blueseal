<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CSelectCouponTypeAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 07/01/2020
 * @since 1.0
 */
class CSelectCouponTypeAjaxController extends AAjaxController
{
    public function get()
    {
        $collectCouponType = [];

        $remoteShopId = \Monkey::app()->router->request()->getRequestData('remoteShopId');
        if ($remoteShopId != null) {
            $sql = 'select ct.id, ct.`name`,
            if(c.name is null,"nessuna campagna","c.name") as campaignName,
           if(c.isActive=1,"Attiva","Non Attiva") as isActive
       from  CouponType ct 
         
            left join Campaign c on ct.campaignId=c.id
  where ct.remoteShopId='.$remoteShopId.'
           ';
        } else {
            $sql = 'select ct.id, ct.`name`,
            if(c.name is null,"nessuna campagna","c.name") as campaignName,
           if(c.isActive=1,"Attiva","Non Attiva") as isActive
       from  CouponType ct 
           
            left join Campaign c on ct.campaignId=c.id
           ';
        }
        $res = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
        foreach ($res as $result) {
            $collectCouponType[] = ['id' => $result['id'],'name' => $result['name'],'campaignName' => $result['campaignName'],'isActive' => $result['isActive']];
        }

        return json_encode($collectCouponType);
    }
}
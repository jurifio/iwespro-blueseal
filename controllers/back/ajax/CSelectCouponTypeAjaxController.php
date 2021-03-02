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
        $sql='select ct.id, ct.`name`,
            if(c.name is null,"nessuna campagna","c.name") as campaignName from  CouponType ct 
            left join Campaign c on ct.campaignId=c.id
           ';
        $res=\Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
        foreach($res as $result){
            $collectCouponType[]=['id'=>$result['id'],'name'=>$result['name'],'campaignName'=>$result['campaignName']];
        }

        return json_encode($collectCouponType);
    }
}
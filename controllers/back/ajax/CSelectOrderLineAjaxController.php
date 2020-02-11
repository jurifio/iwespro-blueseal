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
 * Class CSelectOrderLineAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/02/2020
 * @since 1.0
 */
class CSelectOrderLineAjaxController extends AAjaxController
{
    public function get()
    {
        $orderLines=[];
       $id = $this -> app -> router -> request() -> getRequestData('id');
        $res=\Monkey::app()->repoFactory->create('OrderLine')->findAll();
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');

        foreach ($res as $result) {
$shop=$shopRepo->findOneBy(['id'=>$result->shopId]);
$shopName=$shop->name;
            $orderLines[] = ['id' => $result->id.'-'.$result->orderId,
                'productId' =>  $result->productId,
                'productVariantId' => $result->productVariantId,
                'productSizeId' => $result->productSizeId,
                'netPrice' => money_format('%.2n',$result->netPrice),
                'shopName'=>$shopName
                ];
        }

        return json_encode($orderLines);
    }
}
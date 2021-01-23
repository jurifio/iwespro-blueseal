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
 * Class CSelectAddressBookAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 31/01/2020
 * @since 1.0
 */
class CShopHasProductPriceAjaxController extends AAjaxController
{
    public function get()
    {
        $prices=[];
       $productId = $this -> app -> router -> request() -> getRequestData('productId');
        $productVariantId = $this -> app -> router -> request() -> getRequestData('productVariantId');
        $res = $this -> app -> dbAdapter -> query('SELECT `s`.`name` as nameShop, `sh`.`price` as price, `sh`.`salePrice` as salePrice,`sh`.`value` as value,`ps`.`name` as productStatus,
 if(sh.isOnSale=1,"si","no") as isOnSale
 from ShopHasProductPrice sh join ProductPublicStatus pp on sh.productId=pp.productId and sh.productVariantId=pp.productVariantId and sh.shopIdDestination=pp.shopIdDestination
     
join ProductStatus ps on  pp.productStatusId=ps.id
join Shop s on sh.shopIdDestination=s.id where sh.productId='.$productId.' and sh.productVariantId='.$productVariantId, []) -> fetchAll();

        foreach ($res as $result) {

            $prices[] = ['nameShop' => $result['nameShop'],
                'productStatus' =>  $result['productStatus'],
                'price' => number_format($result['price'],2,',',''),
                'salePrice' => number_format($result['salePrice'],2,',',''),
                'value' => number_format($result['value'],2,',',''),
                'isOnSale' => $result['isOnSale']
                ];
        }

        return json_encode($prices);
    }
}
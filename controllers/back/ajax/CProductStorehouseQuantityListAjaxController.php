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
 * Class CSelectBillRegistryClientAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/02/2020
 * @since 1.0
 */
class CProductStorehouseQuantityListAjaxController extends AAjaxController
{
    public function get()
    {
        $stock = '';
        $product = $this->app->router->request()->getRequestData('product');
        $codeProduct=explode('-',$product);
        $productId=$codeProduct[0];
        $productVariantId = $codeProduct[1];
        $resultProduct = $this->app->dbAdapter->query('SELECT dp.id as dirtyProductId, 
               dp.productId as productId, 
               dp.productVariantId as productVariantId, 
               dpe.generalColor as color,
               dp.shopId as shopId, 
               ps.name as productSizeId, 
               `st`.`name` as storeHouse,
               dst.qty as qty,
                s.name as shopName
               from DirtyProduct dp 
              
               join DirtyProductExtend dpe on dp.id =dpe.dirtyProductId
               join DirtySku  ds on dp.id = ds.dirtyProductId 
                join DirtySkuHasStoreHouse dst on dp.id=dst.dirtyProductId
                join Storehouse st on dst.storeHouseId=st.id  
                   join Shop s on dst.shopId=s.id
                join ProductSize ps on dst.productSizeId=ps.id where  dp.productId='.$productId.' and dp.productVariantId='.$productVariantId.' 
                 group BY dst.shopId,st.name,dst.qty   Order BY ps.name,st.name asc
        ',[])->fetchAll();
        if (count($resultProduct) > 0) {
            $stock.='<table><thead><th>Shop</th><th>Magazzino</th><th>taglia</th><th>qt</th></thead><tbody>';
            foreach ($resultProduct as $res) {
                $stock.='<tr><td>'.$res['shopName'].'</td><td>'.$res['storeHouse'].'</td><td>'.$res['productSizeId'].'</td><td>'.$res['qty'].'</td></tr>';

            }
            $stock.='</tbody></table>';
        } else {
            $stock.='Esaurito su tutti i magazzini';
        }

        return $stock;
    }
}
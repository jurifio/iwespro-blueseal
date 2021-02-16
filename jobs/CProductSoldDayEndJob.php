<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CProductSoldDay;
use PDO;
use DateTime;
use bamboo\amazon\business\builders\AAmazonFeedBuilder;
use bamboo\amazon\business\builders\CAmazonImageFeedBuilder;
use bamboo\amazon\business\builders\CAmazonInventoryFeedBuilder;
use bamboo\amazon\business\builders\CAmazonPricingFeedBuilder;
use bamboo\amazon\business\builders\CAmazonProductFeedBuilder;
use bamboo\amazon\business\builders\CAmazonRelationshipFeedBuilder;
use bamboo\core\application\AApplication;
use bamboo\domain\entities\CMarketplaceAccountHasProduct;

/**
 * Class CProductSoldDayEndJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/02/2021
 * @since 1.0
 */
class CProductSoldDayEndJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->relevationProductEnd();
        $this->report('ProductSoldDayEndJob','end day quantity check','');
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    private function relevationProductEnd()
    {
        try {
            $productSoldDayRepo = \Monkey::app()->repoFactory->create('ProductSoldDay');

            $dateEnd = (new DateTime())->format('Y-m-d H:i:s');
            $day = (new DateTime())->format('d');
            $month = (new DateTime())->format('m');
            $year = (new DateTime())->format('Y');
            $sql = 'SELECT d.productId AS productId,
		 d.productVariantId AS productVariantId,
		 d.shopId AS shopId, 
		 SUM(ds.qty) AS qty, 
		 p.isOnSale,
		 shp.salePrice AS salePrice, 
		 shp.price AS price
FROM DirtyProduct d JOIN DirtySku ds ON ds.dirtyProductId=d.id JOIN Product p ON p.id=d.productId AND p.productVariantId=d.productVariantId 
JOIN ShopHasProduct shp ON d.productId=shp.productId AND d.productVariantId=shp.productVariantId 
WHERE d.productId IS NOT NULL AND d.productVariantId IS NOT NULL    GROUP by d.productId, d.productVariantId 
';
            $res = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
            foreach ($res as $result) {
                $productSoldDay = $productSoldDayRepo->findOneBy(
                    ['productId'=>$result['productId'],
                     'productVariantId'=>$result['productVariantId'],
                     'shopId'=>$result['shopId'],
                      'day'=>$day,
                      'month'=>$month,
                      'year'=>$year
                    ]);
                $startQuantity=$productSoldDay->startQuantity;
                $soldQuantity=$startQuantity-$result['qty'];
                if($result['isOnSale']==1){
                    $productSoldDay->priceActive=$result['salePrice'];
                    $productSoldDay->netTotal=$result['salePrice'] * $soldQuantity;
                }else{
                    $productSoldDay->priceActive=$result['price'];
                    $productSoldDay->netTotal=$result['price'] * $soldQuantity;
                }
                $productSoldDay->endQty = $result['qty'];
                $productSoldDay->soldQuantity = $soldQuantity;
                $productSoldDay->dateEnd = $dateEnd;
                $productSoldDay->update();
            }
            $this->report('ProductSoldDayEndJob','end day Quantity check final Day','');
        } catch(\Throwable $e){
            $this->report('ProductSoldDayEndJob','Error',$e->getMessage());
        }


    }
}
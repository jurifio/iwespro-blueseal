<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProductEan;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CShop;
use PDO;
use PDOException;
use Throwable;

/**
 * Class CAlignNewStockFromDirtySkuJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/08/2021
 * @since 1.0
 */
class CAlignNewStockFromDirtySkuJob extends ACronJob
{

    /**
     * @param null $args
  */
    public function run($args = null)
    {
        $this->report('CAlignNewStockFromDirtySkuJob','log','start Align Quantity');
        $this->alignStockProduct();
    }


    private function alignStockProduct()
    {
        $res = "";
        try {
            $productRepo=\Monkey::app()->repoFactory->create('Product');
            $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');
            $sql='select p.id as productId, p.productVariantId as productVariantId, ds.qty as qty, ds.productSizeId as productSizeId,
                                shp.shopId as shopId from Product p join DirtyProduct shp on p.id=shp.productId
 and p.productVariantId=shp.productVariantId join DirtySku ds on shp.id=ds.dirtyProductId where  ds.productSizeId is not null  and ds.qty is not null ';
            $res=\Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
            foreach($res as $result){
                $this->report('CAlignNewStockFromDirtySkuJob',$result['productId'].'-'.$result['productVariantId'].'-'.$result['productSizeId']);
                $sku = $productSkuRepo->findOneBy(['productId' => $result['productId'],'productVariantId' => $result['productVariantId'],'productSizeId' => $result['productSizeId'],'shopId' => $result['shopId']]);
                if($sku) {
                    $sku->stockQty = $result['qty'];
                    $sku->update();
                }else{
                    $this->report('CAlignNewStockFromDirtySkuJob','jump Align Quantity'.$result['productId'].'-'.$result['productVariantId'].'-'.$result['productSizeId'],'');
                    continue;
                }
            }
            $sqlSku='select productId, productVariantId,sum(stockQty) as qty from ProductSku group by productId,productVariantId';
            $resProduct=\Monkey::app()->dbAdapter->query($sqlSku,[])->fetchAll();
            foreach($resProduct as $resultProduct){
                $product=$productRepo->findOneBy(['id'=>$resultProduct['productId'],'productVariantId'=>$resultProduct['productVariantId']]);
                $product->qty=$resultProduct['qty'];
                $product->update();
            }

        }catch(\Throwable $e){
            $this->report('CAlignNewStockFromDirtySkuJob','Error Align Quantity'.$result['productId'].'-'.$result['productVariantId'].'-'.$result['productSizeId'],$e->getMessage().'-'.$e->getLine());
        }


    }
}
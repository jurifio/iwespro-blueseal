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
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function run($args = null)
    {
        $this->alignStockProduct();
    }

    /**
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function alignStockProduct()
    {
        $res = "";
        try {
            $productRepo = \Monkey::app()->repoFactory->create('Product');
            $dirtyProductRepo = \Monkey::app()->repoFactory->create('DirtyProduct');
            $dirtySkuRepo = \Monkey::app()->repoFactory->create('DirtySku');
            $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');
            $products = $productRepo->findAll();
            foreach ($products as $product) {
                $dp = $dirtyProductRepo->findOneBy(['productId' => $product->id,'productVariantId' => $product->productVariantId]);
                if ($dp) {
                    $ds = $dirtySkuRepo->findBy(['dirtyProductId' => $dp->id]);
                    foreach ($ds as $dirtySku) {
                        if (!is_null($dirtySku->size)) {
                            $sku = $productSkuRepo->findOneBy(['productId' => $product->id,'productVariantId' => $product->productVariantId,'productSizeId' => $dirtySku->productSizeId,'shopId' => $dirtySku->shopId]);
                            $sku->stockQty = $dirtySku->qty;
                            $sku->update();
                        }
                    }
                }
            }

        }catch(\Throwable $e){
            \Monkey::app()->applicationLog('CAlignNewStockFromDirtySkuJob','Error','Error Align Quantity',$e->getMessage(),$e->getLine());
        }


    }
}
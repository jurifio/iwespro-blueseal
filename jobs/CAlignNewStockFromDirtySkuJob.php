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
            $this->report('CAlignNewStockFromDirtySkuJob','log','start Align Quantity');
            $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');
            $sql='select p.id as productId, p.productVariantId as productVariantId,p.qty as qty,ds.productSizeId as productSizeId,
                                shp.shopId as shopId from Product p join DirtyProduct shp on p.id=shp.productId
 and p.productVariantId=shp.productVariantId join DirtySku ds on shp.id=ds.dirtyProductId where shp.shopId IN (1,51,58,61) and ds.productSizeId is not null';
            $res=\Monkey::app()->dbAdapter->query($sql,[]);
            foreach($res as $result){
                                $sku = $productSkuRepo->findOneBy(['productId' => $result['productId'],'productVariantId' => $result['productVariantId'],'productSizeId' => $result['productSizeId'],'shopId' => $result['shopId']]);
                                $sku->stockQty = $dirtySku->qty;
                                $sku->update();
                            }

        }catch(\Throwable $e){
            $this->report('CAlignNewStockFromDirtySkuJob','Error Align Quantity',$e->getMessage().'-'.$e->getLine());
        }


    }
}
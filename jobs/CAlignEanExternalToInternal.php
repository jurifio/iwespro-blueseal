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

/**
 * Class CAlignEanExternalToInternal
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/07/2019
 * @since 1.0
 */
class CAlignEanExternalToInternal extends ACronJob
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
        $this->alignEanToProduct();
    }

    /**
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function alignEanToProduct()
    {
        /* definizione delle repo */


        /** @var CProductSku $productSkuRepo */
        $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');
        /** @var CProductEan $productEanRepo */
        $productEanRepo = \Monkey::app()->repoFactory->create('ProductEan');
        /** @var CProduct $productRepo */
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $productSkuCollect = $productSkuRepo->findAll();
        foreach ($productSkuCollect as $skus) {
            if ($skus->ean == null) {
                $productEan = $productEanRepo->findOneBy(['productId' => $skus->productId, 'productVariantId' => $skus->productVariantId, 'productSizeId' => $skus->productSizeId]);
                if ($productEan != null) {
                    $eanToAssign = $productEan->ean;
                    $productAssign = $productSkuRepo->findOneBy(['productId' => $productEan->productId, 'productVariantId' => $productEan->productVariantId, 'productSizeid' => $productEan->prodcutSizeId]);
                    $productAssign->ean = $eanToAssign;
                    $productAssign->update();
                    $this->report('assign ean from productEan to productSku ', 'assigned to '.$productEan->productId."-".$productEan->productVariantid."-".$productEan->productSizeId);
                }
            } else {
                $eanToAssign=$skus->ean;
                $productEan = $productEanRepo->findOneBy(['productId' => $skus->productId, 'productVariantId' => $skus->productVariantId, 'productSizeId' => $skus->productSizeId]);
                if($productEan==null){
                    $productEanInsert=$productEanRepo-getEmptyEntity();
                    $productEanInsert->productId=$skus->productId;
                    $productEanInsert->productVariantid=$skus->productVariantId;
                    $productEanInsert->productSizeid=$skus->productSizeId;
                    $productEanInsert->usedForParent=0;
                    $productEanInsert->used=1;
                    $findBrandProduct=$productRepo->findOneBy(['id'=>$skus->productId,'productVariantId'=>$skus->productVariantId]);
                    $brandAssociate=$findBrandProduct->productBrandId;
                    $productEanInsert->brandAssociate=$brandAssociate;
                    $productEanInsert->fileImported='ProductSku';
                    $productEanInsert->shopId=1;
                    $productEanInsert->insert();
                    $this->report('insert ean to productEan from productSku ', 'assigned to '.$productEan->productId."-".$productEan->productVariantid."-".$productEan->productSizeId);

                }else{
                    $productClean=$productEan->productId."-".$productEan->productVariantid."-".$productEan->productSizeId;
                    $productEan->productId='';
                    $productEan->productVariantId='';
                    $productEan->productSizeId='';
                    $productEan->used=0;
                    $productEan->branAssociate=0;
                    $productEan->shopid=0;
                    $productEan->update();
                    $this->report('clean  ean to  productEan from previous Assignment ', 'cleaned for '.$productClean);
                    $productEanInsert=$productEanRepo-getEmptyEntity();
                    $productEanInsert->productId=$skus->productId;
                    $productEanInsert->productVariantid=$skus->productVariantId;
                    $productEanInsert->productSizeid=$skus->productSizeId;
                    $productEanInsert->usedForParent=0;
                    $productEanInsert->used=1;
                    $findBrandProduct=$productRepo->findOneBy(['id'=>$skus->productId,'productVariantId'=>$skus->productVariantId]);
                    $brandAssociate=$findBrandProduct->productBrandId;
                    $productEanInsert->brandAssociate=$brandAssociate;
                    $productEanInsert->fileImported='ProductSku';
                    $productEanInsert->shopId=1;
                    $productEanInsert->insert();
                    $this->report('assign ean to productEan from productSku ', 'assigned to '.$skus->productId."-".$skus->productVariantid."-".$skus->productSizeId);
                }
            }
        }


    }
}
<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductEan;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\repositories\CProductRepo;


/**
 * Class CAlignEanInternalToExternalAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 24/07/2019
 * @since 1.0
 */
class CAlignEanInternalToExternalAjaxController extends AAjaxController
{


    public function post()
    {
        /* definizione delle repo */


        /** @var ARepo $productSkuRepo */
        $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');

        $productEanRepo = \Monkey::app()->repoFactory->create('ProductEan');

        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $productActive = $productRepo->findBy(['productStatusId' => 6]);
        foreach ($productActive as $active) {
            $productSkuCollect = $productSkuRepo->findBy(['productId' => $active->id, 'productVariantId' => $active->productVariantId]);
            foreach ($productSkuCollect as $skus) {
                if ($skus->ean != null) {
                    $productEan = $productEanRepo->findOneBy(['productId' => $skus->productId, 'productVariantId' => $skus->productVariantId, 'productSizeId' => $skus->productSizeId]);
                    if ($productEan != null) {

                        if ($productEan->ean == $skus->ean) {
                            continue;
                        } else {

                            $productEan->productId = null;
                            $productEan->productvariantId = null;
                            $productEan->productSizeId = null;
                            $productEan->usedForParent = 0;
                            $productEan->used = 0;
                            $productEan->brandAssociate = 0;
                            $productEan->shopId = null;
                            $productEan->update();
                            \Monkey::app()->applicationLog('CAlignEanInternalToExternalAjaxController', 'log', 'Release  ean  in productEan ' . $productEan->ean, ' previously assigned to ' . $skus->productId . "-" . $skus->productVariantId . "-" . $skus->productSizeId);
                            $productEanInsert = $productEanRepo->getEmptyEntity();
                            $productEanInsert->productId = $skus->productId;
                            $productEanInsert->productVariantId = $skus->productVariantId;
                            $productEanInsert->productSizeId = $skus->productSizeId;
                            $productEanInsert->ean = $skus->ean;
                            $productEanInsert->usedForParent = 0;
                            $productEanInsert->used = 1;
                            $findBrandProduct = $productRepo->findOneBy(['id' => $skus->productId, 'productVariantId' => $skus->productVariantId]);
                            $brandAssociate = $findBrandProduct->productBrandId;
                            $productEanInsert->brandAssociate = $brandAssociate;
                            $productEanInsert->shopId = 1;
                            $productEanInsert->insert();
                            \Monkey::app()->applicationLog('CAlignEanInternalToExternalAjaxController', 'log', 'Insert  ean Supplier  in productEan ' . $skus->ean, '  assigned to ' . $skus->productId . "-" . $skus->productVariantId . "-" . $skus->productSizeId);

                        }

                    }
                }
            }
        }
        return $res = 'Eseguito';
    }
}
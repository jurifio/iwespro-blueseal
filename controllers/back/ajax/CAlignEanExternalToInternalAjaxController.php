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
 * Class CAlignEanExternalToIntenalAjaxController
 * @package bamboo\controllers\back\ajax
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
class CAlignEanExternalToInternalAjaxController extends AAjaxController
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
            $productSkuCollect = $productSkuRepo->findBy(['productId' => $active->id, 'productVariantId' => $active->productVariantId, 'ean' => null]);
            foreach ($productSkuCollect as $skus) {
                if ($skus->ean == null) {
                    $productEan = $productEanRepo->findOneBy(['productId' => $skus->productId, 'productVariantId' => $skus->productVariantId, 'productSizeId' => $skus->productSizeId]);
                    if ($productEan == null) {
                        $productAssign = $productEanRepo->findOneBy(['used' => 0]);
                        if ($productAssign != null) {
                            $eanToAssign = $productAssign->ean;
                            $productAssign->productId = $skus->productId;
                            $productAssign->productVariantId = $skus->productVariantId;
                            $productAssign->productSizeId = $skus->productSizeId;
                            $productAssign->usedForParent = 0;
                            $productAssign->used = 1;
                            $findBrandProduct = $productRepo->findOneBy(['id' => $skus->productId, 'productVariantId' => $skus->productVariantId]);
                            $brandAssociate = $findBrandProduct->productBrandId;
                            $productAssign->brandAssociate = $brandAssociate;
                            $productAssign->shopId = 1;
                            $productAssign->update();
                            \Monkey::app()->applicationLog('CAlignEanExternalToInternalAjaxController', 'log', 'assign ean to ProductSku in productEan  ', 'assigned to ' . $skus->productId . "-" . $skus->productVariantId . "-" . $skus->productSizeId);
                            $skus->ean = $eanToAssign;
                            $skus->update();
                        } else {
                            $res = 'sono Finiti tutti gli ean liberi';
                        }
                    } else {
                        $eanToAssign = $productEan->ean;
                        $skus->ean = $eanToAssign;
                        $skus->update();
                        \Monkey::app()->applicationLog('CAlignEanExternalToInternalAjaxController', 'log', 'assign ean exist to ProductSku in productEan  ', 'assigned to ' . $skus->productId . "-" . $skus->productVariantId . "-" . $skus->productSizeId);
                    }
                }
            }
        }
        return $res;
    }
}
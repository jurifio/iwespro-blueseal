<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CProductEan;
use bamboo\domain\repositories\CProductRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CProductSkuRepo;

/**
 * Class CManageProductSkuAutomaticEan
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/10/2018
 * @since 1.0
 */
class CManageProductSkuAutomaticEan extends AAjaxController
{
    /**
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     * @throws \bamboo\core\exceptions\RedPandaException
     */
    public function post()
    {

        $brandId = \Monkey::app()->router->request()->getRequestData('p');

        /** @var CObjectCollection $products */
        $products = \Monkey::app()->repoFactory->create('Product')->findBy(["productBrandId" => $brandId ]);

        /** @var CRepo $eanrepo */
        $eanrepo = \Monkey::app()->repoFactory->create('ProductEan');

        /** @var CProduct $product */
        foreach ($products as $product) {

            /** @var CObjectCollection $skus */
            $skus = $product->productSku;

            /** @var CProductSku $sku */
            foreach ($skus as $sku){
                if(!is_null($sku->ean) && $sku->ean != 0 ){
                  continue;
                }

                if($sku->stockQty == 0) continue;

                $eanuse = $eanrepo->findOneBy(['used' => 0]);
                if (null == $eanuse) {
                    throw new BambooException('Tutti i codici Sono Stati Assegnati devi rigenerare altri codici');
                } else {
                    $sku->ean = $eanuse->ean;
                    $sku->update();

                    $eanuse->productId = $sku->productId;
                    $eanuse->productVariantId = $sku->productVariantId;
                    $eanuse->productSizeId = $sku->productSizeId;
                    $eanuse->used = 1;
                    $eanuse->brandAssociate = $brandId;
                    $eanuse->update();
                }
            }
        }

        return "Fatto";


    }
}
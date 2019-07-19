<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CProductEan;
use bamboo\domain\entities\CProductHasTag;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CTag;
use bamboo\domain\repositories\CPrestashopHasProductRepo;


/**
 * Class CAssignEanToMarketPlaceProductAssociateAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/07/2019
 * @since 1.0
 */
class CAssignEanToMarketPlaceProductAssociateAjaxController extends AAjaxController
{
    /**
     * @return bool
     */
    public function post()
    {

        /* definizione delle repo */
        /** @var CProductBrand $productBrandRepo */
        $productBrandRepo = \Monkey::app()->repoFactory->create('ProductBrand');
        /** @var CProduct $productRepo */
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        /** @var CProductSku $productSkuRepo */
        $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');
        /** @var CProductEan $productEanRepo */
        $productEanRepo = \Monkey::app()->repoFactory->create('ProductEan');
        /** @var CPrestashopHasProduct $prestashopHasProductRepo */
        $prestashopHasProductRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
        /** @var CPrestashopHasProductHasMarketplaceHasShop $phphmhs */
        $phphmhs = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');
        /*prima estrabolo dalla tabella di ProductBrand i brand con i diritti per la pubblicazione su marketplace*/
        /** @var CProductBrand $productBrand */
        /* ciclo il  brand con il filtro delle colonne hasMarketplaceRights  */
        $productBrands = $productBrandRepo->findBy(['hasMarketplaceRights' => 1]);
        /* verifico se l'array non è nullo */
        if ($productBrands != null) {
            foreach ($productBrands as $productBrand) {

                $hasExternalEan = $productBrand->hasExternalEan;
                $hasMarketplaceRights = $productBrand->hasMarketplaceRights;
                /** @var CProduct $product */

                $product = $productRepo->findBy(['productBrandId' => $productBrand->id, 'productStatusId' => 6]);
                if ($product !== null) {
                    foreach ($product as $products) {
                        $productId = $products->id;
                        $productVariantId = $products->productVariantId;
                        /* ciclo i proodotti da product per prestashophasproduct verificando se gia sono inseriti o meno*/
                        $prestashopHasProduct = $prestashopHasProductRepo->findOneBy(['productId' => $productId, 'productVariantId' => $productVariantId]);
                        /* se non ci sono li inserisco e gli metto lo stato a 0 altrimenti salta il ciclo*/
                        if ($prestashopHasProduct != null) {
                            continue;
                        } else {
                            if ($hasMarketplaceRights == 1) {
                                /** @var CPrestashopHasProduct $prestashopHasProductInsert */
                                $prestashopHasProductInsert = $prestashopHasProductRepo->getEmptyEntity();
                                $prestashopHasProductInsert->productId = $productId;
                                $prestashopHasProductInsert->productVariantId = $productVariantId;
                                $prestashopHasProductInsert->status = 0;
                                $prestashopHasProductInsert->insert();
                            }
                            /*se il brand ha l'utilizzo degli ean esterni passo all'inserimento dell'ean*/
                            if ($hasExternalEan == 1) {
                                /*nell inserimento del prodotto padre verifico  se esiste l'ean  nella tabella productEan con il filtro sul prodotto*/
                                /** @var CProductEan $productEanParentFind */
                                $productEanParentFind = $productEanRepo->findOneBy(['productId' => $productId, 'productVariantId' => $productVariantId, 'productSizeId' => 0]);
                                if ($productEanParentFind == null) {
                                    /*   se non esiste lo inserisco */
                                    /** @var CProductEan $productEanParentInsert */
                                    $productEanParentInsert = $productEanRepo->findOneBy(['used' => 0]);
                                    $productEanParentInsert->productId = $productId;
                                    $productEanParentInsert->productVariantId = $productVariantId;
                                    $productEanParentInsert->productSizeId = 0;
                                    $productEanParentInsert->usedForParent = 1;
                                    $productEanParentInsert->used = 1;
                                    $productEanParentInsert->brandAssociate = $productBrand->id;
                                    $productEanParentInsert->shopId = 1;
                                    $productEanParentInsert->update();
                                }
                                /* colleziono ogni taglia prodotto su productSku*/
                                /** @var CProductSku $productSku */
                                $productSku = $productSkuRepo->findBy(['productId' => $productId, 'productVariantId' => $productVariantId]);
                                if ($productSku != null) {
                                    /* ciclo le taglie*/
                                    foreach ($productSku as $productSkus) {
                                        $productSizeId = $productSkus->productSizeId;
                                        /*vedo se esiste il prodotto per le taglie*/
                                        $productEanFind = $productEanRepo->findOneBy(['productId' => $productId, 'productVariantId' => $productVariantId, 'productSizeId' => $productSizeId]);
                                        if ($productEanFind != null) {
                                            continue;
                                        } else {
                                            /** @var CProductEan $productEanInsert */
                                            $productEanInsert = $productEanRepo->findOneBy(['used' => 0]);
                                            if ($productEanInsert != null) {
                                                $productEanInsert->productId = $productId;
                                                $productEanInsert->productVariantId = $productVariantId;
                                                $productEanInsert->productSizeId = $productSizeId;
                                                $productEanInsert->usedForParent = 0;
                                                $productEanInsert->used = 1;
                                                $productEanInsert->brandAssociate = $productBrand->id;
                                                $productEanInsert->shopId = 1;
                                                $productEanInsert->update();
                                            }


                                        }
                                    }
                                }

                            }
                        }
                    }
                }

            }
        }

        return true;
    }


}
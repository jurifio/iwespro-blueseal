<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooLogicException;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductPhoto;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CShopHasProduct;
use bamboo\domain\entities\CStorehouseOperation;
use bamboo\domain\repositories\CCartRepo;
use bamboo\domain\repositories\CProductRepo;
use bamboo\domain\repositories\CProductSkuRepo;
use bamboo\domain\repositories\CShopHasProductRepo;
use bamboo\domain\repositories\CStorehouseOperationRepo;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CProductMerge extends AAjaxController
{
    public function get()
    {
        $get = $this->app->router->request()->getRequestData();
        $prods = $get['rows'];

        //controllo size group e se ci sono ordini relativi ai prodotti da unire
        $repoPro = $this->app->repoFactory->create('Product');
        $repoOrd = $this->app->repoFactory->create('OrderLine');
        $sizeGroupCompatibility = true;
        $sizeGroupMacroGroup = false;

        foreach ($prods as $k => $v) {
            /** @var CProduct $product */
            $product = $repoPro->findOne($v);

            if ($sizeGroupMacroGroup === false) {
                $sizeGroupMacroGroup = $product->productSizeGroup->productSizeMacroGroup->name;
            } else {
                if ($product->productSizeGroup->productSizeMacroGroup->name != $sizeGroupMacroGroup) {
                    $sizeGroupCompatibility = false;
                    break;
                }
            };
            $prods[$k]['areOrders'] = ($repoOrd->findBy(['productId' => $v['id'], 'productVariantId' => $v['productVariantId']])->count()) ? 1 : 0;


            $skus = [];
            foreach ($product->productSku as $sku) {
                if (!in_array($sku->shopId, $skus)) $skus[] = $sku->shopId;
            }

            if (1 < count($skus)) {
                $prods[$k]['friend'] = 'multipli';
            } else {
                $prods[$k]['friend'] = \Monkey::app()->repoFactory->create('Shop')->findOne([$skus[0]])->title;
            }

            $prods[$k]['cpf'] = $product->printCpf();
        }

        $res = [
            'sizeGroupCompatibility' => $sizeGroupCompatibility,
            'rows' => $prods
        ];
        return json_encode($res);
    }

    public function post()
    {
        $choosen = $this->app->router->request()->getRequestData('choosen');
        $rows = $this->app->router->request()->getRequestData('rows');
        $res = $this->mergeProducts($rows, $choosen);
        return $res;
    }

    /**
     * @param $rows
     * @param $choosen
     * @return string
     */
    public function mergeProducts($rows, $choosen)
    {
        /** @var CProductRepo $productRepo */
        $productRepo = \Monkey::app()->repoFactory->create('Product');

        $chosenProduct = null;
        $otherProducts = [];
        foreach ($rows as $key => $row) {
            /** @var CProduct $product */
            $product = $productRepo->findOne($row);

            if ($product->productStatusId == 13) return "Errore: uno dei prodotti da fondere è già in stato FUSO: " . $product->printId();
            if ($key == $choosen) {
                $chosenProduct = $product;
            } else {
                $otherProducts[] = $product;
            }
        }
        try {
            \Monkey::app()->dbAdapter->beginTransaction();
            /** @var CCartRepo $cartRepo */
            $cartRepo = \Monkey::app()->repoFactory->create('Cart');
            /** @var CShopHasProductRepo $shopHasProductRepo */
            $shopHasProductRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');
            /** @var CProductSkuRepo $productSkuRepo */
            $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');
            /** @var CStorehouseOperationRepo $storehouseOperationRepo */
            $storehouseOperationRepo = \Monkey::app()->repoFactory->create('StorehouseOperation');

            /** @var CProduct $chosenProduct */
            foreach ($otherProducts as $otherProduct) {

                /** @var CProduct $otherProduct */
                if ($otherProduct->productSizeGroupId !== null && $otherProduct->productSizeGroup->productSizeMacroGroup->name != $chosenProduct->productSizeGroup->productSizeMacroGroup->name) {
                    return "Errore: I gruppi taglia dei prodotti da fondere sono incompatibili: " . $otherProduct->printId();
                }

                foreach ($otherProduct->shopHasProduct as $otherShopHasProduct) {
                    /** @var CShopHasProduct $otherShopHasProduct */

                    /** @var CShopHasProduct $chosenShopHasProduct */
                    $chosenShopHasProduct = $shopHasProductRepo->findOneBy([
                        'productId' => $chosenProduct->id,
                        'productVariantId' => $chosenProduct->productVariantId,
                        'shopId' => $otherShopHasProduct->shopId
                    ]);

                    if ($chosenShopHasProduct === null) {
                        try {
                            $chosenShopHasProduct = clone $otherShopHasProduct;
                            $chosenShopHasProduct->productId = $chosenProduct->id;
                            $chosenShopHasProduct->productVariantId = $chosenProduct->productVariantId;
                            $chosenShopHasProduct->smartInsert();
                        } catch (\Throwable $e) {
                            throw new BambooLogicException('Non sono riuscito a inserire il nuovo prodotto nello shop', [], -1, $e);
                        }

                    }

                    foreach ($otherShopHasProduct->dirtyProduct as $otherDirtyProduct) {
                        try {
                            $otherDirtyProduct->productId = $chosenProduct->id;
                            $otherDirtyProduct->productVariantId = $chosenProduct->productVariantId;
                            $otherDirtyProduct->update();
                        } catch (\Throwable $e) {
                            throw new BambooLogicException('Non sono riuscito ad aggiornre il prodotto sporco', [], -1, $e);
                        }

                    }

                    foreach ($otherShopHasProduct->productSku as $otherProductSku) {
                        /** @var CProductSku $chosenProductSku */
                        /** @var CProductSku $otherProductSku */
                        $chosenProductSku = $productSkuRepo->findOneBy([
                            'productId' => $chosenProduct->id,
                            'productVariantId' => $chosenProduct->productVariantId,
                            'shopId' => $otherShopHasProduct->shopId,
                            'productSizeId' => $otherProductSku->productSizeId
                        ]);

                        if ($chosenProductSku === null) {
                            try {
                                $chosenProductSku = clone $otherProductSku;
                                $chosenProductSku->productId = $chosenProduct->id;
                                $chosenProductSku->productVariantId = $chosenProduct->productVariantId;
                                $chosenProductSku->insert();
                            } catch (\Throwable $e) {
                                throw new BambooLogicException('Non sono riuscito ad inserire il nuovo Sku', [], -1, $e);
                            }

                        } else {
                            try {
                                $chosenProductSku->stockQty += $otherProductSku->stockQty;
                                $chosenProductSku->update();
                            } catch (\Throwable $e) {
                                throw new BambooLogicException('Non sono riuscito ad aggiornare il nuovo Sku', [], -1, $e);
                            }
                        }
                        try {
                            $otherProductSku->stockQty = 0;
                            $otherProductSku->update();
                        } catch (\Throwable $e) {
                            throw new BambooLogicException('Non sono riuscito ad azzerare le quantità', [], -1, $e);
                        }
                        try {
                            $storehouseOperationRepo->moveSilentlyMovementOnADifferentProductSku($otherProductSku, $chosenProductSku);
                        } catch (\Throwable $e) {
                            throw new BambooLogicException('Non sono riuscito a modificare i movimenti', [], -1, $e);
                        }

                        try {
                            foreach ($otherProductSku->getPublicProductSku()->cartLine as $cartLine) {
                                $cartRepo->removeSku($cartLine);
                                $cartRepo->addSku($chosenProductSku->getPublicProductSku(), 1, $cartLine->cart);
                            }
                        } catch (\Throwable $e) {
                            throw new BambooLogicException('Non sono riuscito a modificare i carrelli collegati', [], -1, $e);
                        }
                    }
                }
                try {
                    $otherProduct->writeHistory('Fusion','Fused with product: '.$chosenProduct->printId());
                    $otherProduct->productStatusId = 13;
                    $otherProduct->update();
                } catch (\Throwable $e) {
                    throw new BambooLogicException('Non sono riuscito a modificare lo stato del carrello');
                }
            }
            $chosenProduct->updatePublicSkus();
            \Monkey::app()->dbAdapter->commit();
            return "Fusione eseguita!";
        } catch (\Throwable $e) {
            \Monkey::app()->dbAdapter->rollBack();
            return $e->getMessage();
        }
    }
}
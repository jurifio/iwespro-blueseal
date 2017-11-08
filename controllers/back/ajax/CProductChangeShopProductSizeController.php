<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CShopHasProduct;
use bamboo\domain\repositories\CShopHasProductRepo;

/**
 * Class CProductChangeShopProductSizeController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CProductChangeShopProductSizeController extends AAjaxController
{
    public function get()
    {
        $products = \Monkey::app()->router->request()->getRequestData('products');
        $shopHasProductRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');
        $points = [];
        $bind = [];
        $multi = [];
        foreach ($products as $product) {
            /** @var CShopHasProduct $shopHasProduct */
            $shopHasProduct = $shopHasProductRepo->findOneByStringId($product);
            if ($shopHasProduct->product->shopHasProduct->count() > 1) $multi[$shopHasProduct->product->printId()] = $product;
            $points[] = '(?,?,?)';
            $bind = array_merge($bind, explode('-', $product));
        }

        if (count($multi) === 0) {
            return json_encode(\Monkey::app()->repoFactory->create('ProductSizeGroup')->findAll());
        } else {
            $points = implode(',', $points);
            $sql = "SELECT psg.id
                FROM ProductSizeGroup psg
                  JOIN ProductSizeGroup psg2 on psg2.macroName = psg.macroName
                  JOIN ProductSizeGroup psg3 on psg3.macroName = psg.macroName
                  JOIN ShopHasProduct shp ON psg2.id = shp.productSizeGroupId 
                  JOIN Product p on (p.id,p.productVariantId) = (shp.productId, shp.productVariantId) and p.productSizeGroupId = psg3.id
                WHERE (shp.productId,shp.productVariantId,shp.shopId) IN ($points) ORDER BY psg.locale";
            $productSizeGroups = $this->app->repoFactory->create('ProductSizeGroup')->findBySql($sql, $bind);
            \Monkey::app()->router->response()->setContentType('application/json');
            return json_encode($productSizeGroups);
        }
    }

    public function put()
    {
        $productSizeGroupId = $this->app->router->request()->getRequestData('productSizeGroupId');
        $productsIds = $this->app->router->request()->getRequestData('products');
        $forceChange = $this->app->router->request()->getRequestData('forceChange');
        if (!$productSizeGroupId) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return "Errore: nessun gruppo taglie selezionato.";
        } elseif (!$productsIds) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return "Nessun prodotto selezionato";
        } else {
            /** @var CShopHasProductRepo $shopHasProductRepo */
            $shopHasProductRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');
            /** @var CProductSizeGroup $productSizeGroup */
            $productSizeGroup = \Monkey::app()->repoFactory->create('ProductSizeGroup')->findOneByStringId($productSizeGroupId);

            try {
                \Monkey::app()->dbAdapter->beginTransaction();
                foreach ($productsIds as $productIds) {
                    /** @var CShopHasProduct $shopHasProduct */
                    $shopHasProduct = $shopHasProductRepo->findOneByStringId($productIds);
                    $shopHasProductRepo->changeShopHasProductProductSizeGroup($shopHasProduct, $productSizeGroup, $forceChange);
                }
                \Monkey::app()->dbAdapter->commit();
                return "Il gruppo taglie è stato assegnato alle righe selezionate.";
            } catch (\Throwable $e) {
                \Monkey::app()->dbAdapter->rollBack();
                throw $e;
            }
        }
    }

    public function post()
    {
        throw new \Exception();
    }

    public function delete()
    {
        throw new \Exception();
    }
}
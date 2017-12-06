<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CShopHasProduct;
use bamboo\domain\repositories\CShopHasProductRepo;

/**
 * Class CChangePrivateProductSizeGroupController
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
class CChangePrivateProductSizeGroupController extends AAjaxController
{
    public function get()
    {
        $products = \Monkey::app()->router->request()->getRequestData('products');
        if ($products) {
            $productRepo = \Monkey::app()->repoFactory->create('Product');
            $shopHasProducts = [];
            foreach ($products as $productIds) {
                foreach ($productRepo->findOneByStringId($productIds)->shopHasProduct as $shopHasProduct) {
                    $shopHasProducts[] = $shopHasProduct->printId();
                };
            }
        } else {
            $shopHasProducts = \Monkey::app()->router->request()->getRequestData('shopHasProducts');
        }
        $shopHasProductRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');

        $points = [];
        $bind = [];
        $multi = [];
        foreach ($shopHasProducts as $shopHasProductIds) {
            /** @var CShopHasProduct $shopHasProduct */
            $shopHasProduct = $shopHasProductRepo->findOneByStringId($shopHasProductIds);
            if ($shopHasProduct->product->shopHasProduct->count() > 1) $multi[$shopHasProduct->product->printId()] = $shopHasProductIds;
            $points[] = '(?,?,?)';
            $bind = array_merge($bind, explode('-', $shopHasProductIds));
        }

        if (count($multi) === 0) {
            $return = \Monkey::app()->repoFactory->create('ProductSizeGroup')->findAll();
        } else {
            $points = implode(',', $points);
            $sql = "SELECT psg.id
                FROM ProductSizeGroup psg
                  JOIN ProductSizeGroup psg2 on psg2.productSizeMacroGroupId = psg.productSizeMacroGroupId
                  JOIN ProductSizeGroup psg3 on psg3.productSizeMacroGroupId = psg.productSizeMacroGroupId
                  JOIN ShopHasProduct shp ON psg2.id = shp.productSizeGroupId 
                  JOIN Product p on (p.id,p.productVariantId) = (shp.productId, shp.productVariantId) and p.productSizeGroupId = psg3.id
                WHERE (shp.productId,shp.productVariantId,shp.shopId) IN ($points) ORDER BY psg.locale";
            $return = \Monkey::app()->repoFactory->create('ProductSizeGroup')->findBySql($sql, $bind);
        }
        \Monkey::app()->router->response()->setContentType('application/json');
        foreach ($return as $productSizeGroup) {
            $productSizeGroup->productSizeMacroGroup;
            $productSizeGroup->productSize;
        }
        return json_encode($return);
    }

    public function put()
    {
        try {
            $productSizeGroupId = $this->app->router->request()->getRequestData('productSizeGroupId');
            $forceChange = $this->app->router->request()->getRequestData('forceChange');

            $productsIds = \Monkey::app()->router->request()->getRequestData('products');

            if ($productsIds) {
                $productRepo = \Monkey::app()->repoFactory->create('Product');
                $shopHasProductsIds = [];
                foreach ($productsIds as $productIds) {
                    foreach ($productRepo->findOneByStringId($productIds)->shopHasProduct as $shopHasProduct) {
                        $shopHasProductsIds[] = $shopHasProduct->printId();
                    };
                }
                $forceChange = true;
            } else {
                $shopHasProductsIds = \Monkey::app()->router->request()->getRequestData('shopHasProducts');
            }

            if (!$productSizeGroupId) {
                throw new BambooException("Errore: nessun gruppo taglie selezionato.");
            } elseif (!$shopHasProductsIds) {
                throw new BambooException("Nessun prodotto selezionato");
            } else {
                /** @var CShopHasProductRepo $shopHasProductRepo */
                $shopHasProductRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');
                /** @var CProductSizeGroup $productSizeGroup */
                $productSizeGroup = \Monkey::app()->repoFactory->create('ProductSizeGroup')->findOneByStringId($productSizeGroupId);

                try {
                    \Monkey::app()->repoFactory->beginTransaction();
                    foreach ($shopHasProductsIds as $shopHasProductIds) {
                        /** @var CShopHasProduct $shopHasProduct */
                        $shopHasProduct = $shopHasProductRepo->findOneByStringId($shopHasProductIds);
                        $shopHasProductRepo->changeShopHasProductProductSizeGroup($shopHasProduct, $productSizeGroup, $forceChange);
                    }
                    \Monkey::app()->repoFactory->commit();
                    return "Il gruppo taglie è stato assegnato alle righe selezionate.";
                } catch (\Throwable $e) {
                    \Monkey::app()->repoFactory->rollback();
                    throw $e;
                }
            }
        } catch (\Throwable $e) {
            \Monkey::app()->router->response()->setContentType('application/json');
            \Monkey::app()->router->response()->raiseProcessingError();
            return json_encode(['message'=>$e->getMessage(),'trace'=>$e->getTrace()]);
        }

    }

    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData('shopHasProductsGroup');
        /** @var CShopHasProductRepo $shopHasProductRepo */
        $shopHasProductRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');
        $productSizeGroupRepo = \Monkey::app()->repoFactory->create('ProductSizeGroup');
        $productSizeMacroGroupId = false;
        try {

            \Monkey::app()->repoFactory->beginTransaction();
            foreach ($data as $key => $value) {
                /** @var CShopHasProduct $shopHasProduct */
                $shopHasProduct = $shopHasProductRepo->findOneByStringId($key);
                /** @var CProductSizeGroup $productSizeGroup */
                $productSizeGroup = $productSizeGroupRepo->findOneByStringId($value);
                if (!$productSizeMacroGroupId) $productSizeMacroGroupId = $productSizeGroup->productSizeMacroGroup->id;
                if ($productSizeMacroGroupId != $productSizeGroup->productSizeMacroGroup->id) throw new BambooException("Macrogruppi Taglia incompatibili");

                $shopHasProduct = $shopHasProductRepo->changeShopHasProductProductSizeGroup($shopHasProduct,$productSizeGroup,true);
            }
            \Monkey::app()->repoFactory->commit();
            return json_encode(true);
        } catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();
            \Monkey::app()->router->response()->raiseProcessingError();
            return json_encode(['message'=>$e->getMessage(),'trace'=>$e->getTrace()]);
        }

    }

    public function delete()
    {
        throw new \Exception();
    }
}
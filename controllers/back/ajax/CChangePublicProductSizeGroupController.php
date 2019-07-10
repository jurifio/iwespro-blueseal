<?php

namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\repositories\CProductRepo;
use bamboo\domain\repositories\CPrestashopHasProductRepo;

/**
 * Class CChangePublicProductSizeGroupController
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
class CChangePublicProductSizeGroupController extends AAjaxController
{
    public function get()
    {
        $products = \Monkey::app()->router->request()->getRequestData('products');
        $points = [];
        $bind = [];
        foreach ($products as $product) {
            $points[] = '(?,?)';
            $bind = array_merge($bind, explode('-', $product));
        }
        $points = implode(',',$points);
        $sql = "SELECT psg.id
                FROM ProductSizeGroup psg
                  JOIN ProductSizeGroup psg2 on psg2.productSizeMacroGroupId = psg.productSizeMacroGroupId
                  JOIN ShopHasProduct shp ON psg2.id = shp.productSizeGroupId 
                WHERE (shp.productId,shp.productVariantId) IN ($points) ORDER BY psg.locale";
        $productSizeGroups = \Monkey::app()->repoFactory->create('ProductSizeGroup')->findBySql($sql, $bind);
        foreach ($productSizeGroups as $productSizeGroup) {
            $productSizeGroup->productSizeMacroGroup;
            $productSizeGroup->productSize;
        }
        \Monkey::app()->router->response()->setContentType('application/json');
        return json_encode($productSizeGroups);
    }

    public function put()
    {
       /** @var CPrestashopHasProductRepo $prestashopHasProductRepo */
        $prestashopHasProductRepo =\Monkey::app()->repoFactory->create('PrestashopHasProduct');
        /** @var CProductRepo $productRepo */
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $productSizeGroupId = $this->app->router->request()->getRequestData('productSizeGroupId');
        /** @var CProductSizeGroup $productSizeGroup */
        $productSizeGroup = \Monkey::app()->repoFactory->create('ProductSizeGroup')->findOneByStringId($productSizeGroupId);

        $productsIds = $this->app->router->request()->getRequestData('products');
        if (!$productSizeGroupId) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return "Errore: nessun gruppo taglie selezionato.";
        } elseif (!$productsIds) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return "Nessun prodotto selezionato";
        } else {
            foreach ($productsIds as $productIds) {
                /** @var CProduct $product */
                $product = $productRepo->findOneByStringId($productIds);
                $productId=$product->id;
                $productVariantId=$product->productVariantId;

                $productRepo->changeProductSizeGroup($product, $productSizeGroup);
                $prestashopHasProduct=$prestashopHasProductRepo->findOneBy(
                    [
                        'productId' => $productId,
                        'productVariantId' => $productVariantId
                    ]);
                if($prestashopHasProduct!==null){
                    $prestashopHasProduct->status=2;
                    $prestashopHasProduct->update();
                }
            }
            return "Il gruppo taglie è stato assegnato alle righe selezionate.";
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
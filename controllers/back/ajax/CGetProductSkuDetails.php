<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CGetProductSkuDetails extends AAjaxController
{
    /**
     * @return string
     */
    public function get() {
        $id = $this->app->router->request()->getRequestData('productId');
        $product = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($id);
        $res = [];
        foreach ($product->productSku as $sku) {
            if($sku->stockQty < 1) continue;
            $one = $sku->toArray();
            $one['skuCode'] = $sku->printId();
            $one['shop'] = $sku->shop->name;
            $one['size'] = $sku->productSize->name;
            $one['label'] = $sku->shop->name.' '.$sku->productSize->name;
            $res[] = $one;
        }
        return json_encode($res);
    }
}
<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductHasTag;
use bamboo\domain\entities\CTag;
use bamboo\domain\repositories\CPrestashopHasProductRepo;


/**
 * Class CInsertProductInPrestashopListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 14/03/2019
 * @since 1.0
 */
class CManageProductForPrestashopListAjaxController extends AAjaxController
{
    /**
     * @return bool
     */
    public function post()
    {
        $products = \Monkey::app()->router->request()->getRequestData('products');

        /** @var CPrestashopHasProductRepo $prestashopHasProductRepo */
        $prestashopHasProductRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
        foreach ($products as $product) {
            $pr = explode('-', $product);
            $pId = $pr[0];
            $pVariantId = $pr[1];

            /** @var CProduct $product */
            $product = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id'=>$pId, 'productVariantId'=>$pVariantId]);


            if(!is_null($product->prestashopHasProduct) || $product->productStatusId != 6) continue;

            $prestashopHasProduct = $prestashopHasProductRepo->getEmptyEntity();
            $prestashopHasProduct->productId = $pId;
            $prestashopHasProduct->productVariantId = $pVariantId;
            $prestashopHasProduct->smartInsert();
        }

        return true;
    }


}
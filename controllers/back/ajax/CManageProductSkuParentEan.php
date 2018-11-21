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
 * @date 21/11/2018
 * @since 1.0
 *
 */
class CManageProductSkuParentEan extends AAjaxController
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
        $shopId = \Monkey::app()->router->request()->getRequestData('s');


        /** @var CObjectCollection $products */
        $sql="select p.id as productId, p.productVariantId as productVariantId from Product p join ProductSku ps on p.id=ps.productId and p.productVariantId=ps.productVariantId
          where ps.shopId=".$shopId. " and p.productBrandId=".$brandId."   group by productId,productVariantId";
        $products = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();

        /** @var CProduct $product */
        foreach ($products as $product) {

            /** @var CRepo $eanrepo */
            $eanrepo = \Monkey::app()->repoFactory->create('ProductEan')->findOneBy(['productId'=>$product['productId'],'productVariantId'=>$product['productVariantId'],'productSizeId'=>0,'used'=>1,'BrandAssociate'=>$brandId,'shopId'=>$shopId]);

                    if (!is_null($eanrepo)) {
                        continue;
                    }

                    /*if ($sku->stockQty == 0) {
                        continue;
                    }*/
                    $eanuse = $eanrepo->findOneBy(['used' => 0]);


                        $eanuse->productId = $product['productId'];
                        $eanuse->productVariantId = $product['productVariantId'];
                        $eanuse->productSizeId = 0;
                        $eanuse->used = 1;
                        $eanuse->brandAssociate = $brandId;
                        $eanuse->shopId =$shopId;
                        $eanuse->update();
                    }




        return "Fatto";

    }

}
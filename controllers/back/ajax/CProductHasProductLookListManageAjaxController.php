<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductHasProductCorrelation;
use bamboo\domain\repositories\CProductRepo;


/**
 * Class CProductHasProductCorrelationManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/06/2020
 * @since 1.0
 */
class CProductHasProductLookListManageAjaxController extends AAjaxController
{
    public function get()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        /** @var CProductRepo $productRepo */
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');

        $productHasProductLookRepo = \Monkey::app()->repoFactory->create('ProductHasProductLook');

        $code = $data['code'];


        $products = $productHasProductLookRepo->findBy(['productLookId' => $code]);
        if ($products) {
            $res = '<div class="row"><div class="col-md-4">Prodotto</div><div class="col-md-4">Immagine</div><div class="col-md-4">Shop</div></div>';
            foreach ($products as $product) {
                $shop = $shopRepo->findOneBy(['id' => $product->shopId]);
                $shopName = $shop->name;

            $findPr=$productRepo->findOneBy(['id'=>$product->productId,'productVariantId'=>$product->productVariantId]);
                    $image = $findPr->dummyPicture;
                $res .= '<div class="row"><div class="col-md-4">' . $product->productId . '-' . $product->productVariantId . '</div>';
                $res .= '<div class="col-md-4"><img width="50px" src="' . $image . '"/></div>';
                $res .= '<div class="col-md-4">' . $shopName . '</div></div>';
            }
        } else {
            $res = 'non ci sono prodotti abbinati a questo look';
        }

        return $res;

    }

    public function post()
    {


    }

    public function put()
    {

    }

    public function delete()
    {

    }
}
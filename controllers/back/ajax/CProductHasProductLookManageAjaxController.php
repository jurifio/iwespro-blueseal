<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductHasProductCorrelation;


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
class CProductHasProductLookManageAjaxController extends AAjaxController
{
    public function get()
    {
        $look = [];
        $productLook = \Monkey::app()->repoFactory->create('ProductLook')->findAll();
        foreach ($productLook as $collect) {
            if ($collect->image != null) {
                $image = $collect->image;
            } else {
                $image = '';
            }
            array_push($look,['id' => $collect->id,'name' => $collect->name,'img' => $image]);
        }

        return json_encode($look);

    }

    public function post()
    {
        $res = '';
        $data = \Monkey::app()->router->request()->getRequestData();
        $shopHasProductRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');
        $productHasProductLookRepo = \Monkey::app()->repoFactory->create('ProductHasProductLook');

        $code = $data['code'];
        $products = $data['row'];
        foreach ($products as $product) {
            $prod = explode('-',$product);
            $productId = $prod[0];
            $productVariantId = $prod[1];
            $shopId = $shopHasProductRepo->findOneBy(['productId' => $productId,'productVariantId' => $productVariantId])->shopId;
            $findProduct = $productHasProductLookRepo->findOneBy(['lookId' => $code,'productId' => $productId,'productVariantId' => $productVariantId,'shopId' => $shopId]);
            if ($findProduct == null) {
                $productLook = $productHasProductLookRepo->getEmptyEntity();
                $productLook->lookId = $code;
                $productLook->productId = $productId;
                $productLook->productVariantId = $productVariantId;
                $productLook->shopId = $shopId;
                $productLook->insert();
                $res .= 'inserito prodotto ' . $productId . '-' . $productVariantId . '  su look ' . $code . '</br>';
            } else {
                $res .= 'prodotto  ' . $productId . '-' . $productVariantId . ' esistente non inserito su look ' . $code.' </br>';
                continue;
            }
        }


        return $res;

    }

    public function put()
    {

    }

    public function delete()
    {

    }
}
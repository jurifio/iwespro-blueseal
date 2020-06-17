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
class CProductHasProductCorrelationManageAjaxController extends AAjaxController
{
    public function get()
    {
        $correlation = [];
        $productCorrelation = \Monkey::app()->repoFactory->create('ProductCorrelation')->findAll();
        foreach ($productCorrelation as $collect) {
            if ($collect->image != null) {
                $image = $collect->image;
            } else {
                $image = '';
            }
            array_push($correlation,['id' => $collect->id,'name' => $collect->name,'code' => $collect->code,'img' => $image]);
        }

        return json_encode($correlation);

    }

    public function post()
    {
        $res = '';
        $data = \Monkey::app()->router->request()->getRequestData();
        $shopHasProductRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');
        $productHasProductCorrelation = \Monkey::app()->repoFactory->create('ProductHasProductCorrelation');
        $productCorrelationRepo = \Monkey::app()->repoFactory->create('ProductCorrelation');
        $code = $data['code'];
        $products = $data['row'];
        foreach ($products as $product) {
            $prod = explode('-',$product);
            $productId = $prod[0];
            $productVariantId = $prod[1];
            $shopId = $shopHasProductRepo->findOneBy(['productId' => $productId,'productVariantId' => $productVariantId])->shopId;
            $findProduct = $productHasProductCorrelation->findOneBy(['correlationId' => $code,'productId' => $productId,'productVariantId' => $productVariantId,'shopId' => $shopId]);
            if ($findProduct == null) {
                $productCorrel = $productHasProductCorrelation->getEmptyEntity();
                $productCorrel->correlationId = $code;
                $productCorrel->productId = $productId;
                $productCorrel->productVariantId = $productVariantId;
                $productCorrel->shopId = $shopId;
                $productCorrel->insert();
                $res .= 'inserito prodotto ' . $productId . '-' . $productVariantId . '  su correlazione ' . $code . '</br>';
            } else {
                $res .= 'prodotto  ' . $productId . '-' . $productVariantId . ' esistente non inserito su ' . $code.' </br>';
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
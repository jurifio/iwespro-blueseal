<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductHasShooting;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CShooting;
use bamboo\domain\entities\CShop;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\domain\repositories\CProductHasShootingRepo;
use bamboo\domain\repositories\CProductRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CShootingRepo;
use bamboo\domain\repositories\CShopRepo;


/**
 * Class CProductShootingForceAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/03/2018
 * @since 1.0
 */
class CProductShootingForceAjaxController extends AAjaxController
{
    /**
     * @return string
     */
     public function get(){
        $sId = [];

        $products = \Monkey::app()->router->request()->getRequestData('products');

        /** @var CProductRepo $prodRepo */
        $prodRepo = \Monkey::app()->repoFactory->create('Product');


         foreach ($products as $productId){

             $pId = explode('-', $productId)[0];
             $pV = explode('-', $productId)[1];

             /** @var CProduct $product */
             $product = $prodRepo->findOneBy(['id'=>$pId, 'productVariantId'=>$pV]);

             $sId[] = $product->getAllShootingsIdsFromProduct();

        }

         $result = [];
         $z = 0;
         foreach ($sId as $subarray) {
             if($z == 0){
                 $result = $subarray;
                 $z++;
             }
             $result = array_intersect($result, $subarray);
         }


        return json_encode($result);

    }

    public function put() {

        $products = \Monkey::app()->router->request()->getRequestData('products');
        $shootingId = \Monkey::app()->router->request()->getRequestData('shooting');

        if(empty($shootingId)){
            $res = "I prodotti selezionati non hanno shooting in comune";
            return $res;
        }

        /** @var CProductHasShootingRepo $phsRepo */
        $phsRepo = \Monkey::app()->repoFactory->create('ProductHasShooting');

        foreach ($products as $pids){

            $pId = explode('-', $pids)[0];
            $pVa = explode('-', $pids)[1];

            /** @var CProductHasShooting $phs */
            $phs = $phsRepo->findOneBy(['productId'=>$pId, 'productVariantId'=>$pVa, 'shootingId'=>$shootingId]);

            if(is_null($phs)){
                $res = "ERRORE GRAVE, CONTATTARE L'ASSISTENZA. Un prodotto non è stato trovato nello shooting";
                return $res;
            }

        $phsRepo->forceInsertProduct($phs);

        }

        $res = "Prodotti forzati e inseriti correttamente";
        return $res;

    }

}
<?php

namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\repositories\CProductBatchDetailsRepo;
use bamboo\domain\repositories\CProductBatchRepo;


/**
 * Class CMassiveProductBatchManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 21/05/2018
 * @since 1.0
 */
class CMassiveProductBatchManage extends AAjaxController
{

    /**
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     * @throws \bamboo\core\exceptions\RedPandaException
     */
    public function post()
    {
        $strProducts = \Monkey::app()->router->request()->getRequestData('products');
        $pBatch = \Monkey::app()->router->request()->getRequestData('batch');

        if(empty($strProducts) || empty($pBatch)) return false;

        $pbext = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(["id"=>$pBatch]);

        if(is_null($pbext)) return false;

        $allProducts = [];
        $productids = explode("\n", $strProducts);

        $noProd = [];

        foreach ($productids as $id){

            if(empty($id)) continue;

            $pCode = explode('__', $id);

            /** @var CProduct $product */
            $product = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($pCode[0]);

            if(!is_null($product)){

                $noProd[$product->printId()] = $this->checkAvaiable($product);

                if (!empty($noProd[$product->printId()])){
                    continue;
                }

                $allProducts[] = $pCode[0];
            } else continue;

        }

        /** @var CProductBatchDetailsRepo $pbdRepo */
        $pbdRepo = \Monkey::app()->repoFactory->create('ProductBatchDetails');

        if(!empty($allProducts)){
            $res = $pbdRepo->insertProductInEmptyProductBatch($pBatch, $allProducts);
            if (!$res) return 'Errore durante l\'associazione';
            //$resFinal['ok'] = $allProducts;
        }

        $resFinal = [];
        $resFinal['ko'] = $noProd;

        return json_encode($resFinal);
    }


    /**
     * @param CProduct $product
     * @return array
     */
    private function checkAvaiable(CProduct $product) : array {

        $avaiable = [];

        if($product->hasPhoto()) $avaiable['Foto'] = 1;

        if($product->productStatusId == 7 || $product->productStatusId == 8 || $product->productStatusId == 12) $avaiable['Stato'] = 1;

        if(is_null($product->productCardPhoto)) $avaiable['Scheda_prodotto'] = 1;

        return $avaiable;
    }
}
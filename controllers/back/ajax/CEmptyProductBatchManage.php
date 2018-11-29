<?php

namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\repositories\CProductBatchDetailsRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CWorkPriceListRepo;


/**
 * Class CEmptyProductBatchManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/05/2018
 * @since 1.0
 */
class CEmptyProductBatchManage extends AAjaxController
{

    public function post()
    {
        $unitPrice = \Monkey::app()->router->request()->getRequestData('unitPrice');
        $d = \Monkey::app()->router->request()->getRequestData('desc');
        $name = \Monkey::app()->router->request()->getRequestData('name');
        $mp = \Monkey::app()->router->request()->getRequestData('mp');
        $workCat = \Monkey::app()->router->request()->getRequestData('workCat');
        $deliveryTime = \Monkey::app()->router->request()->getRequestData('deliveryTime');

        if(!empty($d) || !empty($name) || !empty($deliveryDate) || !empty($unitPrice)){

            /** @var CProductBatchRepo $pbRepo */
            $pbRepo = \Monkey::app()->repoFactory->create('ProductBatch');

            /** @var CProductBatch $pb */
            $pb = $pbRepo->getEmptyEntity();
            $pb->paid = 0;
            $pb->description = $d;
            $pb->estimatedWorkDays = $deliveryTime;
            $pb->name = $name;
            $pb->workCategoryId = $workCat;
            $pb->unitPrice = $unitPrice;
            $pb->isUnassigned = 0;
            if($mp != "false") $pb->marketplace = 1;

            $pb->smartInsert();


        } else return "Inserisci i dati necessari";

        return true;
    }

    public function put(){
        $batch = \Monkey::app()->router->request()->getRequestData('batch');
        $products = \Monkey::app()->router->request()->getRequestData('products');

        /** @var CProductBatchDetailsRepo $pbdRepo */
        $pbdRepo = \Monkey::app()->repoFactory->create('ProductBatchDetails');
        $res = $pbdRepo->insertProductInEmptyProductBatch($batch, $products);

        if (!$res) {
            return 'Stai cercando di inserire prodotti in un lotto che non esiste';
        }

        return 'Prodotti inseriti con successo';

    }
}
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
        $d = \Monkey::app()->router->request()->getRequestData('desc');
        $name = \Monkey::app()->router->request()->getRequestData('name');
        $mp = \Monkey::app()->router->request()->getRequestData('mp');
        $aWpl = \Monkey::app()->router->request()->getRequestData('aWpl');
        $workCat = \Monkey::app()->router->request()->getRequestData('workCat');
        $wpl = \Monkey::app()->router->request()->getRequestData('wpl');
        $wcps = \Monkey::app()->router->request()->getRequestData('wcp');

        if(!empty($d)){

            /** @var CProductBatchRepo $pbRepo */
            $pbRepo = \Monkey::app()->repoFactory->create('ProductBatch');

            /** @var CProductBatch $pb */
            $pb = $pbRepo->getEmptyEntity();
            $pb->paid = 0;
            $pb->description = $d;
            $pb->name = $name;
            $pb->workCategoryId = $workCat;


            if($aWpl) {
                if (!$wcps) {
                    $pb->workPriceListId = $wpl;
                } else {
                    /** @var CWorkPriceListRepo $workPriceListRepo */
                    $workPriceListRepo = \Monkey::app()->repoFactory->create('WorkPriceList');
                    $ids = $workPriceListRepo->insertNewPrice($wcps, $workCat, 1);
                    $pb->workPriceListId = $ids[0];
                }

                $pb->marketplace = $mp ? 1 : 0;
            }

            $pb->smartInsert();


        } else return "Inserisci una descrizione";

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
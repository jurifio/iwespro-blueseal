<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\domain\repositories\CProductBatchRepo;

/**
 * Class CProductBatchInvoiceManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/03/2018
 * @since 1.0
 */
class CProductBatchInvoiceManage extends AAjaxController
{
    /**
     * @return string
     */
    public function get()
    {

        $productBatchIds = \Monkey::app()->router->request()->getRequestData('rows');
        $invoiceCase = \Monkey::app()->router->request()->getRequestData('invoiceCase');
        $singleProductBatchCost = 0;
        $res = [];

        /** @var CProductBatch $pB */
        $pB = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(['id'=>$productBatchIds[0]]);



        foreach ($productBatchIds as $pbId){

            /** @var CProductBatchRepo $pBRepo */
            $pBRepo = \Monkey::app()->repoFactory->create('ProductBatch');

            $singleProductBatchCost = $singleProductBatchCost + $pBRepo->calculateProductBatchCost($pbId);
        }



        switch ($invoiceCase){
            //fattura
            case 1:
                    $res["imponibile"] = $singleProductBatchCost;
                    $res["vat"] = $singleProductBatchCost * 0.22;
                    $res["total"] = $singleProductBatchCost * 1.22;
                break;
            //Prestazione occasionale
            case 2:
                $res["imponibile"] = $singleProductBatchCost;
                $res["vat"] = $singleProductBatchCost * 0.20;
                $res["total"] = $singleProductBatchCost - ($singleProductBatchCost * 0.20);
                break;
            //Ricevuta
            case 3:
                $res["imponibile"] = $singleProductBatchCost;
                $res["vat"] = 0;
                $res["total"] = $singleProductBatchCost;
                break;
        }

        $res["user"] = $pB->contractDetails->contracts->foison->user->id;

        return json_encode($res);

    }


    /**
     * @throws BambooException
     * @throws BambooInvoiceException
     */
    public function post(){

        $rows = explode(',', \Monkey::app()->router->request()->getRequestData('rows'));
        $number = \Monkey::app()->router->request()->getRequestData('number');
        $date = \Monkey::app()->router->request()->getRequestData('date');
        $total = \Monkey::app()->router->request()->getRequestData('total');
        $userId =\Monkey::app()->router->request()->getRequestData('userId');
        $invTyp =\Monkey::app()->router->request()->getRequestData('invTyp');

        $invoiceTypeId = 0;

        switch ($invTyp){
            case 1:
                $invoiceTypeId = 13;
                break;
            case 2:
                $invoiceTypeId = 14;
                break;
            case 3:
                $invoiceTypeId = 15;
                break;
        }

        /** @var CDocumentRepo $documentRepo */
        $documentRepo = \Monkey::app()->repoFactory->create('Document');

        $documentRepo->insertInvoiceFromFoison($invoiceTypeId, $userId, $date, $total, $number, $_FILES['file'], $rows);

        $res["responseText"] = "Fattura inserita con successo";
        return json_encode($res);
    }

}
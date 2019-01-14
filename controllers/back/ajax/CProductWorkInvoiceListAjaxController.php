<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CContracts;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchDetailsRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CWorkCategoryRepo;


/**
 * Class CProductBatchDetailsListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/03/2018
 * @since 1.0
 */
class CProductWorkInvoiceListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $user = \Monkey::app()->getUser();
        $userId = $user->id;

        $allShop = $user->hasPermission('allShops');
        $isWorker = $user->hasPermission('worker');

        $sql = "
            SELECT 
            d.id,
            d.userId,
            concat(f.name,' ', f.surname) as completeName,
            d.number,
            d.totalWithVat,
            it.name as invoiceTypeName
            FROM Document d
            JOIN InvoiceType it ON d.invoiceTypeId = it.id
            JOIN User u ON d.userId = u.id
            JOIN Foison f ON u.id = f.userId
            JOIN UserDetails ud ON u.id = ud.userId
            WHERE d.invoiceTypeId = 13 OR d.invoiceTypeId = 14 OR d.invoiceTypeId = 15
            ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        if($isWorker && !$allShop) {
            $datatable->addCondition('userId', [$userId]);
        }

        $datatable->doAllTheThings(false);

        $productBatchRepo = \Monkey::app()->repoFactory->create('ProductBatch');
        /** @var CDocumentRepo $document */
        $document = \Monkey::app()->repoFactory->create('Document');

        foreach ($datatable->getResponseSetData() as $key=>$row) {


            /** @var CObjectCollection $prsBatch */
            $prsBatch = $productBatchRepo->findBy(['documentId' =>$row['id']]);
            /** @var CDocument $d */
            $d = $document->findOneBy(['id'=>$row["id"]]);
            $row["id"] = $d->id;
            $row["completeName"] = $d->user->foison->name.' '.$d->user->foison->surname;
            $row["number"] = $d->number;
            $singleProductBatchCost = 0;
            foreach ($prsBatch as $prBatch){
                $singleProductBatchCost = $singleProductBatchCost + $productBatchRepo->calculateProductBatchCost($prBatch->id);
            }

            switch ($d->invoiceTypeId){
                //fattura
                case 13:
                    $effectiveTotal = $singleProductBatchCost * 1.22;
                    break;
                //Prestazione occasionale
                case 14:
                    $effectiveTotal = $singleProductBatchCost - ($singleProductBatchCost * 0.20);
                    break;
                //Ricevuta
                case 15:
                    $effectiveTotal = $singleProductBatchCost;
                    break;
            }

            $row['effectiveTotal'] = $effectiveTotal;
            $row["totalWithVat"] = $d->totalWithVat;
            $row["invoiceTypeName"] = $d->invoiceType->name;

            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}
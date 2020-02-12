<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\RedPandaOrderLogicException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CBillRegistryContract;
use bamboo\domain\entities\CBillRegistryContractRow;


/**
 * Class CBillRegistryContractRowPaymentBillManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 12/02/2020
 * @since 1.0
 */
class CBillRegistryContractRowPaymentBillManageAjaxController extends AAjaxController
{

    public function post()
    {
        $detailRow=[];
        $data = $this->app->router->request()->getRequestData();
        $billRegistryProductId = $data['productBillRegistryProductId'];
        $billRegistryProductFind=\Monkey::app()->repoFactory->create('BillRegistryProduct')->findOneBy(['id'=>$billRegistryProductId]);
        $billRegistryProductName=$billRegistryProductFind->nameProduct;
        $billRegistryProductCode=$billRegistryProductFind->codeProduct;
        $um=$data['um'];
        $qty=$data['qty'];
        $price=$data['price'];
        $billRegistryContractRowId=$data['billRegistryContractRowId'];
        $billRegistryTypeTaxesId=$data['productBillRegistryTypeTaxesId'];
        $billRegistryTypeTaxesFind=\Monkey::app()->repoFactory->create('BillRegistryTypeTaxes')->findOneBy(['id'=>$billRegistryTypeTaxesId]);
        $descritionTaxes=$billRegistryTypeTaxesFind->description;
        $billRegistryClientId=$data['billRegistryClientId'];
        $billRegistryContractRowDetailRepo=\Monkey::app()->repoFactory->create('BillRegistryContractRowDetail');
        $billRegistryPriceListRepo=\Monkey::app()->repoFactory->create('BillRegistryPriceList');
        try {
            $brplFind = $billRegistryPriceListRepo->findOneBy(['billRegistryProductId' => $billRegistryProductId,'billRegistryClientId' => $billRegistryClientId,'isActive' => 1]);
            if ($brplFind != null) {
                $brplFind->isActive = 0;
                $brplFind->update();
            }
            $brpl = $billRegistryPriceListRepo->getEmptyEntity();
            $brpl->billRegistryProductId = $billRegistryProductId;
            $brpl->billRegistryClientId = $billRegistryClientId;
            $brpl->price = $price;
            $brpl->isActive = 1;
            $brpl->insert();
            $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryPriceList ',[])->fetchAll();
            foreach ($res as $result) {
                $lastId = $result['id'];
            }
            $brcrd = $billRegistryContractRowDetailRepo->getEmptyEntity();
            $brcrd->billRegistryContractRowId = $billRegistryContractRowId;
            $brcrd->billRegistryProductId = $billRegistryProductId;
            $brcrd->um = $um;
            $brcrd->billRegistryPriceListId = $lastId;
            $brcrd->qty = $qty;
            $brcrd->billRegistryTypeTaxesId = $billRegistryTypeTaxesId;
            $brcrd->insert();
            $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContractRowDetail ',[])->fetchAll();
            foreach ($res as $result) {
                $lastRowDetailId = $result['id'];
            }

            $detailRow[]=['billRegistryContractRowDetailId'=>$lastRowDetailId,'nameProduct'=>$billRegistryProductCode.'-'.$billRegistryProductName,'taxDesc'=>$descritionTaxes];
            return $detailRow;
        }catch (\Throwable $e){
            \Monkey::app()->applicationLog('CBillRegistryContractRowDetailManageAjaxController','Error','Error insert product in detail Row',$e,'');
            return '0';
        }



    }

    public function get()
    {
        $data = $this->app->router->request()->getRequestData();
        $id = $data['id'];
        $billRegistryClientId=$data['billRegistryClientId'];

        $contractRowPayment = [];
        $brcrd = \Monkey::app()->repoFactory->create('BillRegistryContractRowPaymentBill')->findBy(['billRegistryContractRowId' => $id]);
        foreach ($brcrd as $paymentRow) {
            if($paymentRow->isSubmitted==1){
                $isSubmitted='checked="checked"';
            }else{
                $isSubmitted=' ';
            }
            if($paymentRow->isPaid==1){
                $isPaid='checked="checked"';
            }else{
                $isPaid=' ';
            }
            switch($paymentRow->mandatoryMonth){
                case 1:
                    $mandatoryMonth='Gennaio';
                    break;
                case 2:
                    $mandatoryMonth='Febbraio';
                    break;
                case 3:
                    $mandatoryMonth='Marzo';
                    break;
                case 4:
                    $mandatoryMonth='Aprile';
                    break;
                case 5:
                    $mandatoryMonth='Maggio';
                    break;
                case 6:
                    $mandatoryMonth='Giugno';
                    break;
                case 7:
                    $mandatoryMonth='Luglio';
                    break;
                case 8:
                    $mandatoryMonth='Agosto';
                    break;
                case 9:
                    $mandatoryMonth='Settembre';
                    break;
                case 10:
                    $mandatoryMonth='Ottobre';
                    break;
                case 11:
                    $mandatoryMonth='Novembre';
                    break;
                case 12:
                    $mandatoryMonth='Dicembre';
                    break;
            }
            $dateMandatory=strtotime($result['dateMandatoryMonth']);

        $dateMandatoryMonth=date('d-m-Y\TH:i', $dateMandatory);
            $contractRowPayment[]=['id'=>$paymentRow->id,'mandatoryMonth'=>$mandatoryMonth,' dateMandatoryMonth'=> $dateMandatoryMonth,'um'=>$paymentRow->um,'price'=>$bpl->price,'qty'=>$detailRow->qty,'taxes'=>$brt->description];
            }


return json_encode($contractRowDetail);

}

public
function put()
{


}

public
function delete()
{

}
}
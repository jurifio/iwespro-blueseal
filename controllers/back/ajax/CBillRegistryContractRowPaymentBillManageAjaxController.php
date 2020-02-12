<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\RedPandaOrderLogicException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CBillRegistryContract;
use bamboo\domain\entities\CBillRegistryContractRow;
use bamboo\domain\entities\CBillRegistryContractRowPaymentBill;



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
        $paymentRow=[];
        $data = $this->app->router->request()->getRequestData();
        $billRegistryGroupProductId = $data['billRegistryGroupProductId'];
        $billRegistryContractRowId=$data['billRegistryContractRowId'];
        $billRegistryClientId=$data['billRegistryClientId'];
        $mandatoryMonth=$data['mandatoryMonth'];
        $dateSent=$data['dateMandatoryMonth'];
        $amount=$data['amount'];
        $dateMandatory=new \DateTime($dateSent);
        $dateMandatoryMonth = $dateMandatory->format('Y-m-d H:i:s');
        $socialId=$data['socialId'];
        $campaignId=$data['campaignId'];

        try {

        $billRegistryContractRowPaymentBill=\Monkey::app()->repoFactory->create('BillRegistryContractRowPaymentBill')->getEmptyEntity();
        $billRegistryContractRowPaymentBill->mandatoryMonth=$mandatoryMonth;
        $billRegistryContractRowPaymentBill->billRegistryContractRowId=$billRegistryContractRowId;
        $billRegistryContractRowPaymentBill->billRegistryClientId=$billRegistryClientId;
        $billRegistryContractRowPaymentBill->billRegistryGroupProductId=$billRegistryGroupProductId;
        $billRegistryContractRowPaymentBill->dateMandatoryMonth=$dateMandatoryMonth;

        if($socialId!=null){
            $billRegistryContractRowPaymentBill->socialId=$socialId;
        }
        if($campaignId!=null){
            $billRegistryContractRowPaymentBill->campaignId=$campaignId;
        }
        $billRegistryContractRowPaymentBill->amount=$amount;
        $billRegistryContractRowPaymentBill->insert();


            $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContractRowPaymentBill ',[])->fetchAll();
            foreach ($res as $result) {
                $lastId = $result['id'];
            }
            switch($mandatoryMonth){
                case '1';
                $month='Gennaio';
                break;
                case '2';
                    $month='Febbraio';
                    break;
                case '3';
                    $month='Marzo';
                    break;
                case '4';
                    $month='Aprile';
                    break;
                case '5';
                    $month='Maggio';
                    break;
                case '6';
                    $month='Giugno';
                    break;
                case '7';
                    $month='Luglio';
                    break;
                case '8';
                    $month='Agosto';
                    break;
                case '9';
                    $month='Settembre';
                    break;
                case '10';
                    $month='Ottobre';
                    break;
                case '11';
                    $month='Novembre';
                    break;
                case '12';
                    $month='Dicembre';
                    break;
            }


            $paymentRow[]=['billRegistryContractRowPaymentId'=>$lastId,'mandatoryMonth'=>$month];
            return json_encode($paymentRow);
        }catch (\Throwable $e){
            \Monkey::app()->applicationLog('CBillRegistryContractRowPaymentBillManageAjaxController','Error','Error insert payment Row',$e,'');
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
            if($paymentRow->isSubmited==1){
                $isSubmited='checked="checked"';
            }else{
                $isSubmited=' ';
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
            $dateMandatory=new \DateTime($paymentRow->dateMandatoryMonth);
            $dateMandatoryMonth = $dateMandatory->format('d-m-Y');
            $contractRowPayment[]=['id'=>$paymentRow->id,'mandatoryMonth'=>$mandatoryMonth,'dateMandatoryMonth'=> $dateMandatoryMonth,'amount'=>$paymentRow->amount,'isSubmited'=>$isSubmited,'isPaid'=>$isPaid];
            }


return json_encode($contractRowPayment);

}

public
function put()
{


}

public
function delete()
{
    $data = $this->app->router->request()->getRequestData();
    $id = $data['billRegistryContractRowPaymentId'];
    $brcrd = \Monkey::app()->repoFactory->create('BillRegistryContractRowPaymentBill')->findOneBy(['id' => $id]);
    $brcrd->delete();
    return '1';
}
}
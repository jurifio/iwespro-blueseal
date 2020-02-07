<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\RedPandaOrderLogicException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CBillRegistryContract;
use bamboo\domain\entities\CBillRegistryContractRow;


/**
 * Class CBillRegistryContractManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/02/2020
 * @since 1.0
 */
class CBillRegistryContractRowManageAjaxController extends AAjaxController
{

    public function post()
    {
        $data = $this->app->router->request()->getRequestData();
        $billRegistryProductId=$data['billRegistryProductId'];
        $id=$data['id'];
        $billRegistryContractRowId=$data['billRegistryContractRowId'];
        switch($billRegistryProductId){
            case "1":
                try{
                    $rowInsert=\Monkey::app()->repoFactory->create('BillRegistryContractRowMonkSource')->getEmptyEntity();
                    $rowInsert->billRegistryContractRowId=$data['billRegistryContractRowId'];
                    $rowInsert->automaticInvoice=$data['automaticInvoice'];
                    $rowInsert->value=$data['value'];
                    $rowInsert->billingDay=$data['billingDay'];
                    $rowInsert->typePaymentId=$data['typePaymentId'];
                    $rowInsert->periodTypeCharge=$data['periodTypeCharge'];
                    $rowInsert->sellingFeeCommision=$data['sellingFeeCommision'];
                    $rowInsert->feeCreditCardCommission=$data['feeCreditCardCommission'];
                    $rowInsert->dayChargeFeeCreditCardCommission=$data['dayChargeFeeCreditCardCommission'];
                    $rowInsert->feeCodCommission=$data['feeCodCommission'];
                    $rowInsert->dayChargeFeeCodCommission=$data['dayChargeFeeCodCommission'];
                    $rowInsert->feeBankTransferCommission=$data['feeBankTransferCommission'];
                    $rowInsert->dayChargeFeeBankTransferCommission=$data['dayChargeFeeBankTransferCommission'];
                    $rowInsert->feePaypalCommission=$data['feePaypalCommission'];
                    $rowInsert->dayChargeFeePaypalCommission=$data['dayChargeFeePaypalCommission'];
                    $rowInsert->chargeDeliveryIsActive=$data['chargeDeliveryIsActive'];
                    $rowInsert->feeCostDeliveryCommission=$data['feeCostDeliveryCommission'];
                    $rowInsert->periodTypeChargeDelivery=$data['periodTypeChargeDelivery'];
                    $rowInsert->deliveryTypePaymentId=$data['deliveryTypePaymentId'];
                    $rowInsert->chargePaymentIsActive=$data['chargePaymentIsActive'];
                    $rowInsert->feeCostCommissionPayment=$data['feeCostCommissionPayment'];
                    $rowInsert->periodTypeChargePayment=$data['periodTypeChargePayment'];
                    $rowInsert->paymentTypePaymentId=$data['paymentTypePaymentId'];
                    $rowInsert->insert();

                    $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContractRowMonkSource ',[])->fetchAll();
                    foreach ($res as $result) {
                        $lastId = $result['id'];
                    }
                    \Monkey::app()->applicationLog( 'CBillRegistryContractRowManageAjaxController','Report','Insert BillRegistryContractRowMonkSource','Insert ContractRow' . $lastId,'');
                    return $lastId;
                }catch (\Throwable $e){
                    \Monkey::app()->applicationLog( 'CBillRegistryContractRowManageAjaxController' ,'Error','InsertBillRegistryContractRowMonkSource','Insert contactRow', $e);
                    return 'Errore Inserimento'.$e;

                }
        break;
            case "2":
                try{
                    $rowInsert=\Monkey::app()->repoFactory->create('BillRegistryContractRowMonkAir')->getEmptyEntity();
                    $rowInsert->billRegistryContractRowId=$data['billRegistryContractRowId'];
                    $rowInsert->automaticInvoice=$data['automaticInvoice'];
                    $rowInsert->value=$data['value'];
                    $rowInsert->billingDay=$data['billingDay'];
                    $rowInsert->typePaymentId=$data['typePaymentId'];
                    $rowInsert->periodTypeCharge=$data['periodTypeCharge'];
                    $rowInsert->sellingFeeCommision=$data['sellingFeeCommision'];
                    $rowInsert->feeCreditCardCommission=$data['feeCreditCardCommission'];
                    $rowInsert->dayChargeFeeCreditCardCommission=$data['dayChargeFeeCreditCardCommission'];
                    $rowInsert->feeCodCommission=$data['feeCodCommission'];
                    $rowInsert->dayChargeFeeCodCommission=$data['dayChargeFeeCodCommission'];
                    $rowInsert->feeBankTransferCommission=$data['feeBankTransferCommission'];
                    $rowInsert->dayChargeFeeBankTransferCommission=$data['dayChargeFeeBankTransferCommission'];
                    $rowInsert->feePaypalCommission=$data['feePaypalCommission'];
                    $rowInsert->dayChargeFeePaypalCommission=$data['dayChargeFeePaypalCommission'];
                    $rowInsert->chargeDeliveryIsActive=$data['chargeDeliveryIsActive'];
                    $rowInsert->feeCostDeliveryCommission=$data['feeCostDeliveryCommission'];
                    $rowInsert->periodTypeChargeDelivery=$data['periodTypeChargeDelivery'];
                    $rowInsert->deliveryTypePaymentId=$data['deliveryTypePaymentId'];
                    $rowInsert->chargePaymentIsActive=$data['chargePaymentIsActive'];
                    $rowInsert->feeCostCommissionPayment=$data['feeCostCommissionPayment'];
                    $rowInsert->periodTypeChargePayment=$data['periodTypeChargePayment'];
                    $rowInsert->paymentTypePaymentId=$data['paymentTypePaymentId'];
                    $rowInsert->insert();

                    $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContractRowMonkAir ',[])->fetchAll();
                    foreach ($res as $result) {
                        $lastId = $result['id'];
                    }
                    \Monkey::app()->applicationLog( 'CBillRegistryContractRowManageAjaxController','Report','Insert BillRegistryContractRowMonkAir','Insert ContractRow' . $lastId,'');
                    return $lastId;
                }catch (\Throwable $e){
                    \Monkey::app()->applicationLog( 'CBillRegistryContractRowManageAjaxController' ,'Error','Insert BillRegistryContractRowMonkAir','Insert contactRow', $e);
                    return 'Errore Inserimento'.$e;

                }
                break;
            case "3":
                try{
                    $rowInsert=\Monkey::app()->repoFactory->create('BillRegistryContractRowMonkEntrySocial')->getEmptyEntity();
                    $rowInsert->billRegistryContractRowId=$data['billRegistryContractRowId'];
                    $rowInsert->descriptionInvoice=$data['descriptionInvoice'];
                    $rowInsert->startUpCostCampaign=$data['startUpCostCampaign'];
                    $rowInsert->automaticInvoice=$data['automaticInvoice'];
                    $rowInsert->billingDay=$data['billingDay'];
                    $rowInsert->typePaymentId=$data['typePaymentId'];
                    $rowInsert->feeAgencyCommision=$data['feeAgencyCommision'];
                    $rowInsert->prepaidPaymentIsActive=$data['prepaidPaymentIsActive'];
                    $rowInsert->prepaidCost=$data['prepaidCost'];
                    $rowInsert->insert();

                    $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContractRowMonkEntrySocial ',[])->fetchAll();
                    foreach ($res as $result) {
                        $lastId = $result['id'];
                    }
                    \Monkey::app()->applicationLog( 'CBillRegistryContractRowManageAjaxController','Report','Insert BillRegistryContractRowMonkEntrySocial','Insert ContractRow' . $lastId,'');
                    return $lastId;
                }catch (\Throwable $e){
                    \Monkey::app()->applicationLog( 'CBillRegistryContractRowManageAjaxController' ,'Error','Insert BillRegistryContractRowMonkEntrySocial','Insert contactRow', $e);
                    return 'Errore Inserimento'.$e;

                }
                break;
            case "4":
                try{
                    $rowInsert=\Monkey::app()->repoFactory->create('BillRegistryContractRowMonkEntryTraffic')->getEmptyEntity();
                    $rowInsert->billRegistryContractRowId=$data['billRegistryContractRowId'];
                    $rowInsert->descriptionInvoice=$data['descriptionInvoice'];
                    $rowInsert->startUpCostCampaign=$data['startUpCostCampaign'];
                    $rowInsert->automaticInvoice=$data['automaticInvoice'];
                    $rowInsert->billingDay=$data['billingDay'];
                    $rowInsert->typePaymentId=$data['typePaymentId'];
                    $rowInsert->feeAgencyCommision=$data['feeAgencyCommision'];
                    $rowInsert->prepaidPaymentIsActive=$data['prepaidPaymentIsActive'];
                    $rowInsert->prepaidCost=$data['prepaidCost'];
                    $rowInsert->insert();

                    $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContractRowMonkEntryTraffic ',[])->fetchAll();
                    foreach ($res as $result) {
                        $lastId = $result['id'];
                    }
                    \Monkey::app()->applicationLog( 'CBillRegistryContractRowManageAjaxController','Report','Insert BillRegistryContractRowMonkEntryTraffic','Insert ContractRow' . $lastId,'');
                    return $lastId;
                }catch (\Throwable $e){
                    \Monkey::app()->applicationLog( 'CBillRegistryContractRowManageAjaxController' ,'Error','Insert BillRegistryContractRowMonkEntryTraffic','Insert contactRow', $e);
                    return 'Errore Inserimento'.$e;

                }
                break;

            case "5":
                try{
                    $rowInsert=\Monkey::app()->repoFactory->create('BillRegistryContractRowSocialMonk')->getEmptyEntity();
                    $rowInsert->billRegistryContractRowId=$data['billRegistryContractRowId'];
                    $rowInsert->automaticInvoice=$data['automaticInvoice'];
                    $rowInsert->value=$data['value'];
                    $rowInsert->billingDay=$data['billingDay'];
                    $rowInsert->typePaymentId=$data['typePaymentId'];
                    $rowInsert->periodTypeCharge=$data['periodTypeCharge'];
                    $rowInsert->sellingFeeCommision=$data['sellingFeeCommision'];
                    $rowInsert->feeCreditCardCommission=$data['feeCreditCardCommission'];
                    $rowInsert->dayChargeFeeCreditCardCommission=$data['dayChargeFeeCreditCardCommission'];
                    $rowInsert->feeCodCommission=$data['feeCodCommission'];
                    $rowInsert->dayChargeFeeCodCommission=$data['dayChargeFeeCodCommission'];
                    $rowInsert->feeBankTransferCommission=$data['feeBankTransferCommission'];
                    $rowInsert->dayChargeFeeBankTransferCommission=$data['dayChargeFeeBankTransferCommission'];
                    $rowInsert->feePaypalCommission=$data['feePaypalCommission'];
                    $rowInsert->dayChargeFeePaypalCommission=$data['dayChargeFeePaypalCommission'];
                    $rowInsert->chargeDeliveryIsActive=$data['chargeDeliveryIsActive'];
                    $rowInsert->feeCostDeliveryCommission=$data['feeCostDeliveryCommission'];
                    $rowInsert->periodTypeChargeDelivery=$data['periodTypeChargeDelivery'];
                    $rowInsert->deliveryTypePaymentId=$data['deliveryTypePaymentId'];
                    $rowInsert->chargePaymentIsActive=$data['chargePaymentIsActive'];
                    $rowInsert->feeCostCommissionPayment=$data['feeCostCommissionPayment'];
                    $rowInsert->periodTypeChargePayment=$data['periodTypeChargePayment'];
                    $rowInsert->paymentTypePaymentId=$data['paymentTypePaymentId'];
                    $rowInsert->insert();

                    $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContractRowSocialMonk ',[])->fetchAll();
                    foreach ($res as $result) {
                        $lastId = $result['id'];
                    }
                    \Monkey::app()->applicationLog( 'CBillRegistryContractRowManageAjaxController','Report','Insert BillRegistryContractRowSocialMonk','Insert ContractRow' . $lastId,'');
                    return $lastId;
                }catch (\Throwable $e){
                    \Monkey::app()->applicationLog( 'CBillRegistryContractRowManageAjaxController' ,'Error','Insert BillRegistryContractRowSocialMonk','Insert contactRow', $e);
                    return 'Errore Inserimento'.$e;

                }
                break;

            case "6":
                try{
                    $rowInsert=\Monkey::app()->repoFactory->create('BillRegistryContractRowFriends')->getEmptyEntity();
                    $rowInsert->billRegistryContractRowId=$data['billRegistryContractRowId'];
                    $rowInsert->typeContractId=$data['typeContractId'];
                    $rowInsert->valueMarkUpFullPrice=$data['valueMarkUpFullPrice'];
                    $rowInsert->valueMarkUpSalePrice=$data['valueMarkUpSalePrice'];
                    $rowInsert->insert();

                    $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContractRowFriends ',[])->fetchAll();
                    foreach ($res as $result) {
                        $lastId = $result['id'];
                    }
                    \Monkey::app()->applicationLog( 'CBillRegistryContractRowManageAjaxController','Report','Insert BillRegistryContractRowFriends','Insert ContractRow' . $lastId,'');
                    return $lastId;
                }catch (\Throwable $e){
                    \Monkey::app()->applicationLog( 'CBillRegistryContractRowManageAjaxController' ,'Error','Insert BillRegistryContractRowFriends','Insert contactRow', $e);
                    return 'Errore Inserimento'.$e;

                }
                break;

            case "7":
                try{
                    $rowInsert=\Monkey::app()->repoFactory->create('BillRegistryContractRowMailMonk')->getEmptyEntity();
                    $rowInsert->billRegistryContractRowId=$data['billRegistryContractRowId'];
                    $rowInsert->automaticInvoice=$data['automaticInvoice'];
                    $rowInsert->emailAccount=$data['emailAccount'];
                    $rowInsert->emailAccountSendQty=$data['emailAccountSendQty'];
                    $rowInsert->emailAccountCampaignQty=$data['emailAccountCampaignQty'];
                    $rowInsert->value=$data['value'];
                    $rowInsert->billingDay=$data['billingDay'];
                    $rowInsert->typePaymentId=$data['typePaymentId'];
                    $rowInsert->periodTypeCharge=$data['periodTypeCharge'];
                    $rowInsert->sellingFeeCommision=$data['sellingFeeCommision'];
                    $rowInsert->feeCreditCardCommission=$data['feeCreditCardCommission'];
                    $rowInsert->dayChargeFeeCreditCardCommission=$data['dayChargeFeeCreditCardCommission'];
                    $rowInsert->feeCodCommission=$data['feeCodCommission'];
                    $rowInsert->dayChargeFeeCodCommission=$data['dayChargeFeeCodCommission'];
                    $rowInsert->feeBankTransferCommission=$data['feeBankTransferCommission'];
                    $rowInsert->dayChargeFeeBankTransferCommission=$data['dayChargeFeeBankTransferCommission'];
                    $rowInsert->feePaypalCommission=$data['feePaypalCommission'];
                    $rowInsert->dayChargeFeePaypalCommission=$data['dayChargeFeePaypalCommission'];
                    $rowInsert->chargeDeliveryIsActive=$data['chargeDeliveryIsActive'];
                    $rowInsert->feeCostDeliveryCommission=$data['feeCostDeliveryCommission'];
                    $rowInsert->periodTypeChargeDelivery=$data['periodTypeChargeDelivery'];
                    $rowInsert->deliveryTypePaymentId=$data['deliveryTypePaymentId'];
                    $rowInsert->chargePaymentIsActive=$data['chargePaymentIsActive'];
                    $rowInsert->feeCostCommissionPayment=$data['feeCostCommissionPayment'];
                    $rowInsert->periodTypeChargePayment=$data['periodTypeChargePayment'];
                    $rowInsert->paymentTypePaymentId=$data['paymentTypePaymentId'];
                    $rowInsert->insert();

                    $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContractRowMailMonk ',[])->fetchAll();
                    foreach ($res as $result) {
                        $lastId = $result['id'];
                    }
                    \Monkey::app()->applicationLog( 'CBillRegistryContractRowManageAjaxController','Report','Insert BillRegistryContractRowMailMonk','Insert ContractRow' . $lastId,'');
                    return $lastId;
                }catch (\Throwable $e){
                    \Monkey::app()->applicationLog( 'CBillRegistryContractRowManageAjaxController' ,'Error','Insert BillRegistryContractRowMailMonk','Insert contactRow', $e);
                    return 'Errore Inserimento'.$e;

                }
                break;








        }


    }

    public function get()
    {
        $data = $this->app->router->request()->getRequestData();
        $id=$data['id'];
        $contractRow=[];
        /* @var CBillRegistryContractRow $brc */
        $brc=\Monkey::app()->repoFactory->create('BillRegistryContractRow')->findOneBy(['id'=>$id]);
        /* @var \bamboo\domain\entities\CBillRegistryContractRow $brcr*/
        $brcr=\Monkey::app()->repoFactory->create('BillRegistryContractRow')->findOneBy(['billRegistryContractId'=>$brc->id]);
        $brp=\Monkey::app()->repoFactory->create('BillRegistryProduct')->findOneBy(['id'=>$brcr->billRegistryProductId]);
        switch($brcr->billRegistryProductId){
            case "1":
                $tableData=\Monkey::app()->repoFactory->create('BillRegistryContractRowMonkSource')->findOneBy(['billRegistryContractRowId'=>$brcr->id]);
                if($tableData!=null) {
                    $contractDetailId = $tableData->id;
                    $isContractDetailRow = '0';
                }else{
                    $contractRow[]=[ 'exist'=>'0',
                        'billRegistryContractId'=>'0',
                        'billRegistryContractRowId'=>'0',
                        'billRegistryProductId'=>'0',
                        'contractDetailId'=>'0',
                        'isContractDetailRow'=>'0',
                        'nameProduct'=>'0'];
                    return json_encode($contractRow);
                }
                break;
            case "2":
                $tableData=\Monkey::app()->repoFactory->create('BillRegistryContractRowMonkAir')->findOneBy(['billRegistryContractRowId'=>$brcr->id]);
                if($tableData!=null) {
                    $contractDetailId = $tableData->id;
                    $isContractDetailRow = '0';
                }else{
                    $contractRow[]=[ 'exist'=>'0',
                        'billRegistryContractId'=>'0',
                        'billRegistryContractRowId'=>'0',
                        'billRegistryProductId'=>'0',
                        'contractDetailId'=>'0',
                        'isContractDetailRow'=>'0',
                        'nameProduct'=>'0'];
                    return json_encode($contractRow);
                }
                break;
            case "3":
                $tableData=\Monkey::app()->repoFactory->create('BillRegistryContractRowMonkEntrySocial')->findOneBy(['billRegistryContractRowId'=>$brcr->id]);
                if($tableData!=null) {
                    $contractDetailId = $tableData->id;
                    $isContractDetailRow = '1';
                }else{
                    $contractRow[]=[ 'exist'=>'0',
                        'billRegistryContractId'=>'0',
                        'billRegistryContractRowId'=>'0',
                        'billRegistryProductId'=>'0',
                        'contractDetailId'=>'0',
                        'isContractDetailRow'=>'0',
                        'nameProduct'=>'0'];
                    return json_encode($contractRow);
                }
                break;
            case "4":
                $tableData=\Monkey::app()->repoFactory->create('BillRegistryContractRowMonkEntryTraffic')->findOneBy(['billRegistryContractRowId'=>$brcr->id]);
                if($tableData!=null) {
                    $contractDetailId = $tableData->id;
                    $isContractDetailRow = '1';
                }else{
                    $contractRow[]=[ 'exist'=>'0',
                        'billRegistryContractId'=>'0',
                        'billRegistryContractRowId'=>'0',
                        'billRegistryProductId'=>'0',
                        'contractDetailId'=>'0',
                        'isContractDetailRow'=>'0',
                        'nameProduct'=>'0'];
                    return json_encode($contractRow);
                }
                break;
            case "5":
                $tableData=\Monkey::app()->repoFactory->create('BillRegistryContractRowSocialMonk')->findOneBy(['billRegistryContractRowId'=>$brcr->id]);
                if($tableData!=null) {
                    $contractDetailId = $tableData->id;
                    $isContractDetailRow = '1';
                }else{
                    $contractRow[]=[ 'exist'=>'0',
                        'billRegistryContractId'=>'0',
                        'billRegistryContractRowId'=>'0',
                        'billRegistryProductId'=>'0',
                        'contractDetailId'=>'0',
                        'isContractDetailRow'=>'0',
                        'nameProduct'=>'0'];
                    return json_encode($contractRow);
                }
                break;
            case "6":
                $tableData=\Monkey::app()->repoFactory->create('BillRegistryContractRowFriends')->findOneBy(['billRegistryContractRowId'=>$brcr->id]);
                if($tableData!=null) {
                    $contractDetailId = $tableData->id;
                    $isContractDetailRow = '1';
                }else{
                    $contractRow[]=[ 'exist'=>'0',
                        'billRegistryContractId'=>'0',
                        'billRegistryContractRowId'=>'0',
                        'billRegistryProductId'=>'0',
                        'contractDetailId'=>'0',
                        'isContractDetailRow'=>'0',
                        'nameProduct'=>'0'];
                    return json_encode($contractRow);
                }
                break;
            case "7":
                $tableData=\Monkey::app()->repoFactory->create('BillRegistryContractRowMailMonk')->findOneBy(['billRegistryContractRowId'=>$brcr->id]);
                if($tableData!=null) {
                    $contractDetailId = $tableData->id;
                    $isContractDetailRow = '0';
                }else{
                    $contractRow[]=[ 'exist'=>'0',
                        'billRegistryContractId'=>'0',
                        'billRegistryContractRowId'=>'0',
                        'billRegistryProductId'=>'0',
                        'contractDetailId'=>'0',
                        'isContractDetailRow'=>'0',
                        'nameProduct'=>'0'];
                    return json_encode($contractRow);
                }
                break;

        }
        $contractRow[]=[ 'exist'=>'1',
                        'billRegistryContractId'=>$brc->id,
                        'billRegistryContractRowId'=>$brcr->id,
                        'billRegistryProductId'=>$brp->id,
                        'contractDetailId'=>$contractDetailId,
                        'isContractDetailRow'=>$isContractDetailRow,
                        'nameProduct'=>$brp->name];
        return json_encode($contractRow);

    }
    public function put()
    {


    }
    public function delete()
    {

    }
}
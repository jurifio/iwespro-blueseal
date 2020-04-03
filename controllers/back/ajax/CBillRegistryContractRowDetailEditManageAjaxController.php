<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\RedPandaOrderLogicException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CBillRegistryContract;
use bamboo\domain\entities\CBillRegistryContractRow;


/**
 * Class CBillRegistryContractRowDetailEditManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 02/04/2020
 * @since 1.0
 */
class CBillRegistryContractRowDetailEditManageAjaxController extends AAjaxController
{

    public function get()
    {
        $array = [];
        $data = $this->app->router->request()->getRequestData();
        $billRegistryGroupProductId = $data['billRegistryGroupProductId'];

        $billRegistryContractRowId = $data['id'];
        switch ($billRegistryGroupProductId) {
            case "1":
                try {
                    $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkSource')->findOneBy(['billRegistryContractRowId' => $billRegistryContractRowId]);
                    $array[] = [
                        'idDetail'=>$rowInsert->id,
                        'billRegistryContractRowId' => $rowInsert->billRegistryContractRowId,
                        'automaticInvoice' => $rowInsert->automaticInvoice,
                        'nameRow' => $rowInsert->nameRow,
                        'descriptionRow' => $rowInsert->descriptionRow,
                        'value' => $rowInsert->value,
                        'billingDay' => $rowInsert->billingDay,
                        'typePaymentId' => $rowInsert->typePaymentId,
                        'periodTypeCharge' => $rowInsert->periodTypeCharge,
                        'sellingFeeCommision' => $rowInsert->sellingFeeCommision,
                        'feeCreditCardCommission' => $rowInsert->feeCreditCardCommission,
                        'dayChargeFeeCreditCardCommission' => $rowInsert->dayChargeFeeCreditCardCommission,
                        'feedCodCommission' => $rowInsert->feeCodCommission,
                        'dayChargeFeeCodCommission' => $rowInsert->dayChargeFeeCodCommission,
                        'feeBankTransferCommission' => $rowInsert->feeBankTransferCommission,
                        'dayChargeFeeBankTransferCommission' => $rowInsert->dayChargeFeeBankTransferCommission,
                        'feePaypalCommission' => $rowInsert->feePaypalCommission,
                        'dayChargeFeePaypalCommission' => $rowInsert->dayChargeFeePaypalCommission,
                        'chargeDeliveryIsActive' => $rowInsert->chargeDeliveryIsActive,
                        'feeCostDeliveryCommission' => $rowInsert->feeCostDeliveryCommission,
                        'periodTypeChargeDelivery' => $rowInsert->periodTypeChargeDelivery,
                        'deliveryTypePaymentId' => $rowInsert->deliveryTypePaymentId,
                        'chargePaymentIsActive' => $rowInsert->chargePaymentIsActive,
                        'feeCostCommissionPayment' => $rowInsert->feeCostCommissionPayment,
                        'periodTypeChargePayment' => $rowInsert->periodTypeChargePayment,
                        'paymentTypePaymentId' => $rowInsert->paymentTypePaymentId,
                        'descfeeCodCommission' => $rowInsert->descfeeCodCommission,
                        'descriptionValue' => $rowInsert->descriptionValue,
                        'descfeeCreditCardCommission' => $rowInsert->descfeeCreditCardCommission,
                        'descfeePaypalCommission' => $rowInsert->descfeePaypalCommission,
                        'descfeeBankTransferCommission' => $rowInsert->descfeeBankTransferCommission,
                        'descfeeCostDeliveryCommission' => $rowInsert->descfeeCostDeliveryCommission,
                        'descfeeCostCommissionPayment' => $rowInsert->descfeeCostCommissionPayment,
                        'billRegistryProductValue' => $rowInsert->billRegistryProductValue,
                        'billRegistryProductFeeCodCommission' => $rowInsert->billRegistryProductFeeCodCommission,
                        'billRegistryProductFeePaypalCommission' => $rowInsert->billRegistryProductFeePaypalCommission,
                        'billRegistryProductFeeBankTransferCommission' => $rowInsert->billRegistryProductFeeBankTransferCommission,
                        'billRegistryProductFeeCreditCardCommission' => $rowInsert->billRegistryProductFeeCreditCardCommission,
                        'billRegistryProductFeeCostDeliveryCommission' => $rowInsert->billRegistryProductFeeCostDeliveryCommission,
                        'billRegistryProductFeeCostCommissionPayment' => $rowInsert->billRegistryProductFeeCostCommissionPayment];


                    return json_encode($array);
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('CBillRegistryContractRowDetailEditManageAjaxController','Error','retrieve Data','Retrieve contactRowDetail',$e);
                    return 'Errore Selezione' . $e;

                }
                break;
            case "2":
                try {
                    $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkAir')->findOneBy(['billRegistryContractRowId' => $billRegistryContractRowId]);
                    $array[] = [
                        'idDetail'=>$rowInsert->id,
                        'billRegistryContractRowId' => $rowInsert->billRegistryContractRowId,
                        'automaticInvoice' => $rowInsert->automaticInvoice,
                        'nameRow' => $rowInsert->nameRow,
                        'descriptionRow' => $rowInsert->descriptionRow,
                        'value' => $rowInsert->value,
                        'billingDay' => $rowInsert->billingDay,
                        'typePaymentId' => $rowInsert->typePaymentId,
                        'periodTypeCharge' => $rowInsert->periodTypeCharge,
                        'sellingFeeCommision' => $rowInsert->sellingFeeCommision,
                        'feeCreditCardCommission' => $rowInsert->feeCreditCardCommission,
                        'dayChargeFeeCreditCardCommission' => $rowInsert->dayChargeFeeCreditCardCommission,
                        'feedCodCommission' => $rowInsert->feeCodCommission,
                        'dayChargeFeeCodCommission' => $rowInsert->dayChargeFeeCodCommission,
                        'feeBankTransferCommission' => $rowInsert->feeBankTransferCommission,
                        'dayChargeFeeBankTransferCommission' => $rowInsert->dayChargeFeeBankTransferCommission,
                        'feePaypalCommission' => $rowInsert->feePaypalCommission,
                        'dayChargeFeePaypalCommission' => $rowInsert->dayChargeFeePaypalCommission,
                        'chargeDeliveryIsActive' => $rowInsert->chargeDeliveryIsActive,
                        'feeCostDeliveryCommission' => $rowInsert->feeCostDeliveryCommission,
                        'periodTypeChargeDelivery' => $rowInsert->periodTypeChargeDelivery,
                        'deliveryTypePaymentId' => $rowInsert->deliveryTypePaymentId,
                        'chargePaymentIsActive' => $rowInsert->chargePaymentIsActive,
                        'feeCostCommissionPayment' => $rowInsert->feeCostCommissionPayment,
                        'periodTypeChargePayment' => $rowInsert->periodTypeChargePayment,
                        'paymentTypePaymentId' => $rowInsert->paymentTypePaymentId,
                        'descfeeCodCommission' => $rowInsert->descfeeCodCommission,
                        'descriptionValue' => $rowInsert->descriptionValue,
                        'descfeeCreditCardCommission' => $rowInsert->descfeeCreditCardCommission,
                        'descfeePaypalCommission' => $rowInsert->descfeePaypalCommission,
                        'descfeeBankTransferCommission' => $rowInsert->descfeeBankTransferCommission,
                        'descfeeCostDeliveryCommission' => $rowInsert->descfeeCostDeliveryCommission,
                        'descfeeCostCommissionPayment' => $rowInsert->descfeeCostCommissionPayment,
                        'billRegistryProductValue' => $rowInsert->billRegistryProductValue,
                        'billRegistryProductFeeCodCommission' => $rowInsert->billRegistryProductFeeCodCommission,
                        'billRegistryProductFeePaypalCommission' => $rowInsert->billRegistryProductFeePaypalCommission,
                        'billRegistryProductFeeBankTransferCommission' => $rowInsert->billRegistryProductFeeBankTransferCommission,
                        'billRegistryProductFeeCreditCardCommission' => $rowInsert->billRegistryProductFeeCreditCardCommission,
                        'billRegistryProductFeeCostDeliveryCommission' => $rowInsert->billRegistryProductFeeCostDeliveryCommission,
                        'billRegistryProductFeeCostCommissionPayment' => $rowInsert->billRegistryProductFeeCostCommissionPayment];


                    return json_encode($array);
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('CBillRegistryContractRowDetailEditManageAjaxController','Error','retrieve Data','Retrieve contactRowDetail',$e);
                    return 'Errore Selezione' . $e;

                }
                break;
            case "3":
                try {
                    $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkEntrySocial')->findOneBy(['billRegistryContractRowId' => $billRegistryContractRowId]);
                    $array[] = [
                        'idDetail'=>$rowInsert->id,
                        'billRegistryContractRowId' => $rowInsert->billRegistryContractRowId,
                        'descriptionInvoice' => $rowInsert->descriptionInvoice,
                        'nameRow' => $rowInsert->nameRow,
                        'descriptionRow' => $rowInsert->descriptionRow,
                        'startUpCostCampaign' => $rowInsert->startUpCostCampaign,
                        'automaticInvoice' => $rowInsert->automaticInvoice,
                        'billingDay' => $rowInsert->billingDay,
                        'typePaymentId' => $rowInsert->typePaymentId,
                        'feeAgencyCommision' => $rowInsert->feeAgencyCommision,
                        'prepaidPaymentIsActive' => $rowInsert->prepaidPaymentIsActive,
                        'prepaidCost' => $rowInsert->prepaidCost,
                        'billRegistryProductStartUpCostCampaign' => $rowInsert->billRegistryProductStartUpCostCampaign,
                        'billRegistryProductFeeAgencyCommision' => $rowInsert->billRegistryProductFeeAgencyCommision];


                    return json_encode($array);
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('CBillRegistryContractRowDetailEditManageAjaxController','Error','retrieve Data','Retrieve contactRowDetail',$e);
                    return 'Errore Selezione' . $e;

                }
                break;
            case "4":
                try {
                    $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkEntryTraffic')->findOneBy(['billRegistryContractRowId' => $billRegistryContractRowId]);
                    $array[] = [
                        'idDetail'=>$rowInsert->id,
                        'billRegistryContractRowId' => $rowInsert->billRegistryContractRowId,
                        'descriptionInvoice' => $rowInsert->descriptionInvoice,
                        'nameRow' => $rowInsert->nameRow,
                        'descriptionRow' => $rowInsert->descriptionRow,
                        'startUpCostCampaign' => $rowInsert->startUpCostCampaign,
                        'automaticInvoice' => $rowInsert->automaticInvoice,
                        'billingDay' => $rowInsert->billingDay,
                        'typePaymentId' => $rowInsert->typePaymentId,
                        'feeAgencyCommision' => $rowInsert->feeAgencyCommision,
                        'prepaidPaymentIsActive' => $rowInsert->prepaidPaymentIsActive,
                        'prepaidCost' => $rowInsert->prepaidCost,
                        'billRegistryProductStartUpCostCampaign' => $rowInsert->billRegistryProductStartUpCostCampaign,
                        'billRegistryProductFeeAgencyCommision' => $rowInsert->billRegistryProductFeeAgencyCommision];


                    return json_encode($array);
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('CBillRegistryContractRowDetailEditManageAjaxController','Error','retrieve Data','Retrieve contactRowDetail',$e);
                    return 'Errore Selezione' . $e;

                }
                break;

            case "5":
                try {
                    $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowSocialMonk')->findOneBy(['billRegistryContractRowId' => $billRegistryContractRowId]);
                    $array[] = [
                        'idDetail'=>$rowInsert->id,
                        'billRegistryContractRowId' => $rowInsert->billRegistryContractRowId,
                        'automaticInvoice' => $rowInsert->automaticInvoice,
                        'nameRow' => $rowInsert->nameRow,
                        'descriptionRow' => $rowInsert->descriptionRow,
                        'value' => $rowInsert->value,
                        'billingDay' => $rowInsert->billingDay,
                        'typePaymentId' => $rowInsert->typePaymentId,
                        'periodTypeCharge' => $rowInsert->periodTypeCharge,
                        'sellingFeeCommision' => $rowInsert->sellingFeeCommision,
                        'feeCreditCardCommission' => $rowInsert->feeCreditCardCommission,
                        'dayChargeFeeCreditCardCommission' => $rowInsert->dayChargeFeeCreditCardCommission,
                        'feedCodCommission' => $rowInsert->feeCodCommission,
                        'dayChargeFeeCodCommission' => $rowInsert->dayChargeFeeCodCommission,
                        'feeBankTransferCommission' => $rowInsert->feeBankTransferCommission,
                        'dayChargeFeeBankTransferCommission' => $rowInsert->dayChargeFeeBankTransferCommission,
                        'feePaypalCommission' => $rowInsert->feePaypalCommission,
                        'dayChargeFeePaypalCommission' => $rowInsert->dayChargeFeePaypalCommission,
                        'chargeDeliveryIsActive' => $rowInsert->chargeDeliveryIsActive,
                        'feeCostDeliveryCommission' => $rowInsert->feeCostDeliveryCommission,
                        'periodTypeChargeDelivery' => $rowInsert->periodTypeChargeDelivery,
                        'deliveryTypePaymentId' => $rowInsert->deliveryTypePaymentId,
                        'chargePaymentIsActive' => $rowInsert->chargePaymentIsActive,
                        'feeCostCommissionPayment' => $rowInsert->feeCostCommissionPayment,
                        'periodTypeChargePayment' => $rowInsert->periodTypeChargePayment,
                        'paymentTypePaymentId' => $rowInsert->paymentTypePaymentId,
                        'descfeeCodCommission' => $rowInsert->descfeeCodCommission,
                        'descriptionValue' => $rowInsert->descriptionValue,
                        'descfeeCreditCardCommission' => $rowInsert->descfeeCreditCardCommission,
                        'descfeePaypalCommission' => $rowInsert->descfeePaypalCommission,
                        'descfeeBankTransferCommission' => $rowInsert->descfeeBankTransferCommission,
                        'descfeeCostDeliveryCommission' => $rowInsert->descfeeCostDeliveryCommission,
                        'descfeeCostCommissionPayment' => $rowInsert->descfeeCostCommissionPayment,
                        'billRegistryProductValue' => $rowInsert->billRegistryProductValue,
                        'billRegistryProductFeeCodCommission' => $rowInsert->billRegistryProductFeeCodCommission,
                        'billRegistryProductFeePaypalCommission' => $rowInsert->billRegistryProductFeePaypalCommission,
                        'billRegistryProductFeeBankTransferCommission' => $rowInsert->billRegistryProductFeeBankTransferCommission,
                        'billRegistryProductFeeCreditCardCommission' => $rowInsert->billRegistryProductFeeCreditCardCommission,
                        'billRegistryProductFeeCostDeliveryCommission' => $rowInsert->billRegistryProductFeeCostDeliveryCommission,
                        'billRegistryProductFeeCostCommissionPayment' => $rowInsert->billRegistryProductFeeCostCommissionPayment];


                    return json_encode($array);
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('CBillRegistryContractRowDetailEditManageAjaxController','Error','retrieve Data','Retrieve contactRowDetail',$e);
                    return 'Errore Selezione' . $e;

                }
                break;

            case "6":
                try {
                    $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowFriends')->findOneBy(['billRegistryContractRowId' => $billRegistryContractRowId]);
                    $array[]=[
                        'idDetail'=>$rowInsert->id,
                        'billRegistryContractRowId'=>$rowInsert->billRegistryContractRowId,
                    'typeContractId'=>$rowInsert->typeContractId,
                    'nameRow'=>$rowInsert->nameRow,
                    'descriptionRow'=>$rowInsert->descriptionRow,
                    'valueMarkUpFullPrice'=>$rowInsert->valueMarkUpFullPrice,
                    'valueMarkUpSalePrice'=>$rowInsert->valueMarkUpSalePrice,
                    'billingDay'=>$rowInsert->billingDay,
                    'billRegistryProductValue'=>$rowInsert->billRegistryProductValue];
                    return json_encode($array);
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('CBillRegistryContractRowDetailEditManageAjaxController','Error','retrieve Data','Retrieve contactRowDetail',$e);
                    return 'Errore Selezione' . $e;

                }
                break;

            case "7":
                try {
                    $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowMailMonk')->findOneBy(['billRegistryContractRowId' => $billRegistryContractRowId]);
                    $array[] = [
                        'idDetail'=>$rowInsert->id,
                        'billRegistryContractRowId' => $rowInsert->billRegistryContractRowId,
                        'automaticInvoice' => $rowInsert->automaticInvoice,
                        'emailAccount' => $rowInsert->emailAccount,
                        'emailAccountSendQty' => $rowInsert->emailAccountSendQty,
                        'emailAccountCampaignQty' => $rowInsert->emailAccountCampaignQty,
                        'nameRow' => $rowInsert->nameRow,
                        'descriptionRow' => $rowInsert->descriptionRow,
                        'value' => $rowInsert->value,
                        'billingDay' => $rowInsert->billingDay,
                        'typePaymentId' => $rowInsert->typePaymentId,
                        'periodTypeCharge' => $rowInsert->periodTypeCharge,
                        'sellingFeeCommision' => $rowInsert->sellingFeeCommision,
                        'feeCreditCardCommission' => $rowInsert->feeCreditCardCommission,
                        'dayChargeFeeCreditCardCommission' => $rowInsert->dayChargeFeeCreditCardCommission,
                        'feedCodCommission' => $rowInsert->feeCodCommission,
                        'dayChargeFeeCodCommission' => $rowInsert->dayChargeFeeCodCommission,
                        'feeBankTransferCommission' => $rowInsert->feeBankTransferCommission,
                        'dayChargeFeeBankTransferCommission' => $rowInsert->dayChargeFeeBankTransferCommission,
                        'feePaypalCommission' => $rowInsert->feePaypalCommission,
                        'dayChargeFeePaypalCommission' => $rowInsert->dayChargeFeePaypalCommission,
                        'chargeDeliveryIsActive' => $rowInsert->chargeDeliveryIsActive,
                        'feeCostDeliveryCommission' => $rowInsert->feeCostDeliveryCommission,
                        'periodTypeChargeDelivery' => $rowInsert->periodTypeChargeDelivery,
                        'deliveryTypePaymentId' => $rowInsert->deliveryTypePaymentId,
                        'chargePaymentIsActive' => $rowInsert->chargePaymentIsActive,
                        'feeCostCommissionPayment' => $rowInsert->feeCostCommissionPayment,
                        'periodTypeChargePayment' => $rowInsert->periodTypeChargePayment,
                        'paymentTypePaymentId' => $rowInsert->paymentTypePaymentId,
                        'descfeeCodCommission' => $rowInsert->descfeeCodCommission,
                        'descriptionValue' => $rowInsert->descriptionValue,
                        'descfeeCreditCardCommission' => $rowInsert->descfeeCreditCardCommission,
                        'descfeePaypalCommission' => $rowInsert->descfeePaypalCommission,
                        'descfeeBankTransferCommission' => $rowInsert->descfeeBankTransferCommission,
                        'descfeeCostDeliveryCommission' => $rowInsert->descfeeCostDeliveryCommission,
                        'descfeeCostCommissionPayment' => $rowInsert->descfeeCostCommissionPayment,
                        'billRegistryProductValue' => $rowInsert->billRegistryProductValue,
                        'billRegistryProductFeeCodCommission' => $rowInsert->billRegistryProductFeeCodCommission,
                        'billRegistryProductFeePaypalCommission' => $rowInsert->billRegistryProductFeePaypalCommission,
                        'billRegistryProductFeeBankTransferCommission' => $rowInsert->billRegistryProductFeeBankTransferCommission,
                        'billRegistryProductFeeCreditCardCommission' => $rowInsert->billRegistryProductFeeCreditCardCommission,
                        'billRegistryProductFeeCostDeliveryCommission' => $rowInsert->billRegistryProductFeeCostDeliveryCommission,
                        'billRegistryProductFeeCostCommissionPayment' => $rowInsert->billRegistryProductFeeCostCommissionPayment];


                    return json_encode($array);
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('CBillRegistryContractRowDetailEditManageAjaxController','Error','retrieve Data','Retrieve contactRowDetail',$e);
                    return 'Errore Selezione' . $e;

                }
                break;


        }


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
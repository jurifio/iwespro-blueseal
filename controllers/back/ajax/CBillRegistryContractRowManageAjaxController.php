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
        $billRegistryGroupProductId = $data['billRegistryGroupProductId'];
        $id = $data['id'];
        $billRegistryContractRowId = $data['billRegistryContractRowId'];
        switch ($billRegistryGroupProductId) {
            case "1":
                try {
                    $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkSource')->getEmptyEntity();
                    $rowInsert->billRegistryContractRowId = $data['billRegistryContractRowId'];
                    $rowInsert->automaticInvoice = $data['automaticInvoice'];
                    $rowInsert->nameRow=$data['nameRow'];
                    $rowInsert->descriptionRow=$data['descriptionRow'];
                    $rowInsert->value = $data['value'];
                    $rowInsert->billingDay = $data['billingDay'];
                    $rowInsert->typePaymentId = $data['typePaymentId'];
                    $rowInsert->periodTypeCharge = $data['periodTypeCharge'];
                    $rowInsert->sellingFeeCommision = $data['sellingFeeCommision'];
                    $rowInsert->feeCreditCardCommission = $data['feeCreditCardCommission'];
                    $rowInsert->dayChargeFeeCreditCardCommission=$data['dayChargeFeeCreditCardCommission'];
                    $rowInsert->feeCodCommission = $data['feeCodCommission'];
                    $rowInsert->dayChargeFeeCodCommission = $data['dayChargeFeeCodCommission'];
                    $rowInsert->feeBankTransferCommission = $data['feeBankTransferCommission'];
                    $rowInsert->dayChargeFeeBankTransferCommission = $data['dayChargeFeeBankTransferCommission'];
                    $rowInsert->feePaypalCommission = $data['feePaypalCommission'];
                    $rowInsert->dayChargeFeePaypalCommission = $data['dayChargeFeePaypalCommission'];
                    $rowInsert->chargeDeliveryIsActive = $data['chargeDeliveryIsActive'];
                    $rowInsert->feeCostDeliveryCommission = $data['feeCostDeliveryCommission'];
                    $rowInsert->periodTypeChargeDelivery = $data['periodTypeChargeDelivery'];
                    $rowInsert->deliveryTypePaymentId = $data['deliveryTypePaymentId'];
                    $rowInsert->chargePaymentIsActive = $data['chargePaymentIsActive'];
                    $rowInsert->feeCostCommissionPayment = $data['feeCostCommissionPayment'];
                    $rowInsert->periodTypeChargePayment = $data['periodTypeChargePayment'];
                    $rowInsert->paymentTypePaymentId = $data['paymentTypePaymentId'];
                    $rowInsert->descfeeCodCommission = $data['descfeeCodCommission'];
                    $rowInsert->descriptionValue=$data['descriptionValue'];
                    $rowInsert->descfeeCreditCardCommission = $data['descfeeCreditCardCommission'];
                    $rowInsert->dayChargeFeeCreditCardCommission = $data['dayChargeFeeCreditCardCommission'];
                    $rowInsert->descfeeCreditCardCommission = $data['descfeeCreditCardCommission'];
                    $rowInsert->descfeeBankTransferCommission = $data['descfeeBankTransferCommission'];
                    $rowInsert->descfeeCostDeliveryCommission=$data['descfeeCostDeliveryCommission'];
                    $rowInsert->descfeeCostCommissionPayment=$data['descfeeCostCommissionPayment'];
                    $rowInsert->billRegistryProductValue=$data['billRegistryProductValue'];
                    $rowInsert->billRegistryProductFeeCodCommission=$data['productfeeCodCommission'];
                    $rowInsert->billRegistryProductFeePaypalCommission=$data['productfeePaypalCommission'];
                    $rowInsert->billRegistryProductFeeBankTransferCommission=$data['productfeeBankTransferCommission'];
                    $rowInsert->billRegistryProductFeeCreditCardCommission=$data['productfeeCreditCardCommision'];
                    $rowInsert->billRegistryProductFeeCostDeliveryCommission=$data['productfeeCostDeliveryCommission'];
                    $rowInsert->billRegistryProductFeeCostCommissionPayment=$data['productfeeCostCommissionPayment'];
                    $rowInsert->insert();

                    $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContractRowMonkSource ',[])->fetchAll();
                    foreach ($res as $result) {
                        $lastId = $result['id'];
                    }
                    \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Report','Insert BillRegistryContractRowMonkSource','Insert ContractRow' . $lastId,'');
                    return $lastId;
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Error','InsertBillRegistryContractRowMonkSource','Insert contactRow',$e);
                    return 'Errore Inserimento' . $e;

                }
                break;
            case "2":
                try {
                    $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkAir')->getEmptyEntity();
                    $rowInsert->billRegistryContractRowId = $data['billRegistryContractRowId'];
                    $rowInsert->automaticInvoice = $data['automaticInvoice'];
                    $rowInsert->nameRow=$data['nameRow'];
                    $rowInsert->descriptionRow=$data['descriptionRow'];
                    $rowInsert->value = $data['value'];
                    $rowInsert->billingDay = $data['billingDay'];
                    $rowInsert->typePaymentId = $data['typePaymentId'];
                    $rowInsert->periodTypeCharge = $data['periodTypeCharge'];
                    $rowInsert->sellingFeeCommision = $data['sellingFeeCommision'];
                    $rowInsert->feeCreditCardCommission = $data['feeCreditCardCommission'];
                    $rowInsert->dayChargeFeeCreditCardCommission=$data['dayChargeFeeCreditCardCommission'];
                    $rowInsert->feeCodCommission = $data['feeCodCommission'];
                    $rowInsert->dayChargeFeeCodCommission = $data['dayChargeFeeCodCommission'];
                    $rowInsert->feeBankTransferCommission = $data['feeBankTransferCommission'];
                    $rowInsert->dayChargeFeeBankTransferCommission = $data['dayChargeFeeBankTransferCommission'];
                    $rowInsert->feePaypalCommission = $data['feePaypalCommission'];
                    $rowInsert->dayChargeFeePaypalCommission = $data['dayChargeFeePaypalCommission'];
                    $rowInsert->chargeDeliveryIsActive = $data['chargeDeliveryIsActive'];
                    $rowInsert->feeCostDeliveryCommission = $data['feeCostDeliveryCommission'];
                    $rowInsert->periodTypeChargeDelivery = $data['periodTypeChargeDelivery'];
                    $rowInsert->deliveryTypePaymentId = $data['deliveryTypePaymentId'];
                    $rowInsert->chargePaymentIsActive = $data['chargePaymentIsActive'];
                    $rowInsert->feeCostCommissionPayment = $data['feeCostCommissionPayment'];
                    $rowInsert->periodTypeChargePayment = $data['periodTypeChargePayment'];
                    $rowInsert->paymentTypePaymentId = $data['paymentTypePaymentId'];
                    $rowInsert->descfeeCodCommission = $data['descfeeCodCommission'];
                    $rowInsert->descriptionValue=$data['descriptionValue'];
                    $rowInsert->descfeeCreditCardCommission = $data['descfeeCreditCardCommission'];
                    $rowInsert->dayChargeFeeCreditCardCommission = $data['dayChargeFeeCreditCardCommission'];
                    $rowInsert->descfeeCreditCardCommission = $data['descfeeCreditCardCommission'];
                    $rowInsert->descfeeBankTransferCommission = $data['descfeeBankTransferCommission'];
                    $rowInsert->descfeeCostDeliveryCommission=$data['descfeeCostDeliveryCommission'];
                    $rowInsert->descfeeCostCommissionPayment=$data['descfeeCostCommissionPayment'];
                    $rowInsert->descfeeCostDeliveryCommission=$data['descfeeCostDeliveryCommission'];
                    $rowInsert->descfeeCostCommissionPayment=$data['descfeeCostCommissionPayment'];
                    $rowInsert->billRegistryProductValue=$data['billRegistryProductValue'];
                    $rowInsert->billRegistryProductFeeCodCommission=$data['productfeeCodCommission'];
                    $rowInsert->billRegistryProductFeePaypalCommission=$data['productfeePaypalCommission'];
                    $rowInsert->billRegistryProductFeeBankTransferCommission=$data['productfeeBankTransferCommission'];
                    $rowInsert->billRegistryProductFeeCreditCardCommission=$data['productfeeCreditCardCommision'];
                    $rowInsert->billRegistryProductFeeCostDeliveryCommission=$data['productfeeCostDeliveryCommission'];
                    $rowInsert->billRegistryProductFeeCostCommissionPayment=$data['productfeeCostCommissionPayment'];
                    $rowInsert->insert();

                    $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContractRowMonkAir ',[])->fetchAll();
                    foreach ($res as $result) {
                        $lastId = $result['id'];
                    }
                    \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Report','Insert BillRegistryContractRowMonkAir','Insert ContractRow' . $lastId,'');
                    return $lastId;
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Error','Insert BillRegistryContractRowMonkAir','Insert contactRow',$e);
                    return 'Errore Inserimento' . $e;

                }
                break;
            case "3":
                try {
                    $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkEntrySocial')->getEmptyEntity();
                    $rowInsert->billRegistryContractRowId = $data['billRegistryContractRowId'];
                    $rowInsert->descriptionInvoice = $data['descriptionInvoice'];
                    $rowInsert->nameRow=$data['nameRow'];
                    $rowInsert->descriptionRow=$data['descriptionRow'];
                    $rowInsert->startUpCostCampaign = $data['startUpCostCampaign'];
                    $rowInsert->automaticInvoice = $data['automaticInvoice'];
                    $rowInsert->billingDay = $data['billingDay'];
                    $rowInsert->typePaymentId = $data['typePaymentId'];
                    $rowInsert->feeAgencyCommision = $data['feeAgencyCommision'];
                    $rowInsert->prepaidPaymentIsActive = $data['prepaidPaymentIsActive'];
                    $rowInsert->prepaidCost = $data['prepaidCost'];
                    $rowInsert->billRegistryProductStartUpCostCampaign=$data['productStartUpCostCampaign'];
                    $rowInsert->billRegistryProductFeeAgencyCommision=$data['productFeeAgencyCommision'];
                    $rowInsert->insert();

                    $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContractRowMonkEntrySocial ',[])->fetchAll();
                    foreach ($res as $result) {
                        $lastId = $result['id'];
                    }
                    \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Report','Insert BillRegistryContractRowMonkEntrySocial','Insert ContractRow' . $lastId,'');
                    return $lastId;
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Error','Insert BillRegistryContractRowMonkEntrySocial','Insert contactRow',$e);
                    return 'Errore Inserimento' . $e;

                }
                break;
            case "4":
                try {
                    $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkEntryTraffic')->getEmptyEntity();
                    $rowInsert->billRegistryContractRowId = $data['billRegistryContractRowId'];
                    $rowInsert->descriptionInvoice = $data['descriptionInvoice'];
                    $rowInsert->nameRow=$data['nameRow'];
                    $rowInsert->descriptionRow=$data['descriptionRow'];
                    $rowInsert->startUpCostCampaign = $data['startUpCostCampaign'];
                    $rowInsert->automaticInvoice = $data['automaticInvoice'];
                    $rowInsert->billingDay = $data['billingDay'];
                    $rowInsert->typePaymentId = $data['typePaymentId'];
                    $rowInsert->feeAgencyCommision = $data['feeAgencyCommision'];
                    $rowInsert->prepaidPaymentIsActive = $data['prepaidPaymentIsActive'];
                    $rowInsert->prepaidCost = $data['prepaidCost'];
                    $rowInsert->billRegistryProductStartUpCostCampaign=$data['productStartUpCostCampaign'];
                    $rowInsert->billRegistryProductFeeAgencyCommision=$data['productFeeAgencyCommision'];
                    $rowInsert->insert();

                    $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContractRowMonkEntryTraffic ',[])->fetchAll();
                    foreach ($res as $result) {
                        $lastId = $result['id'];
                    }
                    \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Report','Insert BillRegistryContractRowMonkEntryTraffic','Insert ContractRow' . $lastId,'');
                    return $lastId;
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Error','Insert BillRegistryContractRowMonkEntryTraffic','Insert contactRow',$e);
                    return 'Errore Inserimento' . $e;

                }
                break;

            case "5":
                try {
                    $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowSocialMonk')->getEmptyEntity();
                    $rowInsert->billRegistryContractRowId = $data['billRegistryContractRowId'];
                    $rowInsert->automaticInvoice = $data['automaticInvoice'];
                    $rowInsert->nameRow=$data['nameRow'];
                    $rowInsert->descriptionRow=$data['descriptionRow'];
                    $rowInsert->value = $data['value'];
                    $rowInsert->billingDay = $data['billingDay'];
                    $rowInsert->typePaymentId = $data['typePaymentId'];
                    $rowInsert->periodTypeCharge = $data['periodTypeCharge'];
                    $rowInsert->sellingFeeCommision = $data['sellingFeeCommision'];
                    $rowInsert->feeCreditCardCommission = $data['feeCreditCardCommission'];
                    $rowInsert->dayChargeFeeCreditCardCommission=$data['dayChargeFeeCreditCardCommission'];
                    $rowInsert->feeCodCommission = $data['feeCodCommission'];
                    $rowInsert->dayChargeFeeCodCommission = $data['dayChargeFeeCodCommission'];
                    $rowInsert->feeBankTransferCommission = $data['feeBankTransferCommission'];
                    $rowInsert->dayChargeFeeBankTransferCommission = $data['dayChargeFeeBankTransferCommission'];
                    $rowInsert->feePaypalCommission = $data['feePaypalCommission'];
                    $rowInsert->dayChargeFeePaypalCommission = $data['dayChargeFeePaypalCommission'];
                    $rowInsert->chargeDeliveryIsActive = $data['chargeDeliveryIsActive'];
                    $rowInsert->feeCostDeliveryCommission = $data['feeCostDeliveryCommission'];
                    $rowInsert->periodTypeChargeDelivery = $data['periodTypeChargeDelivery'];
                    $rowInsert->deliveryTypePaymentId = $data['deliveryTypePaymentId'];
                    $rowInsert->chargePaymentIsActive = $data['chargePaymentIsActive'];
                    $rowInsert->feeCostCommissionPayment = $data['feeCostCommissionPayment'];
                    $rowInsert->periodTypeChargePayment = $data['periodTypeChargePayment'];
                    $rowInsert->paymentTypePaymentId = $data['paymentTypePaymentId'];
                    $rowInsert->descfeeCodCommission = $data['descfeeCodCommission'];
                    $rowInsert->descriptionValue=$data['descriptionValue'];
                    $rowInsert->descfeeCreditCardCommission = $data['descfeeCreditCardCommission'];
                    $rowInsert->dayChargeFeeCreditCardCommission = $data['dayChargeFeeCreditCardCommission'];
                    $rowInsert->descfeeCreditCardCommission = $data['descfeeCreditCardCommission'];
                    $rowInsert->descfeeCostDeliveryCommission=$data['descfeeCostDeliveryCommission'];
                    $rowInsert->descfeeCostCommissionPayment=$data['descfeeCostCommissionPayment'];
                    $rowInsert->descfeeBankTransferCommission = $data['descfeeBankTransferCommission'];
                    $rowInsert->billRegistryProductValue=$data['billRegistryProductValue'];
                    $rowInsert->billRegistryProductFeeCodCommission=$data['productfeeCodCommission'];
                    $rowInsert->billRegistryProductFeePaypalCommission=$data['productfeePaypalCommission'];
                    $rowInsert->billRegistryProductFeeBankTransferCommission=$data['productfeeBankTransferCommission'];
                    $rowInsert->billRegistryProductFeeCreditCardCommission=$data['productfeeCreditCardCommision'];
                    $rowInsert->billRegistryProductFeeCostDeliveryCommission=$data['productfeeCostDeliveryCommission'];
                    $rowInsert->billRegistryProductFeeCostCommissionPayment=$data['productfeeCostCommissionPayment'];
                    $rowInsert->insert();

                    $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContractRowSocialMonk ',[])->fetchAll();
                    foreach ($res as $result) {
                        $lastId = $result['id'];
                    }
                    \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Report','Insert BillRegistryContractRowSocialMonk','Insert ContractRow' . $lastId,'');
                    return $lastId;
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Error','Insert BillRegistryContractRowSocialMonk','Insert contactRow',$e);
                    return 'Errore Inserimento' . $e;

                }
                break;

            case "6":
                try {
                    $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowFriends')->getEmptyEntity();
                    $rowInsert->billRegistryContractRowId = $data['billRegistryContractRowId'];
                    $rowInsert->typeContractId = $data['typeContractId'];
                    $rowInsert->nameRow=$data['nameRow'];
                    $rowInsert->descriptionRow=$data['descriptionRow'];
                    $rowInsert->valueMarkUpFullPrice = $data['valueMarkUpFullPrice'];
                    $rowInsert->valueMarkUpSalePrice = $data['valueMarkUpSalePrice'];
                    $rowInsert->billingDay=$data['billingDay'];
                    $rowInsert->billRegistryProductValue=$data['billRegistryProductValue'];
                    $rowInsert->insert();

                    $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContractRowFriends ',[])->fetchAll();
                    foreach ($res as $result) {
                        $lastId = $result['id'];
                    }
                    \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Report','Insert BillRegistryContractRowFriends','Insert ContractRow' . $lastId,'');
                    return $lastId;
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Error','Insert BillRegistryContractRowFriends','Insert contactRow',$e);
                    return 'Errore Inserimento' . $e;

                }
                break;

            case "7":
                try {
                    $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowMailMonk')->getEmptyEntity();
                    $rowInsert->billRegistryContractRowId = $data['billRegistryContractRowId'];
                    $rowInsert->automaticInvoice = $data['automaticInvoice'];
                    $rowInsert->nameRow=$data['nameRow'];
                    $rowInsert->descriptionRow=$data['descriptionRow'];
                    $rowInsert->emailAccount = $data['emailAccount'];
                    $rowInsert->emailAccountSendQty = $data['emailAccountSendQty'];
                    $rowInsert->emailAccountCampaignQty = $data['emailAccountCampaignQty'];
                    $rowInsert->value = $data['value'];
                    $rowInsert->billingDay = $data['billingDay'];
                    $rowInsert->typePaymentId = $data['typePaymentId'];
                    $rowInsert->periodTypeCharge = $data['periodTypeCharge'];
                    $rowInsert->sellingFeeCommision = $data['sellingFeeCommision'];
                    $rowInsert->descfeeCreditCardCommission = $data['descfeeCreditCardCommission'];
                    $rowInsert->descfeeCodCommission = $data['descfeeCodCommission'];
                    $rowInsert->descfeeCreditCardCommission = $data['descfeeCreditCardCommission'];
                    $rowInsert->descfeeBankTransferCommission = $data['descfeeBankTransferCommission'];
                    $rowInsert->descfeeCostDeliveryCommission=$data['descfeeCostDeliveryCommission'];
                    $rowInsert->descfeeCostCommissionPayment=$data['descfeeCostCommissionPayment'];
                    $rowInsert->feeCreditCardCommission = $data['feeCreditCardCommission'];
                    $rowInsert->dayChargeFeeCreditCardCommission = $data['dayChargeFeeCreditCardCommission'];
                    $rowInsert->feeCodCommission = $data['feeCodCommission'];
                    $rowInsert->dayChargeFeeCodCommission = $data['dayChargeFeeCodCommission'];
                    $rowInsert->feeBankTransferCommission = $data['feeBankTransferCommission'];
                    $rowInsert->dayChargeFeeBankTransferCommission = $data['dayChargeFeeBankTransferCommission'];
                    $rowInsert->feePaypalCommission = $data['feePaypalCommission'];
                    $rowInsert->dayChargeFeePaypalCommission = $data['dayChargeFeePaypalCommission'];
                    $rowInsert->chargeDeliveryIsActive = $data['chargeDeliveryIsActive'];
                    $rowInsert->feeCostDeliveryCommission = $data['feeCostDeliveryCommission'];
                    $rowInsert->periodTypeChargeDelivery = $data['periodTypeChargeDelivery'];
                    $rowInsert->deliveryTypePaymentId = $data['deliveryTypePaymentId'];
                    $rowInsert->chargePaymentIsActive = $data['chargePaymentIsActive'];
                    $rowInsert->feeCostCommissionPayment = $data['feeCostCommissionPayment'];
                    $rowInsert->periodTypeChargePayment = $data['periodTypeChargePayment'];
                    $rowInsert->paymentTypePaymentId = $data['paymentTypePaymentId'];
                    $rowInsert->insert();

                    $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContractRowMailMonk ',[])->fetchAll();
                    foreach ($res as $result) {
                        $lastId = $result['id'];
                    }
                    \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Report','Insert BillRegistryContractRowMailMonk','Insert ContractRow' . $lastId,'');
                    return $lastId;
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Error','Insert BillRegistryContractRowMailMonk','Insert contactRow',$e);
                    return 'Errore Inserimento' . $e;

                }
                break;


        }


    }

    public function get()
    {
        $data = $this->app->router->request()->getRequestData();
        $id = $data['id'];
        $contractRow = [];
        /* @var CBillRegistryContractRow $brc */
        $brc = \Monkey::app()->repoFactory->create('BillRegistryContract')->findOneBy(['id' => $id]);

        $brcrs = \Monkey::app()->repoFactory->create('BillRegistryContractRow')->findBy(['billRegistryContractId' => $brc->id]);
        foreach ($brcrs as $brcr) {
            $brp = \Monkey::app()->repoFactory->create('BillRegistryGroupProduct')->findOneBy(['id' => $brcr->billRegistryGroupProductId]);
            switch ($brcr->billRegistryGroupProductId) {
                case "1":
                    $tableDatas = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkSource')->findBy(['billRegistryContractRowId' => $brcr->id]);
                    if ($tableDatas != null) {
                        foreach ($tableDatas as $tableData) {
                            $contractDetailId = $tableData->id;
                            $isContractDetailRow = '0';
                            $contractRow[] = ['exist' => '1',
                                'billRegistryContractId' => $brc->id,
                                'billRegistryContractRowId' => $brcr->id,
                                'billRegistryGroupProductId' => $brp->id,
                                'nameRow'=>$tableData->nameRow,
                                'descriptionRow'=>$tableData->descriptionRow,
                                'contractDetailId' => $contractDetailId,
                                'isContractDetailRow' => $isContractDetailRow,
                                'nameProduct' => $brp->name,
                                'nameContract'=>$brc->nameContract,
                                'contractCodeInt'=>$brc->contractCodeInt];
                        }
                    }

            break;
        case
            "2":
                    $tableDatas = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkAir')->findBy(['billRegistryContractRowId' => $brcr->id]);
                   if ($tableDatas != null) {
                       foreach ($tableDatas as $tableData) {
                           $contractDetailId = $tableData->id;
                           $isContractDetailRow = '0';
                           $contractRow[] = ['exist' => '1',
                               'billRegistryContractId' => $brc->id,
                               'billRegistryContractRowId' => $brcr->id,
                               'billRegistryGroupProductId' => $brp->id,
                               'nameRow'=>$tableData->nameRow,
                               'descriptionRow'=>$tableData->descriptionRow,
                               'contractDetailId' => $contractDetailId,
                               'isContractDetailRow' => $isContractDetailRow,
                               'nameProduct' => $brp->name,
                               'nameContract'=>$brc->nameContract,
                               'contractCodeInt'=>$brc->contractCodeInt];
                       }
                   }
                    break;
                case "3":
                    $tableDatas = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkEntrySocial')->findBy(['billRegistryContractRowId' => $brcr->id]);
                    if ($tableDatas != null) {
                        foreach ($tableDatas as $tableData) {
                            $contractDetailId = $tableData->id;
                            $isContractDetailRow = '0';
                            $contractRow[] = ['exist' => '1',
                                'billRegistryContractId' => $brc->id,
                                'billRegistryContractRowId' => $brcr->id,
                                'billRegistryGroupProductId' => $brp->id,
                                'nameRow'=>$tableData->nameRow,
                                'descriptionRow'=>$tableData->descriptionRow,
                                'contractDetailId' => $contractDetailId,
                                'isContractDetailRow' => $isContractDetailRow,
                                'nameProduct' => $brp->name,
                                'nameContract'=>$brc->nameContract,
                                'contractCodeInt'=>$brc->contractCodeInt];
                        }
                    }
                    break;
                case "4":
                    $tableDatas = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkEntryTraffic')->findBy(['billRegistryContractRowId' => $brcr->id]);
                    if ($tableDatas != null) {
                        foreach ($tableDatas as $tableData) {
                            $contractDetailId = $tableData->id;
                            $isContractDetailRow = '0';
                            $contractRow[] = ['exist' => '1',
                                'billRegistryContractId' => $brc->id,
                                'billRegistryContractRowId' => $brcr->id,
                                'billRegistryGroupProductId' => $brp->id,
                                'nameRow'=>$tableData->nameRow,
                                'descriptionRow'=>$tableData->descriptionRow,
                                'contractDetailId' => $contractDetailId,
                                'isContractDetailRow' => $isContractDetailRow,
                                'nameProduct' => $brp->name,
                                'nameContract'=>$brc->nameContract,
                                'contractCodeInt'=>$brc->contractCodeInt];
                        }
                    }
                    break;
                case "5":
                    $tableDatas = \Monkey::app()->repoFactory->create('BillRegistryContractRowSocialMonk')->findBy(['billRegistryContractRowId' => $brcr->id]);
                    if ($tableDatas != null) {
                        foreach ($tableDatas as $tableData) {
                            $contractDetailId = $tableData->id;
                            $isContractDetailRow = '1';
                            $contractRow[] = ['exist' => '1',
                                'billRegistryContractId' => $brc->id,
                                'billRegistryContractRowId' => $brcr->id,
                                'billRegistryGroupProductId' => $brp->id,
                                'nameRow'=>$tableData->nameRow,
                                'descriptionRow'=>$tableData->descriptionRow,
                                'contractDetailId' => $contractDetailId,
                                'isContractDetailRow' => $isContractDetailRow,
                                'nameProduct' => $brp->name,
                                'nameContract'=>$brc->nameContract,
                                'contractCodeInt'=>$brc->contractCodeInt];
                        }
                    }
                    break;
                case "6":
                    $tableDatas = \Monkey::app()->repoFactory->create('BillRegistryContractRowFriends')->findBy(['billRegistryContractRowId' => $brcr->id]);
                    if ($tableDatas != null) {
                        foreach ($tableDatas as $tableData) {
                            $contractDetailId = $tableData->id;
                            $isContractDetailRow = '1';
                            $contractRow[] = ['exist' => '1',
                                'billRegistryContractId' => $brc->id,
                                'billRegistryContractRowId' => $brcr->id,
                                'billRegistryGroupProductId' => $brp->id,
                                'nameRow'=>$tableData->nameRow,
                                'descriptionRow'=>$tableData->descriptionRow,
                                'contractDetailId' => $contractDetailId,
                                'isContractDetailRow' => $isContractDetailRow,
                                'nameProduct' => $brp->name,
                                'nameContract'=>$brc->nameContract,
                                'contractCodeInt'=>$brc->contractCodeInt];
                        }
                    }
                    break;
                case "7":
                    $tableDatas = \Monkey::app()->repoFactory->create('BillRegistryContractRowMailMonk')->findBy(['billRegistryContractRowId' => $brcr->id]);
                    if ($tableDatas != null) {
                        foreach ($tableDatas as $tableData) {
                            $contractDetailId = $tableData->id;
                            $isContractDetailRow = '0';
                            $contractRow[] = ['exist' => '1',
                                'billRegistryContractId' => $brc->id,
                                'billRegistryContractRowId' => $brcr->id,
                                'billRegistryGroupProductId' => $brp->id,
                                'nameRow'=>$tableData->nameRow,
                                'descriptionRow'=>$tableData->descriptionRow,
                                'contractDetailId' => $contractDetailId,
                                'isContractDetailRow' => $isContractDetailRow,
                                'nameProduct' => $brp->name,
                                'nameContract'=>$brc->nameContract,
                                'contractCodeInt'=>$brc->contractCodeInt];
                        }
                    }
                    break;

            }

    }
return json_encode($contractRow);

}

public
function put()
{
    $data = $this->app->router->request()->getRequestData();
    $billRegistryGroupProductId = $data['billRegistryGroupProductId'];
    $id = $data['idDetail'];
    $billRegistryContractRowId = $data['billRegistryContractRowId'];
    switch ($billRegistryGroupProductId) {
        case "1":
            try {
                $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkSource')->findOneBy(['billRegistryContractRowId'=>$billRegistryContractRowId,'id'=>$id]);
                $rowInsert->billRegistryContractRowId = $data['billRegistryContractRowId'];
                $rowInsert->automaticInvoice = $data['automaticInvoice'];
                $rowInsert->nameRow=$data['nameRow'];
                $rowInsert->descriptionRow=$data['descriptionRow'];
                $rowInsert->value = $data['value'];
                $rowInsert->billingDay = $data['billingDay'];
                $rowInsert->typePaymentId = $data['typePaymentId'];
                $rowInsert->periodTypeCharge = $data['periodTypeCharge'];
                $rowInsert->sellingFeeCommision = $data['sellingFeeCommision'];
                $rowInsert->feeCreditCardCommission = $data['feeCreditCardCommission'];
                $rowInsert->dayChargeFeeCreditCardCommission=$data['dayChargeFeeCreditCardCommission'];
                $rowInsert->feeCodCommission = $data['feeCodCommission'];
                $rowInsert->dayChargeFeeCodCommission = $data['dayChargeFeeCodCommission'];
                $rowInsert->feeBankTransferCommission = $data['feeBankTransferCommission'];
                $rowInsert->dayChargeFeeBankTransferCommission = $data['dayChargeFeeBankTransferCommission'];
                $rowInsert->feePaypalCommission = $data['feePaypalCommission'];
                $rowInsert->dayChargeFeePaypalCommission = $data['dayChargeFeePaypalCommission'];
                $rowInsert->chargeDeliveryIsActive = $data['chargeDeliveryIsActive'];
                $rowInsert->feeCostDeliveryCommission = $data['feeCostDeliveryCommission'];
                $rowInsert->periodTypeChargeDelivery = $data['periodTypeChargeDelivery'];
                $rowInsert->deliveryTypePaymentId = $data['deliveryTypePaymentId'];
                $rowInsert->chargePaymentIsActive = $data['chargePaymentIsActive'];
                $rowInsert->feeCostCommissionPayment = $data['feeCostCommissionPayment'];
                $rowInsert->periodTypeChargePayment = $data['periodTypeChargePayment'];
                $rowInsert->paymentTypePaymentId = $data['paymentTypePaymentId'];
                $rowInsert->descfeeCodCommission = $data['descfeeCodCommission'];
                $rowInsert->descriptionValue=$data['descriptionValue'];
                $rowInsert->descfeeCreditCardCommission = $data['descfeeCreditCardCommission'];
                $rowInsert->dayChargeFeeCreditCardCommission = $data['dayChargeFeeCreditCardCommission'];
                $rowInsert->descfeeCreditCardCommission = $data['descfeeCreditCardCommission'];
                $rowInsert->descfeeBankTransferCommission = $data['descfeeBankTransferCommission'];
                $rowInsert->descfeeCostDeliveryCommission=$data['descfeeCostDeliveryCommission'];
                $rowInsert->descfeeCostCommissionPayment=$data['descfeeCostCommissionPayment'];
                $rowInsert->billRegistryProductValue=$data['billRegistryProductValue'];
                $rowInsert->billRegistryProductFeeCodCommission=$data['productfeeCodCommission'];
                $rowInsert->billRegistryProductFeePaypalCommission=$data['productfeePaypalCommission'];
                $rowInsert->billRegistryProductFeeBankTransferCommission=$data['productfeeBankTransferCommission'];
                $rowInsert->billRegistryProductFeeCreditCardCommission=$data['productfeeCreditCardCommision'];
                $rowInsert->billRegistryProductFeeCostDeliveryCommission=$data['productfeeCostDeliveryCommission'];
                $rowInsert->billRegistryProductFeeCostCommissionPayment=$data['productfeeCostCommissionPayment'];
                $rowInsert->update();


                \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Report','update BillRegistryContractRowMonkSource','update 1 ContractRow' . $id,'');
                return $lastId;
            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Error','update BillRegistryContractRowMonkSource','update 1 contactRow',$e);
                return 'Errore Inserimento' . $e;

            }
            break;
        case "2":
            try {
                $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkAir')->findOneBy(['billRegistryContractRowId'=>$billRegistryContractRowId,'id'=>$id]);
                $rowInsert->billRegistryContractRowId = $data['billRegistryContractRowId'];
                $rowInsert->automaticInvoice = $data['automaticInvoice'];
                $rowInsert->nameRow=$data['nameRow'];
                $rowInsert->descriptionRow=$data['descriptionRow'];
                $rowInsert->value = $data['value'];
                $rowInsert->billingDay = $data['billingDay'];
                $rowInsert->typePaymentId = $data['typePaymentId'];
                $rowInsert->periodTypeCharge = $data['periodTypeCharge'];
                $rowInsert->sellingFeeCommision = $data['sellingFeeCommision'];
                $rowInsert->feeCreditCardCommission = $data['feeCreditCardCommission'];
                $rowInsert->dayChargeFeeCreditCardCommission=$data['dayChargeFeeCreditCardCommission'];
                $rowInsert->feeCodCommission = $data['feeCodCommission'];
                $rowInsert->dayChargeFeeCodCommission = $data['dayChargeFeeCodCommission'];
                $rowInsert->feeBankTransferCommission = $data['feeBankTransferCommission'];
                $rowInsert->dayChargeFeeBankTransferCommission = $data['dayChargeFeeBankTransferCommission'];
                $rowInsert->feePaypalCommission = $data['feePaypalCommission'];
                $rowInsert->dayChargeFeePaypalCommission = $data['dayChargeFeePaypalCommission'];
                $rowInsert->chargeDeliveryIsActive = $data['chargeDeliveryIsActive'];
                $rowInsert->feeCostDeliveryCommission = $data['feeCostDeliveryCommission'];
                $rowInsert->periodTypeChargeDelivery = $data['periodTypeChargeDelivery'];
                $rowInsert->deliveryTypePaymentId = $data['deliveryTypePaymentId'];
                $rowInsert->chargePaymentIsActive = $data['chargePaymentIsActive'];
                $rowInsert->feeCostCommissionPayment = $data['feeCostCommissionPayment'];
                $rowInsert->periodTypeChargePayment = $data['periodTypeChargePayment'];
                $rowInsert->paymentTypePaymentId = $data['paymentTypePaymentId'];
                $rowInsert->descfeeCodCommission = $data['descfeeCodCommission'];
                $rowInsert->descriptionValue=$data['descriptionValue'];
                $rowInsert->descfeeCreditCardCommission = $data['descfeeCreditCardCommission'];
                $rowInsert->dayChargeFeeCreditCardCommission = $data['dayChargeFeeCreditCardCommission'];
                $rowInsert->descfeeCreditCardCommission = $data['descfeeCreditCardCommission'];
                $rowInsert->descfeeBankTransferCommission = $data['descfeeBankTransferCommission'];
                $rowInsert->descfeeCostDeliveryCommission=$data['descfeeCostDeliveryCommission'];
                $rowInsert->descfeeCostCommissionPayment=$data['descfeeCostCommissionPayment'];
                $rowInsert->descfeeCostDeliveryCommission=$data['descfeeCostDeliveryCommission'];
                $rowInsert->descfeeCostCommissionPayment=$data['descfeeCostCommissionPayment'];
                $rowInsert->billRegistryProductValue=$data['billRegistryProductValue'];
                $rowInsert->billRegistryProductFeeCodCommission=$data['productfeeCodCommission'];
                $rowInsert->billRegistryProductFeePaypalCommission=$data['productfeePaypalCommission'];
                $rowInsert->billRegistryProductFeeBankTransferCommission=$data['productfeeBankTransferCommission'];
                $rowInsert->billRegistryProductFeeCreditCardCommission=$data['productfeeCreditCardCommision'];
                $rowInsert->billRegistryProductFeeCostDeliveryCommission=$data['productfeeCostDeliveryCommission'];
                $rowInsert->billRegistryProductFeeCostCommissionPayment=$data['productfeeCostCommissionPayment'];
                $rowInsert->update();


                \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Report','update BillRegistryContractRowMonkAir','update ContractRow' . $id,'');
                return "Modifica Eseguita";
            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Error','update BillRegistryContractRowMonkAir','update contactRow',$e);
                return 'Errore Aggiornamento' . $e;

            }
            break;
        case "3":
            try {
                $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkEntrySocial')->findOneBy(['billRegistryContractRowId'=>$billRegistryContractRowId,'id'=>$id]);
                $rowInsert->billRegistryContractRowId = $data['billRegistryContractRowId'];
                $rowInsert->descriptionInvoice = $data['descriptionInvoice'];
                $rowInsert->nameRow=$data['nameRow'];
                $rowInsert->descriptionRow=$data['descriptionRow'];
                $rowInsert->startUpCostCampaign = $data['startUpCostCampaign'];
                $rowInsert->automaticInvoice = $data['automaticInvoice'];
                $rowInsert->billingDay = $data['billingDay'];
                $rowInsert->typePaymentId = $data['typePaymentId'];
                $rowInsert->feeAgencyCommision = $data['feeAgencyCommision'];
                $rowInsert->prepaidPaymentIsActive = $data['prepaidPaymentIsActive'];
                $rowInsert->prepaidCost = $data['prepaidCost'];
                $rowInsert->billRegistryProductStartUpCostCampaign=$data['productStartUpCostCampaign'];
                $rowInsert->billRegistryProductFeeAgencyCommision=$data['productFeeAgencyCommision'];
                $rowInsert->update();


                \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Report','update BillRegistryContractRowMonkEntrySocial','update ContractRow' . $lastId,'');
                return 'Modifica Eseguita';
            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Error','update BillRegistryContractRowMonkEntrySocial','update contactRow',$e);
                return 'Errore Aggiornamento' . $e;

            }
            break;
        case "4":
            try {
                $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkEntryTraffic')->findOneBy(['billRegistryContractRowId'=>$billRegistryContractRowId,'id'=>$id]);
                $rowInsert->billRegistryContractRowId = $data['billRegistryContractRowId'];
                $rowInsert->descriptionInvoice = $data['descriptionInvoice'];
                $rowInsert->nameRow=$data['nameRow'];
                $rowInsert->descriptionRow=$data['descriptionRow'];
                $rowInsert->startUpCostCampaign = $data['startUpCostCampaign'];
                $rowInsert->automaticInvoice = $data['automaticInvoice'];
                $rowInsert->billingDay = $data['billingDay'];
                $rowInsert->typePaymentId = $data['typePaymentId'];
                $rowInsert->feeAgencyCommision = $data['feeAgencyCommision'];
                $rowInsert->prepaidPaymentIsActive = $data['prepaidPaymentIsActive'];
                $rowInsert->prepaidCost = $data['prepaidCost'];
                $rowInsert->billRegistryProductStartUpCostCampaign=$data['productStartUpCostCampaign'];
                $rowInsert->billRegistryProductFeeAgencyCommision=$data['productFeeAgencyCommision'];
                $rowInsert->update();


                \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Report','update 4 BillRegistryContractRowMonkEntryTraffic','update ContractRow' . $id,'');
                return "Modifica Eseguita";
            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Error','update 4 BillRegistryContractRowMonkEntryTraffic','update contactRow',$e);
                return 'Errore Aggiornamento' . $e;

            }
            break;

        case "5":
            try {
                $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowSocialMonk')->findOneBy(['billRegistryContractRowId'=>$billRegistryContractRowId,'id'=>$id]);
                $rowInsert->billRegistryContractRowId = $data['billRegistryContractRowId'];
                $rowInsert->automaticInvoice = $data['automaticInvoice'];
                $rowInsert->nameRow=$data['nameRow'];
                $rowInsert->descriptionRow=$data['descriptionRow'];
                $rowInsert->value = $data['value'];
                $rowInsert->billingDay = $data['billingDay'];
                $rowInsert->typePaymentId = $data['typePaymentId'];
                $rowInsert->periodTypeCharge = $data['periodTypeCharge'];
                $rowInsert->sellingFeeCommision = $data['sellingFeeCommision'];
                $rowInsert->feeCreditCardCommission = $data['feeCreditCardCommission'];
                $rowInsert->dayChargeFeeCreditCardCommission=$data['dayChargeFeeCreditCardCommission'];
                $rowInsert->feeCodCommission = $data['feeCodCommission'];
                $rowInsert->dayChargeFeeCodCommission = $data['dayChargeFeeCodCommission'];
                $rowInsert->feeBankTransferCommission = $data['feeBankTransferCommission'];
                $rowInsert->dayChargeFeeBankTransferCommission = $data['dayChargeFeeBankTransferCommission'];
                $rowInsert->feePaypalCommission = $data['feePaypalCommission'];
                $rowInsert->dayChargeFeePaypalCommission = $data['dayChargeFeePaypalCommission'];
                $rowInsert->chargeDeliveryIsActive = $data['chargeDeliveryIsActive'];
                $rowInsert->feeCostDeliveryCommission = $data['feeCostDeliveryCommission'];
                $rowInsert->periodTypeChargeDelivery = $data['periodTypeChargeDelivery'];
                $rowInsert->deliveryTypePaymentId = $data['deliveryTypePaymentId'];
                $rowInsert->chargePaymentIsActive = $data['chargePaymentIsActive'];
                $rowInsert->feeCostCommissionPayment = $data['feeCostCommissionPayment'];
                $rowInsert->periodTypeChargePayment = $data['periodTypeChargePayment'];
                $rowInsert->paymentTypePaymentId = $data['paymentTypePaymentId'];
                $rowInsert->descfeeCodCommission = $data['descfeeCodCommission'];
                $rowInsert->descriptionValue=$data['descriptionValue'];
                $rowInsert->descfeeCreditCardCommission = $data['descfeeCreditCardCommission'];
                $rowInsert->dayChargeFeeCreditCardCommission = $data['dayChargeFeeCreditCardCommission'];
                $rowInsert->descfeeCreditCardCommission = $data['descfeeCreditCardCommission'];
                $rowInsert->descfeeCostDeliveryCommission=$data['descfeeCostDeliveryCommission'];
                $rowInsert->descfeeCostCommissionPayment=$data['descfeeCostCommissionPayment'];
                $rowInsert->descfeeBankTransferCommission = $data['descfeeBankTransferCommission'];
                $rowInsert->billRegistryProductValue=$data['billRegistryProductValue'];
                $rowInsert->billRegistryProductFeeCodCommission=$data['productfeeCodCommission'];
                $rowInsert->billRegistryProductFeePaypalCommission=$data['productfeePaypalCommission'];
                $rowInsert->billRegistryProductFeeBankTransferCommission=$data['productfeeBankTransferCommission'];
                $rowInsert->billRegistryProductFeeCreditCardCommission=$data['productfeeCreditCardCommision'];
                $rowInsert->billRegistryProductFeeCostDeliveryCommission=$data['productfeeCostDeliveryCommission'];
                $rowInsert->billRegistryProductFeeCostCommissionPayment=$data['productfeeCostCommissionPayment'];
                $rowInsert->update();

                \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Report',' 5 update BillRegistryContractRowSocialMonk','update ContractRow' . $id,'');
                return "Modifica Eseguita";
            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Error','5 update BillRegistryContractRowSocialMonk','update contactRow',$e);
                return 'Errore Aggiornamento' . $e;

            }
            break;

        case "6":
            try {
                $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowFriends')->findOneBy(['billRegistryContractRowId'=>$billRegistryContractRowId,'id'=>$id]);
                $rowInsert->billRegistryContractRowId = $data['billRegistryContractRowId'];
                $rowInsert->typeContractId = $data['typeContractId'];
                $rowInsert->nameRow=$data['nameRow'];
                $rowInsert->descriptionRow=$data['descriptionRow'];
                $rowInsert->valueMarkUpFullPrice = $data['valueMarkUpFullPrice'];
                $rowInsert->valueMarkUpSalePrice = $data['valueMarkUpSalePrice'];
                $rowInsert->billingDay=$data['billingDay'];
                $rowInsert->billRegistryProductValue=$data['billRegistryProductValue'];
                $rowInsert->update();

                \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Report','update BillRegistryContractRowFriends','update ContractRow' . $id,'');
                return "Modifica Eseguita";
            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Error','update BillRegistryContractRowFriends','update contactRow',$e);
                return 'Errore Inserimento' . $e;

            }
            break;

        case "7":
            try {
                $rowInsert = \Monkey::app()->repoFactory->create('BillRegistryContractRowMailMonk')->findOneBy(['billRegistryContractRowId'=>$billRegistryContractRowId,'id'=>$id]);
                $rowInsert->billRegistryContractRowId = $data['billRegistryContractRowId'];
                $rowInsert->automaticInvoice = $data['automaticInvoice'];
                $rowInsert->nameRow=$data['nameRow'];
                $rowInsert->descriptionRow=$data['descriptionRow'];
                $rowInsert->emailAccount = $data['emailAccount'];
                $rowInsert->emailAccountSendQty = $data['emailAccountSendQty'];
                $rowInsert->emailAccountCampaignQty = $data['emailAccountCampaignQty'];
                $rowInsert->value = $data['value'];
                $rowInsert->billingDay = $data['billingDay'];
                $rowInsert->typePaymentId = $data['typePaymentId'];
                $rowInsert->periodTypeCharge = $data['periodTypeCharge'];
                $rowInsert->sellingFeeCommision = $data['sellingFeeCommision'];
                $rowInsert->descfeeCreditCardCommission = $data['descfeeCreditCardCommission'];
                $rowInsert->descfeeCodCommission = $data['descfeeCodCommission'];
                $rowInsert->descfeeCreditCardCommission = $data['descfeeCreditCardCommission'];
                $rowInsert->descfeeBankTransferCommission = $data['descfeeBankTransferCommission'];
                $rowInsert->descfeeCostDeliveryCommission=$data['descfeeCostDeliveryCommission'];
                $rowInsert->descfeeCostCommissionPayment=$data['descfeeCostCommissionPayment'];
                $rowInsert->feeCreditCardCommission = $data['feeCreditCardCommission'];
                $rowInsert->dayChargeFeeCreditCardCommission = $data['dayChargeFeeCreditCardCommission'];
                $rowInsert->feeCodCommission = $data['feeCodCommission'];
                $rowInsert->dayChargeFeeCodCommission = $data['dayChargeFeeCodCommission'];
                $rowInsert->feeBankTransferCommission = $data['feeBankTransferCommission'];
                $rowInsert->dayChargeFeeBankTransferCommission = $data['dayChargeFeeBankTransferCommission'];
                $rowInsert->feePaypalCommission = $data['feePaypalCommission'];
                $rowInsert->dayChargeFeePaypalCommission = $data['dayChargeFeePaypalCommission'];
                $rowInsert->chargeDeliveryIsActive = $data['chargeDeliveryIsActive'];
                $rowInsert->feeCostDeliveryCommission = $data['feeCostDeliveryCommission'];
                $rowInsert->periodTypeChargeDelivery = $data['periodTypeChargeDelivery'];
                $rowInsert->deliveryTypePaymentId = $data['deliveryTypePaymentId'];
                $rowInsert->chargePaymentIsActive = $data['chargePaymentIsActive'];
                $rowInsert->feeCostCommissionPayment = $data['feeCostCommissionPayment'];
                $rowInsert->periodTypeChargePayment = $data['periodTypeChargePayment'];
                $rowInsert->paymentTypePaymentId = $data['paymentTypePaymentId'];
                $rowInsert->update();

                \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Report','update 7 BillRegistryContractRowMailMonk','update ContractRow' . $id,'');
                 return "Modifica Eseguita";
            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('CBillRegistryContractRowManageAjaxController','Error','update 7 BillRegistryContractRowMailMonk','update contactRow',$e);
                return 'Errore Inserimento' . $e;

            }
            break;


    }


}

public
function delete()
{

}
}
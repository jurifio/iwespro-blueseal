<?php

namespace bamboo\blueseal\jobs;

use bamboo\controllers\api\Helper\DateTime;
use bamboo\core\jobs\ACronJob;

/**
 * Class CDispatchPreorderToFriend
 * @package bamboo\blueseal\jobs
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CGenerateCustomerInvoiceJob extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->generateInvoice();
    }

    /**
     * @param int $days
     */
    public function generateInvoice()
    {

        $billRegistryClientRepo = \Monkey::app()->repoFactory->create('BillRegistryClient');
        $billRegistryContractRepo = \Monkey::app()->repoFactory->create('BillRegistryContract');
        $billRegistryContractRowRepo = \Monkey::app()->repoFactory->create('BillRegistryContractRow');
        $billRegistryContractRowDetailRepo = \Monkey::app()->repoFactory->create('BillRegistryContractRowDetail');
        $billRegistryClientAccountRepo = \Monkey::app()->repoFactory->create('BillRegistryClientAccount');
        $billRegistryGroupProductRepo = \Monkey::app()->repoFactory->create('BillRegistryGroupProductRepo');
        $billRegistryPriceListRepo = \Monkey::app()->repoFactory->create('BillRegistryPriceList');
        $billRegistryProductRepo = \Monkey::app()->repoFactory->create('BillRegistryProduct');
        $billRegistryProductDetailRepo = \Monkey::app()->repoFactory->create('BillRegistryProductDetail');
        $invoiceRepo = \Monkey::app()->repoFactory->create('Invoice');
        $billRegistryInvoiceRowRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoiceRow');
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $billRegistrySocialRepo = \Monkey::app()->repoFactory->create('BillRegistrySocial');
        $campaignRepo = \Monkey::app()->repoFactory->create('Campaign');
        $marketplaceRepo = \Monkey::app()->repoFactory->create('Marketplace');
        $marketplaceAccountRepo = \Monkey::app()->repoFactory->create('MarketplaceAccount');
        $billRegistryTypeTaxesRepo = \Monkey::app()->repoFactory->create('BillRegistryTypeTaxes');
        $billRegistryTypePaymentRepo = \Monkey::app()->repoFactory->create('BillRegistryTypePayment');
        $billRegistryClientBillingInfoRepo = \Monkey::app()->repoFactory->create('BillRegistryClientBillingInfo');
        $billRegistryTimeTableRepo = \Monkey::app()->repoFactory->create('BillRegistryTimeTable');
        $today = new \DateTime();
        $day = (new \DateTime())->format('d');
        try {
            $billRegistryContracts = $billRegistryContractRepo->findAll();
            foreach ($billRegistryContracts as $billRegistryContract) {
                $contractId = $billRegistryContract->id;
                $billRegistryClientId = $billRegistryContract->billRegistryClientId;
                $billRegistryClientAccount = $billRegistryClientAccountRepo->findOneBy(['billRegistryClientId' => $billRegistryClientId]);
                $billRegistryClientBillingInfo = $billRegistryClientBillingInfoRepo->findOneBy(['billRegistryClientId' => $billRegistryClientId]);
                $shopId = $billRegistryClientAccount->shopId;

                $billRegistryContractRow = $billRegistryContractRowRepo->findBy(['billRegistryContractId' => $contractId,'statusId' => '1']);
                if ($billRegistryContractRow != null) {
                    $invoiceHeader = [];
                    $rowInvoiceDetail = [];
                    $rowInvoiceExtraFee = [];
                    $grossTotal = 0;
                    $netTotal = 0;
                    $vat = 0;
                    $creditCardCommission = 0;
                    $bankTransferCommission = 0;
                    $codCommission = 0;
                    $paypalCommission = 0;
                    $costDeliveryCommission = 0;
                    foreach ($billRegistryContractRow as $contractRow) {
                        $contractRowId = $contractRow->id;

                        $typeContract = $contractRow->billRegistryGroupProductId;
                        switch ($typeContract) {
                            case 1:
                                //definizione dei parametri del tipo di contratto
                                $billRegistryRowMonkSource = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkSource')->findOneBy(['billRegistryContractRowId' => $contractRowId]);
                                if ($billRegistryRowMonkSource!=null) {
                                    $automaticInvoice = $billRegistryRowMonkSource->automaticInvoice;
                                    $valueContractRow = $billRegistryRowMonkSource->value;
                                    $billingDay = $billRegistryRowMonkSource->billingDay;
                                    $typePaymentId = $billRegistryRowMonkSource->typePaymentId;
                                    $periodTypeCharge = $billRegistryRowMonkSource->periodTypeCharge;
                                    $sellingFeeCommision = $billRegistryRowMonkSource->sellingFeeCommision;
                                    $feeCreditCardCommission = $billRegistryRowMonkSource->feeCreditCardCommission;
                                    $dayChargeFeeCreditCardCommission = $billRegistryRowMonkSource->dayChargeFeeCreditCardCommission;
                                    $feeCodCommission = $billRegistryRowMonkSource->feeCodCommission;
                                    $dayChargeFeeCodCommission = $billRegistryRowMonkSource->dayChargeFeeCodCommission;
                                    $feeBankTransferCommission = $billRegistryRowMonkSource->feeBankTransferCommission;
                                    $dayChargeFeeBankTransferCommission = $billRegistryRowMonkSource->dayChargeFeeBankTransferCommission;
                                    $feePaypalCommission = $billRegistryRowMonkSource->feePaypalCommission;
                                    $dayChargeFeePaypalCommission = $billRegistryRowMonkSource->dayChargeFeePaypalCommission;
                                    $chargeDeliveryIsActive = $billRegistryRowMonkSource->chargeDeliveryIsActive;
                                    $feeCostDeliveryCommission = $billRegistryRowMonkSource->feeCostDeliveryCommission;
                                    $periodTypeChargeDelivery = $billRegistryRowMonkSource->periodTypeChargeDelivery;
                                    $deliveryTypePaymentId = $billRegistryRowMonkSource->deliveryTypePaymentId;
                                    $chargePaymentIsActive = $billRegistryRowMonkSource->chargePaymentIsActive;
                                    $feeCostCommissionPayment = $billRegistryRowMonkSource->feeCostCommissionPayment;
                                    $periodTypeChargePayment = $billRegistryRowMonkSource->periodTypeChargePayment;
                                    $paymentTypePaymentId = $billRegistryRowMonkSource->paymentTypePaymentId;
                                    $orderLine = \Monkey::app()->repoFactory->create('OrderLine')->findBy(['remoteShopSellerId' => $shopId]);

                                    foreach ($orderLine as $orl) {
                                        if ($orl->status == 'ORD_DELIVERED' || $orl->status == 'ORD_SENT' || $orl->status == 'ORD_FRND_PYD') {
                                            if ($orl->isBill == null) {
                                                $order = \Monkey::app()->repoFactory->create('Order')->findOneBy(['id' => $orl->orderId]);

                                                switch ($order->orderPaymentMethodId) {
                                                    case 1:
                                                        $paypalCommission += $orl->netPrice /100 * $feePaypalCommission;
                                                        break;
                                                    case 2:
                                                        $creditCardCommission += $orl->netPrice/100 * $feeCreditCardCommission;
                                                        break;
                                                    case 3:
                                                        $bankTransferCommission += $orl->netPrice /100* $feeBankTransferCommission;
                                                        break;
                                                    case 5:
                                                        $codCommission += $orl->netPrice /100 * $feeCodCommission;
                                                        break;

                                                }


                                                $orderLineHasShipment = \Monkey::app()->repoFactory->create('OrderLineHasShipment')->findBy(['orderLineId' => $orl->id,'orderId' => $orl->orderId]);
                                                foreach ($orderLineHasShipment as $olhs) {
                                                    $shipment = \Monkey::app()->repoFactory->create('Shipment')->findOneBy(['id' => $olhs->shipmentId]);
                                                    if ($shipment->realShipmentPrice != null) {
                                                        $costDeliveryCommission += $shipment->realShipmentPrice + ($shipment->realShipmentPrice / 100 * $feeCostDeliveryCommission);
                                                    }

                                                }
                                                $orl->isBill = 1;
                                                $orl->update();
                                            }
                                        } else {
                                            continue;
                                        }
                                    }
                                    $customerTaxes = $billRegistryTypeTaxesRepo->findOneBy(['id' => $billRegistryClientBillingInfo->billRegistryTypeTaxesId]);


                                    $netTotalRow = $paypalCommission + $codCommission + $bankTransferCommission + $creditCardCommission + $costDeliveryCommission;
                                    $netTotal += $netTotalRow;
                                    $vatRowTotalExtra = ($paypalCommission + $codCommission + $bankTransferCommission + $creditCardCommission + $costDeliveryCommission) / 100 * $customerTaxes->perc;
                                    $vat += $vatRowTotalExtra;
                                    $grossTotal += $netTotalRow + $vatRowTotalExtra;
                                    if ($paypalCommission != 0) {
                                        $rowInvoiceExtraFee[] = [
                                            'billRegistryProductId' => 0,
                                            'description' => 'commissioni Pagamenti Paypal',
                                            'qty' => 1,
                                            'priceRow' => $paypalCommission,
                                            'netPrice' => $paypalCommission,
                                            'vatRow' => $paypalCommission / 100 * $customerTaxes->perc,
                                            'grossTotalRow' => $paypalCommission + ($paypalCommission / 100 * $customerTaxes->perc),
                                            'billRegistryTypeTaxesId' => $customerTaxes->id,
                                            'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                            'billRegistryContractId' => $contractId,
                                            'billRegistryContractRowId' => $contractRowId,
                                            'billRegistryContractRowDetailId' => 0
                                        ];
                                    }
                                    if ($codCommission != 0) {
                                        $rowInvoiceExtraFee[] = [
                                            'billRegistryProductId' => 0,
                                            'description' => 'commissioni Pagamenti Contrassegni',
                                            'qty' => 1,
                                            'priceRow' => $codCommission,
                                            'netPrice' => $codCommission,
                                            'vatRow' => $codCommission / 100 * $customerTaxes->perc,
                                            'grossTotalRow' => $codCommission + ($codCommission / 100 * $customerTaxes->perc),
                                            'billRegistryTypeTaxesId' => $customerTaxes->id,
                                            'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                            'billRegistryContractId' => $contractId,
                                            'billRegistryContractRowId' => $contractRowId,
                                            'billRegistryContractRowDetailId' => 0
                                        ];
                                    }
                                    if ($bankTransferCommission != 0) {
                                        $rowInvoiceExtraFee[] = [
                                            'billRegistryProductId' => 0,
                                            'description' => 'commissioni Pagamenti Bonifici Sepa',
                                            'qty' => 1,
                                            'priceRow' => $bankTransferCommission,
                                            'netPrice' => $bankTransferCommission,
                                            'vatRow' => $bankTransferCommission / 100 * $customerTaxes->perc,
                                            'grossTotalRow' => $bankTransferCommission + ($bankTransferCommission / 100 * $customerTaxes->perc),
                                            'billRegistryTypeTaxesId' => $customerTaxes->id,
                                            'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                            'billRegistryContractId' => $contractId,
                                            'billRegistryContractRowId' => $contractRowId,
                                            'billRegistryContractRowDetailId' => 0
                                        ];
                                    }
                                    if ($creditCardCommission != 0) {
                                        $rowInvoiceExtraFee[] = [
                                            'billRegistryProductId' => 0,
                                            'description' => 'commissioni Pagamenti Carte di Credito',
                                            'qty' => 1,
                                            'priceRow' => $creditCardCommission,
                                            'netPrice' => $creditCardCommission,
                                            'vatRow' => $creditCardCommission / 100 * $customerTaxes->perc,
                                            'grossTotalRow' => $creditCardCommission + ($creditCardCommission / 100 * $customerTaxes->perc),
                                            'billRegistryTypeTaxesId' => $customerTaxes->id,
                                            'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                            'billRegistryContractId' => $contractId,
                                            'billRegistryContractRowId' => $contractRowId,
                                            'billRegistryContractRowDetailId' => 0
                                        ];
                                    }
                                    if ($creditCardCommission != 0) {
                                        $rowInvoiceExtraFee[] = [
                                            'billRegistryProductId' => 0,
                                            'description' => 'commissioni su Spedizioni',
                                            'qty' => 1,
                                            'priceRow' => $creditCardCommission,
                                            'netPrice' => $creditCardCommission,
                                            'vatRow' => $creditCardCommission / 100 * $customerTaxes->perc,
                                            'grossTotalRow' => $creditCardCommission + ($creditCardCommission / 100 * $customerTaxes->perc),
                                            'billRegistryTypeTaxesId' => $customerTaxes->id,
                                            'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                            'billRegistryContractId' => $contractId,
                                            'billRegistryContractRowId' => $contractRowId,
                                            'billRegistryContractRowDetailId' => 0
                                        ];
                                    }
                                    $netTotalRowContract = $valueContractRow;
                                    $netTotal += $netTotalRowContract;
                                    $vatRowTotalExtra = $valueContractRow / 100 * $customerTaxes->perc;
                                    $vat += $vatRowTotalExtra;
                                    $grossTotal += $netTotalRowContract + $vatRowTotalExtra;
                                    if ($valueContractRow != 0) {
                                        $rowInvoiceExtraFee[] = [
                                            'billRegistryProductId' => 0,
                                            'description' => 'Servizio MonkSource',
                                            'qty' => 1,
                                            'priceRow' => $valueContractRow,
                                            'netPrice' => $valueContractRow,
                                            'vatRow' => $valueContractRow / 100 * $customerTaxes->perc,
                                            'grossTotalRow' => $valueContractRow + ($valueContractRow / 100 * $customerTaxes->perc),
                                            'billRegistryTypeTaxesId' => $customerTaxes->id,
                                            'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                            'billRegistryContractId' => $contractId,
                                            'billRegistryContractRowId' => $contractRowId,
                                            'billRegistryContractRowDetailId' => 0
                                        ];
                                    }
                                }else{
                                    continue 2;
                                }

                                break;
                            case 2:
                                $billRegistryRowMonkAir = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkAir')->findOneBy(['billRegistryContractRowId' => $contractRowId]);
                                if ($billRegistryRowMonkAir != null) {
                                    $automaticInvoice = $billRegistryRowMonkAir->automaticInvoice;
                                    $valueContractRow = $billRegistryRowMonkAir->value;
                                    $billingDay = $billRegistryRowMonkAir->billingDay;
                                    $typePaymentId = $billRegistryRowMonkAir->typePaymentId;
                                    $periodTypeCharge = $billRegistryRowMonkAir->periodTypeCharge;
                                    $sellingFeeCommision = $billRegistryRowMonkAir->sellingFeeCommision;
                                    $feeCreditCardCommission = $billRegistryRowMonkAir->feeCreditCardCommission;
                                    $dayChargeFeeCreditCardCommission = $billRegistryRowMonkAir->dayChargeFeeCreditCardCommission;
                                    $feeCodCommission = $billRegistryRowMonkAir->feeCodCommission;
                                    $dayChargeFeeCodCommission = $billRegistryRowMonkAir->dayChargeFeeCodCommission;
                                    $feeBankTransferCommission = $billRegistryRowMonkAir->feeBankTransferCommission;
                                    $dayChargeFeeBankTransferCommission = $billRegistryRowMonkAir->dayChargeFeeBankTransferCommission;
                                    $feePaypalCommission = $billRegistryRowMonkAir->feePaypalCommission;
                                    $dayChargeFeePaypalCommission = $billRegistryRowMonkAir->dayChargeFeePaypalCommission;
                                    $chargeDeliveryIsActive = $billRegistryRowMonkAir->chargeDeliveryIsActive;
                                    $feeCostDeliveryCommission = $billRegistryRowMonkAir->feeCostDeliveryCommission;
                                    $periodTypeChargeDelivery = $billRegistryRowMonkAir->periodTypeChargeDelivery;
                                    $deliveryTypePaymentId = $billRegistryRowMonkAir->deliveryTypePaymentId;
                                    $chargePaymentIsActive = $billRegistryRowMonkAir->chargePaymentIsActive;
                                    $feeCostCommissionPayment = $billRegistryRowMonkAir->feeCostCommissionPayment;
                                    $periodTypeChargePayment = $billRegistryRowMonkAir->periodTypeChargePayment;
                                    $paymentTypePaymentId = $billRegistryRowMonkAir->paymentTypePaymentId;


                                    $customerTaxes = $billRegistryTypeTaxesRepo->findOneBy(['id' => $billRegistryClientBillingInfo->billRegistryTypeTaxesId]);
                                    $netTotalRow = $valueContractRow;
                                    $netTotal += $netTotalRow;
                                    $vatRowTotalExtra = $valueContractRow / 100 * $customerTaxes->perc;
                                    $vat += $vatRowTotalExtra;
                                    $grossTotal += $netTotalRow + $vatRowTotalExtra;
                                    if ($valueContractRow != 0) {
                                        $rowInvoiceExtraFee[] = [
                                            'billRegistryProductId' => 0,
                                            'description' => 'Servizio Monk Air',
                                            'qty' => 1,
                                            'priceRow' => $valueContractRow,
                                            'netPrice' => $valueContractRow,
                                            'vatRow' => $valueContractRow / 100 * $customerTaxes->perc,
                                            'grossTotalRow' => $valueContractRow + ($valueContractRow / 100 * $customerTaxes->perc),
                                            'billRegistryTypeTaxesId' => $customerTaxes->id,
                                            'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                            'billRegistryContractId' => $contractId,
                                            'billRegistryContractRowId' => $contractRowId,
                                            'billRegistryContractRowDetailId' => 0
                                        ];
                                    }
                                } else {
                                    continue 2;
                                }

                                break;
                            case 3:
                                $billRegistryRowMonkEntrySocial = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkEntrySocial')->findOneBy(['billRegistryContractRowId' => $contractRowId]);
                                $automaticInvoice = $billRegistryRowMonkEntrySocial->automaticInvoice;
                                $descriptionInvoice = $billRegistryRowMonkEntrySocial->descriptionInvoice;
                                $startUpCostCampaign = $billRegistryRowMonkEntrySocial->startUpCostCampaign;
                                $billingDay = $billRegistryRowMonkEntrySocial->billingDay;
                                $typePaymentId = $billRegistryRowMonkEntrySocial->typePaymentId;
                                $feeAgencyCommision = $billRegistryRowMonkEntrySocial->feeAgencyCommision;
                                $prepaidPaymentIsActive = $billRegistryRowMonkEntrySocial->prepaidPaymentIsActive;
                                $prepaidCost = $billRegistryRowMonkEntrySocial->prepaidCost;
                                $startUpCostIsPaid = $billRegistryRowMonkEntrySocial->startUpCostIsPaid;

                                if ($startUpCostIsPaid == null || $startUpCostIsPaid == 0) {
                                    $netTotalRowStartUpCost = $startUpCostIsPaid;
                                    $vatTotalRowStartUPCost = $netTotalRowStartUpCost / 100 * $customerTaxes->perc;
                                    $vat += $vatTotalRowStartUPCost;
                                    $netTotal += $netTotalRowStartUpCost;
                                    $grossTotal += $netTotalRowStartUpCost + $vatTotalRowStartUPCost;
                                    $rowInvoiceExtraFee[] = [
                                        'billRegistryProductId' => 0,
                                        'description' => 'Costo di StartUp Campagna',
                                        'qty' => 1,
                                        'priceRow' => $startUpCostIsPaid,
                                        'netPrice' => $startUpCostIsPaid,
                                        'vatRow' => $startUpCostIsPaid / 100 * $customerTaxes->perc,
                                        'grossTotalRow' => $startUpCostIsPaid + ($startUpCostIsPaid / 100 * $customerTaxes->perc),
                                        'billRegistryTypeTaxesId' => $customerTaxes->id,
                                        'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                        'billRegistryContractId' => $contractId,
                                        'billRegistryContractRowId' => $contractRowId,
                                        'billRegistryContractRowDetailId' => 0
                                    ];
                                }
                                $netTotalRowPrepaidCost = $prepaidCost;
                                $netTotal += $netTotalRowPrepaidCost;
                                $vatRowTotalPrepaidCost = $netTotalRowPrepaidCost / 100 * $customerTaxes->perc;
                                $vat += $vatRowTotalPrepaidCost;
                                $grossTotal += $netTotalRowPrepaidCost + $vatRowTotalPrepaidCost;
                                if ($prepaidCost != 0) {
                                    $rowInvoiceExtraFee[] = [
                                        'billRegistryProductId' => 0,
                                        'description' => 'Servizio MonkEntrySocial',
                                        'qty' => 1,
                                        'priceRow' => $prepaidCost,
                                        'netPrice' => $prepaidCost,
                                        'vatRow' => $prepaidCost / 100 * $customerTaxes->perc,
                                        'grossTotalRow' => $prepaidCost + ($prepaidCost / 100 * $customerTaxes->perc),
                                        'billRegistryTypeTaxesId' => $customerTaxes->id,
                                        'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                        'billRegistryContractId' => $contractId,
                                        'billRegistryContractRowId' => $contractRowId,
                                        'billRegistryContractRowDetailId' => 0
                                    ];
                                }
                                $netTotalRowAgencyCommision = $prepaidCost / 100 * $feeAgencyCommision;
                                $netTotal += $netTotalRowAgencyCommision;
                                $vatRowTotalAgencyCommision = $netTotalRowAgencyCommision / 100 * $customerTaxes->perc;
                                $vat += $vatRowTotalAgencyCommision;
                                $grossTotal += $netTotalRowAgencyCommision + $vatRowTotalAgencyCommision;
                                if ($prepaidCost != 0) {
                                    $rowInvoiceExtraFee[] = [
                                        'billRegistryProductId' => 0,
                                        'description' => 'Servizio MonkEntrySocial',
                                        'qty' => 1,
                                        'priceRow' => $netTotalRowAgencyCommision,
                                        'netPrice' => $netTotalRowAgencyCommision,
                                        'vatRow' => $vatRowTotalAgencyCommision,
                                        'grossTotalRow' => $netTotalRowAgencyCommision + $vatRowTotalAgencyCommision,
                                        'billRegistryTypeTaxesId' => $customerTaxes->id,
                                        'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                        'billRegistryContractId' => $contractId,
                                        'billRegistryContractRowId' => $contractRowId,
                                        'billRegistryContractRowDetailId' => 0
                                    ];
                                }


                                break;
                            case 4:
                                $billRegistryRowMonkEntryTraffic = \Monkey::app()->repoFactory->create('BillRegistryContractRowMonkEntryTraffic')->findOneBy(['billRegistryContractRowId' => $contractRowId]);
                                if($billRegistryRowMonkEntryTraffic!=null) {
                                    $automaticInvoice = $billRegistryRowMonkEntryTraffic->automaticInvoice;
                                    $descriptionInvoice = $billRegistryRowMonkEntryTraffic->descriptionInvoice;
                                    $startUpCostCampaign = $billRegistryRowMonkEntryTraffic->startUpCostCampaign;
                                    $billingDay = $billRegistryRowMonkEntryTraffic->billingDay;
                                    $typePaymentId = $billRegistryRowMonkEntryTraffic->typePaymentId;
                                    $feeAgencyCommision = $billRegistryRowMonkEntryTraffic->feeAgencyCommision;
                                    $prepaidPaymentIsActive = $billRegistryRowMonkEntryTraffic->prepaidPaymentIsActive;
                                    $prepaidCost = $billRegistryRowMonkEntryTraffic->prepaidCost;
                                    $startUpCostIsPaid = $billRegistryRowMonkEntryTraffic->startUpCostIsPaid;

                                    if ($startUpCostIsPaid == null || $startUpCostIsPaid == 0) {
                                        $netTotalRowStartUpCost = $startUpCostIsPaid;
                                        $vatTotalRowStartUpCost = $netTotalRowStartUpCost / 100 * $customerTaxes->perc;
                                        $vat += $vatTotalRowStartUpCost;
                                        $netTotal += $netTotalRowStartUpCost;
                                        $grossTotal += $netTotalRowStartUpCost + $vatTotalRowStartUpCost;
                                        $rowInvoiceExtraFee[] = [
                                            'billRegistryProductId' => 0,
                                            'description' => 'Costo di StartUp Campagna',
                                            'qty' => 1,
                                            'priceRow' => $startUpCostIsPaid,
                                            'netPrice' => $startUpCostIsPaid,
                                            'vatRow' => $startUpCostIsPaid / 100 * $customerTaxes->perc,
                                            'grossTotalRow' => $startUpCostIsPaid + ($startUpCostIsPaid / 100 * $customerTaxes->perc),
                                            'billRegistryTypeTaxesId' => $customerTaxes->id,
                                            'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                            'billRegistryContractId' => $contractId,
                                            'billRegistryContractRowId' => $contractRowId,
                                            'billRegistryContractRowDetailId' => 0
                                        ];
                                    }
                                    $netTotalRowPrepaidCost = $prepaidCost;
                                    $netTotal += $netTotalRowPrepaidCost;
                                    $vatRowTotalPrepaidCost = $netTotalRowPrepaidCost / 100 * $customerTaxes->perc;
                                    $vat += $vatRowTotalPrepaidCost;
                                    $grossTotal += $netTotalRowPrepaidCost + $vatRowTotalPrepaidCost;
                                    if ($prepaidCost != 0) {
                                        $rowInvoiceExtraFee[] = [
                                            'billRegistryProductId' => 0,
                                            'description' => 'Servizio MonkEntrySocial',
                                            'qty' => 1,
                                            'priceRow' => $prepaidCost,
                                            'netPrice' => $prepaidCost,
                                            'vatRow' => $prepaidCost / 100 * $customerTaxes->perc,
                                            'grossTotalRow' => $prepaidCost + ($prepaidCost / 100 * $customerTaxes->perc),
                                            'billRegistryTypeTaxesId' => $customerTaxes->id,
                                            'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                            'billRegistryContractId' => $contractId,
                                            'billRegistryContractRowId' => $contractRowId,
                                            'billRegistryContractRowDetailId' => 0
                                        ];
                                    }
                                    $netTotalRowAgencyCommision = $prepaidCost / 100 * $feeAgencyCommision;
                                    $netTotal += $netTotalRowAgencyCommision;
                                    $vatRowTotalAgencyCommision = $netTotalRowAgencyCommision / 100 * $customerTaxes->perc;
                                    $vat += $vatRowTotalAgencyCommision;
                                    $grossTotal += $netTotalRowAgencyCommision + $vatRowTotalAgencyCommision;
                                    if ($prepaidCost != 0) {
                                        $rowInvoiceExtraFee[] = [
                                            'billRegistryProductId' => 0,
                                            'description' => 'Commissioni Agenzia MonkEntrySocial',
                                            'qty' => 1,
                                            'priceRow' => $netTotalRowAgencyCommision,
                                            'netPrice' => $netTotalRowAgencyCommision,
                                            'vatRow' => $vatRowTotalAgencyCommision,
                                            'grossTotalRow' => $netTotalRowAgencyCommision + $vatRowTotalAgencyCommision,
                                            'billRegistryTypeTaxesId' => $customerTaxes->id,
                                            'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                            'billRegistryContractId' => $contractId,
                                            'billRegistryContractRowId' => $contractRowId,
                                            'billRegistryContractRowDetailId' => 0
                                        ];
                                    }
                                }else{
                                    continue 2;
                                }
                                break;
                            case 5:
                                $billRegistryRowSocialMonk = \Monkey::app()->repoFactory->create('BillRegistryContractRowSocialMonk')->findOneBy(['billRegistryContractRowId' => $contractRowId]);
                                if ($billRegistryRowSocialMonk!=null){
                                    $automaticInvoice = $billRegistryRowSocialMonk->automaticInvoice;
                                    $valueContractRow = $billRegistryRowSocialMonk->value;
                                    $billingDay = $billRegistryRowSocialMonk->billingDay;
                                    $typePaymentId = $billRegistryRowSocialMonk->typePaymentId;
                                    $periodTypeCharge = $billRegistryRowSocialMonk->periodTypeCharge;
                                    $sellingFeeCommision = $billRegistryRowSocialMonk->sellingFeeCommision;
                                    $feeCreditCardCommission = $billRegistryRowSocialMonk->feeCreditCardCommission;
                                    $dayChargeFeeCreditCardCommission = $billRegistryRowSocialMonk->dayChargeFeeCreditCardCommission;
                                    $feeCodCommission = $billRegistryRowSocialMonk->feeCodCommission;
                                    $dayChargeFeeCodCommission = $billRegistryRowSocialMonk->dayChargeFeeCodCommission;
                                    $feeBankTransferCommission = $billRegistryRowSocialMonk->feeBankTransferCommission;
                                    $dayChargeFeeBankTransferCommission = $billRegistryRowSocialMonk->dayChargeFeeBankTransferCommission;
                                    $feePaypalCommission = $billRegistryRowSocialMonk->feePaypalCommission;
                                    $dayChargeFeePaypalCommission = $billRegistryRowSocialMonk->dayChargeFeePaypalCommission;
                                    $chargeDeliveryIsActive = $billRegistryRowSocialMonk->chargeDeliveryIsActive;
                                    $feeCostDeliveryCommission = $billRegistryRowSocialMonk->feeCostDeliveryCommission;
                                    $periodTypeChargeDelivery = $billRegistryRowSocialMonk->periodTypeChargeDelivery;
                                    $deliveryTypePaymentId = $billRegistryRowSocialMonk->deliveryTypePaymentId;
                                    $chargePaymentIsActive = $billRegistryRowSocialMonk->chargePaymentIsActive;
                                    $feeCostCommissionPayment = $billRegistryRowSocialMonk->feeCostCommissionPayment;
                                    $periodTypeChargePayment = $billRegistryRowSocialMonk->periodTypeChargePayment;
                                    $paymentTypePaymentId = $billRegistryRowSocialMonk->paymentTypePaymentId;
                                } else{
                                    continue 2;
                                }
                                break;
                            case 7:
                                $billRegistryRowMailMonk = \Monkey::app()->repoFactory->create('BillRegistryContractRowMailMonk')->findOneBy(['billRegistryContractRowId' => $contractRowId]);
                                $automaticInvoice = $billRegistryRowMailMonk->automaticInvoice;
                                $emailAccount = $billRegistryRowMailMonk->emailAccount;
                                $emailAccountSendQty = $billRegistryRowMailMonk->emailAccountSendQty;
                                $emailAccountCampaignQty = $billRegistryRowMailMonk->emailAccountCampaignQty;
                                $valueContractRow = $billRegistryRowMailMonk->value;
                                $billingDay = $billRegistryRowMailMonk->billingDay;
                                $typePaymentId = $billRegistryRowMailMonk->typePaymentId;
                                $periodTypeCharge = $billRegistryRowMailMonk->periodTypeCharge;
                                $sellingFeeCommision = $billRegistryRowMailMonk->sellingFeeCommision;
                                $feeCreditCardCommission = $billRegistryRowMailMonk->feeCreditCardCommission;
                                $dayChargeFeeCreditCardCommission = $billRegistryRowMailMonk->dayChargeFeeCreditCardCommission;
                                $feeCodCommission = $billRegistryRowMailMonk->feeCodCommission;
                                $dayChargeFeeCodCommission = $billRegistryRowMailMonk->dayChargeFeeCodCommission;
                                $feeBankTransferCommission = $billRegistryRowMailMonk->feeBankTransferCommission;
                                $dayChargeFeeBankTransferCommission = $billRegistryRowMailMonk->dayChargeFeeBankTransferCommission;
                                $feePaypalCommission = $billRegistryRowMailMonk->feePaypalCommission;
                                $dayChargeFeePaypalCommission = $billRegistryRowMailMonk->dayChargeFeePaypalCommission;
                                $chargeDeliveryIsActive = $billRegistryRowMailMonk->chargeDeliveryIsActive;
                                $feeCostDeliveryCommission = $billRegistryRowMailMonk->feeCostDeliveryCommission;
                                $periodTypeChargeDelivery = $billRegistryRowMailMonk->periodTypeChargeDelivery;
                                $deliveryTypePaymentId = $billRegistryRowMailMonk->deliveryTypePaymentId;
                                $chargePaymentIsActive = $billRegistryRowMailMonk->chargePaymentIsActive;
                                $feeCostCommissionPayment = $billRegistryRowMailMonk->feeCostCommissionPayment;
                                $periodTypeChargePayment = $billRegistryRowMailMonk->periodTypeChargePayment;
                                $paymentTypePaymentId = $billRegistryRowMailMonk->paymentTypePaymentId;
                                break;

                        }

                        $billRegistryContractRowDetail = $billRegistryContractRowDetailRepo->findBy(['billRegistryContractRowId' => $contractRowId]);
                        foreach ($billRegistryContractRowDetail as $rowDetail) {
                            $billRegistryProduct = $billRegistryProductRepo->findOneBy(['id' => $rowDetail->billRegistryProductId]);
                            $nameProduct = $billRegistryProduct->nameProduct;
                            $codeProduct = $billRegistryProduct->codeProduct;
                            $detailProductDescription = '';
                            $billRegistryProductDetail = $billRegistryProductDetailRepo->findBy(['billRegistryProductId' => $billRegistryProduct->id]);
                            foreach ($billRegistryProductDetail as $productDetailDescription) {
                                $detailProductDescription .= $productDetailDescription->detailDescription . '<br>';


                            }
                            $billRegistryPriceList = $billRegistryPriceListRepo->findOneBy(['billRegistryProductId' => $billRegistryProduct->id,'billRegistryClientId' => $billRegistryClientId,'isActive' => 1]);
                            $priceRow = $billRegistryPriceList->price;

                            $billRegistryTypeTaxes = $billRegistryTypeTaxesRepo->findOneBy(['id' => $rowDetail->billRegistryTypeTaxesId]);
                            $perc = $billRegistryTypeTaxes->perc;
                            $qty = $rowDetail->qty;
                            $netPriceRow = $priceRow * $qty;
                            $vatRow = $netPriceRow / 100 * $perc;
                            $grossTotalRow = $netPriceRow + $vatRow;
                            $netTotal += $netPriceRow;
                            $vat += $vatRow;
                            $grossTotal += $grossTotalRow;
                            $rowInvoiceDetail[] = [
                                'billRegistryProductId' => $billRegistryProduct->id,
                                'description' => $codeProduct . '-' . $nameProduct . '<br>' . $detailProductDescription,
                                'qty' => $qty,
                                'priceRow' => $priceRow,
                                'netPrice' => $netPriceRow,
                                'vatRow' => $vatRow,
                                'grossTotalRow' => $grossTotalRow,
                                'billRegistryTypeTaxesId' => $rowDetail->billRegistryTypeTaxesId,
                                'billRegistryTypeTaxesDesc' => $billRegistryTypeTaxes->perc,
                                'billRegistryContractId' => $contractId,
                                'billRegistryContractRowId' => $contractRowId,
                                'billRegistryContractRowDetailId' => $rowDetail->id];

                        }
                        $day = (new \DateTime())->format('d');
                        if ($day == $billingDay) {
                            if ($netTotal != 0) {
                                $today = new \DateTime();
                                $invoiceDate = $today->format('Y-m-d H:i:s');
                                $todayInvoice = $today->format('d-m-Y');
                                $year = (new \DateTime())->format('Y');
                                $billRegistryClient = $billRegistryClientRepo->findOneBy(['id' => $billRegistryClientId]);
                                $country = \Monkey::app()->repoFactory->create('Country')->findOneBy(['id' => $billRegistryClient->countryId]);
                                $isExtraUe = $country->extraue;
                                $shopHub = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => 57]);
                                $intestation = $shopHub->intestation;
                                $intestation2 = $shopHub->intestation2;
                                $address = $shopHub->address;
                                $address2 = $shopHub->address2;
                                $iva = $shopHub->iva;
                                $tel = $shopHub->tel;
                                $email = $shopHub->email;
                                $logoSite = $shopHub->logoSite;
                                $logoThankYou = $shopHub->logoThankYou;

                                $em = $this->app->entityManagerFactory->create('ShopHasCounter');
                                if ($isExtraUe == "1") {
                                    $invoiceNumber = $em->query('SELECT (shc.invoiceExtraUeCounter+1) as `new`  from ShopHasCounter shc
                                            join Shop s ON shc.shopId=s.id where shc.shopId=? and shc.invoiceYear= ?',[57,$year])->fetchAll()[0]['new'];
                                    $invoiceType = $shopHub->invoiceExtraUe;
                                } else {
                                    $invoiceNumber = $em->query('SELECT (shc.invoiceCounter+1) as `new` from ShopHasCounter shc
                                            join Shop s ON shc.shopId=s.id where shc.shopId=? and shc.invoiceYear=?',[57,$year])->fetchAll()[0]['new'];
                                    $invoiceType = $shopHub->invoiceUe;
                                }


                                $customerTaxes = $billRegistryTypeTaxesRepo->findOneBy(['id' => $billRegistryClientBillingInfo->billRegistryTypeTaxesId]);

                                $billRegistryInvoice = \Monkey::app()->repoFactory->create('BillRegistryInvoice')->getEmptyEntity();
                                $billRegistryInvoice->invoiceNumber = $invoiceNumber;
                                $billRegistryInvoice->invoiceYear = $year;
                                $billRegistryInvoice->invoiceType = $invoiceType;
                                $billRegistryInvoice->invoiceSiteChar = 'W';
                                $billRegistryInvoice->billRegistryClientId = $billRegistryClientId;
                                $billRegistryInvoice->billRegistryTypePaymentId = $typePaymentId;
                                $billRegistryInvoice->billRegistryClientBillingInfoId = $billRegistryClientBillingInfo->id;
                                $billRegistryInvoice->netTotal = $netTotal;
                                if ($isExtraUe != '1') {
                                    $billRegistryInvoice->vat = $vat;
                                    $billRegistryInvoice->grossTotal = $grossTotal;
                                    $realTotal = $grossTotal;
                                } else {
                                    $billRegistryInvoice->vat = 0;
                                    $billRegistryInvoice->grossTotal = $netTotal;
                                    $realTotal = $netTotal;
                                }
                                $billRegistryInvoice->invoiceDate = $invoiceDate;
                                $billRegistryInvoice->automaticInvoice = $automaticInvoice;
                                $billRegistryInvoice->insert();
                                $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryInvoice ',[])->fetchAll();
                                foreach ($res as $result) {
                                    $lastBillRegistryInvoiceId = $result['id'];
                                }

                                $billRegistryTypePayment = $billRegistryTypePaymentRepo->findOneBy(['id' => $typePaymentId]);
                                $namePayment = $billRegistryTypePayment->name;

                                $filterBillRegistryTypePayment = $billRegistryTypePaymentRepo->findBy(['name' => $namePayment]);
                                $numberRate = 1;
                                foreach ($filterBillRegistryTypePayment as $rowsPayment) {
                                    $billRegistryTimeTable = $billRegistryTimeTableRepo->getEmptyEntity();
                                    $billRegistryTimeTable->typeDocument = '7';
                                    $billRegistryTimeTable->billRegistryInvoiceId = $lastBillRegistryInvoiceId;
                                    $amountRate = $realTotal / 100 * $rowsPayment->prc;
                                    $dateNow=new \DateTime();
                                    if ($rowsPayment->day == '0') {

                                        $modPayment = $dateNow->modify('+ ' . $rowsPayment->numDay . ' day');
                                        $isWeekEnd = $dateNow->format('D');

                                        if ($isWeekEnd == 'Sat' || $isWeekEnd == 'Sun') {
                                            $modPayment = $dateNow->modify('+ 2 day');
                                        }
                                        $estimatedPayment = $modPayment->format('d-m-Y');
                                        $dbEstimatedPayment = $modPayment->format('Y-m-d H:i:s');


                                    } elseif ($rowsPayment->day == '15') {
                                        if ($day15 <= 16) {
                                            $modPayment = $today->modify('+ ' . $rowsPayment->numDay . 'day');
                                            $day15 = $modPayment->format('d');
                                            $month15 = $modPayment->format('m');
                                            $year15 = $modPayment->format('Y');
                                            $estimatedPayment = '15' . '-' . $mont15 . '-' . $year15;
                                            $dbEstimatedPaymentTemp = $year15 . '-' . $month15 . '-' . $day15;
                                            $dbEstimatedPaymentTemp2 = new \DateTime($dbEstimatedPaymentTemp);
                                            $dbEstimatedPayment = $dbEstimatedPaymentTemp2->format('Y-m-d H:i:s');
                                        } else {
                                            $modPayment = $today->modify('+ 60 day');

                                            $day15 = $modPayment->format('d');
                                            $month15 = $modPayment->format('m');
                                            $year15 = $modPayment->format('Y');
                                            $isWeekEnd = $modPayment->format('D');
                                            if ($isWeekEnd == 'Sat' || $isWeekEnd == 'Sun') {
                                                $estimatedPayment = '18' . '-' . $mont15 . '-' . $year15;
                                                $dbEstimatedPaymentTemp = $year15 . '-' . $month15 . '-18';
                                            } else {
                                                $estimatedPayment = '15' . '-' . $mont15 . '-' . $year15;
                                                $dbEstimatedPaymentTemp = $year15 . '-' . $month15 . '-' . $day15;
                                            }

                                            $dbEstimatedPaymentTemp2 = new \DateTime($dbEstimatedPaymentTemp);
                                            $dbEstimatedPayment = $dbEstimatedPaymentTemp2->format('Y-m-d H:i:s');
                                        }


                                    } elseif ($rowsPayment->day == '-1') {
                                        $modPayment = $today->modify('+ ' . $rowsPayment->numDay . 'day');
                                        $day30 = $modPayment->format('d');
                                        $month30 = $modPayment->format('m');
                                        $year30 = $modPayment->format('Y');
                                        $isWeekEnd = $modPayment->format('D');
                                        if ($isWeekEnd == 'Sat' || $isWeekEnd == 'Sun') {
                                            $estimatedPayment = '28' . '-' . $mont30 . '-' . $year30;
                                            $dbEstimatedPaymentTemp = $year30 . '-' . $month30 . '-28';
                                        } else {
                                            $estimatedPayment = '30' . '-' . $mont30 . '-' . $year30;
                                            $dbEstimatedPaymentTemp = $year30 . '-' . $month30 . '-' . $day30;
                                        }

                                        $dbEstimatedPaymentTemp2 = new \DateTime($dbEstimatedPaymentTemp);
                                        $dbEstimatedPayment = $dbEstimatedPaymentTemp2->format('Y-m-d H:i:s');

                                    }

                                    $billRegistryTimeTable->description = money_format('%.2n',$amountRate) . '  &euro; da corrisponedere entro il ' . $estimatedPayment;
                                    $billRegistryTimeTable->dateEstimated = $dbEstimatedPayment;
                                    $billRegistryTimeTable->amountPayment = $amountRate;
                                    $billRegistryTimeTable->insert();

                                }


                                $invoiceText = '';
                                $invoiceText .= addslashes('
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>');
                                $invoiceText .= addslashes('<meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<link rel="icon" type="image/x-icon" sizes="32x32" href="/assets/img/favicon32.png"/>
<link rel="icon" type="image/x-icon" sizes="256x256" href="/assets/img/favicon256.png"/>
<link rel="icon" type="image/x-icon" sizes="16x16" href="/assets/img/favicon16.png"/>
<link rel="apple-touch-icon" type="image/x-icon" sizes="256x256" href="/assets/img/favicon256.png"/>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta content="" name="description"/>
<meta content="" name="author"/>
<script>
    paceOptions = {
        ajax: {ignoreURLs: [\'/blueseal/xhr/TemplateFetchController\', \'/blueseal/xhr/CheckPermission\']}
    }
</script>
    <link type="text/css" href="https://www.iwes.pro/assets/css/pace.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" media="screen,print"/>
<link type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" media="screen,print"/>
<link type="text/css" href="https://code.jquery.com/ui/1.11.4/themes/flick/jquery-ui.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://s3-eu-west-1.amazonaws.com/bamboo-css/jquery.scrollbar.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://s3-eu-west-1.amazonaws.com/bamboo-css/bootstrap-colorpicker.min.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://github.com/mar10/fancytree/blob/master/dist/skin-common.less" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.fancytree/2.24.0/skin-bootstrap/ui.fancytree.min.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/css/selectize.min.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/basic.min.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://www.iwes.pro/assets/css/ui.dynatree.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.6.16/summernote.min.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700,300" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://raw.githubusercontent.com/kleinejan/titatoggle/master/dist/titatoggle-dist-min.css" rel="stylesheet" media="screen,print"/>
<link type="text/css" href="https://www.iwes.pro/assets/css/pages-icons.css" rel="stylesheet" media="screen,print"/>
<link type="text/css" href="https://www.iwes.pro/assets/css/pages.css" rel="stylesheet" media="screen,print"/>
<link type="text/css" href="https://www.iwes.pro/assets/css/style.css" rel="stylesheet" media="screen,print"/>
<link type="text/css" href="https://www.iwes.pro/assets/css/fullcalendar.css" rel="stylesheet" media="screen,print"/>
<script  type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
<script  type="application/javascript" src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script  type="application/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script  type="application/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script defer type="application/javascript" src="https://www.iwes.pro/assets/js/pages.js"></script>
<script defer type="application/javascript" src="https://www.iwes.pro/assets/js/blueseal.prototype.js"></script>
<script defer type="application/javascript" src="https://www.iwes.pro/assets/js/blueseal.ui.js"></script>
<script defer type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
<script defer type="application/javascript" src="https://cdn.jsdelivr.net/jquery.bez/1.0.11/jquery.bez.min.js"></script>
<script defer type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/unveil/1.3.0/jquery.unveil.min.js"></script>
<script defer type="application/javascript" src="https://s3-eu-west-1.amazonaws.com/bamboo-js/jquery.scrollbar.min.js"></script>
<script defer type="application/javascript" src="https://www.iwes.pro/assets/js/Sortable.min.js"></script>
<script defer type="application/javascript" src="https://s3-eu-west-1.amazonaws.com/bamboo-js/bootstrap-colorpicker.min.js"></script>
<script defer type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.fancytree/2.24.0/jquery.fancytree-all.min.js"></script>
<script defer type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/js/standalone/selectize.min.js"></script>
<script defer type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.js"></script>
<script defer type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone-amd-module.min.js"></script>
<script defer type="application/javascript" src="https://cdn.jsdelivr.net/jquery.dynatree/1.2.4/jquery.dynatree.min.js"></script>
<script defer type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.6.16/summernote.min.js"></script>
<script defer type="application/javascript" src="https://s3-eu-west-1.amazonaws.com/bamboo-js/summernote-it-IT.js"></script>
<script defer type="application/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js"></script>
<script defer type="application/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/additional-methods.min.js"></script>
<script defer type="application/javascript" src="https://www.iwes.pro/assets/js/blueseal.kickstart.js"></script>
<script  type="application/javascript" src="https://www.iwes.pro/assets/js/monkeyUtil.js"></script>
<script defer type="application/javascript" src="https://www.iwes.pro/assets/js/invoice_print.js"></script>
<script defer async type="application/javascript" src="https://www.iwes.pro/assets/js/blueseal.common.js"></script>
    <title>BlueSeal - Stampa fattura</title>
    <style type="text/css">');


                                $invoiceText .= '
        @page {
            size: A4;
            margin: 5mm 0mm 0mm 0mm;
        }

        @media print {
            body {
                zoom: 100%;
                width: 800px;
                height: 1100px;
                overflow: hidden;
            }

            .container {
                width: 100%;
            }

            .newpage {
                page-break-before: always;
                page-break-after: always;
                page-break-inside: avoid;
            }

            @page {
                size: A4;
                margin: 5mm 0mm 0mm 0mm;
            }

            .cover {
                display: none;
            }

            .page-container {
                display: block;
            }

            /*remove chrome links*/
            a[href]:after {
                content: none !important;
            }

            .col-md-1,
            .col-md-2,
            .col-md-3,
            .col-md-4,
            .col-md-5,
            .col-md-6,
            .col-md-7,
            .col-md-8,
            .col-md-9,
            .col-md-10,
            .col-md-11,
            .col-md-12 {
                float: left;
            }

            .col-md-12 {
                width: 100%;
            }

            .col-md-11 {
                width: 91.66666666666666%;
            }

            .col-md-10 {
                width: 83.33333333333334%;
            }

            .col-md-9 {
                width: 75%;
            }

            .col-md-8 {
                width: 66.66666666666666%;
            }

            .col-md-7 {
                width: 58.333333333333336%;
            }

            .col-md-6 {
                width: 50%;
            }

            .col-md-5 {
                width: 41.66666666666667%;
            }

            .col-md-4 {
                width: 33.33333333333333%;
            }

            .col-md-3 {
                width: 25%;
            }

            .col-md-2 {
                width: 16.666666666666664%;
            }

            .col-md-1 {
                width: 8.333333333333332%;
            }

            .col-md-pull-12 {
                right: 100%;
            }

            .col-md-pull-11 {
                right: 91.66666666666666%;
            }

            .col-md-pull-10 {
                right: 83.33333333333334%;
            }

            .col-md-pull-9 {
                right: 75%;
            }

            .col-md-pull-8 {
                right: 66.66666666666666%;
            }

            .col-md-pull-7 {
                right: 58.333333333333336%;
            }

            .col-md-pull-6 {
                right: 50%;
            }

            .col-md-pull-5 {
                right: 41.66666666666667%;
            }

            .col-md-pull-4 {
                right: 33.33333333333333%;
            }

            .col-md-pull-3 {
                right: 25%;
            }

            .col-md-pull-2 {
                right: 16.666666666666664%;
            }

            .col-md-pull-1 {
                right: 8.333333333333332%;
            }

            .col-md-pull-0 {
                right: 0;
            }

            .col-md-push-12 {
                left: 100%;
            }

            .col-md-push-11 {
                left: 91.66666666666666%;
            }

            .col-md-push-10 {
                left: 83.33333333333334%;
            }

            .col-md-push-9 {
                left: 75%;
            }

            .col-md-push-8 {
                left: 66.66666666666666%;
            }

            .col-md-push-7 {
                left: 58.333333333333336%;
            }

            .col-md-push-6 {
                left: 50%;
            }

            .col-md-push-5 {
                left: 41.66666666666667%;
            }

            .col-md-push-4 {
                left: 33.33333333333333%;
            }

            .col-md-push-3 {
                left: 25%;
            }

            .col-md-push-2 {
                left: 16.666666666666664%;
            }

            .col-md-push-1 {
                left: 8.333333333333332%;
            }

            .col-md-push-0 {
                left: 0;
            }

            .col-md-offset-12 {
                margin-left: 100%;
            }

            .col-md-offset-11 {
                margin-left: 91.66666666666666%;
            }

            .col-md-offset-10 {
                margin-left: 83.33333333333334%;
            }

            .col-md-offset-9 {
                margin-left: 75%;
            }

            .col-md-offset-8 {
                margin-left: 66.66666666666666%;
            }

            .col-md-offset-7 {
                margin-left: 58.333333333333336%;
            }

            .col-md-offset-6 {
                margin-left: 50%;
            }

            .col-md-offset-5 {
                margin-left: 41.66666666666667%;
            }

            .col-md-offset-4 {
                margin-left: 33.33333333333333%;
            }

            .col-md-offset-3 {
                margin-left: 25%;
            }

            .col-md-offset-2 {
                margin-left: 16.666666666666664%;
            }

            .col-md-offset-1 {
                margin-left: 8.333333333333332%;
            }

            .col-md-offset-0 {
                margin-left: 0;
            }
        }
    </style>
</head>
<body class="fixed-header">

<!--start-->
<div class="container container-fixed-lg">

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="invoice padding-50 sm-padding-10">
                <div>
                    <div class="pull-left">
                        <!--logo negozio-->
                        <img width="235" height="47" alt="" class="invoice-logo"
                             data-src-retina=' . '"/assets/img/' . $logoSite . '" data-src=' . '"/assets/img/' . $logoSite . '" src=' . '"/assets/img/' . $logoSite . '">
                        <!--indirizzo negozio-->
                        <br><br>
                        <address class="m-t-10"><b>' . $intestation . '
                                <br>' . $intestation2 . '</b>
                            <br>' . $address . '
                            <br>' . $address2 . '
                            <br>' . $iva . '
                            <br>' . $tel . '
                            <br>' . $email . '
                        </address>
                        <br>
                        <div>
                            <div class="pull-left font-montserrat all-caps small">
                                 <strong>Fattura</strong>  ' . $invoiceNumber . "/" . $invoiceType . '<strong> del </strong>' . $todayInvoice . '
                            </div>
                        </div>
                        <div><br>
                            <div class="pull-left font-montserrat small"><strong>';

                                if ($isExtraUe != '1') {
                                    $referOrder = 'Rif. Contratto N. ';
                                } else {
                                    $referOrder = 'Contract Reference N:';
                                }
                                $invoiceText .= $referOrder;
                                $invoiceText .= '</strong>';
                                $date = new \DateTime($billRegistryContract->dateCreate);
                                if ($isExtraUe != '1') {
                                    $invoiceHeaderText='Fattura';
                                    $refertOrderIdandDate = '  ' . $billRegistryContract->id . ' del ' . $date->format('d-m-Y');
                                } else {
                                    $invoiceHeaderText='Invoice';
                                    $refertOrderIdandDate = '  ' . $billRegistryContract->id . ' date ' . $date->format('Y-d-m');
                                }
                                $invoiceText .= $refertOrderIdandDate . '</div>
                        </div>
                        <div><br>
                            <div class="pull-left font-montserrat small"><strong>';

                                $invoiceText .= '</strong>
                           </div>
                        </div>
                    </div>
                    <div class="pull-right sm-m-t-0">
                        <h2 class="font-montserrat all-caps hint-text">' . $invoiceHeaderText . '</h2>

                        <div class="col-md-12 col-sm-height sm-padding-20">
                            <p class="small no-margin">';
                                if ($isExtraUe != '1') {
                                    $invoiceText .= 'Intestata a';
                                } else {
                                    $invoiceText .= 'Invoice Address';
                                }
                                $invoiceText .= '</p>';

                                $invoiceText .= '<h5 class="semi-bold m-t-0 no-margin">' . addslashes($billRegistryClient->companyName) . '</h5>';
                                $invoiceText .= '<address>';
                                $invoiceText .= '<strong>';


                                $invoiceText .= addslashes($billRegistryClient->address . ' ' . $billRegistryClient->extra);
                                $invoiceText .= '<br>' . addslashes($billRegistryClient->zipcode . ' ' . $billRegistryClient->city . ' (' . $billRegistryClient->province . ')');
                                $countryRepo = \Monkey::app()->repoFactory->create('Country')->findOneBy(['id' => $billRegistryClient->countryId]);
                                $invoiceText .= '<br>' . $countryRepo->name;
                                if ($isExtraUe != '1') {
                                    $transfiscalcode = 'C.FISC. o P.IVA: ';
                                } else {
                                    $transfiscalcode = 'VAT: ';
                                }
                                $invoiceText .= '<br>';

                                $invoiceText .= $transfiscalcode . $billRegistryClient->vatNumber;


                                $invoiceText .= '</strong>';
                                $invoiceText .= '</address>';
                                $invoiceText .= '<div class="clearfix"></div><br><p class="small no-margin">';


                                $invoiceText .= '</p><address>';

                                $invoiceText .= '<strong>';

                                $invoiceText .= '<br>';
                                $invoiceText .= '<br>';

                                $invoiceText .= '<br>';
                                $invoiceText .= '</address>';
                                $invoiceText .= '</div>';
                                $invoiceText .= '</div>';
                                $invoiceText .= '</div>';
                                $invoiceText .= '</div>';
                                $invoiceText .= '<table class="table invoice-table m-t-0">';
                                $invoiceText .= '<thead>
                    <!--tabella prodotti-->
                    <tr>';
                                $invoiceText .= '<th class="small">';
                                if ($isExtraUe != "1") {
                                    $invoiceText .= 'Descrizione Prodotto';
                                } else {
                                    $invoiceText .= 'Description';
                                }
                                $invoiceText .= '</th>';
                                $invoiceText .= '<th class="text-center small">';
                                if ($isExtraUe != "1") {
                                    $invoiceText .= 'Importo';
                                } else {
                                    $invoiceText .= 'Amount';
                                }
                                $invoiceText .= '</th>';
                                $invoiceText .= '<th class="text-center small">';
                                if ($isExtraUe != "1") {
                                    $invoiceText .= 'iva';
                                } else {
                                    $invoiceText .= 'vat Total Row';
                                }
                                $invoiceText .= '<th class="text-center small">';
                                if ($isExtraUe != "1") {
                                    $invoiceText .= 'Importo Totale';
                                } else {
                                    $invoiceText .= 'Tot Row';
                                }
                                $invoiceText .= '</th>';


                                $invoiceText .= '</th>';

                                $invoiceText .= '</tr></thead><tbody>';

                                if ($rowInvoiceExtraFee != null) {
                                    foreach ($rowInvoiceExtraFee as $rowInvoice) {
                                        $rowInvoiceInsert = $billRegistryInvoiceRowRepo->getEmptyEntity();
                                        $rowInvoiceInsert->billRegistryInvoiceId = $invoiceNumber;
                                        $rowInvoiceInsert->billRegistryProductId = $rowInvoice['billRegistryProductId'];
                                        $rowInvoiceInsert->description = $rowInvoice['description'];
                                        $rowInvoiceInsert->qty = $rowInvoice['qty'];
                                        $rowInvoiceInsert->priceRow = $rowInvoice['priceRow'];
                                        $rowInvoiceInsert->vatRow = $rowInvoice['vatRow'];
                                        $rowInvoiceInsert->grossTotalRow = $rowInvoice['grossTotalRow'];
                                        $rowInvoiceInsert->billRegistryTypeTaxesId = $rowInvoice['billRegistryTypeTaxesId'];
                                        $rowInvoiceInsert->billRegistryContractId = $rowInvoice['billRegistryContractId'];
                                        $rowInvoiceInsert->billRegistryContractRowId = $rowInvoice['billRegistryContractRowId'];
                                        $rowInvoiceInsert->billRegistryContractRowDetailId = $rowInvoice['billRegistryContractRowDetailId'];
                                        $rowInvoiceInsert->insert();
                                        $invoiceText .= '<tr><td class="text-center">' . $rowInvoice['description'] . '</td>';
                                        $invoiceText .= '<td class="text-center">' . money_format('%.2n',$rowInvoice['priceRow']) . ' &euro;' . '</td>';
                                        $customerTaxesRow = \Monkey::app()->repoFactory->create('BillRegistryTypeTaxes')->findOneBy(['id' => $rowInvoice['billRegistryTypeTaxesId']]);
                                        $invoiceText .= '<td class="text-center">' . $customerTaxesRow->perc . '%: ' . money_format('%.2n',$rowInvoice['vatRow']) . ' &euro;' . '</td>';
                                        $invoiceText .= '<td class="text-center">' . money_format('%.2n',$rowInvoice['grossTotalRow']) . ' &euro;' . '</td></tr>';
                                    }
                                }
                                if ($rowInvoiceDetail != null) {
                                    foreach ($rowInvoiceDetail as $rowInvoice) {
                                        $rowInvoiceInsert = $billRegistryInvoiceRowRepo->getEmptyEntity();
                                        $rowInvoiceInsert->billRegistryInvoiceId = $invoiceNumber;
                                        $rowInvoiceInsert->billRegistryProductId = $rowInvoice['billRegistryProductId'];
                                        $rowInvoiceInsert->description = $rowInvoice['description'];
                                        $rowInvoiceInsert->qty = $rowInvoice['qty'];
                                        $rowInvoiceInsert->priceRow = $rowInvoice['priceRow'];
                                        $rowInvoiceInsert->vatRow = $rowInvoice['vatRow'];
                                        $rowInvoiceInsert->grossTotalRow = $rowInvoice['grossTotalRow'];
                                        $rowInvoiceInsert->billRegistryTypeTaxesId = $rowInvoice['billRegistryTypeTaxesId'];
                                        $rowInvoiceInsert->billRegistryContractId = $rowInvoice['billRegistryContractId'];
                                        $rowInvoiceInsert->billRegistryContractRowId = $rowInvoice['billRegistryContractRowId'];
                                        $rowInvoiceInsert->billRegistryContractRowDetailId = $rowInvoice['billRegistryContractRowDetailId'];
                                        $rowInvoiceInsert->insert();
                                        $invoiceText .= '<tr><td class="text-center">' . $rowInvoice['description'] . '</td>';
                                        $invoiceText .= '<td class="text-center">' . money_format('%.2n',$rowInvoice['priceRow']) . ' &euro;' . '</td>';
                                        $customerTaxesRow = \Monkey::app()->repoFactory->create('BillRegistryTypeTaxes')->findOneBy(['id' => $rowInvoice['billRegistryTypeTaxesId']]);
                                        $invoiceText .= '<td class="text-center">' . $customerTaxesRow->perc . '%: ' . money_format('%.2n',$rowInvoice['vatRow']) . ' &euro;' . '</td>';
                                        $invoiceText .= '<td class="text-center">' . money_format('%.2n',$rowInvoice['grossTotalRow']) . ' &euro;' . '</td></tr>';
                                    }

                                }
                                $invoiceText .= '</tbody><br><tr class="text-left font-montserrat small">
                        <td style="border: 0px"></td>
                        <td style="border: 0px"></td>
                        <td style="border: 0px">
                            <strong>';
                                if ($isExtraUe != 1) {
                                    $invoiceText .= 'Totale Netto';
                                } else {
                                    $invoiceText .= 'Total Amount ';
                                }
                                $invoiceText .= '</strong></td>
                        <td style="border: 0px"
                            class="text-center">' . money_format('%.2n',$netTotal) . ' &euro;' . '</td>
                    </tr>';

                                $invoiceText .= '<tr style="border: 0px" class="text-left font-montserrat small hint-text">
                        <td style="border: 0px"></td>
                        <td style="border: 0px"></td>
                        <td style="border: 0px">
                            <strong>';
                                if ($isExtraUe != "1") {
                                    $invoiceText .= 'Totale Tasse';
                                } else {
                                    $invoiceText .= 'Total Taxes non imponibile ex art 8/A  D.P.R. n. 633/72 ';
                                }
                                $invoiceText .= '</strong></td>';
                                if ($isExtraUe != 1) {
                                    $invoiceText .= '<td style="border: 0px" class="text-center">' . money_format('%.2n',$vat) . ' &euro;' . '</td></tr>';
                                } else {
                                    $invoiceText .= '<td style="border: 0px" class="text-center">' . money_format('%.2n',0) . ' &euro;' . '</td></tr>';
                                }

                                $invoiceText .= '<tr style="border: 0px" class="text-left font-montserrat small hint-text">
                        <td style="border: 0px"></td>
                        <td style="border: 0px"></td>
                        <td style="border: 0px">
                            <strong>';
                                if ($isExtraUe != "1") {
                                    $invoiceText .= 'Totale Dovuto';
                                } else {
                                    $invoiceText .= 'Total Invoice';
                                }
                                $invoiceText .= '</strong></td>';
                                if ($isExtraUe != "1") {
                                    $invoiceText .= '<td style="border: 0px" class="text-center">' . money_format('%.2n',$grossTotal) . ' &euro;' . '</td></tr>';
                                } else {
                                    $invoiceText .= '<td style="border: 0px" class="text-center">' . money_format('%.2n',$netTotal) . ' &euro;' . '</td></tr>';
                                }
                                $invoiceText .= '<tr style="border: 0px" class="text-center">
                        <td colspan="2" style="border: 0px">';
                                if ($isExtraUe != "1") {
                                    $invoiceText .= 'Modalità di pagamento ' . $namePayment;
                                } else {
                                    $invoiceText .= 'Type Payment';
                                }

                                $invoiceText .= '</td>';
                                $invoiceText .= ' <td style="border: 0px"></td>';
                                $invoiceText .= ' <td style="border: 0px"></td></tr>';
                                $billRegistryTimeTableFind = $billRegistryTimeTableRepo->findBy(['billRegistryInvoiceId' => $lastBillRegistryInvoiceId]);
                                foreach ($billRegistryTimeTableFind as $paymentInvoice) {
                                    $invoiceText .= '<tr style="border: 0px" class="text-center">
                        <td colspan="2" style="border: 0px">';
                                    $invoiceText .= $paymentInvoice->description;
                                    $invoiceText .= '</td>';
                                    $invoiceText .= ' <td style="border: 0px"></td>';
                                    $invoiceText .= ' <td style="border: 0px"></td></tr>';
                                }
                                $invoiceText .= '</table>
            </div>
            <br>
            <br>
            <br>
            <br>
            <br>
            <div>
              <center><img alt="" class="invoice-thank" data-src-retina="/assets/img/' . $logoThankYou . '"
                             data-src="/assets/img/' . $logoThankYou . '" src="/assets/img/' . $logoThankYou . '">
                </center>
            </div>
            <br>
            <br>
        </div>
    </div>
</div><!--end-->';

                                $invoiceText .= addslashes('<script type="application/javascript">
    $(document).ready(function () {

        Pace.on(\'done\', function () {

            setTimeout(function () {
                window.print();

                setTimeout(function () {
                    window.close();
                }, 1);

            }, 200);

        });
    });
</script>
</body>
</html>');
                                $updateBillRegistryInvoice = \Monkey::app()->repoFactory->create('BillRegistryInvoice')->findOneBy(['id' => $lastBillRegistryInvoiceId]);
                                $updateBillRegistryInvoice->invoiceText = stripslashes($invoiceText);
                                $updateBillRegistryInvoice->update();
                                $shopHasCounter=\Monkey::app()->repoFactory->create('ShopHasCounter')->findOneBy(['shopId'=>57,'invoiceYear'=>$year]);
                                if($isExtraUe!="1"){
                                    $shopHasCounter->invoiceCounter=$invoiceNumber;
                                }else{
                                    $shopHasCounter->invoiceextraUeCounter=$invoiceNumber;
                                }
                                $shopHasCounter->update();

                            }
                        }
                    }
                } else {
                    continue;
                }
            }
        } catch (\Throwable $e) {
            $this->report('CGenerateCustomerInvoiceJob','error ',$e);
        }


    }
}
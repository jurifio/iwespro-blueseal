<?php

namespace bamboo\blueseal\jobs;

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
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $billRegistrySocialRepo = \Monkey::app()->repoFactory->create('BillRegistrySocial');
        $campaignRepo = \Monkey::app()->repoFactory->create('Campaign');
        $marketplaceRepo = \Monkey::app()->repoFactory->create('Marketplace');
        $marketplaceAccountRepo = \Monkey::app()->repoFactory->create('MarketplaceAccount');
        $billRegistryTypeTaxesRepo = \Monkey::app()->repoFactory->create('BillRegistryTypeTaxes');
        $billRegistryTypePaymentRepo = \Monkey::app()->repoFactory->create('BillRegistryTypePayment');
        $billRegistryClientBillingInfoRepo = \Monkey::app()->repoFactory->create('BillRegistryClientBillingInfoRepo');
        $today = new \DateTime();
        try {
            $billRegistryContracts = $billRegistryContractRepo->findAll();
            foreach ($billRegistryContracts as $billRegistryContract) {
                $contractId = $billRegistryContract->id;
                $billRegistryClientId = $billRegistryContract->billregistryClientId;
                $billRegistryClientAccount = $billRegistryClientAccountRepo->findOneBy(['billRegistryClientId' => $billRegistryClientId]);
                $billRegistryClientBillingInfo=$billRegistryClientBillingInfoRepo->findOneBy(['billRegistryClientId'=>$billRegistryClientId]);
                $shop = $billRegistryClientAccount->shopId;

                $billRegistryContractRow = $billRegistryContractRowRepo->findBy(['billRegistryContractId' => $contractId,'statusId' => '1']);
                $invoiceHeader = [];
                $rowInvoiceDetail = [];
                $rowInvoiceExtraFee=[];
                $grossTotal = 0;
                $netTotal = 0;
                $vat = 0;
                foreach ($billRegistryContractRow as $contractRow) {
                    $contractRowId = $contractRow->id;

                    $typeContract = $contractRow->billRegistryGroupProductId;
                    switch ($typeContract) {
                        case 1:
                            //definizione dei parametri del tipo di contratto
                            $billRegistryRowMonkSource = \Monkey::app()->repoFactory->create('BillRegistryRowMonkSource')->findOneBy(['billRegistryContractRowId' => $contractRowId]);
                            $automaticInvoice = $billRegistryRowMonkSource->automaticInvoice;
                            $valueContractRow = $billRegistryRowMonkSource->value;
                            $billingDay = $billRegistryRowMonkSource->billingDay;
                            $typePaymentId = $billRegistryRowMonkSource->typePaymentId;
                            $periodTypeCharge = $billRegistryRowMonkSource->periodTypeCharge;
                            $sellingFeeCommision = $billRegistryRowMonkSource->sellingFeecommision;
                            $feeCreditCardCommission = $billRegistryRowMonkSource->feeCreditCardCommission;
                            $dayChargeFeeCreditCardCommission = $billRegistryRowMonkSource->dayChargeFeeCreditCardCommission;
                            $feeCodCommission = $billRegistryRowMonkSource->feeCodCommision;
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
                            $creditCardCommission = 0;
                            $bankTransferCommission = 0;
                            $codCommission = 0;
                            $paypalCommission = 0;
                            foreach ($orderLine as $orl) {
                                if ($orl->status == 'ORD_DELIVERED' || $orl->status == 'ORD_SENT' || $orl->status == 'ORD_FRND_PYD') {
                                    if ($orl->isBill == null) {
                                        $order = \Monkey::app()->repoFactory->create('Order')->findOneBy(['id' => $orl->orderId]);

                                        switch ($order->orderPaymentMethodId) {
                                            case 1:
                                                $paypalCommission += $orl->netPrice * $feePaypalCommission;
                                                break;
                                            case 2:
                                                $creditCardCommission += $orl->netPrice * $feeCreditCardCommission;
                                                break;
                                            case 3:
                                                $bankTransferCommission += $orl->netPrice * $feeBankTransferCommission;
                                                break;
                                            case 5:
                                                $codCommission += $orl->netPrice * $feeCodCommission;
                                                break;

                                        }
                                        $costDeliveryCommission=0;
                                        $orderLineHasShipment=\Monkey::app()->repoFactory->create('OrderLineHasShipment')->findBy(['orderLineId'=>$orl->id,'orderId'=>$orl->orderId]);
                                        foreach($orderLineHasShipment as $olhs ){
                                            $shipment=\Monkey::app()->repoFactory->create('Shipment')->findOneBy(['id'=>$olhs->shipmentId]);
                                            if($shipment->realShipmentPrice!=null){
                                                $costDeliveryCommission+=$shipment->realShipmentPrice+($shipment->realShipmentPrice/100*$feeCostDeliveryCommission);
                                            }

                                        }

                                    }
                                } else {
                                    continue;
                                }
                            }
                            $customerTaxes=$billRegistryTypeTaxesRepo->findOneBy(['id'=>$billRegistryClientBillingInfo->billRegistryTypeTaxesId]);
                            $netTotalRow=$paypalCommission+$codCommission+$bankTransferCommission+$creditCardCommission+$costDeliveryCommission;
                            $netTotal+=$netTotalRow;
                            $vatRowTotalExtra=($paypalCommission+$codCommission+$bankTransferCommission+$creditCardCommission+$costDeliveryCommission)/100*$customerTaxes->perc;
                            $vat+=$vatRowTotalExtra;
                            $grossTotal+=$netTotal+$vatRowTotalExtra;
                            if($paypalCommission!=0) {
                                $rowInvoiceExtraFee[] = [
                                    'billRegistryProductId'=> 0,
                                    'description' => 'commissioni Pagamenti Paypal',
                                    'qty' => 1,
                                    'priceRow' => $paypalCommission,
                                    'netPrice' => $paypalCommission,
                                    'vatRow' => $paypalCommission/100*$customerTaxes->perc,
                                    'grossTotalRow' => $paypalCommision+($paypalCommission/100*$customerTaxes->perc),
                                    'billRegistryTypeTaxesId' => $customerTaxes->id,
                                    'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                    'billRegistryContractId'=>$contractId,
                                    'billRegistryContractRowId'=>$contractRowId,
                                    'billRegistryContractRowDetailId' => 0
                                    ];
                            }
                            if ($codCommission!=0){
                                $rowInvoiceExtraFee[] = [
                                    'billRegistryProductId'=> 0,
                                    'description' => 'commissioni Pagamenti Contrassegni',
                                    'qty' => 1,
                                    'priceRow' => $codCommission,
                                    'netPrice' => $codCommission,
                                    'vatRow' => $codCommission/100*$customerTaxes->perc,
                                    'grossTotalRow' => $codCommission+($codCommission/100*$customerTaxes->perc),
                                    'billRegistryTypeTaxesId' => $customerTaxes->id,
                                    'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                    'billRegistryContractId'=>$contractId,
                                    'billRegistryContractRowId'=>$contractRowId,
                                    'billRegistryContractRowDetailId' => 0
                                ];
                            }
                            if ($bankTransferCommission!=0){
                                $rowInvoiceExtraFee[] = [
                                    'billRegistryProductId'=> 0,
                                    'description' => 'commissioni Pagamenti Bonifici Sepa',
                                    'qty' => 1,
                                    'priceRow' => $bankTransferCommission,
                                    'netPrice' => $bankTransferCommission,
                                    'vatRow' => $bankTransferCommission/100*$customerTaxes->perc,
                                    'grossTotalRow' => $bankTransferCommission+($bankTransferCommission/100*$customerTaxes->perc),
                                    'billRegistryTypeTaxesId' => $customerTaxes->id,
                                    'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                    'billRegistryContractId'=>$contractId,
                                    'billRegistryContractRowId'=>$contractRowId,
                                    'billRegistryContractRowDetailId' => 0
                                ];
                            }
                            if ($creditCardCommission!=0){
                                $rowInvoiceExtraFee[] = [
                                    'billRegistryProductId'=> 0,
                                    'description' => 'commissioni Pagamenti Carte di Credito',
                                    'qty' => 1,
                                    'priceRow' => $creditCardCommission,
                                    'netPrice' => $creditCardCommission,
                                    'vatRow' => $creditCardCommission/100*$customerTaxes->perc,
                                    'grossTotalRow' => $creditCardCommission+($creditCardCommission/100*$customerTaxes->perc),
                                    'billRegistryTypeTaxesId' => $customerTaxes->id,
                                    'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                    'billRegistryContractId'=>$contractId,
                                    'billRegistryContractRowId'=>$contractRowId,
                                    'billRegistryContractRowDetailId' => 0
                                ];
                            }
                            if ($creditCardCommission!=0){
                                $rowInvoiceExtraFee[] = [
                                    'billRegistryProductId'=> 0,
                                    'description' => 'commissioni su Spedizioni',
                                    'qty' => 1,
                                    'priceRow' => $creditCardCommission,
                                    'netPrice' => $creditCardCommission,
                                    'vatRow' => $creditCardCommission/100*$customerTaxes->perc,
                                    'grossTotalRow' => $creditCardCommission+($creditCardCommission/100*$customerTaxes->perc),
                                    'billRegistryTypeTaxesId' => $customerTaxes->id,
                                    'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                    'billRegistryContractId'=>$contractId,
                                    'billRegistryContractRowId'=>$contractRowId,
                                    'billRegistryContractRowDetailId' => 0
                                ];
                            }

                            break;
                        case 2:
                            $billRegistryRowMonkAir = \Monkey::app()->repoFactory->create('BillRegistryRowMonkAir')->findOneBy(['billRegistryContractRowId' => $contractRowId]);
                            $automaticInvoice = $billRegistryRowMonkAir->automaticInvoice;
                            $valueContractRow = $billRegistryRowMonkAir->value;
                            $billingDay = $billRegistryRowMonkAir->billingDay;
                            $typePaymentId = $billRegistryRowMonkAir->typePaymentId;
                            $periodTypeCharge = $billRegistryRowMonkAir->periodTypeCharge;
                            $sellingFeeCommision = $billRegistryRowMonkAir->sellingFeecommision;
                            $feeCreditCardCommission = $billRegistryRowMonkAir->feeCreditCardCommission;
                            $dayChargeFeeCreditCardCommission = $billRegistryRowMonkAir->dayChargeFeeCreditCardCommission;
                            $feeCodCommission = $billRegistryRowMonkAir->feeCodCommision;
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


                            $customerTaxes=$billRegistryTypeTaxesRepo->findOneBy(['id'=>$billRegistryClientBillingInfo->billRegistryTypeTaxesId]);
                            $netTotalRow=$valueContractRow;
                            $netTotal+=$netTotalRow;
                            $vatRowTotalExtra=$valueContractRow/100*$customerTaxes->perc;
                            $vat+=$vatRowTotalExtra;
                            $grossTotal+=$netTotal+$vatRowTotalExtra;
                            if($valueContractRow!=0) {
                                $rowInvoiceExtraFee[] = [
                                    'billRegistryProductId'=> 0,
                                    'description' => 'Servizio Monk Air',
                                    'qty' => 1,
                                    'priceRow' => $valueContractRow,
                                    'netPrice' => $valueContractRow,
                                    'vatRow' => $valueContractRow/100*$customerTaxes->perc,
                                    'grossTotalRow' => $valueContractRow+($valueContractRow/100*$customerTaxes->perc),
                                    'billRegistryTypeTaxesId' => $customerTaxes->id,
                                    'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                    'billRegistryContractId'=>$contractId,
                                    'billRegistryContractRowId'=>$contractRowId,
                                    'billRegistryContractRowDetailId' => 0
                                ];
                            }
                            break;
                        case 3:
                            $billRegistryRowMonkEntrySocial = \Monkey::app()->repoFactory->create('BillRegistryRowMonkEntrySocial')->findOneBy(['billRegistryContractRowId' => $contractRowId]);
                            $automaticInvoice = $billRegistryRowMonkEntrySocial->automaticInvoice;
                            $descriptionInvoice = $billRegistryRowMonkEntrySocial->descriptionInvoice;
                            $startUpCostCampaign = $billRegistryRowMonkEntrySocial->startUpCostCampaign;
                            $billingDay = $billRegistryRowMonkEntrySocial->billingDay;
                            $typePaymentId = $billRegistryRowMonkEntrySocial->typePaymentId;
                            $feeAgencyCommision = $billRegistryRowMonkEntrySocial->feeAgencyCommision;
                            $prepaidPaymentIsActive = $billRegistryRowMonkEntrySocial->prepaidPaymentIsActive;
                            $prepaidCost = $billRegistryRowMonkEntrySocial->prepaidCost;
                            $startUpCostIsPaid=$billRegistryRowMonkEntrySocial->startUpCostIsPaid;

                            if($startUpCostIsPaid==null ||$startUpCostIsPaid==0) {
                            $netTotalRowStartUpCost=$startUpCostIsPaid;
                            $vatTotalRowStartUPCost=$netTotalRowStartUpCost/100*$customerTaxes->perc;
                            $vat+=$vatTotalRowStartUPCost;
                            $netTotal+=$netTotalRowStartUpCost;
                            $grossTotal+=$netTotalRowStartUpCost+$vatTotalRowStartUPCost;
                                $rowInvoiceExtraFee[] = [
                                    'billRegistryProductId'=> 0,
                                    'description' => 'Costo di StartUp Campagna',
                                    'qty' => 1,
                                    'priceRow' => $startUpCostIsPaid,
                                    'netPrice' => $startUpCostIsPaid,
                                    'vatRow' => $startUpCostIsPaid/100*$customerTaxes->perc,
                                    'grossTotalRow' => $startUpCostIsPaid+($startUpCostIsPaid/100*$customerTaxes->perc),
                                    'billRegistryTypeTaxesId' => $customerTaxes->id,
                                    'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                    'billRegistryContractId'=>$contractId,
                                    'billRegistryContractRowId'=>$contractRowId,
                                    'billRegistryContractRowDetailId' => 0
                                ];
                            }
                            $netTotalRowPrepaidCost=$prepaidCost;
                            $netTotal+=$netTotalRowPrepaidCost;
                            $vatRowTotalPrepaidCost=$netTotalRowPrepaidCost/100*$customerTaxes->perc;
                            $vat+=$vatRowTotalPrepaidCost;
                            $grossTotal+=$netTotal+$vatRowTotalPrepaidCost;
                            if($prepaidCost!=0) {
                                $rowInvoiceExtraFee[] = [
                                    'billRegistryProductId'=> 0,
                                    'description' => 'Servizio MonkEntrySocial',
                                    'qty' => 1,
                                    'priceRow' => $prepaidCost,
                                    'netPrice' => $prepaidCost,
                                    'vatRow' => $prepaidCost/100*$customerTaxes->perc,
                                    'grossTotalRow' => $prepaidCost+($prepaidCost/100*$customerTaxes->perc),
                                    'billRegistryTypeTaxesId' => $customerTaxes->id,
                                    'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                    'billRegistryContractId'=>$contractId,
                                    'billRegistryContractRowId'=>$contractRowId,
                                    'billRegistryContractRowDetailId' => 0
                                ];
                            }
                            $netTotalRowAgencyCommision=$prepaidCost/100*$feeAgencyCommision;
                            $netTotal+=$netTotalRowAgencyCommision;
                            $vatRowTotalAgencyCommision=$netTotalRowAgencyCommision/100*$customerTaxes->perc;
                            $vat+=$vatRowTotalAgencyCommision;
                            $grossTotal+=$netTotal+$vatRowTotalAgencyCommision;
                            if($prepaidCost!=0) {
                                $rowInvoiceExtraFee[] = [
                                    'billRegistryProductId'=> 0,
                                    'description' => 'Servizio MonkEntrySocial',
                                    'qty' => 1,
                                    'priceRow' => $netTotalRowAgencyCommision,
                                    'netPrice' => $netTotalRowAgencyCommision,
                                    'vatRow' => $vatRowTotalAgencyCommision,
                                    'grossTotalRow' => $netTotalRowAgencyCommision+$vatRowTotalAgencyCommision,
                                    'billRegistryTypeTaxesId' => $customerTaxes->id,
                                    'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                    'billRegistryContractId'=>$contractId,
                                    'billRegistryContractRowId'=>$contractRowId,
                                    'billRegistryContractRowDetailId' => 0
                                ];
                            }


                            break;
                        case 4:
                            $billRegistryRowMonkEntryTraffic = \Monkey::app()->repoFactory->create('BillRegistryRowMonkEntryTraffic')->findOneBy(['billRegistryContractRowId' => $contractRowId]);
                            $automaticInvoice = $billRegistryRowMonkEntryTraffic->automaticInvoice;
                            $descriptionInvoice = $billRegistryRowMonkEntryTraffic->descriptionInvoice;
                            $startUpCostCampaign = $billRegistryRowMonkEntryTraffic->startUpCostCampaign;
                            $billingDay = $billRegistryRowMonkEntryTraffic->billingDay;
                            $typePaymentId = $billRegistryRowMonkEntryTraffic->typePaymentId;
                            $feeAgencyCommision = $billRegistryRowMonkEntryTraffic->feeAgencyCommision;
                            $prepaidPaymentIsActive = $billRegistryRowMonkEntryTraffic->prepaidPaymentIsActive;
                            $prepaidCost = $billRegistryRowMonkEntryTraffic->prepaidCost;
                            $startUpCostIsPaid=$billRegistryRowMonkEntryTraffic->startUpCostIsPaid;

                            if($startUpCostIsPaid==null ||$startUpCostIsPaid==0) {
                                $netTotalRowStartUpCost=$startUpCostIsPaid;
                                $vatTotalRowStartUPCost=$netTotalRowStartUpCost/100*$customerTaxes->perc;
                                $vat+=$vatTotalRowStartUPCost;
                                $netTotal+=$netTotalRowStartUpCost;
                                $grossTotal+=$netTotalRowStartUpCost+$vatTotalRowStartUPCost;
                                $rowInvoiceExtraFee[] = [
                                    'billRegistryProductId'=> 0,
                                    'description' => 'Costo di StartUp Campagna',
                                    'qty' => 1,
                                    'priceRow' => $startUpCostIsPaid,
                                    'netPrice' => $startUpCostIsPaid,
                                    'vatRow' => $startUpCostIsPaid/100*$customerTaxes->perc,
                                    'grossTotalRow' => $startUpCostIsPaid+($startUpCostIsPaid/100*$customerTaxes->perc),
                                    'billRegistryTypeTaxesId' => $customerTaxes->id,
                                    'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                    'billRegistryContractId'=>$contractId,
                                    'billRegistryContractRowId'=>$contractRowId,
                                    'billRegistryContractRowDetailId' => 0
                                ];
                            }
                            $netTotalRowPrepaidCost=$prepaidCost;
                            $netTotal+=$netTotalRowPrepaidCost;
                            $vatRowTotalPrepaidCost=$netTotalRowPrepaidCost/100*$customerTaxes->perc;
                            $vat+=$vatRowTotalPrepaidCost;
                            $grossTotal+=$netTotal+$vatRowTotalPrepaidCost;
                            if($prepaidCost!=0) {
                                $rowInvoiceExtraFee[] = [
                                    'billRegistryProductId'=> 0,
                                    'description' => 'Servizio MonkEntrySocial',
                                    'qty' => 1,
                                    'priceRow' => $prepaidCost,
                                    'netPrice' => $prepaidCost,
                                    'vatRow' => $prepaidCost/100*$customerTaxes->perc,
                                    'grossTotalRow' => $prepaidCost+($prepaidCost/100*$customerTaxes->perc),
                                    'billRegistryTypeTaxesId' => $customerTaxes->id,
                                    'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                    'billRegistryContractId'=>$contractId,
                                    'billRegistryContractRowId'=>$contractRowId,
                                    'billRegistryContractRowDetailId' => 0
                                ];
                            }
                            $netTotalRowAgencyCommision=$prepaidCost/100*$feeAgencyCommision;
                            $netTotal+=$netTotalRowAgencyCommision;
                            $vatRowTotalAgencyCommision=$netTotalRowAgencyCommision/100*$customerTaxes->perc;
                            $vat+=$vatRowTotalAgencyCommision;
                            $grossTotal+=$netTotal+$vatRowTotalAgencyCommision;
                            if($prepaidCost!=0) {
                                $rowInvoiceExtraFee[] = [
                                    'billRegistryProductId'=> 0,
                                    'description' => 'Commissioni Agenzia MonkEntrySocial',
                                    'qty' => 1,
                                    'priceRow' => $netTotalRowAgencyCommision,
                                    'netPrice' => $netTotalRowAgencyCommision,
                                    'vatRow' => $vatRowTotalAgencyCommision,
                                    'grossTotalRow' => $netTotalRowAgencyCommision+$vatRowTotalAgencyCommision,
                                    'billRegistryTypeTaxesId' => $customerTaxes->id,
                                    'billRegistryTypeTaxesDesc' => $customerTaxes->description,
                                    'billRegistryContractId'=>$contractId,
                                    'billRegistryContractRowId'=>$contractRowId,
                                    'billRegistryContractRowDetailId' => 0
                                ];
                            }
                            break;
                        case 5:
                            $billRegistryRowSocialMonk = \Monkey::app()->repoFactory->create('BillRegistryRowSocialMonk')->findOneBy(['billRegistryContractRowId' => $contractRowId]);
                            $automaticInvoice = $billRegistryRowSocialMonk->automaticInvoice;
                            $valueContractRow = $billRegistryRowSocialMonk->value;
                            $billingDay = $billRegistryRowSocialMonk->billingDay;
                            $typePaymentId = $billRegistryRowSocialMonk->typePaymentId;
                            $periodTypeCharge = $billRegistryRowSocialMonk->periodTypeCharge;
                            $sellingFeeCommision = $billRegistryRowSocialMonk->sellingFeecommision;
                            $feeCreditCardCommission = $billRegistryRowSocialMonk->feeCreditCardCommission;
                            $dayChargeFeeCreditCardCommission = $billRegistryRowSocialMonk->dayChargeFeeCreditCardCommission;
                            $feeCodCommission = $billRegistryRowSocialMonk->feeCodCommision;
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
                            break;
                        case 7:
                            $billRegistryRowMailMonk = \Monkey::app()->repoFactory->create('BillRegistryRowMailMonk')->findOneBy(['billRegistryContractRowId' => $contractRowId]);
                            $automaticInvoice = $billRegistryRowMailMonk->automaticInvoice;
                            $emailAccount = $billRegistryRowMailMonk->emailAccount;
                            $emailAccountSendQty = $billRegistryRowMailMonk->emailAccountSendQty;
                            $emailAccountCampaignQty = $billRegistryRowMailMonk->emailAccountCampaignQty;
                            $valueContractRow = $billRegistryRowMailMonk->value;
                            $billingDay = $billRegistryRowMailMonk->billingDay;
                            $typePaymentId = $billRegistryRowMailMonk->typePaymentId;
                            $periodTypeCharge = $billRegistryRowMailMonk->periodTypeCharge;
                            $sellingFeeCommision = $billRegistryRowMailMonk->sellingFeecommision;
                            $feeCreditCardCommission = $billRegistryRowMailMonk->feeCreditCardCommission;
                            $dayChargeFeeCreditCardCommission = $billRegistryRowMailMonk->dayChargeFeeCreditCardCommission;
                            $feeCodCommission = $billRegistryRowMailMonk->feeCodCommision;
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
                        $billRegistryProduct = $billRegistryProductRepo->findOneBy(['id' => $rowdetail->billRegistryProductId]);
                        $nameProduct = $billRegistryProduct->nameProduct;
                        $codeProduct = $billRegistryProduct->codeProudct;
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
                        $vatRow = $netPice / 100 * $perc;
                        $grossTotalRow = $netPriceRow + $vatRow;
                        $netTotal += $netTotalRow;
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
                            'billRegistryContractId'=>$contractId,
                            'billRegistryContractRowId'=>$contractRowId,
                            'billRegistryContractRowDetailId' => $rowDetail->id];

                    }


                }
            }
        } catch (\Throwable $e) {

        }


        $this->app->cacheService->getCache('entities')->flush();
        $this->report('deleted Job Logs','deleted ' . $rows . ' rows');
    }
}
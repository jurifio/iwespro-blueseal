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

        $billRegistryClientRepo=\Monkey::app()->repoFactory->create('BillRegistryClient');
        $billRegistryContractRepo=\Monkey::app()->repoFactory->create('BillRegistryContract');
        $billRegistryContractRowRepo=\Monkey::app()->repoFactory->create('BillRegistryContractRow');
        $billRegistryContractRowDetailRepo=\Monkey::app()->repoFactory->create('BillRegistryContractRowDetail');
        $billRegistryGroupProductRepo=\Monkey::app()->repoFactory->create('BillRegistryGroupProductRepo');
        $billRegistryPriceListRepo=\Monkey::app()->repoFactory->create('BillRegistryPriceList');
        $billRegistryProductRepo=\Monkey::app()->repoFactory->create('BillRegistryProduct');
        $billRegistryProductDetailRepo=\Monkey::app()->repoFactory->create('BillRegistryProductDetail');
        $invoiceRepo=\Monkey::app()->repoFactory->create('Invoice');
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
        $billRegistrySocialRepo=\Monkey::app()->repoFactory->create('BillRegistrySocial');
        $campaignRepo=\Monkey::app()->repoFactory->create('Campaign');
        $marketplaceRepo=\Monkey::app()->repoFactory->create('Marketplace');
        $marketplaceAccountRepo=\Monkey::app()->repoFactory->create('MarketplaceAccount');
        $billRegistryTypeTaxesRepo=\Monkey::app()->repoFactory->create('BillRegistryTypeTaxes');
        $billRegistryTypePaymentRepo=\Monkey::app()->repoFactory->create('BillRegistryTypePayment');
        $billRegistryClientBillingInfoRepo=\Monkey::app()->repoFactory->create('BillRegistryClientBillingInfoRepo');
        $today = new \DateTime();
            try{
                $billRegistryContracts=$billRegistryContractRepo->findAll();
                foreach($billRegistryContracts as $billRegistryContract) {
                    $contractId = $billRegistryContract->id;
                    $billRegistryClientId = $billRegistryContract->billregistryClientId;
                    $billRegistryContractRow = $billRegistryContractRowRepo->findBy(['billRegistryContractId' => $contractId,'statusId' => '1']);

                    foreach ($billRegistryContractRow as $contractRow ) {
                    $contractRowId=$contractRow->id;

                        $typeContract = $contractRow->billRegistryGroupProductId;
                        switch ($typeContract) {
                            case 1:
                                //definizione dei parametri del tipo di contratto
                                $billRegistryRowMonkSource = \Monkey::app()->repoFactory->create('BillRegistryRowMonkSource')->findOneBy(['billRegistryContractRowId' => $contractRowId]);
                                $automaticInvoice=$billRegistryRowMonkSource->automaticInvoice;
                                $valueContractRow=$billRegistryRowMonkSource->value;
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


                            break;
                            case 2:
                                $billRegistryRowMonkAir = \Monkey::app()->repoFactory->create('BillRegistryRowMonkAir')->findOneBy(['billRegistryContractRowId' => $contractRowId]);
                                $automaticInvoice=$billRegistryRowMonkAir->automaticInvoice;
                                $valueContractRow=$billRegistryRowMonkAir->value;
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
                                break;
                            case 3:
                                $billRegistryRowMonkEntrySocial = \Monkey::app()->repoFactory->create('BillRegistryRowMonkEntrySocial')->findOneBy(['billRegistryContractRowId' => $contractRowId]);
                                $automaticInvoice=$billRegistryRowMonkEntrySocial->automaticInvoice;
                                $descriptionInvoice=$billRegistryRowMonkEntrySocial->descriptionInvoice;
                                $startUpCostCampaign=$billRegistryRowMonkEntrySocial->startUpCostCampaign;
                                $billingDay=$billRegistryRowMonkEntrySocial->billingDay;
                                $typePaymentId=$billRegistryRowMonkEntrySocial->typePaymentId;
                                $feeAgencyCommision=$billRegistryRowMonkEntrySocial->feeAgencyCommision;
                                $prepaidPaymentIsActive=$billRegistryRowMonkEntrySocial->prepaidPaymentIsActive;
                                $prepaidCost=$billRegistryRowMonkEntrySocial->prepaidCost;
                                break;
                            case 4:
                                $billRegistryRowMonkEntryTraffic = \Monkey::app()->repoFactory->create('BillRegistryRowMonkEntryTraffic')->findOneBy(['billRegistryContractRowId' => $contractRowId]);
                                $automaticInvoice=$billRegistryRowMonkEntryTraffic->automaticInvoice;
                                $descriptionInvoice=$billRegistryRowMonkEntryTraffic->descriptionInvoice;
                                $startUpCostCampaign=$billRegistryRowMonkEntryTraffic->startUpCostCampaign;
                                $billingDay=$billRegistryRowMonkEntryTraffic->billingDay;
                                $typePaymentId=$billRegistryRowMonkEntryTraffic->typePaymentId;
                                $feeAgencyCommision=$billRegistryRowMonkEntryTraffic->feeAgencyCommision;
                                $prepaidPaymentIsActive=$billRegistryRowMonkEntryTraffic->prepaidPaymentIsActive;
                                $prepaidCost=$billRegistryRowMonkEntryTraffic->prepaidCost;
                                break;
                            case 5:
                                $billRegistryRowSocialMonk = \Monkey::app()->repoFactory->create('BillRegistryRowSocialMonk')->findOneBy(['billRegistryContractRowId' => $contractRowId]);
                                $automaticInvoice=$billRegistryRowSocialMonk->automaticInvoice;
                                $valueContractRow=$billRegistryRowSocialMonk->value;
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
                                $automaticInvoice=$billRegistryRowMailMonk->automaticInvoice;
                                $emailAccount=$billRegistryRowMailMonk->emailAccount;
                                $emailAccountSendQty=$billRegistryRowMailMonk->emailAccountSendQty;
                                $emailAccountCampaignQty=$billRegistryRowMailMonk->emailAccountCampaignQty;
                                $valueContractRow=$billRegistryRowMailMonk->value;
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
                        if($automaticInvoice==1){
                            $billRegistryContractRowDetail=$billRegistryContractRowDetailRepo->findBy(['billRegistryContractRowId'=>$contractRowId]);
                            foreach($billRegistryContractRowDetail as )



                        }else{
                            continue;
                        }



                    }
                }
            }catch(\Throwable $e){

            }





		$this->app->cacheService->getCache('entities')->flush();
        $this->report('deleted Job Logs', 'deleted ' . $rows . ' rows');
    }
}
<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProductEan;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CShop;
use bamboo\domain\entities\CUserAddress;
use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use PDO;
use prepare;
use AEntity;
use bamboo\domain\entities\CSite;
use bamboo\domain\entities\CUserHasShop;
use bamboo\domain\repositories\CUserAddressRepo;
use bamboo\domain\entities\CUser;
use PDOException;


class CImportGainPlanJob extends ACronJob
{

    /**
     * @param null $args
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function run($args = null)
    {
        $this -> importMovement();
    }


    private function importMovement()
    {
        try {
            $invoiceRepo = \Monkey::app()->repoFactory->create('Invoice');
            $orderRepo = \Monkey::app()->repoFactory->create('Order');
            $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');
            $shopRepo = \Monkey::app()->repoFactory->create('Shop');
            $userRepo = \Monkey::app()->repoFactory->create('User');
            $countryRepo = \Monkey::app()->repoFactory->create('Country');
            $gpsmRepo = \Monkey::app()->repoFactory->create('GainPlanPassiveMovement');
            $seasonRepo = \Monkey::app()->repoFactory->create('ProductSeason');
            $orderPaymentMethodRepo = \Monkey::app()->repoFactory->create('OrderPaymentMethod');
            $gainPlanRepo = \Monkey::app()->repoFactory->create('GainPlan');

            $invoices = $invoiceRepo->findBy(['invoiceSiteChar' => 'P']);
            foreach ($invoices as $invoice) {
                $invoiceDate = $invoice->invoiceDate;
                $orderId = $invoice->orderId;
                $order = $orderRepo->findOneBy(['id' => $orderId]);
                $userAddress = \bamboo\domain\entities\CUserAddress::defrost($order->frozenBillingAddress);
                $customer = $userAddress->name . ' ' . $userAddress->surname . ' ' . $userAddress->company;
                $invoiceId = $invoice->id;
                $shopId=$invoice->invoiceShopId;
                $seasons = $seasonRepo->findAll();
                foreach ($seasons as $season) {
                    $dateStart = strtotime($season->dateStart);
                    $dateEnd = strtotime($season->dateEnd);
                    $dateInvoice = strtotime($invoiceDate);
                    if ($dateInvoice >= $dateStart && $dateInvoice <= $dateEnd) {
                        $seasonId = $season->id;
                    }
                }
                $gainPlanFind = \Monkey::app()->repoFactory->create('GainPlan')->findOneBy(['invoiceId' => $invoiceId,'orderId' => $orderId]);
                if ($gainPlanFind == null) {
                    $gainPlanInsert = $gainPlanRepo->getEmptyEntity();
                    $gainPlanInsert->invoiceId = $invoiceId;
                    $gainPlanInsert->orderId = $orderId;
                    $gainPlanInsert->seasonId = $seasonId;
                    $gainPlanInsert->customerName = $customer;
                    $gainPlanInsert->typeMovement = 1;
                    $gainPlanInsert->dateMovement = $invoiceDate;
                    $gainPlanInsert->shopId=$shopId;
                    $gainPlanInsert->isVisible=1;
                    $gainPlanInsert->insert();
                }

            }
        }catch(\Throwable $e){
            $this -> report('CImportGainPlanJob', 'error', $e,'');
        }


    }


}
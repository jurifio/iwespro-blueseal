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
        $this->importMovement();
    }


    private function importMovement()
    {
        try {
            $invoiceRepo = \Monkey::app()->repoFactory->create('Invoice');
            $orderRepo = \Monkey::app()->repoFactory->create('Order');
            $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');

            $seasonRepo = \Monkey::app()->repoFactory->create('ProductSeason');
            $gainPlanRepo = \Monkey::app()->repoFactory->create('GainPlan');
            $sql = "select * from `Order`";

            $orders = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
            foreach ($orders as $order) {
                $orderId = $order['id'];
                $customer = '';



                    $invoice = $invoiceRepo->findOneBy(['orderId' => $orderId,'invoiceSiteChar'=>'P']);

                    if ($invoice != null) {

                        $invoiceId = $invoice->id;
                        $invoiceExternal=$invoice->invoiceNumber.'/'.$invoice->invoiceSiteChar;
                    }
                    if ($order['frozenBillingAddress'] != null) {
                        $userAddress = json_decode($order['frozenBillingAddress'],false);
                        if ($userAddress != null) {
                            if ($userAddress->company == null) {
                                $customer = addslashes($userAddress->name . ' ' . $userAddress->surname);
                            } else {
                                $customer = addslashes($userAddress->name . ' ' . $userAddress->surname . ' ' . $userAddress->company);
                            }
                        }
                    } else {
                        $customer = '';
                    }

                    $shopId = $order['remoteShopSellerId'];
                    $seasons = $seasonRepo->findAll();
                    foreach ($seasons as $season) {
                        $dateStart = strtotime($season->dateStart);
                        $dateEnd = strtotime($season->dateEnd);
                        $orderDate = strtotime($order['creationDate']);
                        if ($orderDate >= $dateStart && $orderDate <= $dateEnd) {
                            $seasonId = $season->id;
                            break;
                        }
                    }
                    $gainPlanFind = \Monkey::app()->repoFactory->create('GainPlan')->findOneBy(['orderId' => $orderId]);
                    if ($gainPlanFind == null) {
                        $gainPlanInsert = $gainPlanRepo->getEmptyEntity();
                        if ($invoice != null) {
                            $gainPlanInsert->invoiceId = $invoiceId;
                            $gainPlanInsert->invoiceExternal=$invoiceExternal;
                        }
                        $gainPlanInsert->orderId = $orderId;
                        $gainPlanInsert->seasonId = $seasonId;
                        $gainPlanInsert->customerName = $customer;
                        $gainPlanInsert->typeMovement = 1;
                        $gainPlanInsert->dateMovement = $order['creationDate'];
                        $gainPlanInsert->shopId = $shopId;
                        $gainPlanInsert->isActive = 1;
                        $gainPlanInsert->insert();
                    }

                }


        } catch (\Throwable $e) {
            $this->report('CImportGainPlanJob','error',$e);
        }


    }


}
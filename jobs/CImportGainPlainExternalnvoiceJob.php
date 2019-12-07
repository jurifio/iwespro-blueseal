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


class CImportGainPlainExternalnvoiceJob extends ACronJob
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
        $res = "";
        $shopRepo = \Monkey::app()->repoFactory->create('Shop')->findBy(['hasEcommerce' => 1]);
        $gp = \Monkey::app()->repoFactory->create('GainPlan');
        $seasonRepo = \Monkey::app()->repoFactory->create('ProductSeason');
        $gainPlanPassiveMovementRepo = \Monkey::app()->repoFactory->create('GainPlanPassiveMovement');

        foreach ($shopRepo as $value) {
            if ($svalue->id != 44) {
                $this->report('Start ImportOrder Gain Plan Passive From PickySite ','Shop To Import' . $value->name);
                /********marketplace********/
                $db_host = $value->dbHost;
                $db_name = $value->dbName;
                $db_user = $value->dbUsername;
                $db_pass = $value->dbPassword;

                try {

                    $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                    $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

                } catch (PDOException $e) {

                }

                $shopName = $value->title;
                $shopId = $value->id;
                try {
                    $stmtOrder = $db_con->prepare('SELECT distinct SUM(ol.netPrice) AS amount,
                                                                ' . $shopId . ' AS shopId,
                                                                ol.id as id,
                                                                ol.orderId as orderId,
                                                                o.frozenBillingAddress as frozenBillingAddress,
                                                                o.remoteIwesOrderId as remoteIwesOrderId,
                                                                i.invoiceType AS invoiceType,
                                                                i.invoiceNumber AS invoiceNumber,
                                                                i.invoiceDate AS invoiceDate,
                                                                \'' . $shopName . '\' AS fornitureName,
                                                                \'Acquisto Ordine Parallelo\' AS serviceName
                                                                FROM Invoice i JOIN `Order` o ON i.orderId=o.id
                                                                LEFT JOIN OrderLine ol ON ol.orderId=o.id
                                                                WHERE  ol.status IN(\'ORD_SENT\'
                                                                ,ol.status=\'ORD_FRND_ORDSNT\'
                                                                ,ol.status=\'ORD_FRND_PYD\') AND i.invoiceType=\'BP\' AND o.remoteIwesOrderId  IS NOT NULL GROUP BY i.id');
                    $stmtOrder->execute();
                    while ($rowOrder = $stmtOrder->fetch(PDO::FETCH_ASSOC)) {
                        $dateMovement = $rowOrder['invoiceDate'];
                        $remoteIwesOrderId = $rowOrder['remoteIwesOrderId'];
                        $amountTotal = $rowOrder['amount'];
                        $amount = ($amountTotal * 100) / 122;
                        $amountVat = $amountTotal - $amount;
                        $dateInvoice = strtotime($dateMovement);
                        $newdateInvoice = $date('d/m/Y',$dateInvoice);
                        $invoiceNumber = $rowOrder['invoiceType'] . '-' . $rowOrder['invoiceNumber'] . ' del ' . $newdateInvoice;
                        $gppm = $gainPlanPassiveMovementRepo->findOneBy(['invoice' => $invoiceNumber]);
                        if ($gppm == null) {
                            $gppmi = \Monkey::app()->repoFactory->create('GainPlanPassiveMovement')->getEmptyEntity();
                            $gppmi->invoice = $invoiceNunmber;
                            $gppmi->amount = $amount;
                            $gainPlanFind = $gp->finOneBy(['orderId' => $remoteIwesOrderId]);
                            if ($gainPlanFind != null) {
                                $gppmi->gainPlanId = $gainPlanFind->id;
                            }
                            $gppmi->shopId = $shopId;
                            $gppmi->fornitureName = $shopName;
                            $seasons = $seasonRepo->findAll();
                            foreach ($seasons as $season) {
                                $dateStart = strtotime($season->dateStart);
                                $dateEnd = strtotime($season->dateEnd);
                                if ($dateInvoice >= $dateStart && $dateInvoice <= $dateEnd) {
                                    $gppmi->seasonId = $season->id;
                                }
                            }

                            $gppmi->isActive = 1;
                            $gppmi->dateMovement = $dateMovement;
                            $gppmi->typeMovement = 1;
                            $gppmi->amountVar = $amountVat;
                            $gppmi->amountTotal = $amountTotal;
                            $gppmi->insert();
                        }
                    }
                } catch (\Throwable $e) {
                    $this->report('CImportGainExternalInvoiceJob','error',$e,'');
                }


            }


        }


    }
}
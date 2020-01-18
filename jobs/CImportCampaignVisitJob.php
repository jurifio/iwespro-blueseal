<?php

namespace bamboo\blueseal\jobs;

use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CCampaign;
use bamboo\domain\entities\CCampaignVisitHasOrder;
use bamboo\domain\entities\CCampaignVisit;
use bamboo\domain\entities\CCampaignVisitHasProduct;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CProduct;
use bamboo\domain\repositories\CCampaignRepo;
use bamboo\domain\repositories\CMarketplaceAccountHasProductRepo;
use bamboo\domain\repositories\CProductRepo;
use PDO;
use PDOException;

/**
 * Class CImportCampaignVisitJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 05/01/2020
 * @since 1.0
 */
class CImportCampaignVisitJob extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $config = json_decode($args);
        $this->report('run','Starting with config' . $args);
        $campaignRepo = \Monkey::app()->repoFactory->create('Campaign');
        $campaignVisitRepo = \Monkey::app()->repoFactory->create('CampaignVisit');
        $campaignVisitHasOrderRepo = \Monkey::app()->repoFactory->create('CampaignVisitHasOrder');
        $campaignVisitHasProductRepo = \Monkey::app()->repoFactory->create('CampaignVisitHasProduct');
        $orderRepo = \Monkey::app()->repoFactory->create('Order');
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $shops = $shopRepo->findBy(['hasEcommerce' => 1]);
        $res = "";


        foreach ($shops as $value) {
            $this->report('Start Import Campaign Visit  From Hub ','Shop To Import' . $value->name);
            /********marketplace********/
            $db_host = $value->dbHost;
            $db_name = $value->dbName;
            $db_user = $value->dbUsername;
            $db_pass = $value->dbPassword;
            $shop = $value->id;
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $res .= " connessione ok <br>";
            } catch (PDOException $e) {
                $res .= $e->getMessage();
            }

            $campaigns = $campaignRepo->findBy(['remoteShopId'=>$shop]);
            foreach ($campaigns as $campaign) {

                $remoteCampaignId = $campaign->remoteCampaignId;
                if ($remoteCampaignId != null) {
                    try {
                        $stmtRemoteCampaignVisit = $db_con->prepare('SELECT id as remoteCampaignVisitId,
                                                                                      `timestamp` as `timestamp`,
                                                                                      cost as cost, 
                                                                                      costCustomer as costCustomer,
                                                                                     campaignId as remoteCampaignId 
                            from CampaignVisit WHERE campaignId=' . $remoteCampaignId . ' and isImport is null');
                        $stmtRemoteCampaignVisit->execute();
                        while ($rowRemoteCampaignVisit = $stmtRemoteCampaignVisit->fetch(PDO::FETCH_ASSOC)) {
                            $campaignVisitInsert = $campaignVisitRepo->getEmptyEntity();
                            $campaignVisitInsert->campaignId = $campaign->id;
                            $campaignVisitInsert->timestamp = $rowRemoteCampaignVisit['timestamp'];
                            $campaignVisitInsert->cost = $rowRemoteCampaignVisit['cost'];
                            $campaignVisitInsert->costCustomer = $rowRemoteCampaignVisit['costCustomer'];
                            $campaignVisitInsert->remoteCampaignVisitId = $rowRemoteCampaignVisit['remoteCampaignVisitId'];
                            $campaignVisitInsert->remoteCampaignId = $rowRemoteCampaignVisit['remoteCampaignId'];
                            $campaignVisitInsert->remoteShopId = $shop;
                            $campaignVisitInsert->insert();
                        }
                        $stmtUpdateRemoteCampaignVisit = $db_con->prepare('UPDATE CampaignVisit set isImport=1 where isImport is  null');
                        $stmtUpdateRemoteCampaignVisit->execute();
                    } catch (\Throwable $e) {
                        $this->report('CImportCampaignVisitJob','error','Errore campaignVisitInsert ' . $e);
                    }
                    $stmtRemoteCampaignVisitHasOrder = $db_con->prepare('SELECT  campaignVisitId as remoteCampaignVisitId ,
                                                                                         campaignId as remoteCampaignId,
                                                                                         orderId as remoteOrderId from CampaignVisitHasOrder 
                                                                                         where campaignId=\''.$remoteCampaignId.'\' and  isImport is null');
                    $stmtRemoteCampaignVisitHasOrder->execute();
                    while ($rowRemoteCampaignVisitHasOrder = $stmtRemoteCampaignVisitHasOrder->fetch(PDO::FETCH_ASSOC)) {
                        $order = $orderRepo->findOneBy(['remoteOrderSellerId' => $rowRemoteCampaignVisitHasOrder['remoteOrderId'],'remoteShopSellerId' => $shop]);
                        if ($order != null) {
                            try {
                                $findCampaignVisitId = $campaignVisitRepo->findOneBy(['remoteCampaignVisitId' => $rowRemoteCampaignVisitHasOrder['remoteCampaignVisitId'],'remoteShopId'=>$shop]);
                                $campaignVisitHasOrderInsert = $campaignVisitHasOrderRepo->getEmptyEntity();
                                $campaignVisitHasOrderInsert->campaignVisitId = $findCampaignVisitId->id;
                                $campaignVisitHasOrderInsert->campaignId = $campaign->id;
                                $campaignVisitHasOrderInsert->orderId = $order->id;
                                $campaignVisitHasOrderInsert->remoteCampaignVisitId = $rowRemoteCampaignVisitHasOrder['remoteCampaignVisitId'];
                                $campaignVisitHasOrderInsert->remoteCampaignId = $rowRemoteCampaignVisitHasOrder['remoteCampaignId'];
                                $campaignVisitHasOrderInsert->remoteOrderId = $rowRemoteCampaignVisitHasOrder['remoteOrderId'];
                                $campaignVisitHasOrderInsert->remoteShopId = $shop;
                                $campaignVisitHasOrderInsert->insert();


                            } catch (\Throwable $e) {
                                $this->report('CImportCampaignVisitJob','error','Error campaignVisitHasOrderInsert ' . $e);
                            }

                        } else {
                            continue;
                        }
                    }
                    $stmtUpdateRemoteCampaignVisitHasOrder = $db_con->prepare('UPDATE CampaignVisitHasOrder set isImport=1 where isImport is null');
                    $stmtUpdateRemoteCampaignVisitHasOrder->execute();
                    $stmtRemoteCampaignVisitHasProduct = $db_con->prepare('
                    SELECT 
                    campaignVisitId as remoteCampaignVisitId,
                    campaignId as remoteCampaignId,
                    productId as productId,
                    productVariantId as productVariantId
                    from CampaignVisitHasProduct where campaignId='.$remoteCampaignId.' and  isImport is null ');
                    $stmtRemoteCampaignVisitHasProduct->execute();
                    while ($rowRemoteCampaignVisitHasProduct = $stmtRemoteCampaignVisitHasProduct->fetch(PDO::FETCH_ASSOC)) {
                        try {
                            $findCampaignVisitId = $campaignVisitRepo->findOneBy(['remoteCampaignVisitId' => $rowRemoteCampaignVisitHasProduct['remoteCampaignVisitId'],'remoteCampaignId'=>$rowRemoteCampaignVisitHasProduct['remoteCampaignId'],'remoteShopId'=>$shop]);
                            $campaignVisitHasProductInsert = $campaignVisitHasProductRepo->getEmptyEntity();

                            $campaignVisitHasProductInsert->campaignVisitId = $findCampaignVisitId->id;
                            $campaignVisitHasProductInsert->campaignId=$campaign->id;
                            $campaignVisitHasProductInsert->productId=$rowRemoteCampaignVisitHasProduct['productId'];
                            $campaignVisitHasProductInsert->productVariantId=$rowRemoteCampaignVisitHasProduct['productVariantId'];
                            $campaignVisitHasproductInsert->remoteCampaignVisitId = $findCampaignVisitId->remoteCampaignVisitId;
                            $campaignVisitHasProductInsert->remoteCampaignId = $rowRemoteCampaignVisitHasProduct['remoteCampaignId'];
                            $campaignVisitHasProductInsert->remoteShopId = $shop;
                            $campaignVisitHasProductInsert->insert();


                        } catch (\Throwable $e) {
                            $this->report('CImportCampaignVisitJob','error','Error campaignVisitHasProductInsert ' . $e);

                        }
                    }
                    $stmtUpdateRemoteCampaignVisitHasProduct = $db_con->prepare('UPDATE CampaignVisitHasProduct set isImport=1 where isImport is null');
                    $stmtUpdateRemoteCampaignVisitHasProduct->execute();


                }else{
                    continue;
                }

            }
        }
    }
}
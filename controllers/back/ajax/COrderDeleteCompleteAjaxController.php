<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\blueseal\business\CDownloadFileFromDb;
use bamboo\core\exceptions\BambooException;
use bamboo\core\intl\CLang;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\domain\entities\CInvoiceDocument;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CUser;
use bamboo\utils\price\SPriceToolbox;
use bamboo\domain\entities\CUserAddress;
use PDO;
use PDOException;

/**
 * Class COrderDeleteCompleteAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/10/2019
 * @since 1.0
 */
class COrderDeleteCompleteAjaxController extends AAjaxController
{


    public function delete()
    {
        $orderId = \Monkey::app()->router->request()->getRequestData('orderId');
        if (!$orderId) throw new \Exception('Id ordine non pervenuto. Non posso cancellarlo');

        $orderHistoryRepo = \Monkey::app()->repoFactory->create('OrderHistory');
        $fidelityBalanceRepo = \Monkey::app()->repoFactory->create('FidelityBalance');
        $logRepo = \Monkey::app()->repoFactory->create('Log');
        $storehouseHoperationLineRepo = \Monkey::app()->repoFactory->create('StorehouseOperationLine');
        $userSessionHasOrderRepo = \Monkey::app()->repoFactory->create('UserSessionHasOrder');
        $camppaginVisitHasOrderRepo = \Monkey::app()->repoFactory->create('CampaignVisitHasOrder');
        $eloyVoucherRepo = \Monkey::app()->repoFactory->create('EloyVoucher');
        $invoiceDocumentRepo=\Monkey::app()->repoFactory->create('InvoiceDocument');
        $invoiceRepo=\Monkey::app()->repoFactory->create('Invoice');
        $orderLineHasShipmentRepo=\Monkey::app()->repoFactory->create('OrderLineHasShipment');




        $orderRepo = \Monkey::app()->repoFactory->create('Order')->findOneBy(['id' => $orderId]);
        iwesMail(
            'gianluca@iwes.it','Cancellazione ordine', "Hai 
            cancellato ha rifiutato l'ordine: " .
             $orderId. " con remoteShopSellerId=".$orderRepo->remoteShopSellerId
        );
        $shopId = $orderRepo->remoteShopSellerId;
        if ($shopId == null) {
            $shopId = 44;
        }
        $shopRepo = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $shopId]);
        $db_host = $shopRepo->dbHost;
        $db_name = $shopRepo->dbName;
        $db_user = $shopRepo->dbUsername;
        $db_pass = $shopRepo->dbPassword;
        try {

            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
            $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $res = ' connessione ok <br>';
        } catch (PDOException $e) {
            $res = $e->getMessage();
        }
        try {
            $stmtOrderLineStatistics = $db_con->prepare('DELETE FROM  OrderLineStatistics WHERE orderId=' . $orderRepo->remoteOrderSellerId);
            $stmtOrderLineStatistics->execute();
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione OrderHistory','DELETE FROM  OrderLineStatistics WHERE orderId=' . $orderRepo->remoteOrderSellerId,'');
        }

        try {
            $stmtOrderHistory = $db_con->prepare('DELETE FROM  OrderHistory WHERE orderId=' . $orderRepo->remoteOrderSellerId);
            $stmtOrderHistory->execute();
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione OrderHistory','DELETE FROM  OrderHistory WHERE orderId=' . $orderRepo->remoteOrderSellerId,'');
        }
        try {
            $stmtFidelityBalance = $db_con->prepare('DELETE FROM FidelityBalance WHERE  orderId=' . $orderRepo->remoteOrderSellerId);
            $stmtFidelityBalance->execute();
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione fidelityBalance','DELETE FROM FidelityBalance WHERE  orderId=' . $orderRepo->remoteOrderSellerId,'');
        }
        if ($shopId != null || $shopId != 44) {
            try {
                $stmtCampaignVisitHasOrder = $db_con->prepare('DELETE FROM CampaignVisitHasOrder WHERE orderId=' . $orderRepo->remoteOrderSellerId);
                $stmtCampaignVisitHasOrder->execute();
            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione CampaignVisitHasOrder','DELETE FROM CampaignVisitHasOrder WHERE orderId=' . $orderRepo->remoteOrderSellerId,'');
            }
        }
        try {
            $stmtInvoice = $db_con->prepare('DELETE FROM Invoice WHERE orderId=' . $orderRepo->remoteOrderSellerId);
            $stmtInvoice->execute();
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione Invoice','DELETE FROM Invoice WHERE orderId=' . $orderRepo->remoteOrderSellerId,'');
        }
        try {
            $stmtEloyVoucher = $db_con->prepare('DELETE FROM EloyVoucher WHERE orderId=' . $orderRepo->remoteOrderSellerId);
            $stmtEloyVoucher->execute();
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione EloyVoucher','DELETE FROM EloyVoucher WHERE orderId=' . $orderRepo->remoteOrderSellerId,'');
        }

        if ($shopId != null || $shopId != 44) {
            try {
                $stmtShopMovements = $db_con->prepare('DELETE FROM ShopMovements WHERE orderId=' . $orderRepo->remoteOrderSellerId);
                $stmtShopMovements->execute();
            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione ShopMovementes','DELETE FROM ShopMovements WHERE orderId=' . $orderRepo->remoteOrderSellerId,'');
            }
        }
        try {
            $stmtInvoiceDocument = $db_con->prepare('DELETE FROM InvoiceDocument WHERE orderId=' . $orderRepo->remoteOrderSellerId);
            $stmtInvoiceDocument->execute();
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione InvoiceDocument','DELETE FROM InvoiceDocument WHERE orderId=' . $orderRepo->remoteOrderSellerId,'');
        }
        try {
            $stmtOrderLineHasShipment = $db_con->prepare('DELETE FROM OrderLineHasShipment WHERE orderId=' . $orderRepo->remoteOrderSellerId);
            $stmtOrderLineHasShipment->execute();
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione OrderLineHasShipment','DELETE FROM OrderLineHasShipment WHERE orderId=' . $orderRepo->remoteOrderSellerId,'');
        }
        try {
            $stmtOrderLine = $db_con->prepare('DELETE FROM OrderLine WHERE orderId=' . $orderRepo->remoteOrderSellerId);
            $stmtOrderLine->execute();
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione OrderLine','DELETE FROM OrderLine WHERE orderId=' . $orderRepo->remoteOrderSellerId,'');
        }
        try {
            $stmtOrder = $db_con->prepare(' DELETE FROM `Order` WHERE id=' . $orderRepo->remoteOrderSellerId);
            $stmtOrder->execute();
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione Order','DELETE FROM `Order` WHERE id=' . $orderRepo->remoteOrderSellerId,'');
        }

        $orderHistory = $orderHistoryRepo->findBy(['orderId' => $orderRepo->id]);
        if ($orderHistory != null) {
            foreach ($orderHistory as $oh) {
                $oh->delete();
            }
        }
        $fidelityBalance = $fidelityBalanceRepo->findBy(['orderId' => $orderRepo->id]);
        if ($fidelityBalanceRepo != null) {
            foreach ($fidelityBalance as $fb) {
                $fb->delete();
                }
        }
        $invoiceDocument=$invoiceDocumentRepo->findBy(['orderId' => $orderRepo->id]);
        if ($invoiceDocument != null) {
            foreach ($invoiceDocument as $id) {
                $id->delete();
                }
        }
        $orderLineHasShipment=$orderLineHasShipmentRepo->findBy(['orderId' => $orderRepo->id]);
        if ($orderLineHasShipment != null) {
            foreach ($orderLineHasShipment as $io) {
                $io->delete();
            }
        }
        $invoice=$invoiceRepo->findBy(['orderId' => $orderRepo->id]);
        if ($invoice != null) {
            foreach ($invoice as $i) {
                $i->delete();
            }
        }
        $campaginVisitHasOrder=$camppaginVisitHasOrderRepo->findBy(['orderId' => $orderRepo->id]);
        if ($campaginVisitHasOrder != null) {
            foreach ($campaginVisitHasOrder as $cvho) {
                $cvho->delete();
            }
        }
        $eloyVoucher=$eloyVoucherRepo->findBy(['orderId' => $orderRepo->id]);
        if ($eloyVoucher != null) {
            foreach ($eloyVoucher as $evrr) {
                $evrr->delete();
            }
        }
        $orderLineCancel = \Monkey::app()->repoFactory->create('OrderLine')->findBy(['orderId' => $orderId]);


        foreach ($orderLineCancel as $orlc) {
            if($orlc->remoteOrderSupplierId!=null){
              $remoteOrderSupplierId=$orlc->remoteOrderSupplierId;
              $shopSupplierId=$orlc->shopId;

              /**** operazione su shopsupplier*/
                if ($shopSupplierId == null) {
                    $shopSupplierId = 44;
                }
                $shopRepo1 = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $shopSupplierId]);
                $db_host1 = $shopRepo1->dbHost;
                $db_name1 = $shopRepo1->dbName;
                $db_user1 = $shopRepo1->dbUsername;
                $db_pass1 = $shopRepo1->dbPassword;
                try {

                    $db_con1 = new PDO("mysql:host={$db_host1};dbname={$db_name1}",$db_user1,$db_pass1);
                    $db_con1->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                    $res1 = ' connessione ok <br>';
                } catch (PDOException $e) {
                    $res1 = $e->getMessage();
                }
                try {
                    $stmtOrderLineStatistics = $db_con1->prepare('DELETE FROM  OrderLineStatistics WHERE orderId=' . $remoteOrderSupplierId);
                    $stmtOrderLineStatistics->execute();
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione OrderHistory','DELETE FROM  OrderLineStatistics WHERE orderId=' . $remoteOrderSupplierId,'');
                }

                try {
                    $stmtOrderHistory = $db_con1->prepare('DELETE FROM  OrderHistory WHERE orderId=' . $remoteOrderSupplierId);
                    $stmtOrderHistory->execute();
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione OrderHistory Parallel','DELETE FROM  OrderHistory WHERE orderId=' . $remoteOrderSupplierId,'');
                }
                try {
                    $stmtFidelityBalance = $db_con1->prepare('DELETE FROM FidelityBalance WHERE  orderId=' . $orderRepo->remoteOrderSellerId);
                    $stmtFidelityBalance->execute();
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione fidelityBalance Parallel','DELETE FROM FidelityBalance WHERE  orderId=' .$remoteOrderSupplierId,'');
                }
                if ($shopId != null || $shopId != 44) {
                    try {
                        $stmtCampaignVisitHasOrder = $db_con1->prepare('DELETE FROM CampaignVisitHasOrder WHERE orderId=' . $remoteOrderSupplierId);
                        $stmtCampaignVisitHasOrder->execute();
                    } catch (\Throwable $e) {
                        \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione CampaignVisitHasOrder Parallel','DELETE FROM CampaignVisitHasOrder WHERE orderId=' . $remoteOrderSupplierId,'');
                    }
                }
                try {
                    $stmtInvoice = $db_con1->prepare('DELETE FROM Invoice WHERE orderId=' . $remoteOrderSupplierId);
                    $stmtInvoice->execute();
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione Invoice Parallel','DELETE FROM Invoice WHERE orderId=' . $remoteOrderSupplierId,'');
                }
                try {
                    $stmtEloyVoucher = $db_con1->prepare('DELETE FROM EloyVoucher WHERE orderId=' . $remoteOrderSupplierId);
                    $stmtEloyVoucher->execute();
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione EloyVoucher Parallel ','DELETE FROM EloyVoucher WHERE orderId=' . $remoteOrderSupplierId,'');
                }
                try {
                    $stmtOrderLineHasShipment = $db_con1->prepare('DELETE FROM OrderLineHasShipment WHERE orderId=' . $remoteOrderSupplierId);
                    $stmtOrderLineHasShipment->execute();
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione OrderLineHasShipment','DELETE FROM OrderLineHasShipment WHERE orderId=' . $remoteOrderSupplierId,'');
                }

                if ($shopId != null || $shopId != 44) {
                    try {
                        $stmtShopMovements = $db_con1->prepare('DELETE FROM ShopMovements WHERE orderId=' . $remoteOrderSupplierId);
                        $stmtShopMovements->execute();
                    } catch (\Throwable $e) {
                        \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione ShopMovementes Parallel ','DELETE FROM ShopMovements WHERE orderId=' . $remoteOrderSupplierId,'');
                    }
                }
                try {
                    $stmtInvoiceDocument = $db_con1->prepare('DELETE FROM InvoiceDocument WHERE orderId=' . $remoteOrderSupplierId);
                    $stmtInvoiceDocument->execute();
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione InvoiceDocument Parallel','DELETE FROM InvoiceDocument WHERE orderId=' . $remoteOrderSupplierId,'');
                }
                try {
                    $stmtOrderLine = $db_con1->prepare('DELETE FROM OrderLine WHERE orderId=' . $remoteOrderSupplierId);
                    $stmtOrderLine->execute();
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione OrderLine Parallel','DELETE FROM OrderLine WHERE orderId=' . $remoteOrderSupplierId,'');
                }
                try {
                    $stmtOrder = $db_con1->prepare(' DELETE FROM `Order` WHERE id=' . $remoteOrderSupplierId);
                    $stmtOrder->execute();
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('COrderDeleteCompleteAjaxController','Error','Cancellazione Order','DELETE FROM `Order` WHERE id=' . $remoteOrderSupplierId,'');
                }


              /*   fine opeazione su shopsupplier */////
            }
            $orlc->delete();

        }
        $orderRepo->delete();
return $res='ok';
    }
}
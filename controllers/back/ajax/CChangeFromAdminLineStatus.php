<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CDirtySku;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\offline\productsync\import\alducadaosta\CAlducadaostaOrderAPI;
use PDO;
use PDOException;

/**
 * Class CChangeFromAdminLineStatus
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 25/10/2019
 * @since 1.0
 */
class CChangeFromAdminLineStatus extends AAjaxController
{
    /**
     * @return bool
     */
    public function post()
    {
        $request = \Monkey::app()->router->request();
        $row = $request->getRequestData('rows');
        $statusLine = $request->getRequestData('statusLine');
        $dba = \Monkey::app()->dbAdapter;
        try {
            \Monkey::app()->repoFactory->beginTransaction();
            $lineO=$row[0];
            $ids = explode('-', $lineO);
            /** @var COrderLineRepo $repo */
            $repo = \Monkey::app()->repoFactory->create('OrderLine');
            $line = $repo->findOne(['id' => $ids[0], 'orderId' => $ids[1]]);
            $oldActive = $line->orderLineStatus->isActive;


            /** @var COrderLine $line */
            $line = $repo->updateStatus($line, $statusLine);
            $orderLine=$line;



            $newActive = $line->orderLineStatus->isActive;

            if($line->shopId == 46 AND $line->status == "ORD_WAIT") {
                //Value for api

                /** @var CObjectCollection $dirtySkus */
                $dirtySkus = $line->productSku->dirtySku;

                if($dirtySkus->count() != 1) {
                    throw new BambooException('Collezione fatta da più sku, controllare');
                }

                /** @var CDirtySku $dirtySku */
                $dirtySku = $dirtySkus->getFirst();

                $orderId = $line->orderId;
                $rowN = $line->id;

                $row = [
                    "RowID" => $rowN,
                    "SKU" => $dirtySku->extSkuId,
                    "Value" => $line->friendRevenue,
                    "Payment_type" => $line->order->orderPaymentMethod->name,
                ];
                $alduca = new CAlducadaostaOrderAPI($orderId, $row);
                $alduca->newOrder();
            }
            $shopRepo=\Monkey::app()->repoFactory->create('Shop')->findOneBy(['id'=>$orderLine->remoteShopSellerId]);
            $orderRepo=\Monkey::app()->repoFactory->create('Order')->findOneBy(['id'=>$orderLine->orderId,'remoteShopSellerId'=>$orderLine->remoteShopSellerId]);
            if($orderLine->remoteOrderSellerId!=null) {
                $db_host = $shopRepo->dbHost;
                $db_name = $shopRepo->dbName;
                $db_user = $shopRepo->dbUsername;
                $db_pass = $shopRepo->dbPassword;
                $shop = $shopRepo->id;
                try {

                    $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
                    $db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $res = ' connessione ok <br>';
                } catch (PDOException $e) {
                    $res = $e->getMessage();
                }

                $stmtOrderLine = $db_con->prepare("UPDATE OrderLine SET `status`='" . $orderLine->status . "' WHERE id=" . $orderLine->remoteOrderLineSellerId . " and orderId=" . $orderLine->remoteOrderSellerId);
                $stmtOrderLine->execute();
                $stmtOrder = $db_con->prepare("UPDATE `Order` SET `status`='" . $orderRepo->status . "' WHERE id=" . $orderRepo->remoteOrderSellerId);
                $stmtOrder->execute();
            }

            \Monkey::app()->repoFactory->commit();

            if ($oldActive != $newActive) return 'reload';
            else return 'don\'t do it';
        } catch (BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
            \Monkey::app()->router->response()->raiseProcessingError();
            return false;
        }
    }

}
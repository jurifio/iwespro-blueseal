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
 * Class CChangeLineStatus
 * @package bamboo\blueseal\controllers\ajax
 */
class CChangeLineStatus extends AAjaxController
{
    /**
     * @return bool
     */
    public function put()
    {
        $dba = \Monkey::app()->dbAdapter;
        try {
            \Monkey::app()->repoFactory->beginTransaction();
            $ids = explode('-', $this->data['value']);
            /** @var COrderLineRepo $repo */
            $repo = \Monkey::app()->repoFactory->create('OrderLine');
            $line = $repo->findOne(['id' => $ids[0], 'orderId' => $ids[1]]);
            $oldActive = $line->orderLineStatus->isActive;


            /** @var COrderLine $line */
            $line = $repo->updateStatus($line, $ids[2]);



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
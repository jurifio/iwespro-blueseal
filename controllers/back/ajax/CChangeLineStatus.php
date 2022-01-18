<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CDirtySku;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\offline\productsync\import\alducadaosta\CAlducadaostaOrderAPI;
use bamboo\offline\productsync\import\edstema\CEdsTemaOrderApi;
use bamboo\offline\productsync\import\mpk\CMpkOrderApi;
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
            $ids = explode('-',$this->data['value']);
            /** @var COrderLineRepo $repo */
            $repo = \Monkey::app()->repoFactory->create('OrderLine');
            $line = $repo->findOne(['id' => $ids[0],'orderId' => $ids[1]]);
            $oldActive = $line->orderLineStatus->isActive;


            /** @var COrderLine $line */
            $line = $repo->updateStatus($line,$ids[2]);
            $orderLine = $line;
            $newActive = $line->orderLineStatus->isActive;
            if ($line->status == "ORD_FRND_SNDING" && $line->shopId==1) {
                /** @var CEmailRepo $emailRepo */
                $emailRepo = \Monkey::app()->repoFactory->create('Email');
                $res = $emailRepo->newMail( 'noreply@iwes.pro', ['amministrazione@iwes.it'], [], [],
                    'Ordine '.$line->id.'-'.$line->orderId.' Inoltrato allo Shop <a href="https://www.cartechinishop.com/blueseal/friend/ordini">clicca qui</a>',
                        'Ordine inviato allo shop per l\'accettazione e la preparazione dei documenti e del pacco',
                    '',
                    null,
                null,
                'MailGun',
                false,
                null);
            }

            if ($line->status == "ORD_WAIT") {
                //Value for api
                $currentYear=date('Y');
                    /** @var CObjectCollection $dirtySkus */
                  //  $dirtySkus = $line->productSku->dirtySku;
                    $dirtyProducts=\Monkey::app()->repoFactory->create('DirtyProduct')->findBy(['productId'=>$line->productId,'productVariantId'=>$line->productVariantId]);
                    foreach ($dirtyProducts as $dirtyProduct){
                        $dirtyDate=strtotime($dirtyProduct->creationDate);
                        $dirtyDate=date('Y',$dirtyDate);
                      //  if($dirtyDate==$currentYear){
                            $dirtySkus = \Monkey::app()->repoFactory->create('DirtySku')->findBy(['dirtyProductId' => $dirtyProduct->id,'productSizeId' => $line->productSizeId]);
                            foreach($dirtySkus as $dirtySku) {
                                $extSkuId = $dirtySku->extSkuId;
                            }
                       // }
                    }


                   /* if ($dirtySkus->count() != 1) {
                        throw new BambooException('Collezione fatta da più sku, controllare');
                    }*/

                    /** @var CDirtySku $dirtySku */
                    //$dirtySku = $dirtySkus->getFirst();

                    $orderId = $line->orderId;
                    $rowN = $line->id;

                    $row = [
                        "RowID" => $rowN,
                        "SKU" => $extSkuId,
                        "Value" => $line->friendRevenue,
                        "Payment_type" => $line->order->orderPaymentMethod->name,
                        "shopId"=>$line->shopId,
                        "productId"=>$line->productId,
                        "productVariantId"=>$line->productVariantId,
                        "productSizeId"=>$line->productSizeId
                    ];
                    /*
                if (ENV == 'prod') {
                    switch (true) {
                        case $line->shopId == 61:
                            $edstema = new CMpkOrderApi($orderId,$row);
                            $edstema->newOrder();
                            break;
                        case $line->shopId == 1:
                            $edstema = new CEdsTemaOrderApi($orderId,$row);
                            $edstema->newOrder();
                            break;
                    }


                }
                    */

            }
            $shopRepo = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $orderLine->remoteShopSellerId]);
            $orderRepo = \Monkey::app()->repoFactory->create('Order')->findOneBy(['id' => $orderLine->orderId,'remoteShopSellerId' => $orderLine->remoteShopSellerId]);
            IF(ENV=='prod') {
                if ($orderLine->remoteOrderSellerId != null) {
                    $db_host = $shopRepo->dbHost;
                    $db_name = $shopRepo->dbName;
                    $db_user = $shopRepo->dbUsername;
                    $db_pass = $shopRepo->dbPassword;
                    $shop = $shopRepo->id;
                    try {

                        $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                        $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                        $res = ' connessione ok <br>';
                    } catch (PDOException $e) {
                        $res = $e->getMessage();
                    }

                    $stmtOrderLine = $db_con->prepare("UPDATE OrderLine SET `status`='" . $orderLine->status . "' WHERE id=" . $orderLine->remoteOrderLineSellerId . " and orderId=" . $orderLine->remoteOrderSellerId);
                    $stmtOrderLine->execute();
                    $stmtOrder = $db_con->prepare("UPDATE `Order` SET `status`='" . $orderRepo->status . "' WHERE id=" . $orderRepo->remoteOrderSellerId);
                    $stmtOrder->execute();
                    $typePayment=\Monkey::app()->repoFactory->create('OrderPaymentMethod')->findOneBy(['id'=>$orderRepo->orderPaymentMethodId]);
                    $amountToReturn=$orderLine->netPrice-($orderLine->netPrice/100*$typePayment->paymentCommissionRate)-($orderLine->netPrice/100*11);
                    if($orderLine->status=='ORD_FRND_CANC' || $orderLine->status=='ORD_MISSNG' || $orderLine->status=='ORD_FRND_CANC' || $orderLine->status== 'ORD_ERR_SEND' || $orderLine->status== 'ORD_QLTY_KO') {
                        $stmtFindShopMovements = $db_con->prepare('select count(id) as countId   from ShopMovements where  orderId =' . $orderLine->remoteOrderSellerId . ' and isLocked=1');
                        $stmtFindShopMovements->execute();
                        while ($rowFindShopMovements = $stmtFindShopMovements->fetch(PDO::FETCH_ASSOC)) {
                            $countRow=$rowFindShopMovements['countId'];
                        }
                        if ($countRow == 0) {
                            $stmtUpdateRemoteShopMovements = $db_con->prepare("INSERT INTO ShopMovements (orderId,returnId,shopRefundRequestId,amount,`date`,valueDate,typeId,shopWalletId,note,isVisible,remoteIwesOrderId)
                    values(
                         '" . $orderLine->remoteOrderSellerId . "',
                          null,
                          null,
                          '" . $amountToReturn . "',
                          '" . $dateNow . "',
                          '" . $dateNow . "',
                         '2',
                          1,
                          'ordine Cancellato',
                          1,
                          '" . $orderLine->orderId . "'
                                                                                                                                                                                
                                                                                                                                                               
) ");
                            $stmtUpdateRemoteShopMovements->execute();
                        }
                    }
                }
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
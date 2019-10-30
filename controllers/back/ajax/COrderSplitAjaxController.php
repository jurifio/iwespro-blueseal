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
 * Class COrderSplitAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 28/10/2019
 * @since 1.0
 */
class COrderSplitAjaxController extends AAjaxController
{


    public function POST()
    {
        $orderId = \Monkey::app()->router->request()->getRequestData('orderId');
        $orderRepo = \Monkey::app()->repoFactory->create('Order');
        $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');
        $invoiceLineHasOrderLineRepo=\Monkey::app()->repoFactory->create('InvoiceLineHasOrderLineRepo');
        $orderLineStatisticsRepo=\Monkey::app()->repoFactory->create('OrderLineStatistics');
        $orderLineHasShipmentRepo=\Monkey::app()->repoFactory->create('OrderLineStatistics');
        $cartRepo = \Monkey::app()->repoFactory->create('Cart');
        $cartLineRepo = \Monkey::app()->repoFactory->create('CartLine');
        $originalOrder = $orderRepo->findOneBy(['id' => $orderId]);
        $originalCart = $cartRepo->findOneBy(['id' => $originalOrder->cartId]);
        $orderLineCollect = $orderLineRepo->findBy(['orderId' => $orderId]);
        $orderLineWorking = ['ORD_WAIT','ORD_PENDING','ORD_LAB','ORD_FRND_OK','ORD_FRND_SENT','ORD_CHK_IN','ORD_PCK_CLI','ORD_FRND_SNDING','ORD_MAIL_PREP_C','ORD_FRND_ORDSNT'];
        $orderLineShipped = ['ORD_ARCH','ORD_SENT','ORD_FRND_PYD'];
        $orderLineCancel = ['ORD_FRND_CANC','ORD_MISSNG','ORD_CANCEL','ORD_QLTY_KO','ORD_ERR_SEND'];

        foreach ($orderLineCollect as $orderLines) {
            //controllo la riga se è cancellata
            if (in_array($orderLines->status,$orderLineCancel,true)) {
                //creo il carrello
                $cart = $cartRepo->getEmptyEntity('Cart');
                $cart->orderPaymentMethodId = $originalCart->orderPaymentMethodId;
                $cart->userId = $originalCart->userId;
                $cart->cartTypeId = $originalCart->cartTypeId;
                $cart->billingAddressId = $originalCart->billingAddressId;
                $cart->shipmentAddressId = $originalCart->shipmentAddressId;
                $cart->lastUpdate = $originalCart->lastUpdate;
                $cart->creationDate = $originalCart->creationDate;
                if ($originalCart->hasInvoice = null) {
                    $cart->hasInvoice = $originalCart->hasInvoice;
                }
                $cart->remoteShopSellerId = $originalCart->remoteShopSellerId;
                $shopFindSeller = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $cart->remoteShopSellerId]);
                $db_hostSeller = $shopFindSeller->dbHost;
                $db_nameSeller = $shopFindSeller->dbName;
                $db_userSeller = $shopFindSeller->dbUsername;
                $db_passSeller = $shopFindSeller->dbPassword;

                try {

                    $db_con = new PDO("mysql:host={$db_hostSeller};dbname={$db_nameSeller}",$db_userSeller,$db_passSeller);
                    $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                    $res = ' connessione ok <br>';
                } catch (PDOException $e) {
                    $res = $e->getMessage();
                }
                try {
                    $selectRemoteCart = $db_con->prepare('SELECT *  FROM  Cart WHERE id=' . $originalCart->remoteCartSellerId);
                    $selectRemoteCart->execute();
                    $rowselectRemoteCart = $selectRemoteCart->fetch(PDO::FETCH_ASSOC);
                    if ($rowselectRemoteCart['isParallel'] != null) {
                        $isParallel = $rowselectRemoteCart['isParallel'];
                    } else {
                        $isParallel = "null";
                    }
                    if ($rowselectRemoteCart['isImport'] != null) {
                        $isImport = $rowselectRemoteCart['isImport'];
                    } else {
                        $isImport = "null";
                    }

                    $insertRemoteCart = $db_con->prepare('INSERT INTO Cart (
                                                                          orderPaymentMethodId,
                                                                          couponId,
                                                                          userId,
                                                                          cartTypeId,  
                                                                          billingAddressId,
                                                                          shipmentAddressId,
                                                                          lastUpdate,
                                                                          creationDate,
                                                                          hasInvoice,
                                                                          isParallel,
                                                                          isImport)
                                                                           VALUES (
                                                                          \'' . $rowselectRemoteCart['orderPaymentMethodId'] . '\',
                                                                          null,
                                                                          \'' . $rowselectRemoteCart['userId'] . '\',
                                                                          \''. $rowselectRemoteCart['cartTypeId'] . '\',
                                                                          \'' . $rowselectRemoteCart['billingAddressId'] . '\',
                                                                          \'' . $rowselectRemoteCart['shipmentAddressId'] . '\',
                                                                          \'' . $rowselectRemoteCart['lastUpdate'] . '\',
                                                                          \'' . $rowselectRemoteCart['creationDate'] . '\',
                                                                          \'' . $rowselectRemoteCart['hasInvoice'] . '\',
                                                                          ' . $isParallel . ',
                                                                          ' . $isImport . ')');
                    $insertRemoteCart->execute();
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('COrderSplitAjaxController','Error','select remote Cart  ' , $originalCart->remoteCartSellerId,$e);
                    $res=$e;
                    return $res;
                }
                $newremoteCartId = $db_con->lastInsertId();
                $cart->remoteCartSellerId = $newremoteCartId;
                $cart->insert();
                //id nuovo carrello
                $FindLastCartId=\Monkey::app()->repoFactory->create('Cart')->findOneBy(['remoteCartSellerId'=>$newremoteCartId,'remoteShopSellerId'=>$originalCart->remoteShopSellerId]);
                $newCartId = $FindLastCartId->id;
                //inserimento righe carrello
                $cartLine = $cartLineRepo->findOneBy(['cartId' => $originalOrder->cartId,'productId' => $orderLines->productId,'productVariantId' => $orderLines->productVariantId,'productSizeId' => $orderLines->productSizeId]);
                $cartLine->cartId = $newCartId;
                try {
                    $selectRemoteCartLine = $db_con->prepare('SELECT * FROM CartLine WHERE id=' . $cartLine->remoteCartLineSellerId . ' AND cartId=' . $cart->remoteCartSellerId);
                    $selectRemoteCartLine->execute();
                    $rowselectRemoteCartLine = $selectRemoteCartLine->fetch(PDO::FETCH_ASSOC);
                    if ($rowselectRemoteCartLine['isParallel'] != null) {
                        $isParallel = $rowselectRemoteCartLine['isParallel'];
                    } else {
                        $isParallel = "null";
                    }
                    if ($rowselectRemoteCartLine['isImport'] != null) {
                        $isImport = $rowselectRemoteCartLine['isImport'];
                    } else {
                        $isImport = "null";
                    }
                    $updateRemoteCartLine = $db_con->prepare('UPDATE CartLine  SET cartId=' . $newremoteCartId . '  where id=' . $cartLine->remoteCartLineSellerId . ' 
                                                                                           and cartId=' . $cart->remoteCartSellerId . '  
                                                                                           and productId=' . $cartLine->productId . '
                                                                                           and productVariantId=' . $cartLine->productVariantId . '
                                                                                           and productSizeId='. $cartLine->productSizeId);
                    $updateRemoteCartLine->execute();
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('COrderSplitAjaxController','Error',' update remote CartLine  ' , $cartLine->remoteCartLineSellerId,$e);
                    $res=$e;
                    return $res;
                }
                $cartLine->update();
                $newOrder = $orderRepo->getEmptyEntity();
                $newOrder->orderPaymentMethodId = $originalOrder->orderPaymentMethodId;
                $newOrder->orderShippingMethodId = $originalOrder->orderShippingMethodId;
                $newOrder->userId = $originalOrder->userId;
                $newOrder->status = 'ORD_CANCEL';
                $newOrder->frozenBillingAddress = $originalOrder->frozenBillingAddress;
                $newOrder->frozenShippingAddress = $originalOrder->frozenShippingAddress;
                $newOrder->billingAddressId = $originalOrder->billingAddressId;
                $newOrder->shippingPrice = $orderLines->shippingCharge;
                $newOrder->userDiscount = $orderLines->userCharge;
                $newOrder->couponDiscount = $orderLines->couponCharge;
                $newOrder->paymentModifier = $originalOrder->paymentModifier;
                $newOrder->grossTotal = $orderLines->netPrice;
                $newOrder->netTotal = $orderLines->netPrice - $orderLines->shippingCharge;
                $newOrder->vat = $orderLines->vat;
                $newOrder->sellingFee = 0;
                $newOrder->customModifier = 0;
                $newOrder->orderDate = $originalOrder->orderDate;
                $newOrder->note = $originalOrder->note;
                if ($originalOrder != null) {
                    $newOrder->transactionNumber = $originalOrder->transactionNumber;
                    $newOrder->transactionMac = $originalOrder->transactionMac;
                    $newOrder->paidAmount = $orderLines->netPrice;
                    $newOrder->paymentDate = $originalOrder->paymentDate;
                }
                $newOrder->lastUpdate = $originalOrder->lastUpdate;
                $newOrder->creationDate = $originalOrder->creationDate;
                $newOrder->remoteShopSellerId = $originalOrder->remoteShopSellerId;
                $newOrder->isOrderMarketplace = $originalOrder->isOrderMarketplace;
                $newOrder->marketplaceId = $originalOrder->marketplaceId;
                $newOrder->marketplaceOrderId = $originalOrder->marketplaceOrderId;
                $newOrder->isShippingToIwes = $originalOrder->isShippingToIwes;
                $newOrder->orderIdFather = $originalOrder->id;
                if ($originalOrder != null) {
                    $transactionNumber = $originalOrder->transactionNumber;
                    $transactionMac = $originalOrder->transactionMac;
                    $paidAmount = $orderLines->netPrice;
                    $paymentDate = $originalOrder->paymentDate;
                } else {
                    $transactionNumber = 'null';
                    $transactionMac = 'null';
                    $paidAmount = 'null';
                    $paymentDate = 'null';
                }
                $netTotal=$orderLines->netPrice - $orderLines->shippingCharge;
                if($originalOrder->orderPaymentMethodId!=null){
                    $orderPaymentMethodId=$originalOrder->orderPaymentMethodId;
                }else{
                    $orderPaymentMethodId='null';
                }
                if($originalOrder->orderShippingMethodId!=null){
                    $orderShippingMethodId=$originalOrder->orderShippingMethodId;
                }else{
                    $orderShippingMethodId='null';
                }



                try {
                    if($originalCart->remoteShopSellerId!=1) {
                        $insertRemoteOrder = $db_con->prepare("INSERT INTO `Order` (
           orderPaymentMethodId,
           orderShippingMethodId,
           couponId,
           userId,
           cartId,
          `status`,
           frozenBillingAddress,
           frozenShippingAddress,
           billingAddressId,
           shipmentAddressId,
           shippingPrice,
           userDiscount,
           couponDiscount,
           paymentModifier,
           grossTotal,
           netTotal,
           `vat`,
           sellingFee,
           customModifier,
           orderDate,
           `note`,
           shipmentNote,  
           transactionNumber,          
           transactionMac,
           paidAmount,
           paymentDate,
           lastUpdate,
           creationDate,
           hasInvoice,
           remoteIwesOrderId,          
           isParallel,
           remoteSellerId,
           isOrderMarketplace,
           marketplaceId,
           marketplaceOrderId,
           isShippingToIwes ,
           isImport,
           orderIdFather          
           ) VALUES (
           '" . $orderPaymentMethodId . "',   
           '" . $orderShippingMethodId . "',
            null,
            '" . $originalOrder->userId . "',
            '" . $newremoteCartId . "',
            'ORD_CANCEL',
            '" . $originalOrder->frozenBillingAddress. "',
            '" . $originalOrder->frozenShippingAddress . "',
            '" . $originalOrder->billingAddressId . "',
            '" . $originalOrder->shipmentAddressId . "',
            '" . $orderLines->shippingCharge . "',
            '" . $orderLines->userCharge . "',
            '" . $orderLines->couponCharge . "',
            '" . $originalOrder->paymentModifier . "',
            '" . $orderLines->netPrice . "',
            '" . $netTotal . "',
            '" . $orderLines->vat . "',
            0,
            0,
            '" . $originalOrder->orderDate . "',
            '" . $originalOrder->note . "',
            null,
            '" . $transactionNumber . "',
            '" . $transactionMac . "',
            '" . $paidAmount . "',
            '" . $paymentDate . "',
            '" . $originalOrder->lastUpdate . "',
            '" . $originalOrder->creationDate . "',
            null,
            null,
            null,
            '" . $originalOrder->remoteShopSellerId . "',
            '" . $originalOrder->isOrderMarketplace . "',
            '" . $originalOrder->marketplaceId . "',
            '" . $originalOrder->marketplaceOrderId . "',
            '" . $originalOrder->isShippingToIwes . "',
            1,
            '" . $originalOrder->remoteOrderSellerId . "')");
                    }else{
                        $insertRemoteOrder = $db_con->prepare("INSERT INTO `Order` (
           orderPaymentMethodId,
           orderShippingMethodId,
           couponId,
           userId,
           cartId,
          `status`,
           frozenBillingAddress,
           frozenShippingAddress,
           billingAddressId,
           shipmentAddressId,
           shippingPrice,
           userDiscount,
           couponDiscount,
           paymentModifier,
           grossTotal,
           netTotal,
           `vat`,
           sellingFee,
           customModifier,
           orderDate,
            typeOrder,
           `note`,
           transactionNumber,          
           transactionMac,
           shipmentNote,  
           paidAmount,
           paymentDate,
           lastUpdate,
           creationDate,
           remoteIwesOrderId, 
           hasInvoice,         
           isParallel,
           remoteSellerId,
           isOrderMarketplace,
           marketplaceId,
           marketplaceOrderId,
           isShippingToIwes ,
           isImport,
           orderIdFather          
           ) VALUES (
           '" . $orderPaymentMethodId . "',   
           '" . $orderShippingMethodId . "',
            null,
            '" . $originalOrder->userId . "',
            '" . $newremoteCartId . "',
            'ORD_CANCEL',
            '" . addslashes($originalOrder->frozenBillingAddress) . "',
            '" . addslashes($originalOrder->frozenShippingAddress) . "',
            '" . $originalOrder->billingAddressId . "',
            '" . $originalOrder->shipmentAddressId . "',
            '" . $orderLines->shippingCharge . "',
            '" . $orderLines->userCharge . "',
            '" . $orderLines->couponCharge . "',
            '" . $originalOrder->paymentModifier . "',
            '" . $orderLines->netPrice . "',
            '" . $netTotal . "',
            '" . $orderLines->vat . "',
            0,
            0,
            '" . $originalOrder->orderDate . "',
            null,
            '" . $originalOrder->note . "',
            '" . $transactionNumber . "',
            '" . $transactionMac . "',
             null,
            '" . $paidAmount . "',
            '" . $paymentDate . "',
            '" . $originalOrder->lastUpdate . "',
            '" . $originalOrder->creationDate . "',
            null,
            null,
            null,
            '" . $originalOrder->remoteShopSellerId . "',
            '" . $originalOrder->isOrderMarketplace . "',
            '" . $originalOrder->marketplaceId . "',
            '" . $originalOrder->marketplaceOrderId . "',
            '" . $originalOrder->isShippingToIwes . "',
            1,
            '" . $originalOrder->remoteOrderSellerId . "')");
                    }
            $insertRemoteOrder->execute();

                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('COrderSplitAjaxController','Error',' insert remote Order  ' , $originalOrder->id,$e);
                    return $res=$e;
                }
                $newremoteOrderId=$db_con->lastInsertId();
                $newOrder->remoteOrderSellerId=$newremoteOrderId;
                $newOrder->insert();
                $findLastOrder=\Monkey::app()->repoFactory->create('Order')->findOneBy(['remoteOrderSellerId'=>$newremoteOrderId,'remoteShopSellerId'=>$originalOrder->remoteShopSellerId]);
                $newOrderId=$findLastOrder->id;
                try{
                    $stmtDeleteinvoiceLineHasOrderLineRepo=$db_con->prepare('delete from InvoiceLineHasOrderLine where orderLineOrderId='.$originalOrder->remoteOrderSellerId.' and orderLineId='.$orderLines->id);
                    $stmtDeleteinvoiceLineHasOrderLineRepo->execute();
                }catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('COrderSplitAjaxController','Error',' Delete InvoiceLineHasOrderLine  ' , $orderLines->id.'-'.$orderLines->orderId,$e);
                    $res=$e;
                    return $res;
                }
                try{
                    $stmtDeleteOrderLineStatistics=$db_con->prepare('delete from  OrderLineStatistics  WHERE orderId='. $originalOrder->remoteOrderSellerId.' and orderLineId='.$orderLines->id);
                    $stmtDeleteOrderLineStatistics->execute();
                }catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('COrderSplitAjaxController','Error',' Delete OrderLineStatistics   ' , $orderLines->id.'-'.$orderLines->orderId,$e);
                    $res=$e;
                    return $res;
                }
                try{
                    $stmtDeleteOrderLineHasShipment=$db_con->prepare('delete from  OrderLineHasShipment  WHERE orderId='. $originalOrder->remoteOrderSellerId.' and orderLineId='.$orderLines->id);
                    $stmtDeleteOrderLineHasShipment->execute();
                }catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('COrderSplitAjaxController','Error',' Delete OrderLineStatistics   ' , $orderLines->id.'-'.$orderLines->orderId,$e);
                    $res=$e;
                    return $res;
                }

                try {

                    $stmtUpdateRemoteOrderLine = $db_con->prepare("Update OrderLine SET OrderId=" . $newremoteOrderId . " WHERE 
                                                                      id=" . $orderLines->remoteOrderLineSellerId . " 
                                                                      AND  orderId=" . $orderLines->remoteOrderSellerId . "
                                                                      AND  productId=" . $orderLines->productId . "
                                                                      AND  productVariantId=" . $orderLines->productVariantId . "
                                                                      AND  productSizeId=" . $orderLines->productSizeId
                                                                      );
                    $stmtUpdateRemoteOrderLine->execute();
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('COrderSplitAjaxController','Error',' update remoteOrderLine  ' , $orderLines->id.'-'.$orderLines->orderId,$e);
                    $res=$e;
                    return $res;
                }

                $orderLines->orderId=$newOrderId;
                $findDeleteInvoiceLineHasOrderLine=$invoiceLineHasOrderLineRepo->findOneBy(['orderLineId'=>$orderLines->id,'orderLineOrderId'=>$orderLines->orderId]);
                if($findDeleteInvoiceLineHasOrderLine!=null){
                    $findDeleteInvoiceLineHasOrderLine->delete();
                }
                $findDeleteOrderLineStatistics=$orderLineStatisticsRepo->findOneBy(['orderLineId'=>$orderLines->id,'orderId'=>$orderLines->orderId]);
                if($findDeleteOrderLineStatistics!=null) {
                    $findDeleteOrderLineStatistics->delete();
                }
                $findDeleteOrderLineHasShipment=$orderLineHasShipmentRepo->findOneBy(['orderLineId'=>$orderLines->id,'orderId'=>$orderLines->orderId]);
                if($findDeleteOrderLineHasShipment!=null) {
                    $findDeleteOrderLineHasShipment->delete();
                }
                $orderLines->update();
                $originalOrder->shippingPrice -= $orderLines->shippingCharge;
                $originalOrder->userDiscount-=$orderLines->userCharge;
                $originalOrder->couponDiscount-=$orderLines->couponCharge;
                $originalOrder->grossTotal-=$orderLines->netPrice;
                $netTotal=$orderLines->netPrice+$orderLines->shippingCharge;
                $originalOrder->netTotal-=$netTotal;
                $originalOrder->vat-=$orderLines->vat;
                $originalOrder->paidAmount-=$netTotal;
                $originalOrder->update();
                try{
                $stmtUpdateRemoteOrder=$db_con->prepare('UPDATE `Order` SET shippingPrice=shippingPrice-'.$orderLines->shippingCharge.',
                                                                                     userDiscount=userDiscount-'.$orderLines->userCharge.',       
                                                                                     couponDiscount=couponDiscount-'.$orderLines->couponCharge.',
                                                                                     grossTotal=grossTotal-'.$orderLines->netPrice.',
                                                                                     netTotal=netTotal-'.$netTotal.',
                                                                                     vat=vat-'.$orderLines->vat.',
                                                                                     paidAmount=paidAmount-'.$netTotal.' 
                                                                                     WHERE id='.$originalOrder->remoteOrderSellerId
                                                                                     );
                $stmtUpdateRemoteOrder->execute();
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('COrderSplitAjaxController','Error',' update remoteOrder  ' , $originalOrder->remoteOrderSellerId,$e);
                    $res=$e;
                    return $res;
                }
                                                                                     
                                                                                     
                                                                                     
                                                                                     

                            } else {
                continue;
            }


        }


        return $res = 'ok Split degli ordni eseguito';
    }
}
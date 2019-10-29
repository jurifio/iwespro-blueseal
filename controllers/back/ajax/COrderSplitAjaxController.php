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
      $orderRepo=\Monkey::app()->repoFactory->create('Order');
      $orderLineRepo=\Monkey::app()->repoFactory->create('OrderLine');
      $cartRepo=\Monkey::app()->repoFactory->create('Cart');
      $cartLineRepo=\Monkey::app()->repoFactory->create('CartLine');
      $originalOrder=$orderRepo->findOneBy(['id'=>'$orderId']);
      $originalCart=$cartRepo->findOneBy(['id'=>$originalOrder->cartId]);
      $orderLineCollect=$orderLineRepo->findBy(['orderId'=>$orderId]);
        $orderLineWorking = ['ORD_WAIT','ORD_PENDING','ORD_LAB','ORD_FRND_OK','ORD_FRND_SENT','ORD_CHK_IN','ORD_PCK_CLI','ORD_FRND_SNDING','ORD_MAIL_PREP_C','ORD_FRND_ORDSNT'];
        $orderLineShipped = ['ORD_ARCH','ORD_SENT','ORD_FRND_PYD'];
        $orderLineCancel = ['ORD_FRND_CANC','ORD_MISSNG','ORD_CANCEL','ORD_QLTY_KO','ORD_ERR_SEND'];

      foreach($orderLineCollect as $orderLines){
          //controllo la riga se è cancellata
          if(in_array($orderLines->status,$orderLineCancel,true)){
              //creo il carrello
            $cart=$cartRepo->getEmptyEntity('Cart');
            $cart->orderPaymentMethodId=$originalCart->orderParymentmethodId;
            $cart->userId=$originalCart->userId;
            $cart->cartTypeId=$originalCart->cartTypeId;
            $cart->billingAddressId=$originalCart->billingAddessId;
            $cart->shipmentAddressId=$originalCart->shipmentAddressId;
            $cart->lastUpdate=$orginalCart->lastUpdate;
            $cart->creationDate=$orginalCart->creationDate;
            if($originalCart->hasInvoice1=null){
                $cart->hasInvoice=$originalCart->hasInvoice;
            }
              $cart->remoteShopSellerId=$originalCart->remoteShopSellerId;
              $shopFindSeller = \Monkey ::app() -> repoFactory -> create('Shop') -> findOneBy(['id' => $cart->remoteShopSellerId]);
              $db_hostSeller = $shopFindSeller -> dbHost;
              $db_nameSeller = $shopFindSeller -> dbName;
              $db_userSeller = $shopFindSeller -> dbUsername;
              $db_passSeller = $shopFindSeller -> dbPassword;

              try {

                  $db_con = new PDO("mysql:host={$db_hostSeller};dbname={$db_nameSeller}", $db_userSeller, $db_passSeller);
                  $db_con -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                  $res = ' connessione ok <br>';
              } catch (PDOException $e) {
                  $res = $e -> getMessage();
              }
              try {
                  $selectRemoteCart = $db_con->prepare('SELECT *  FROM  Cart WHERE id='.$orginalCart->remoteCartSellerId);
                  $selectRemoteCart->execute();
                  $rowselectRemoteCart=$selectRemoteCart->fetch(PDO::FETCH_ASSOC);
                  if($rowselectRemoteCart['isParallel'] != null) {
                      $isParallel=$rowselectRemoteCart['isParallel'];
                  }else{
                      $isParallel="null";
                  }
                  if($rowselectRemoteCart['isImport'] != null) {
                      $isImport=$rowselectRemoteCart['isImport'];
                  }else{
                      $isImport="null";
                  }

                  $insertRemoteCart = $db_con->prepare("INSERT INTO Cart (orderPaymentMethodId,
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
                                                                          " . $rowselectRemoteCart['orderPaymentMethodId'] . ",
                                                                          null,
                                                                          " . $rowselectRemoteCart['userId'] . ",
                                                                          " . $rowselectRemoteCart['cartTypeId'] . ",
                                                                          " . $rowselectRemoteCart['billingAddressId'] . ",
                                                                          " . $rowselectRemoteCart['shipmentAddressId'] . ",
                                                                          '" . $rowselectRemoteCart['lastUpdate'] . "',
                                                                          '" . $rowselectRemoteCart['creationDate'] . "',
                                                                          " . $rowselectRemoteCart['hasInvoice'] . ",
                                                                          " . $isParallel. ",
                                                                          " . $isImport. ")");
                  $insertRemoteCart->execute();
              } catch (\Throwable $e) {
                  \Monkey::app()->applicationLog('COrderSplitAjaxController','Error','select remote Cart  ' . $orginalCart->remoteCartSellerId,$e);
              }
              $newremoteCartId=$db_con->lastInsertId();
              $cart->remoteCartSellerId=$newremoteCartId;
              $cart->insert();
              //id nuovo carrello
              $newcartId=$cart->lastInsertId();
              //inserimento righe carrello
              $cartLine=$cartLineRepo->finOneBy(['cartId'=>$originalOrder->cartId,'productId'=>$orderLines->productId,'productVariantId'=>$orderLines->productVariantId,'productSizeId'=>$orderLines->productSizeId]);
              $newCartLine=$cartLineRepo->getEmptyEntity();
              $newCartLine->cartId=$cartLine->cartId;
              $newCartLine->productId=$cartLine->productId;
              $newCartLine->productVariantId=$cartLine->productVariantId;
              $newCartLine->productSizeId=$cartLine->productSizeId;
              $newCartLine->remoteShopSellerId=$cartLine->remoteShopSellerId;
              try {
              $selectRemoteCartLine=$db_con->prepare('SELECT * FROM CartLine WHERE id='.$cartLine->remoteCartLineSellerId.' AND cartId='.$cart->remoteCartSellerId);
              $selectRemoteCartLine->execute();
              $rowselectRemoteCartline=$selectRemoteCartLine->fetch(PDO::FETCH_ASSOC);
              if($rowselectRemoteCartLine['isParallel'] != null) {
                  $isParallel=$rowselectRemoteCartLine['isParallel'];
              }else{
                  $isParallel="null";
              }
              if($rowselectRemoteCartLine['isImport'] != null) {
                  $isImport=$rowselectRemoteCartLine['isImport'];
              }else{
                  $isImport="null";
              }
              $updateRemoteCartLine=$db_con->prepare("UPDATE CartLine  SET cartId=".$newremoteCartId."  where id=".$cartLine->remoteCartLineSellerId."  and cartId=".$cart->remoteCartSellerId."  
                                                                                           and productId=".$cartLine->productId."
                                                                                           and productVariantId=$cartLine->produtVariantId."
                                                                                           and productSize" . $rowselectRemoteCartline['productId'] . ",
                                                                                           " . $rowselectRemoteCartline['productVariantId'] . ",  
                                                                                           " . $rowselectRemoteCartline['rpoductSizeId'] . ",  
                                                                                           " . $isParallel . ",  
                                                                                           " . $isImport . "   
                                                                                        ) ");
                $updateRemoteCartLine->execute();
              } catch (\Throwable $e) {
                  \Monkey::app()->applicationLog('COrderSplitAjaxController','Error','select and update remote CartLine  ' . $cartLine->remoteCartLineSellerId,$e);
              }
              $newRemoteCartLineSeller=$db_con->lastInsertId();
                $newCartLine->remoteCartLIneSellerId=






          }else{
              continue;
          }

      }


return $res='ok';
    }
}
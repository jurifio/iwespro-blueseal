<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CUserAddress;
use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use PDO;
use prepare;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CSite;
use bamboo\domain\entities\CUserHasShop;

/**
 * Class CImportExternalOrder
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 05/10/2018
 * @since 1.0
 */
class CImportExternalOrder extends AAjaxController
{


    public function POST()
    {

        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        $res = "";
        /********marketplace********/
        $db_host = "dev.barbagalloshop.com";
        $db_name = "barbagalloshop_dev";
        $db_user = "root";
        $db_pass = "geh44fed";
        define("HOST", "dev.barbagalloshop.com");
        define("USERNAME", "root");
        define("PASSWORD", "geh44fed");
        define("DATABASE", "barbagalloshop_dev");
        try {

            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
            $db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $res .= " connessione ok <br>";
        } catch ( PDOException $e ) {
            $res .= $e->getMessage();
        }
        $stmtUser = $db_con->prepare("SELECT 
                                     p.id AS remoteId,
                                     p.id_lang AS  langId,
                                     p.username as username,
                                     p.passwotd as password,
                                     p.email AS email,
                                     concat(p.registrationEntryPoint,'-','51') as registrationEntryPoint,
                                     p.isActive as isActive,
                                     p.isDelete as isDelete,
                                     p.lastSeen as lastSeen,
                                     p.creationDate as creationDate,
                                     p.isEmailChanged as isEmailChanged,
                                     ud.name AS name,
                                     ud.surname AS surname,
                                     ud.screenName as screenName,
                                     ud.birthDate as birtDate,
                                     ud.phone as phone,
                                     ud.gender as gender,
                                     ud.regDate as regDate,
                                     ud.fiscalCode as fiscalCode,
                                     ud.note as note
                                    
                                     FROM Product  p
                                      JOIN UserDetails ud ON p.id =ud.userId");
        $stmtUser->execute();

        $userRepo = \Monkey::app()->repoFactory->create('User');
        $userDetailsRepo = \Monkey::app()->repoFactory->create('UserDetails');
        $userEmailRepo = \Monkey::app()->repoFactory->create('UserEmail');
        $getUserIdRepo = \Monkey::app()->repoFactory->create('User');
        $userAddressRepo = \Monkey::app()->repoFactory->create('UserAddress');
        $findCountryRepo = \Monkey::app()->repoFactory->create('Country');
        $cartRepo = \Monkey::app()->repoFactory->create('Cart');
        $cartLineRepo =\Monkey::app()->repoFactory->create('CartLine');
        $orderRepo = \Monkey::app()->repoFactory->create('Order');
        $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');
        $insertOrderRepo = \Monkey::app()->repoFactory->create('Order');
        $findshippingDetailsRepo = \Monkey::app()->repoFactory->create('UserAddress');
        $findaddressBillingDetailsRepo = \Monkey::app()->repoFactory->create('UserAddress');
        $findaddressShippingDetailsRepo = \Monkey::app()->repoFactory->create('UserAddress');
        $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');
        $orderRepo = \Monkey::app()->repoFactory->create('Order');
        $marketplaceHasShopRepo = \Monkey::app()->repoFactory->create('MarketplaceHasShop');
        $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');
        //ALL USER CREATION
        while ($rowUser = $stmtUser->fetch(PDO::FETCH_ASSOC)) {
            $checkUserIfExist = $userRepo->findOneBy(['email' => $rowUser['email']]);
            if (null == $checkUserIfExist) {

                //User creation
                $insertUser = $userRepo->getEmptyEntity();
                $insertUser->langId = $rowUser['langId'];
                $insertUser->email = $rowUser['email'];
                $insertUser->registrationEntryPoint = $rowUser['registrationEntryPoint'];
                $insertUser->isActive = $rowUser['isActive'];
                $insertUser->isDeleted = $rowUser['isDelete'];
                $insertUser->creationDate = $rowUser['creationDate'];
                $insertUser->isEmailChanged = $rowUser['isEmailChanged'];
                $insertUser->remoteId = $rowUser['remoteId'];
                $insertUser->remoteShopId = 51;
                $insertUser->insert();
                $getuserId = $userRepo->findOneBy(['email' => $rowUser['email']]);
                $userId = $getuserId->id;


                //UserDetail Creation
                $insertUserDetails = $userDetailsRepo->getEmptyEntity();
                $insertUserDetails->userId = $userId;
                $insertUserDetails->name = $rowUser['name'];
                $insertUserDetails->surname = $rowUser['surname'];
                $insertUserDetails->birthDate = $rowUser['birthday'];
                $insertUserDetails->phone = $rowUser['phone'];

                $insertUserDetails->gender = $rowUser['gender'];
                $insertUserDetails->regDate = $rowUser['regDate'];
                $insertUserDetails->fiscalcode = $rowUser['fiscalcode'];
                $insertUserDetails->note = $rowUser['note'];
                $insertUserDetails->insert();


                //UserEmail Creation
                $insertUserEmail = $userEmailRepo->getEmptyEntity();
                $insertUserEmail->userid = $userId;
                $insertUserEmail->address = $rowUser['email'];
                $insertUserEmail->isPrimary = '1';
                $insertUserEmail->insert();
                $res .= " inserimento utente" . $rowUser['email'] . " eseguito<br>";
            } else {
                $res .= "utente" . $rowUser['email'] . " già in elenco";
            }

        }

        $stmtUserAddress = $db_con->prepare("SELECT 
                                                          us.userId    as remoteId,
                                                          us.isBilling as isBilling,
                                                          us.isDefault as isDefault,
                                                          us.name      as name,
                                                          us.surname   as surname,
                                                          us.company   as company,
                                                          us.address   as address,
                                                          us.extra     as extra,
                                                          us.province  as province,
                                                          us.city      as city,
                                                          us.postcode  as postcode,
                                                          us.countryId as countryId,
                                                          us.phone     as phone,
                                                          us.lastUsed  as lastUsed,
                                                          us.fiscalCode as fiscalCode from UserAddress us");

        $stmtUserAddress->execute();
        while ($rowUserAddress = $stmtUserAddress->fetch(PDO::FETCH_ASSOC)) {
            $checkUserAddressisBillingIfExist = $userAddressRepo->findOneBy(
                ['remoteId' => $rowUserAddress['remoteId'],
                    'name' => $rowUserAddress['name'],
                    'surname' => $rowUserAddress['surname'],
                    'address' => $rowUserAddress['address'],
                    'province' => $rowUserAddress['province'],
                    'city' => $rowUserAddress['city'],
                    'postcode' => $rowUserAddress['postcode'],
                    'countryId' => $rowUserAddress['countryId']
                ]);
            if (null == $checkUserAddressisBillingIfExist) {
                $userAddressInsert = $userAddressRepo->getEmptyEntity();
                $findUserInsert = $userRepo->findOneBy(['remoteId' => $rowUserAddress['remoteId']]);
                $userId = $findUserInsert->id;
                $userAddressInsert->userId = $userId;
                $userAddressInsert->isBilling = $rowUserAddress['isBilling'];
                $userAddressInsert->isDefault = $rowUserAddress['isDefault'];
                $userAddressInsert->name = $rowUserAddress['name'];
                $userAddressInsert->surname = $rowUserAddress['surname'];
                $userAddressInsert->company = $rowUserAddress['company'];
                $userAddressInsert->address = $rowUserAddress['address'];
                $userAddressInsert->extra = $rowUserAddress['extra'];
                $userAddressInsert->province = $rowUserAddress['province'];
                $userAddressInsert->city = $rowUserAddress['city'];
                $userAddressInsert->postcode = $rowUserAddress['postcode'];
                $userAddressInsert->countryId = $rowUserAddress['countryId'];
                $userAddressInsert->phone = $rowUserAddress['phone'];
                $userAddressInsert->lastUsed = $rowUserAddress['lastUsed'];
                $userAddressInsert->fiscalCode = $rowUserAddress['fiscalCode'];
                $userAddressInsert->remoteId = $rowUserAddress['remoteId'];
                $userAddressInsert->remoteShopId = 51;
                $userAddressInsert->insert();
            }
        }



        /***** inserimento Carrello *******/
        $stmtCart =$db_con->prepare("SELECT 
                                               c.id as remoteId,
                                               c.orderPaymentMethodId as orderPaymentMethodId,
                                               c.couponId as couponId,
                                               c.userId as userId,
                                               c.cartTypeId as cartTypeId,
                                               c.billingAddressId as billingAddressId,
                                               c.shipmentAddressId as shipmentAddressId,
                                               c.lastUpdate as lastUpdate,
                                               c.creationDate as creationDate,
                                               c.hasInvoice as hasInvoice from Cart c");
        $stmtCart->execute();
        while ($rowCart = $stmtCart->fetch(PDO::FETCH_ASSOC)) {
            $checkCartIfExist = $cartRepo->findOneBy(
                    ['remoteId' => $rowCart['remoteId'],
                    'remoteShopId' => 51]);
            if (null == $checkCartIfExist) {
                $insertCart=$cartRepo->getEmptyEntity();
                $insertCart->orderPaymentMethodId=$rowCart['orderPaymentMethodId'];
                $userFind=$userRepo->findOneBy(['remoteId'=>$rowCart['userId'], 'remoteShopId' => 51]);
                $userId=$userFind->id;
                $insertCart->userId=$userId;
                $insertCart->cartType=$rowCart['cartTypeId'];;
                $billingAddressIdFind=$userAddressRepo->findOneBy(['remoteId'=>$rowCart['billingAddressId'],'remoteShopId'=>51]);
                $billingAddressId=$billingAddressIdFind->id;
                $insertCart->billingAddressId=$billingAddressId;
                $shipmentAddressIdFind=$userAddressRepo->findOneBy(['remoteId'=>$rowCart['shipmentAddressId'],'remoteShopId'=>51]);
                $shipmentAddressId=$shipmentAddressIdFind->id;
                $insertCart->shipmentAddressId=$shipmentAddressId;
                $insertCart->lastUpdate=$rowCart['lastUpdate'];
                $insertCart->hasInvoice=$rowCart['hasInvoice'];
                $insertCart->remoteId=$rowCart['remoteId'];
                $insertCart->remoteShopId=51;
                $insertCart->insert();

            }else{
                $res .= '<br>carrello già esistente';
            }
        }

        /***** inserimento righe carrello *********/

        $stmtCartLine=$db_con->prepare("SELECT
                                            cl.id as remoteId
                                            cl.cartId as remoteCartId,
                                            cl.productId as productId,
                                            cl.productVariantId as productVariantId,
                                            cl.productSizeId as productSizeId
                                            from CartLine cl");
        $stmtCartLine->execute();
        while ($rowCartLineOrder = $stmtCartLine->fetch(PDO::FETCH_ASSOC)) {
            $findCartLineIdIfExist=$cartLineRepo->findOneBy(['remoteId'=>$rowCartLineOrder['remoteId'],'remoteShopId'=>51]);

                if($findCartLineIdIfExist == null ){
                $cartIdFind=$cartRepo->findOneBy(['remoteId'=>$rowCartLineOrder['remoteCartId'],'remoteShopId'=>51]);
                $cartId=$cartIdFind->id;
                $cartLineInsert=$cartLineRepo->getEmptyEntity();
                $cartLineInsert->cartId=$cartId;
                $cartLineInsert->productId=$rowCartLineOrder['productId'];
                $cartLineInsert->productVariantId=$rowCartLineOrder['productVariantId'];
                $cartLineInsert->productSizeId=$rowCartLineOrder['productSizeId'];
                $cartLineInsert->remoteId=$rowCartLineOrder['remoteId'];
                $cartLineInsert->remoteShopId=$rowCartLineOrder['remoteShopId'];
                $cartLineInsert->insert();

                }else{
                    $res .= '<br>Riga Carrello  già esistente';
                }
        }

        /***inserimento ordini */
        $stmtOrder = $db_con->prepare("SELECT 
                                               o.id as remoteId,
                                               o.orderPaymentMethodId as orderPaymentMethodId,
                                               o.orderShippinMethodId as orderShippingMethodId,
                                               o.couponId as couponId,
                                               o.userId as userId,
                                               o.cartId as cartId,
                                               o.status as status,
                                               o.frozenBillingAddress as frozenBillingAddress,
                                               o.frozenShippingAddress as frozenShippingAddress,
                                               o.shippingPrice as shippingPrice,
                                               o.userDiscount as userDiscount,
                                               o.couponDiscount as couponDiscount,
                                               o.paymentModifier as paymentModifier,
                                               o.grossTotal as grossTotal,
                                               o.netTotal as netTotal,
                                               o.vat as vat,
                                               o.sellingFee as sellingFee,
                                               o.customModifier as customModifier,
                                               o.orderDate as orderDate,
                                               o.note as note,
                                               o.shipmentNote as shipmentNote,
                                               o.transactionNumber as transactionNumber,
                                               o.transactionMac as transactionMac,
                                               o.paidAmount as paidAmount,
                                               o.paymentDate as paymentDate,
                                               o.lastUpdate as lastUpdate,
                                               o.creationDate as creationDate,
                                               o.HasInvoice as HasInvoice
                                               from Order O");
        $stmtOrder->execute();
        while ($rowOrder = $stmtOrder->fetch(PDO::FETCH_ASSOC)) {
            $checkOrderIfExist = $orderRepo->findOneBy(['remoteId' => $rowOrder['remoteId'],'remoteShopId'=>51]);

            if ($checkOrderIfExist == null) {

                $insertOrder = $orderRepo->getEmptyEntity();
                $insertOrder->orderPaymentMethodId = $rowOrder['orderPaymentMethodId'];
                $insertOrder->orderShippingmethodId =$rowOrder['orderShippingMethodId'];
                $insertOrder->couponId=$rowOrder['couponId'];
                $findUser = $userRepo->findOneBy(['id' => $rowOrder['id'],'remoteShopId'=>51]);
                $userId=$findUser->id;
                $insertOrder->userId =$userId;
                $findCart=$cartRepo->findOneBy(['remoteId'=>$rowOrder['cartId'],'remoteShopId'=>51]);
                $cartId=$findCart->id;
                $insertOrder->cartId = $cartId;
                $insertOrder->status = $rowOrder['status'];
                /* defrost indirizzo  Fatturazione remoto */
                $defrostRemoteBillingAddress=defrost($rowOrder['frozenBillingAddress']);
                $remoteBillingAddressId=$defrostRemoteBillingAddress->id;
                $findBillingAddressDetails = $userAddressRepo->findOneBy(['remoteId'=>$remoteBillingAddressId,'userId' => $userId,'remoteShopId'=>51]);
                $insertOrder->frozenShippingAddress = $findBillingAddressDetails->froze();
                /* defrost indirizzo Spedizione remoto */
                $defrostRemoteShippingAddress=defrost($rowOrder['frozenShippingAddress']);
                $remoteShippingAddressId=$defrostRemoteShippingAddress->id;
                $findShippingAddressDetails = $userAddressRepo->findOneBy(['remoteId'=>$remoteShippingAddressId,'userId' => $userId,'remoteShopId'=>51]);
                $insertOrder->frozenShippingAddress = $findShippingAddressDetails->froze();


                $insertOrder->billingAddressId =  $findBillingAddressDetails->id;
                $insertOrder->shipmentAddressId = $findShippingAddressDetails->id;
                $insertOrder->shippingPrice = $rowOrder['shippingPrice'];
                $insertOrder->paymentModifier = 0 - $rowOrder['paymentModifier'];
                $insertOrder->grossTotal = $rowOrder['grossTotal'];
                $insertOrder->netTotal = $rowOrder['netTotal'];
                $insertOrder->vat = $rowOrder['vat'];
                $insertOrder->sellingFee = $rowOrder['sellingFee'];
                $insertOrder->customModifier = $rowOrder['customModifier'];
                $insertOrder->orderDate = $rowOrder['orderDate'];
                $insertOrder->note = $rowOrder['note'];
                $insertOrder->paidAmount = $rowOrder['paidAmount'];
                $insertOrder->lastUpdate = $rowOrder['lastUpdate'];
                $insertOrder->paymentDate = $rowOrder['paymentDate'];
                $insertOrder->remoteId = $rowOrder['remoteId'];
                $insertOrder->remoteShopId = 51;
                $insertOrder->insert();
                $res .= '<br>inserimento ordine ' . $rowOrder['remoteId'];

            } else {
                $res .= '<br>Ordine già esistente';

            }
        }



        /**** inserimento righe Ordine*****/

        $stmtOrderLine = $db_con->prepare("SELECT 
                                     ol.id AS remoteId,
                                     ol.orderId as orderId,
                                     ol.productId as productId,
                                     ol.productVariantId as productVariantId,
                                     ol.productSizeId as productSizeId,
                                     ol.shopId as shopId,
                                     ol.status as status,
                                     ol.orderLineFriendPaymentStatusId as orderLineFriendPaymentStatusId,
                                     ol.orderLineFriendPaymentDate as  orderLineFriendPaymentDate,
                                     ol.warehouseSheifPositionId as warehouseSheifPositionId,
                                     ol.frozenProduct as frozenProduct,
                                     ol.fullPrice as fullPrice,
                                     ol.activePrice as activePrice,
                                     ol.vat as ol.vat,
                                     ol.cost as ol.cost,
                                     ol.shippingCharge as shippingCharge,
                                     ol.couponCharge as couponCharge,
                                     ol.userCharge as userCharge,
                                     ol.paymentCharge as paymentCharge,
                                     ol.sellingFeeCharge as sellingFeeCharge,
                                     ol.customModifierCharge as customModifierCharge,
                                     ol.netPrice as netPrice,
                                     ol.friendRevenue as friendRevenue,
                                     ol.creationDate as creationDate,
                                     ol.lastUpdate as lastUpdate,
                                     ol.note as ol.note,
                                     ppa.reference AS product,
                                     pod.id_shop  AS siteId,
                                     po.id_customer AS userId,
                                     pod.original_product_price AS fullPrice,
                                     pod.total_price_tax_incl AS activePrice,
                                     pod.total_price_tax_incl   AS total_price_tax_incl,
                                     pod.unit_price_tax_incl AS unit_price_tax_incl,
                                     pod.unit_price_tax_excl AS unit_price_atax_excl,
                                     pod.total_shipping_price_tax_incl AS total_shipping_price_tax_incl,
                                     pod.total_shipping_price_tax_excl AS total_shipping_price_tax_excl,
                                     pod.product_quantity AS quantity,
                                     po.date_add AS creationDate,
                                     po.date_upd AS lastUpdate
                                     FROM orderLine ol");
        $stmtOrderLine->execute();
        while ($rowOrderLine = $stmtOrderLine->fetch(PDO::FETCH_ASSOC)) {
            $checkOrderLineExist = $orderLineRepo->findOneBy(['remoteId' => $rowOrderLine['remoteId'],'remoteShopId'=>51]);
            if ($checkOrderLineExist == null) {
                $insertOrderLine = $orderLineRepo->getEmptyEntity();
                $findOrder = $orderRepo->findOneBy(['remoteId' => $rowOrderLine['orderId']]);
                $orderId = $findOrder->id;
                $insertOrderLine->orderId = $orderId;
                $insertOrderLine->productId = $rowOrderLine['productId'];
                $insertOrderLine->productVariantId = $rowOrderLine['productVariantId'];
                $insertOrderLine->productSizeId = $rowOrderLine['productSizeid'];
                $skufind = $productSkuRepo->findOneBy([
                                                        'productId' => $rowOrderLine['productId'],
                                                        'productVariantId' => $rowOrderLine['productVariantId'],
                                                        'productSizeId' => $rowOrderLine['productSizeId'],
                                                        'shopId' => $rowOrderLine['shopId']
                                                      ]);
                $insertOrderLine->frozenProduct = $skufind->froze();
                $insertOrderLine->shopId = $rowOrderLine['shopId'];
                $insertOrderLine->status = $rowOrderLine['status'];
                $insertOrderLine->fullPrice = $rowOrderLine['fullPrice'];
                $insertOrderLine->activePrice = $rowOrderLine['activePrice'];
                $insertOrderLine->vat = $rowOrderLine['vat'];
                $insertOrderLine->shippingCharge = $rowOrderLine['shippingCharge'];
                $insertOrderLine->netPrice = $rowOrderLine['netPrice'];
                $insertOrderLine->creationDate = $rowOrderLine['creationDate'];
                $insertOrderLine->lastUpdate = $rowOrderLine['lastUpdate'];
                $insertOrderLine->remoteId = $rowOrderLine['remoteId'];
                $insertOrderLine->remoteShopId =51;

                $insertOrderLine->insert();

                $res .= "Riga Ordine  gia esistente";

            } else {
                $res .= "Riga Ordine  gia esistente";

            }
        }


        return $res;

    }


}


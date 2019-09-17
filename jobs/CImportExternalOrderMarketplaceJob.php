<?php

namespace bamboo\blueseal\jobs;

use bamboo\domain\entities\CCartAbandonedEmailSend;
use bamboo\domain\repositories\CCartAbandonedEmailSendRepo;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CCart;
use bamboo\domain\entities\CCartAbandonedEmailParam;
use bamboo\domain\entities\CCouponType;
use bamboo\domain\entities\CCoupon;
use bamboo\domain\entities\CCartLine;
use bamboo\core\base\CSerialNumber;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;
use PDO;
use prepare;

use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProduct;
use bamboo\core\events\AEventListener;


/**
 * Class CImportExternalOrderMarketplaceJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 09/01/2019
 * @since 1.0
 */
class CImportExternalOrderMarketplaceJob extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {

        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        $res="";
        /********marketplace********/
        $db_host = "5.189.159.187";
        $db_name = "iwesPrestaDB";
        $db_user = "iwesprestashop";
        $db_pass = "X+]l&LEa]zSI";
        try {

            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
            $db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $res.= " connessione ok <br>";
        } catch (PDOException $e) {
            $res.= $e->getMessage();
        }
        $stmtUser = $db_con->prepare("SELECT 
                                     pc.id_customer AS remoteId,
                                     pac.id_address AS remoteaddressId,
                                     pc.id_lang AS  langId,
                                     pc.id_shop AS shopId,
                                     pc.id_shop AS siteId,
                                     pc.email AS email,
                                     pc.id_gender AS gender,
                                     pc.firstname AS firstname,
                                     pc.lastname AS surname,
                                     pc.birthday AS birthday,
                                     pc.company AS company,
                                     pac.address1 AS address,
                                     pac.address2 AS extra,
                                     pac.postcode AS postcode,
                                     pac.city AS city,
                                     pst.iso_code AS province,
                                     pac.phone AS phone,
                                     pac.phone_mobile AS phone_mobile,
                                     pac.dni AS fiscalcode,
                                     pc.date_add AS creationDate
                                     FROM psz6_customer pc
                                     JOIN psz6_address pac ON pc.id_customer =pac.id_customer
                                     JOIN psz6_state pst ON pac.id_state
                                   GROUP BY  remoteId");
        $stmtUser->execute();
        while ($rowUser = $stmtUser->fetch(PDO::FETCH_ASSOC)) {
            $checkUserIfExist = \Monkey::app()->repoFactory->create('User')->findOneBy( ['email' => $rowUser['email']]);
            if (null == $checkUserIfExist) {
                $insertUser = \Monkey::app()->repoFactory->create('User')->getEmptyEntity();
                $insertUser->langId = $rowUser['langId'];
                $insertUser->email = $rowUser['email'];
                $insertUser->registrationEntryPoint = 'MarketPlace';
                $insertUser->isActive = '1';
                $insertUser->isDeleted = '0';
                $insertUser->creationDate = $rowUser['creationDate'];
                $insertUser->isEmailChanged = '0';
                $insertUser->remoteId=$rowUser['remoteId'];
                $insertUser->insert();
                $insertUserAddress = \Monkey::app()->repoFactory->create('UserAddress')->getEmptyEntity();
                $getuserId = \Monkey::app()->repoFactory->create('User')->findOneBy(['email' => $rowUser['email']]);
                $userId = $getuserId->id;
                $insertUserAddress->userId = $userId;
                $insertUserAddress->isBilling = '1';
                $insertUserAddress->isDefault = '0';
                $insertUserAddress->name = $rowUser['firstname'];
                $insertUserAddress->surname = $rowUser['surname'];
                $insertUserAddress->company = $rowUser['company'];
                $insertUserAddress->address = $rowUser['address'];
                $insertUserAddress->extra = $rowUser['extra'];
                $insertUserAddress->province = $rowUser['province'];
                $insertUserAddress->city = $rowUser['city'];
                $insertUserAddress->postcode = $rowUser['postcode'];
                $findCountry = \Monkey::app()->repoFactory->create('ZipCode')->findOneBy(['code' => $rowUser['postcode'], 'orderSubdivision2Code' => $rowUser['province']]);
                if ($findCountry == null) {
                    $insertUserAddress->countryId = '101';
                } else {
                    $country = $findCountry->countryId;
                    $insertUserAddress->countryId = $country;
                }
                if ($rowUser['phone'] == null) {
                    $phone = $rowUser['phone_mobile'];
                } else {
                    $phone = $rowUser['phone'];
                }
                $insertUserAddress->phone = $phone;
                $insertUserAddress->lastUsed = 1;
                $insertUserAddress->insert();
                $insertUserDetails = \Monkey::app()->repoFactory->create('UserDetails')->getEmptyEntity();
                $insertUserDetails->userId = $userId;
                $insertUserDetails->name = $rowUser['firstname'];
                $insertUserDetails->surname = $rowUser['surname'];
                $insertUserDetails->birthDate = $rowUser['birthday'];
                $insertUserDetails->phone = $phone;
                if ($rowUser['gender'] == 1) {
                    $gender = 'M';
                } else {
                    $gender = 'F';
                }
                $insertUserDetails->gender = $gender;
                $insertUserDetails->regDate = $rowUser['creationDate'];
                $insertUserDetails->fiscalcode = $rowUser['fiscalcode'];
                $insertUserDetails->insert();
                $insertUserEmail = \Monkey::app()->repoFactory->create('UserEmail')->getEmptyEntity();
                $insertUserEmail->userid = $userId;
                $insertUserEmail->address = $rowUser['email'];
                $insertUserEmail->isPrimary = '1';
                $insertUserEmail->insert();
                $res.= " inserimento utente" . $rowUser['email'] . " eseguito<br>";


            } else {
                $res.= "utente" . $rowUser['email'] . " già in elenco";

            }


        }

        /* Inserimento Carrelli **/

        $stmtCart = $db_con->prepare("SELECT 
                                     pcart.id_cart AS remoteId,
                                     ppa.reference AS product,
                                     p.id_shop_default  AS siteId,
                                     pcart.id_customer AS id_customer,
                                     pcart.id_address_delivery AS shipmentAddressId,
                                     pcart.id_address_invoice AS billingAddressId,
                                     pcart.date_add AS creationDate,
                                     pcart.date_upd AS lastUpdate
                            
                                     FROM psz6_cart pcart 
                                      JOIN psz6_cart_product pcp ON pcart.id_cart = pcp.id_cart
                                      JOIN psz6_product_attribute ppa ON pcp.id_product=ppa.id_product AND pcp.id_product_attribute =ppa.id_product_attribute
                                      JOIN psz6_product p ON ppa.id_product=p.id_product 
                               
                                  ");
        $stmtCart->execute();
        while ($rowCart = $stmtCart->fetch(PDO::FETCH_ASSOC)) {
            $checkCartExist = \Monkey::app()->repoFactory->create('Cart')->findOneBy(['remoteId' => $rowCart['remoteId']]);
            if ($checkCartExist == null) {
                $insertCart = \Monkey::app()->repoFactory->create('Cart')->getEmptyEntity();
                $findUserId = \Monkey::app()->repoFactory->create('User')->findOneBy(['remoteId' => $rowCart['id_customer']]);
                $userId = $findUserId->id;
                $insertCart->userId = $userId;
                $findshippingDetails = \Monkey::app()->repoFactory->create('UserAddress')->findOneBy(['userId' => $userId]);
                $shipmentAddressId = $findshippingDetails->id;
                $insertCart->cartTypeId = 3;
                $insertCart->billingAddressId = $shipmentAddressId;
                $insertCart->shipmentAddressId = $shipmentAddressId;
                $insertCart->lastUpdate = $rowCart['lastUpdate'];
                $insertCart->creationDate = $rowCart['creationDate'];
                $insertCart->remoteId = $rowCart['remoteId'];
                $insertCart->insert();
                $res="Inserito Nuovo Carrello";

            } else {
                $res.= "carrello gia esistente";

            }
        }


        /***inserimento ordini */

        $stmtOrder = $db_con->prepare("SELECT 
                                     po.id_order AS remoteId,
                                     concat(po.id_order,'-',po.payment) AS remoteOrderId,
                                     po.reference AS reference,
                                     po.id_shop AS shopId,
                                     po.id_shop AS siteId,
                                     po.id_carrier AS carrier,
                                     po.id_lang AS  langId,
                                     po.id_shop AS shopId,
                                     po.id_customer AS id_customer,
                                     po.id_cart AS cartId,
                                     po.id_currency AS currency ,
                                     po.id_address_delivery    AS address_delivery,
                                     po.id_address_invoice     AS billing_delivery,
                                     po.current_state AS current_state,
                                     po.payment AS payment,
                                     po.conversion_rate AS conversion_rate,
                                     po.shipping_number AS shipping_number,
                                     po.total_discounts AS total_discounts,
                                     po.total_discounts_tax_incl AS total_discounts_tax_incl,
                                     po.total_discounts_tax_excl AS total_discount_tax_excl,
                                     po.total_paid AS total_paid,
                                     po.total_paid_real AS total_paid_real,
                                     po.total_paid_tax_incl AS total_paid_tax_incl,
                                     po.total_paid_tax_excl AS total_paid_tax_excl,
                                     po.total_shipping AS total_shipping,
                                     po.total_shipping_tax_incl AS total_shipping_tax_incl,
                                     po.total_shipping_tax_excl AS total_shipping_tax_excl,
                                     po.total_wrapping AS total_wrapping,
                                     po.total_wrapping_tax_incl AS total_wrapping_tax_incl,
                                     po.total_shipping_tax_excl AS total_wrapping_tax_excl,
                                     po.round_mode AS round_mode,
                                     po.round_type AS round_type,
                                     po.invoice_date AS invoice_date,
                                     po.delivery_date AS delivery_date,
                                     po.valid AS valid,
                                     po.date_add AS date_add,
                                     po.date_upd AS date_upd,
                                     pap.date_add AS paymentDate
                                     
                                     
                                     FROM psz6_orders po
                                     INNER JOIN psz6_order_payment  pap ON po.id_order=pap.id_order_payment
                                    
                                  ");
        $stmtOrder->execute();
        while ($rowOrder = $stmtOrder->fetch(PDO::FETCH_ASSOC)) {
            $checkOrderExist = \Monkey::app()->repoFactory->create('Order')->findOneBy(['remoteOrderId' => $rowOrder['remoteOrderId']]);
            if ($checkOrderExist == null) {
                if (strpos($rowOrder['remoteOrderId'], 'PayPal') != false) {
                    $orderPaymentMethodId = 1;
                }
                if (strpos($rowOrder['remoteOrderId'], 'carta di Credito') != false) {
                    $orderPaymentMethodId = 2;
                }
                if (strpos($rowOrder['remoteOrderId'], 'Bonifico') != false) {
                    $orderPaymentMethodId = 3;
                }
                if (strpos($rowOrder['remoteOrderId'], 'Contrassegno') != false) {
                    $orderPaymentMethodId = 5;
                }
                if (strpos($rowOrder['remoteOrderId'], 'PickandPay') != false) {
                    $orderPaymentMethodId = 4;
                }
                $findcartId = \Monkey::app()->repoFactory->create('Cart')->findOneBy(['remoteId' => $rowOrder['cartId']]);
                $cartId = $findcartId->id;
                $insertOrder = \Monkey::app()->repoFactory->create('Order')->getEmptyEntity();
                $insertOrder->orderPaymentMethodId = $orderPaymentMethodId;
                $findUserId = \Monkey::app()->repoFactory->create('User')->findOneBy(['remoteId' => $rowOrder['id_customer']]);
                $orderUserId = $findUserId->id;
                $insertOrder->userId = $orderUserId;
                $insertOrder->cartId = $cartId;
                $insertOrder->status = "ORD_WAIT";
                $findshippingDetails = \Monkey::app()->repoFactory->create('UserAddress')->findOneBy(['userId' => $orderUserId]);
                $shipmentAddressId = $findshippingDetails->id;
                $findaddressBillingDetails = \Monkey::app()->repoFactory->create('UserAddress')->findOneBy(['userId' => $orderUserId, 'isBilling' => 1]);

                $findaddressShippingDetails = \Monkey::app()->repoFactory->create('UserAddress')->findOneBy(['userId' => $orderUserId, 'isBilling' => 0]);
                if (null == $findaddressBillingDetails) {
                    $insertOrder->frozenShippingAddress = $findaddressShippingDetails->froze();
                    $insertOrder->frozenBillingAddress = $findaddressShippingDetails->froze();
                } elseif (null == $findaddressShippingDetails) {
                    $insertOrder->frozenShippingAddress = $findaddressBillingDetails->froze();
                    $insertOrder->frozenBillingAddress = $findaddressBillingDetails->froze();
                } else {
                    $insertOrder->frozenShippingAddress = $findaddressShippingDetails->froze();
                    $insertOrder->frozenBillingAddress = $findaddressBillingDetails->froze();
                }
                $insertOrder->shipmentAddressId = $shipmentAddressId;
                $insertOrder->billingAddressId = $shipmentAddressId;
                $insertOrder->shippingPrice = $rowOrder['total_shipping'];
                $insertOrder->paymentModifier = 0 - $rowOrder['total_discounts'];
                $insertOrder->grossTotal = $rowOrder['total_paid'];
                $insertOrder->netTotal = $rowOrder['total_paid_real'];
                $insertOrder->vat = $rowOrder['total_paid_real'] / 122 * 22;
                $insertOrder->sellingFee = '0';
                $insertOrder->customModifier = '0';
                $insertOrder->orderDate = $rowOrder['date_add'];
                $insertOrder->paidAmount = $rowOrder['total_paid_real'];
                $insertOrder->lastUpdate = $rowOrder['date_upd'];
                $insertOrder->paymentDate = $rowOrder['paymentDate'];
                $insertOrder->remoteOrderId = $rowOrder['remoteOrderId'];
                $insertOrder->insert();
                $res.= '<br>inserimento ordine ' . $rowOrder['remoteOrderId'];

            } else {
                $res.= '<br>Ordine già esistente';


            }
        }

        /**** inserimento righe carrello*****/

        $stmtCartLine = $db_con->prepare("SELECT 
                                     concat(pcart.id_cart,'-',ppa.reference,'-',p.id_shop_default) AS remoteId,
                                     pcart.id_cart AS cartId,
                                     ppa.reference AS product,
                                     p.id_shop_default  AS siteId,
                                     pcart.id_address_delivery AS shipmentAddressId,
                                     pcart.id_address_invoice AS billingAddressId,
                                     pcart.date_add AS creationDate,
                                     pcart.date_upd AS lastUpdate
                            
                                     FROM psz6_cart pcart 
                                      JOIN psz6_cart_product pcp ON pcart.id_cart = pcp.id_cart
                                      JOIN psz6_product_attribute ppa ON pcp.id_product=ppa.id_product AND pcp.id_product_attribute =ppa.id_product_attribute
                                      JOIN psz6_product p ON ppa.id_product=p.id_product 
                               
                                  ");
        $stmtCartLine->execute();
        while ($rowCartLine = $stmtCartLine->fetch(PDO::FETCH_ASSOC)) {
            $productSkuCartLine[] = explode('-', $rowCartLine['remoteId']);

            $checkCartLineExist = \Monkey::app()->repoFactory->create('CartLine')->findOneBy(['remoteId' => $rowCartLine['remoteId']]);
            if ($checkCartLineExist == null) {
                $insertCartLine = \Monkey::app()->repoFactory->create('CartLine')->getEmptyEntity();
                $findCartId = \Monkey::app()->repoFactory->create('Cart')->findOneBy(['remoteId' => $rowCartLine['cartId']]);
                $cartId = $findCartId->id;
                $insertCartLine->cartId = $cartId;
                $insertCartLine->productId = $productSkuCartLine[0][1];
                $insertCartLine->productVariantId = $productSkuCartLine[0][2];
                $insertCartLine->productSizeId = $productSkuCartLine[0][3];
                $insertCartLine->siteId = $rowCartLine['siteId'];
                $insertCartLine->remoteId = $rowCartLine['remoteId'];
                $insertCartLine->insert();
                $res.= "Riga Carrello  inserita";

            } else {
                $res.= "Riga Carrello  gia esistente";

            }
        }

        /**** inserimento righe Ordine*****/

        $stmtOrderLine = $db_con->prepare("SELECT 
                                     pod.id_order_detail AS id_order_detail,
                                     concat(pod.id_order,'-',pod.id_order_detail,'-',ppa.reference,'-',pod.id_shop) AS remoteId,
                                     pod.id_order AS orderId,
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
                            
                                     FROM psz6_orders po
                                      JOIN psz6_order_detail pod ON po.id_order = pod.id_order
                                      JOIN psz6_product_attribute ppa ON pod.product_id=ppa.id_product AND pod.product_attribute_id =ppa.id_product_attribute
                                      JOIN psz6_product p ON ppa.id_product=p.id_product 
                               
                                  ");
        $stmtOrderLine->execute();
        while ($rowOrderLine = $stmtOrderLine->fetch(PDO::FETCH_ASSOC)) {
            $productSkuOrderLine[] = explode('-', $rowOrderLine['remoteId']);

            $checkOrderLineExist = \Monkey::app()->repoFactory->create('OrderLine')->findOneBy(['remoteId' => $rowOrderLine['remoteId']]);
            if ($checkOrderLineExist == null) {
                $insertOrderLine = \Monkey::app()->repoFactory->create('OrderLine')->getEmptyEntity();
                $findOrderId = \Monkey::app()->repoFactory->create('Order')->findOneBy(['remoteId' => $rowOrderLine['orderId']]);
                $orderId = $findOrderId->id;
                $insertOrderLine->orderId = $orderId;
                $insertOrderLine->productId = $productSkuOrderLine[0][2];
                $insertOrderLine->productVariantId = $productSkuOrderLine[0][3];
                $insertOrderLine->productSizeId = $productSkuOrderLine[0][4];
                $insertOrderLine->siteId = $rowOrderLine['siteId'];
                $skufind = \Monkey::app()->repoFactory->create('ProductSku')->findOneBy(
                    ['productId' => $productSkuOrderLine[0][2],
                        'productVariantId' => $productSkuOrderLine[0][3],
                        'productSizeId' => $productSkuOrderLine[0][4],
                        'shopId' => $productSkuOrderLine[0][5]]);
                if($skufind!=null) {
                    $insertOrderLine->frozenProduct = $skufind->froze();
                }
                $findshopId=\Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['prestashopId'=>$rowOrderLine['siteId']]);
                $shopId=$findshopId->shopId;
                $insertOrderLine->shopId = $shopId;
                $insertOrderLine->status = 'ORD_WAIT';
                $insertOrderLine->fullPrice = $rowOrderLine['fullPrice'];
                $insertOrderLine->activePrice = $rowOrderLine['activePrice'];
                $insertOrderLine->vat = $rowOrderLine['activePrice'] / 122 * 22;
                $insertOrderLine->shippingCharge = $rowOrderLine['total_shipping_price_tax_incl'];
                $insertOrderLine->netPrice = $rowOrderLine['activePrice'] + $rowOrderLine['total_shipping_price_tax_incl'];
                $insertOrderLine->creationDate = $rowOrderLine['creationDate'];
                $insertOrderLine->lastUpdate = $rowOrderLine['lastUpdate'];
                $insertOrderLine->remoteId = $rowOrderLine['remoteId'];

                $insertOrderLine->insert();

                $res.= "Riga Ordine  gia esistente";

            } else {
                $res.= "Riga Ordine  gia esistente";

            }
        }


        return $res;

    }


}
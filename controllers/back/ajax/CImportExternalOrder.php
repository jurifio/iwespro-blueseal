<?php

namespace bamboo\controllers\back\ajax;

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
        $db_host = "iwes.shop";
        $db_name = "iwesshop_pres848";
        $db_user = "iwesshop_pres848";
        $db_pass = "@5pM5S)Mn8";
        define("HOST", "iwes.shop");
        define("USERNAME", "iwesshop_pres848");
        define("PASSWORD", "@5pM5S)Mn8");
        define("DATABASE", "iwesshop_pres848");
        try {

            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
            $db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $res .= " connessione ok <br>";
        } catch (PDOException $e) {
            $res .= $e->getMessage();
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
                                     pc.date_add AS creationDate,
                                     pac.id_country as id_country,
                                     psc.iso_code as country_isocode
                                     FROM psz6_customer pc
                                      JOIN psz6_address pac ON pc.id_customer =pac.id_customer
                                     LEFT JOIN psz6_state pst ON pac.id_state
                                     LEFT JOIN psz6_country psc on pac.id_country=psc.id_country
                                     
                                   GROUP BY  remoteId");
        $stmtUser->execute();

        $userRepo = \Monkey::app()->repoFactory->create('User');
        $getUserIdRepo = \Monkey::app()->repoFactory->create('User');
        $insertUserAddressRepo = \Monkey::app()->repoFactory->create('UserAddress');
        $findCountryRepo = \Monkey::app()->repoFactory->create('Country');
        $checkOrderExistRepo = \Monkey::app()->repoFactory->create('Order');
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
                $insertUser = \Monkey::app()->repoFactory->create('User')->getEmptyEntity();
                $insertUser->langId = $rowUser['langId'];
                $insertUser->email = $rowUser['email'];
                $insertUser->registrationEntryPoint = 'MarketPlace';
                $insertUser->isActive = '1';
                $insertUser->isDeleted = '0';
                $insertUser->creationDate = $rowUser['creationDate'];
                $insertUser->isEmailChanged = '0';
                $insertUser->remoteId = $rowUser['remoteId'];
                $insertUser->insert();

                //UserAddress creation
                $insertUserAddress = $insertUserAddressRepo->getEmptyEntity();
                $getuserId = $getUserIdRepo->findOneBy(['email' => $rowUser['email']]);
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
                $findCountry = $findCountryRepo->findOneBy(['ISO' => $rowUser['country_isocode']]);
                if ($findCountry == null) {
                    $insertUserAddress->countryId = null;
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

                //UserDetail Creation
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

                //UserEmail Creation
                $insertUserEmail = \Monkey::app()->repoFactory->create('UserEmail')->getEmptyEntity();
                $insertUserEmail->userid = $userId;
                $insertUserEmail->address = $rowUser['email'];
                $insertUserEmail->isPrimary = '1';
                $insertUserEmail->insert();
                $res .= " inserimento utente" . $rowUser['email'] . " eseguito<br>";
            } else {
                $res .= "utente" . $rowUser['email'] . " già in elenco";
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
                                     pap.date_add AS paymentDate,
                                     pc.email AS email
                                     
                                     FROM psz6_orders po
                                      LEFT JOIN psz6_order_payment  pap ON po.id_order=pap.id_order_payment
                                      LEFT JOIN psz6_customer pc ON po.id_customer = pc.id_customer
                                  ");
        $stmtOrder->execute();
        while ($rowOrder = $stmtOrder->fetch(PDO::FETCH_ASSOC)) {
            $checkOrderExist = $checkOrderExistRepo->findOneBy(['remoteOrderId' => $rowOrder['remoteId']]);

            if ($checkOrderExist == null) {
                if (strpos($rowOrder['remoteOrderId'], 'PayPal') != false) {
                    $orderPaymentMethodId = 1;

                }
                if (strpos($rowOrder['remoteOrderId'], 'carta di Credito') != false) {
                    $orderPaymentMethodId = 2;
                }
                if (strpos($rowOrder['remoteOrderId'], 'eBay CCAccepted') != false) {
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
                if (strpos($rowOrder['remoteOrderId'], 'Amazon') != false) {
                    $orderPaymentMethodId = 3;
                    }
                if (strpos($rowOrder['remoteOrderId'], 'eBay') != false) {
                    $orderPaymentMethodId = 2;
                }


                $insertOrder = $insertOrderRepo->getEmptyEntity();
                $insertOrder->orderPaymentMethodId = $orderPaymentMethodId;
                $findUserId = \Monkey::app()->repoFactory->create('User')->findOneBy(['email' => $rowOrder['email']]);
                $orderUserId = $findUserId->id;
                $insertOrder->userId = $orderUserId;
                $insertOrder->cartId = null;
                $insertOrder->status = "ORD_WAIT";
                $findshippingDetails = $findshippingDetailsRepo->findOneBy(['userId' => $orderUserId]);
                $shipmentAddressId = $findshippingDetails->id;
                $findaddressBillingDetails = $findaddressBillingDetailsRepo->findOneBy(['userId' => $orderUserId, 'isBilling' => 1]);

                $findaddressShippingDetails = $findaddressShippingDetailsRepo->findOneBy(['userId' => $orderUserId, 'isBilling' => 0]);
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
                $insertOrder->note = $rowOrder['remoteOrderId'];
                $insertOrder->paidAmount = $rowOrder['total_paid_real'];
                $insertOrder->lastUpdate = $rowOrder['date_upd'];
                $insertOrder->paymentDate = $rowOrder['paymentDate'];
                $insertOrder->remoteOrderId = $rowOrder['remoteId'];
                $insertOrder->insert();
                $res .= '<br>inserimento ordine ' . $rowOrder['remoteOrderId'];

            } else {
                $res .= '<br>Ordine già esistente';


            }
        }

        /**** inserimento righe carrello*****/
        /*
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
        */

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
                                    LEFT   JOIN psz6_order_detail pod ON po.id_order = pod.id_order
                                    LEFT  JOIN psz6_product_attribute ppa ON pod.product_id=ppa.id_product AND pod.product_attribute_id =ppa.id_product_attribute
                                     LEFT  JOIN psz6_product p ON ppa.id_product=p.id_product 
                               
                                  ");
        $stmtOrderLine->execute();
        while ($rowOrderLine = $stmtOrderLine->fetch(PDO::FETCH_ASSOC)) {
            $productSkuOrderLine[] = explode('-', $rowOrderLine['remoteId']);

            $checkOrderLineExist = $orderLineRepo->findOneBy(['remoteId' => $rowOrderLine['remoteId']]);
            if ($checkOrderLineExist == null) {
                $insertOrderLine = $orderLineRepo->getEmptyEntity();
                $findOrderId = $orderRepo->findOneBy(['remoteOrderId' => $rowOrderLine['orderId']]);
                $orderId = $findOrderId->id;
                $insertOrderLine->orderId = $orderId;
                $insertOrderLine->productId = $productSkuOrderLine[0][2];
                $insertOrderLine->productVariantId = $productSkuOrderLine[0][3];
                $insertOrderLine->productSizeId = $productSkuOrderLine[0][4];
                $skufind = $productSkuRepo->findOneBy(
                    ['productId' => $productSkuOrderLine[0][2],
                        'productVariantId' => $productSkuOrderLine[0][3],
                        'productSizeId' => $productSkuOrderLine[0][4],
                        'shopId' => $productSkuOrderLine[0][5]]);
                $insertOrderLine->frozenProduct = $skufind->froze();
                $findshopId = $marketplaceHasShopRepo->findOneBy(['prestashopId' => $rowOrderLine['siteId']]);
                $shopId = $findshopId->shopId;
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

                $res .= "Riga Ordine  gia esistente";

            } else {
                $res .= "Riga Ordine  gia esistente";

            }
        }


        return $res;

    }


}


<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\RedPandaException;
use bamboo\domain\entities\CUserAddress;
use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use PDO;
use prepare;
use AEntity;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CSite;
use bamboo\domain\entities\CUserHasShop;
use bamboo\domain\repositories\CUserAddressRepo;
use bamboo\domain\entities\CUser;
use PDOException;


/**
 * Class CImportExternalPickySiteOrder
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/06/2019
 * @since 1.0
 */
class CImportExternalPickySiteOrder extends AAjaxController
{
    /**
     * @return bool|string
     * @throws RedPandaException
     * @throws \Exception
     * @throws \bamboo\core\exceptions\BambooException
     */
    public function post()
    {

        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        $res = "";
        $shopRepo=\Monkey::app()->repoFactory->create('Shop')->findBy(['hasEcommerce'=>1]);
        foreach ($shopRepo as $value) {
            /********marketplace********/

            $db_host = $value->dbHost;
            $db_name = $value->dbName;
            $db_user = $value->dbUsername;
            $db_pass = $value->dbPassword;
            $shop = $value->id;
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $res .= ' connessione ok <br>';
            } catch (PDOException $e) {
                $res .= $e->getMessage();
            }


            $userRepo = \Monkey::app()->repoFactory->create('User');
            $userDetailsRepo = \Monkey::app()->repoFactory->create('UserDetails');
            $userEmailRepo = \Monkey::app()->repoFactory->create('UserEmail');
            $getUserIdRepo = \Monkey::app()->repoFactory->create('User');
            $userAddressRepo = \Monkey::app()->repoFactory->create('UserAddress');
            $findCountryRepo = \Monkey::app()->repoFactory->create('Country');
            $cartRepo = \Monkey::app()->repoFactory->create('Cart');
            $cartLineRepo = \Monkey::app()->repoFactory->create('CartLine');
            $orderRepo = \Monkey::app()->repoFactory->create('Order');
            $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');
            $couponTypeRepo = \Monkey::app()->repoFactory->create('CouponType');
            $couponEventRepo = \Monkey::app()->repoFactory->create('CouponEvent');
            $couponRepo = \Monkey::app()->repoFactory->create('Coupon');


            $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');
            //ALL USER CREATION
            try {
                $stmtUser = $db_con->prepare(sprintf("SELECT 
                                     u.id AS remoteId,
                                     u.langId AS langId,
                                     u.username as username,
                                     u.password as password,
                                     u.email AS email,
                                     concat(u.registrationEntryPoint, '-','%s') as registrationEntryPoint,
                                     u.isActive as isActive,
                                     u.isDeleted as isDeleted,
                                     u.lastSeen as lastSeen,
                                     u.creationDate as creationDate,
                                     u.isEmailChanged as isEmailChanged,
                                     ud.name AS name,
                                     ud.surname AS surname,
                                     ud.screenName as screenName,
                                     ud.birthDate as birthDate,
                                     ud.phone as phone,
                                     ud.gender as gender,
                                     ud.regDate as regDate,
                                     ud.fiscalCode as fiscalCode,
                                     ud.note as note
                                     FROM User  u
                                      JOIN UserDetails ud ON u.id = ud.userId ", $shop));
                $stmtUser->execute();
                while ($rowUser = $stmtUser->fetch(PDO::FETCH_ASSOC)) {
                    $checkUserIfExist = $userRepo->findOneBy(['email' => $rowUser['email']]);
                    if (null == $checkUserIfExist) {

                        //User creation
                        $insertUser = $userRepo->getEmptyEntity();
                        $insertUser->langId = $rowUser['langId'];
                        $insertUser->email = $rowUser['email'];
                        $insertUser->registrationEntryPoint = $rowUser['registrationEntryPoint'];
                        $insertUser->isActive = $rowUser['isActive'];
                        $insertUser->isDeleted = $rowUser['isDeleted'];
                        $insertUser->creationDate = $rowUser['creationDate'];
                        $insertUser->isEmailChanged = $rowUser['isEmailChanged'];
                        $insertUser->remoteId = $rowUser['remoteId'];
                        $insertUser->remoteShopId = $shop;
                        $insertUser->insert();
                        $getuserId = $userRepo->findOneBy(['email' => $rowUser['email']]);
                        $userId = $getuserId->id;


                        //UserDetail Creation
                        $insertUserDetails = $userDetailsRepo->getEmptyEntity();
                        $insertUserDetails->userId = $userId;
                        $insertUserDetails->name = $rowUser['name'];
                        $insertUserDetails->surname = $rowUser['surname'];
                        $insertUserDetails->birthDate = $rowUser['birthDate'];
                        $insertUserDetails->phone = $rowUser['phone'];

                        $insertUserDetails->gender = $rowUser['gender'];
                        $insertUserDetails->regDate = $rowUser['regDate'];
                        $insertUserDetails->fiscalCode = $rowUser['fiscalCode'];
                        $insertUserDetails->note = $rowUser['note'];
                        $insertUserDetails->insert();


                        //UserEmail Creation
                        $insertUserEmail = $userEmailRepo->getEmptyEntity();
                        $insertUserEmail->userId = $userId;
                        $insertUserEmail->address = $rowUser['email'];
                        $insertUserEmail->isPrimary = '1';
                        $insertUserEmail->insert();
                        $res .= "<br>inserimento utente" . $rowUser['email'] . " eseguito<br>";
                    } else {
                        $res .= "<br>utente" . $rowUser['email'] . " già in elenco";
                        continue;

                    }

                }
            } catch (\throwable $e) {
                \Monkey::app()->applicationLog('CImportExternalPickysiteOrder', 'error', 'Errore User ' . $e);
            }
            try {
                $stmtUserAddress = $db_con->prepare('SELECT 
                                                          us.id as remoteId,  
                                                          us.userId    as remoteUserId,
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
                                                          us.fiscalCode as fiscalCode from UserAddress us ');

                $stmtUserAddress->execute();
                while ($rowUserAddress = $stmtUserAddress->fetch(PDO::FETCH_ASSOC)) {
                    $checkUserAddressisBillingIfExist = $userAddressRepo->findOneBy(['remoteId' => $rowUserAddress['remoteId'], 'remoteUserId' => $rowUserAddress['remoteUserId'], 'remoteShopId' => $shop]);
                    if (null == $checkUserAddressisBillingIfExist) {
                        $findUserInsert = $userRepo->findOneBy(['remoteId' => $rowUserAddress['remoteUserId'], 'remoteShopId' => $shop]);
                        if ($findUserInsert != null) {
                            $userAddressInsert = $userAddressRepo->getEmptyEntity();
                            $userAddressId = $findUserInsert->id;
                            $userAddressInsert->userId = $userAddressId;
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
                            $userAddressInsert->remoteUserId = $rowUserAddress['remoteUserId'];
                            $userAddressInsert->remoteShopId = $shop;
                            $userAddressInsert->insert();
                            $res .= '<br>Indirizzo inserito' . $rowUserAddress['remoteId'] . ' shop ' . $shop;
                        }
                    } else {
                        $res .= '<br>Indirizzo esistenze' . $rowUserAddress['remoteId'] . ' shop ' . $shop;
                        continue;

                    }

                }
            } catch (\throwable $e) {
                \Monkey::app()->applicationLog('CImportExternalPickysiteOrder', 'error', 'Errore UserAddress ' . $e);
            }

            try {
                $stmtCouponType = $db_con->prepare("SELECT
                                           ct.id as remoteId,
                                           `ct`.`name`  as name,
                                           ct.amount as amount,
                                           ct.amountType as amountType,
                                           ct.validity as validity,
                                           ct.validForCartTotal as validForCartTotal,
                                           ct.hasFreeShipping as hasFreeShipping,
                                           ct.hasFreeReturn as hasFreeReturn
                                           FROM CouponType ct");
                $stmtCouponType->execute();
                while ($rowCouponType = $stmtCouponType->fetch(PDO::FETCH_ASSOC)) {
                    $checkCouponTypeIfExist = $couponTypeRepo->findOneBy(['remoteId' => $rowCouponType['remoteId'], 'remoteShopId' => $shop]);
                    if ($checkCouponTypeIfExist == null) {
                        $couponTypeInsert = $couponTypeRepo->getEmptyEntity();
                        $couponTypeInsert->name = $rowCouponType['name'];
                        $couponTypeInsert->amount = $rowCouponType['amount'];
                        $couponTypeInsert->amountType = $rowCouponType['amountType'];
                        $couponTypeInsert->validity = $rowCouponType['validity'];
                        $couponTypeInsert->validForCartTotal = $rowCouponType['validForCartTotal'];
                        $couponTypeInsert->hasFreeShipping = $rowCouponType['hasFreeShipping'];
                        $couponTypeInsert->hasFreeReturn = $rowCouponType['hasFreeReturn'];
                        $couponTypeInsert->remoteId = $rowCouponType['remoteId'];
                        $couponTypeInsert->remoteShopId = $shop;
                        $couponTypeInsert->insert();
                        $res .= '<br>inserito il tipo coupon' . $rowCouponType['remoteId'] . ' shop ' . $shop;
                    } else {
                        $res .= '<br>Tipo Coupon Gia Esistente' . $rowCouponType['remoteId'] . ' shop ' . $shop;
                        continue;
                    }

                }
            } catch (\throwable $e) {
                \Monkey::app()->applicationLog('CImportExternalPickysiteOrder', 'error', 'Errore CouponType ' . $e);
            }

            try {
                $stmtCouponEvent = $db_con->prepare("SELECT
                                                      ce.id as remoteId,
                                                      ce.couponTypeId as couponTypeId,
                                                      `ce`.`name` as `name`,
                                                      ce.description as description,
                                                      ce.click as click,
                                                      ce.startDate as startDate,
                                                      ce.endDate as endDate
                                                      FROM CouponEvent ce");
                $stmtCouponEvent->execute();
                while ($rowCouponEvent = $stmtCouponEvent->fetch(PDO::FETCH_ASSOC)) {
                    $checkCouponEventIfExist = $couponEventRepo->findOneBy(['remoteId' => $rowCouponEvent['remoteId'], 'name' => $rowCouponEvent['name'], 'remoteShopId' => $shop]);
                    if ($checkCouponEventIfExist == null) {
                        $checkCouponTypeIfExist = $couponTypeRepo->findOneBy(['remoteId' => $rowCouponEvent['couponTypeId'], 'remoteShopId' => $shop]);
                        if ($checkCouponTypeIfExist != null) {
                            $couponEventInsert = $couponEventRepo->getEmptyEntity();
                            $couponEventInsert->couponTypeId = $checkCouponTypeIfExist->id;
                            $couponEventInsert->name = $rowCouponEvent['name'];
                            $couponEventInsert->description = $rowCouponEvent['description'];
                            $couponEventInsert->click = $rowCouponEvent['click'];
                            $couponEventInsert->startDate = $rowCouponEvent['startDate'];
                            $couponEventInsert->endDate = $rowCouponEvent['endDate'];
                            $couponEventInsert->remoteId = $rowCouponEvent['remoteId'];
                            $couponEventInsert->remoteShopId = $shop;
                            $couponEventInsert->insert();
                            $res .= 'Coupon Evento ' . $rowCouponEvent['remoteId'] . ' shopId ' . $shop;
                        }
                    } else {

                        $res .= 'Coupon Evento Già esistente' . $rowCouponEvent['remoteId'] . ' shopId ' . $shop;
                        continue;
                    }

                }
            } catch (\throwable $e) {
                \Monkey::app()->applicationLog('CImportExternalPickysiteOrder', 'error', 'Errore Coupon Event ' . $e);
            }

            try {
                $stmtCoupon = $db_con->prepare(" SELECT 
                                                 co.id as remoteId,
                                                 co.couponTypeId as couponTypeId,
                                                 co.code as code,
                                                 co.issueDate as issueDate,
                                                 co.validThru as validThru,
                                                 co.amount as amount,
                                                 co.userId as userId,
                                                 co.valid as valid,
                                                 co.couponEventId as couponEventId
                                                 from Coupon co");
                $stmtCoupon->execute();
                while ($rowCoupon = $stmtCoupon->fetch(PDO::FETCH_ASSOC)) {
                    $checkCouponIfExist = $couponRepo->findOneBy(['remoteId' => $rowCoupon['remoteId'], 'remoteShopId' => $shop]);
                    if ($checkCouponIfExist == null) {
                        $checkUserIfExist = $userRepo->findOneBy(['remoteId' => $rowCoupon['userId'], 'remoteShopId' => $shop]);
                        if ($checkUserIfExist != null) {
                            $checkCouponTypeIfExist = $couponTypeRepo->findOneBy(['remoteId' => $rowCoupon['couponTypeId'], 'remoteShopId' => $shop]);
                            if ($checkCouponTypeIfExist != null) {
                                $checkCouponEventIfExist = $couponEventRepo->findOneBy(['remoteId' => $rowCoupon['couponEventId'], 'remoteShopId' => $shop]);
                                if ($checkCouponEventIfExist != null) {
                                    $couponInsert = $couponRepo->getEmptyEntity();
                                    $couponInsert->couponTypeId = $checkCouponTypeIfExist->id;
                                    $couponInsert->code = $rowCoupon['code'];
                                    $couponInsert->issueDate = $rowCoupon['issueDate'];
                                    $couponInsert->validThru = $rowCoupon['validThru'];
                                    $couponInsert->amount = $rowCoupon['amount'];
                                    $couponInsert->userId = $checkUserIfExist->id;
                                    $couponInsert->valid = $rowCoupon['valid'];
                                    $couponInsert->couponEventId = $checkCouponEventIfExist->id;
                                    $couponInsert->remoteId = $rowCoupon['remoteId'];
                                    $couponInsert->remoteShopId = $shop;
                                    $couponInsert->insert();
                                    $res .= '<br> Coupon  inserito' . $rowCoupon['remoteId'] . ' shopId ' . $shop;

                                }
                            }
                        }
                    } else {
                        $res .= '<br> Coupon  già esistente ' . $rowCoupon['remoteId'] . ' shopId ' . $shop;
                        continue;
                    }

                }
            } catch (\throwable $e) {
                \Monkey::app()->applicationLog('CImportExternalPickysiteOrder', 'error', 'Errore Coupon ' . $e);
            }

            try {
                $stmtCart = $db_con->prepare('SELECT 
                                               c.id as remoteId,
                                               c.orderPaymentMethodId as orderPaymentMethodId,
                                               c.couponId as couponId,
                                               c.userId as userId,
                                               U.`email` AS `email`,
                                               c.cartTypeId as cartTypeId,
                                               c.billingAddressId as billingAddressId,
                                               c.shipmentAddressId as shipmentAddressId,
                                               c.lastUpdate as lastUpdate,
                                               c.creationDate as creationDate
                                               from Cart c join User U on c.userId = U.id order BY remoteId ASC   ');
                $stmtCart->execute();
                foreach ($stmtCart as $rowCart) {
                    //hile ($rowCart = $stmtCart->fetch(PDO::FETCH_ASSOC)) {
                    $checkCartIfExist = $cartRepo->findOneBy(['remoteId' => $rowCart['remoteId'], 'remoteShopId' => $shop]);
                    if (null == $checkCartIfExist) {
                        $userEmailFind = $userRepo->findOneBy(['email' => $rowCart['email']]);
                        $userIdEmail = $userEmailFind->id;
                        $userFind = $userRepo->findOneBy(['remoteId' => $rowCart['userId'], 'remoteShopId' => $shop]);
                        if ($userFind !== null) {
                            $userId = $userFind->id;

                            $billingAddressIdFind = $userAddressRepo->findOneBy(['remoteId' => $rowCart['billingAddressId'], 'remoteShopId' => $shop]);
                            if ($billingAddressIdFind != null) {
                                $billingAddressIdFind->userId = $userIdEmail;
                                $billingAddressIdFind->update();

                                $billingAddressId = $billingAddressIdFind->id;
                                $shipmentAddressIdFind = $userAddressRepo->findOneBy(['remoteId' => $rowCart['shipmentAddressId'], 'remoteShopId' => $shop]);
                                if ($shipmentAddressIdFind != null) {
                                    $shipmentAddressIdFind->userId = $userIdEmail;
                                    $shipmentAddressIdFind->update();
                                    $shipmentAddressId = $shipmentAddressIdFind->id;
                                    $insertCart = $cartRepo->getEmptyEntity();
                                    if ($rowCart['couponId'] != '') {
                                        $FindCoupon = $couponRepo->findOneBy(['remoteId' => $rowCoupon['couponId'], 'remoteShopId' => $shop]);
                                        if ($FindCoupon != null) {
                                            $insertCart->couponId = $FindCoupon->id;

                                        }
                                    }

                                    $insertCart->orderPaymentMethodId = $rowCart['orderPaymentMethodId'];
                                    $insertCart->userId = $userId;
                                    $insertCart->cartTypeId = $rowCart['cartTypeId'];

                                    $insertCart->billingAddressId = $billingAddressId;

                                    $insertCart->shipmentAddressId = $shipmentAddressId;
                                    $insertCart->lastUpdate = $rowCart['lastUpdate'];
                                    $insertCart->remoteId = $rowCart['remoteId'];
                                    $insertCart->remoteShopId = $shop;
                                    $insertCart->insert();
                                    $res .= '<br>carrello inserito' . $rowCart['remoteId'] . ' shopId ' . $shop;
                                }
                            }
                        }

                    } else {
                        $res .= '<br>carrello già esistente' . $rowCart['remoteId'] . ' shopId ' . $shop;
                        continue;
                    }

                }
            } catch (\throwable $e) {
                \Monkey::app()->applicationLog('CImportExternalPickysiteOrder', 'error', 'Errore Cart ' . $e);
            }
            /***** inserimento righe carrello *********/
            try {
                $stmtCartLine = $db_con->prepare("SELECT
                                            cl.id as remoteId,
                                            cl.cartId as remoteCartId,
                                            cl.productId as productId,
                                            cl.productVariantId as productVariantId,
                                            cl.productSizeId as productSizeId
                                            from CartLine cl ");
                $stmtCartLine->execute();
                while ($rowCartLineOrder = $stmtCartLine->fetch(PDO::FETCH_ASSOC)) {
                    $findCartLineIdIfExist = $cartLineRepo->findOneBy(['remoteId' => $rowCartLineOrder['remoteId'], 'remoteShopId' => $shop]);

                    if ($findCartLineIdIfExist == null) {
                        $cartIdFind = $cartRepo->findOneBy(['remoteId' => $rowCartLineOrder['remoteCartId'], 'remoteShopId' => $shop]);
                        if ($cartIdFind !== null) {
                            $cartId = $cartIdFind->id;
                            $cartLineInsert = $cartLineRepo->getEmptyEntity();
                            $cartLineInsert->cartId = $cartId;
                            $cartLineInsert->productId = $rowCartLineOrder['productId'];
                            $cartLineInsert->productVariantId = $rowCartLineOrder['productVariantId'];
                            $cartLineInsert->productSizeId = $rowCartLineOrder['productSizeId'];
                            $cartLineInsert->remoteId = $rowCartLineOrder['remoteId'];
                            $cartLineInsert->remoteShopId = $shop;
                            $cartLineInsert->insert();
                            $res .= '<br>Riga Carrello inserita' . $rowCartLineOrder['remoteId'] . ' shopId ' . $shop;

                        }
                    } else {
                        $res .= '<br>Riga Carrello  già esistente' . $rowCartLineOrder['remoteId'] . ' shopId ' . $shop;
                        continue;
                    }

                }
            } catch (\throwable $e) {
                \Monkey::app()->applicationLog('CImportExternalPickysiteOrder', 'error', 'Errore cartline ' . $e);
            }
            try {
                /***inserimento ordini */
                $stmtOrder = $db_con->prepare("SELECT 
                                               o.id as remoteId,
                                               o.orderPaymentMethodId as orderPaymentMethodId,
                                               o.orderShippingMethodId as orderShippingMethodId,
                                               o.couponId as couponId,
                                               o.userId as userId,
                                               U.email as email, 
                                               o.cartId as cartId,
                                               `o`.`status` as `status`,
                                               o.frozenBillingAddress as frozenBillingAddress,
                                               o.frozenShippingAddress as frozenShippingAddress,
                                               o.billingAddressId as billingAddressId,
                                               o.shipmentAddressId as shipmentAddressId,
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
                                               o.hasInvoice as hasInvoice
                                               from `Order` o join User U on o.userId = U.id ");
                $stmtOrder->execute();
                while ($rowOrder = $stmtOrder->fetch(PDO::FETCH_ASSOC)) {


                    $checkOrderIfExist = $orderRepo->findOneBy(['remoteId' => $rowOrder['remoteId'], 'remoteShopId' => $shop]);

                    if ($checkOrderIfExist == null) {
                        $findUser = $userRepo->findOneBy(['email' => $rowOrder['email']]);
                        if ($findUser !== null) {
                            $userId = $findUser->id;
                            $findCart = $cartRepo->findOneBy(['remoteId' => $rowOrder['cartId'], 'remoteShopId' => $shop]);
                            if ($findCart != null) {
                                $cartId = $findCart->id;
                                $insertOrder = $orderRepo->getEmptyEntity();
                                $insertOrder->orderPaymentMethodId = $rowOrder['orderPaymentMethodId'];
                                $insertOrder->orderShippingmethodId = $rowOrder['orderShippingMethodId'];
                                if ($rowOrder['couponId'] != '') {
                                    $FindCoupon = $couponRepo->findOneBy(['remoteId' => $rowOrder['couponId'], 'remoteShopId' => $shop]);
                                    if ($FindCoupon != null) {
                                        $insertOrder->couponId = $FindCoupon->id;
                                    }
                                }


                                $insertOrder->userId = $userId;
                                $insertOrder->cartId = $cartId;
                                $insertOrder->status = $rowOrder['status'];
                                /* defrost indirizzo  Fatturazione remoto */
                                $remoteBillingAddressId = $rowOrder['billingAddressId'];
                                if ($remoteBillingAddressId != '') {
                                    $findBillingAddressDetails = $userAddressRepo->findOneBy(['remoteId' => $remoteBillingAddressId, 'userId' => $userId, 'remoteShopId' => $shop]);
                                    $insertOrder->frozenBillingAddress = $findBillingAddressDetails->froze();
                                    $insertOrder->billingAddressId = $findBillingAddressDetails->id;
                                }
                                /* defrost indirizzo Spedizione remoto */
                                $remoteShippingAddressId = $rowOrder['shipmentAddressId'];
                                if ($remoteShippingAddressId != '') {
                                    $findShippingAddressDetails = $userAddressRepo->findOneBy(['remoteId' => $remoteShippingAddressId, 'userId' => $userId, 'remoteShopId' => $shop]);
                                    $insertOrder->frozenShippingAddress = $findShippingAddressDetails->froze();
                                    $insertOrder->shipmentAddressId = $findShippingAddressDetails->id;
                                }


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
                                $insertOrder->transactionNumber = $rowOrder['transactionNumber'];
                                $insertOrder->transactionMac = $rowOrder['transactionMac'];
                                $insertOrder->paymentDate = $rowOrder['paymentDate'];
                                $insertOrder->remoteId = $rowOrder['remoteId'];
                                $insertOrder->remoteShopId = $shop;
                                $insertOrder->hasInvoice = $rowOrder['hasInvoice'];
                                $insertOrder->insert();
                                $res .= '<br>Ordine inserita' . $rowOrder['remoteId'] . 'shop ' . $shop;
                                continue;

                            }
                        }
                    } else {
                        $res .= '<br>Ordine già esistente' . $rowOrder['remoteId'] . 'shop ' . $shop;
                        continue;

                    }

                }

            } catch (\throwable $e) {
                \Monkey::app()->applicationLog('CImportExternalPickysiteOrder', 'error', 'Errore Order' . $e);
            }
            try {
                /**** inserimento righe Ordine*****/
                if ($shop == 1) {
                    $stmtOrderLine = $db_con->prepare(" SELECT ol.id AS remoteId,
                                     ol.orderId as orderId,
                                     ol.productId as productId,
                                     ol.productVariantId as productVariantId,
                                     ol.productSizeId as productSizeId,
                                     ol.shopId as shopId,
                                     ol.status as status,
                                     ol.orderLineFriendPaymentStatusId as orderLineFriendPaymentStatusId,
                                     ol.orderLineFriendPaymentDate as  orderLineFriendPaymentDate,
                                     NULL as warehouseShelfPositionId,
                                     ol.frozenProduct as frozenProduct,
                                     ol.fullPrice as fullPrice,
                                     ol.activePrice as activePrice,
                                     ol.vat as vat,
                                     ol.cost as cost,
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
                                     ol.note as note
                                     FROM OrderLine ol WHERE ol.frozenProduct IS NOT NULL");
                } else {
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
                                     ol.warehouseShelfPositionId as warehouseShelfPositionId,
                                     ol.frozenProduct as frozenProduct,
                                     ol.fullPrice as fullPrice,
                                     ol.activePrice as activePrice,
                                     ol.vat as vat,
                                     ol.cost as cost,
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
                                     ol.note as note
                                     FROM OrderLine ol WHERE ol.frozenProduct IS NOT NULL");
                }
                $stmtOrderLine->execute();
                while ($rowOrderLine = $stmtOrderLine->fetch(PDO::FETCH_ASSOC)) {
                    $checkOrderLineExist = $orderLineRepo->findOneBy(['remoteId' => $rowOrderLine['remoteId'], 'remoteOrderId' => $rowOrderLine['orderId'], 'remoteShopId' => $shop]);
                    if ($checkOrderLineExist == null) {

                        $findOrder = $orderRepo->findOneBy(['remoteId' => $rowOrderLine['orderId'], 'remoteShopId' => $shop]);
                        if ($findOrder != null) {


                            $skufind = $productSkuRepo->findOneBy([
                                'productId' => $rowOrderLine['productId'],
                                'productVariantId' => $rowOrderLine['productVariantId'],
                                'productSizeId' => $rowOrderLine['productSizeId'],
                                'shopId' => $rowOrderLine['shopId']
                            ]);
                            if ($skufind != null) {
                                $orderId = $findOrder->id;
                                $insertOrderLine = $orderLineRepo->getEmptyEntity();
                                $insertOrderLine->orderId = $orderId;
                                $insertOrderLine->productId = $rowOrderLine['productId'];
                                $insertOrderLine->productVariantId = $rowOrderLine['productVariantId'];
                                $insertOrderLine->productSizeId = $rowOrderLine['productSizeId'];
                                $insertOrderLine->orderLineFriendPaymentStatusId = $rowOrderLine['orderLineFriendPaymentStatusId'];
                                $insertOrderLine->orderLineFriendPaymentDate = $rowOrderLine['orderLineFriendPaymentDate'];
                                $insertOrderLine->warehouseShelfPositionId = $rowOrderLine['warehouseShelfPositionId'];
                                $insertOrderLine->frozenProduct = $skufind->froze();
                                $insertOrderLine->shopId = $skufind->shopId;
                                $insertOrderLine->status = $rowOrderLine['status'];
                                $insertOrderLine->fullPrice = $rowOrderLine['fullPrice'];
                                $insertOrderLine->activePrice = $rowOrderLine['activePrice'];
                                $insertOrderLine->vat = $rowOrderLine['vat'];
                                $insertOrderLine->cost = $rowOrderLine['cost'];
                                $insertOrderLine->shippingCharge = $rowOrderLine['shippingCharge'];
                                $insertOrderLine->couponCharge = $rowOrderLine['couponCharge'];
                                $insertOrderLine->userCharge = $rowOrderLine['userCharge'];
                                $insertOrderLine->paymentCharge = $rowOrderLine['paymentCharge'];
                                $insertOrderLine->sellingFeeCharge = $rowOrderLine['sellingFeeCharge'];
                                $insertOrderLine->customModifierCharge = $rowOrderLine['customModifierCharge'];
                                $insertOrderLine->netPrice = $rowOrderLine['netPrice'];
                                $insertOrderLine->friendRevenue = $rowOrderLine['friendRevenue'];
                                $insertOrderLine->creationDate = $rowOrderLine['creationDate'];
                                $insertOrderLine->lastUpdate = $rowOrderLine['lastUpdate'];
                                $insertOrderLine->note = $rowOrderLine['note'];
                                $insertOrderLine->remoteId = $rowOrderLine['remoteId'];
                                $insertOrderLine->remoteShopId = $shop;
                                $insertOrderLine->remoteOrderId = $rowOrderLine['orderId'];
                                $insertOrderLine->insert();
                                $res .= "<br>Riga Ordine  inserita " . $rowOrderLine['remoteId'] . '-' . $rowOrderLine['orderId'] . ' shop ' . $shop;
                            }


                        }
                    } else {
                        $res .= "<br>Riga Ordine  gia esistente " . $rowOrderLine['remoteId'] . '-' . $rowOrderLine['orderId'] . ' shop ' . $shop;
                        continue;

                    }

                }

            } catch (\throwable $e) {
                \Monkey::app()->applicationLog('CImportExternalPickysiteOrder', 'error', 'Errore User ' . $e);
            }

        }

        return $res;


    }



}


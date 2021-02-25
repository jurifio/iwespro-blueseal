<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProductEan;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CShop;
use bamboo\domain\entities\CUserAddress;
use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use PDO;
use prepare;
use AEntity;
use bamboo\domain\entities\CSite;
use bamboo\domain\entities\CUserHasShop;
use bamboo\domain\repositories\CUserAddressRepo;
use bamboo\domain\entities\CUser;
use PDOException;


class CImportExternalPickySiteOrderJob extends ACronJob
{

    /**
     * @param null $args
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function run($args = null)
    {
        $this -> importOrder();
    }

    /**
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function importOrder()
    {

        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        $res = "";
        $shopRepo = \Monkey ::app() -> repoFactory -> create('Shop') -> findBy(['hasEcommerce' => 1]);

        foreach ($shopRepo as $value) {
            $this -> report('Start ImportOrder From PickySite ', 'Shop To Import' . $value -> name);
            /********marketplace********/
            $db_host = $value -> dbHost;
            $db_name = $value -> dbName;
            $db_user = $value -> dbUsername;
            $db_pass = $value -> dbPassword;
            $shop = $value -> id;
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
                $db_con -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $res .= " connessione ok <br>";
            } catch (PDOException $e) {
                $res .= $e -> getMessage();
            }


            $userRepo = \Monkey ::app() -> repoFactory -> create('User');
            $userDetailsRepo = \Monkey ::app() -> repoFactory -> create('UserDetails');
            $userEmailRepo = \Monkey ::app() -> repoFactory -> create('UserEmail');
            $getUserIdRepo = \Monkey ::app() -> repoFactory -> create('User');
            $userAddressRepo = \Monkey ::app() -> repoFactory -> create('UserAddress');
            $findCountryRepo = \Monkey ::app() -> repoFactory -> create('Country');
            $cartRepo = \Monkey ::app() -> repoFactory -> create('Cart');
            $cartLineRepo = \Monkey ::app() -> repoFactory -> create('CartLine');
            $orderRepo = \Monkey ::app() -> repoFactory -> create('Order');
            $orderLineRepo = \Monkey ::app() -> repoFactory -> create('OrderLine');
            $couponTypeRepo = \Monkey ::app() -> repoFactory -> create('CouponType');
            $couponEventRepo = \Monkey ::app() -> repoFactory -> create('CouponEvent');
            $couponRepo = \Monkey ::app() -> repoFactory -> create('Coupon');
            $newsletterUserRepo=\Monkey::app()->repoFactory->create('NewsletterUser');


            $productSkuRepo = \Monkey ::app() -> repoFactory -> create('ProductSku');
            //ALL USER CREATION
            try {
                $stmtUser = $db_con -> prepare(sprintf("SELECT 
                                     u.id AS remoteId,
                                     u.langId AS langId,
                                     u.username as username,
                                     u.password as password,
                                     u.email AS email,
                                     u.ip as ip,
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
                                      JOIN UserDetails ud ON u.id = ud.userId WHERE u.isImport is null", $shop));
                $stmtUser -> execute();
                while ($rowUser = $stmtUser -> fetch(PDO::FETCH_ASSOC)) {
                    $checkUserIfExist = $userRepo -> findOneBy(['email' => $rowUser['email']]);
                    if (null == $checkUserIfExist) {

                        //User creation
                        $insertUser = $userRepo -> getEmptyEntity();
                        $insertUser -> langId = $rowUser['langId'];
                        $insertUser -> email = $rowUser['email'];
                        $insertUser -> registrationEntryPoint = $rowUser['registrationEntryPoint'];
                        $insertUser -> isActive = $rowUser['isActive'];
                        $insertUser -> isDeleted = $rowUser['isDeleted'];
                        $insertUser -> creationDate = $rowUser['creationDate'];
                        $insertUser -> isEmailChanged = $rowUser['isEmailChanged'];
                        $insertUser -> remoteId = $rowUser['remoteId'];
                        $insertUser -> remoteShopId = $shop;
                        $insertUser -> ip =$rowUser['ip'];
                        $insertUser -> insert();
                        $getuserId = $userRepo -> findOneBy(['email' => $rowUser['email']]);
                        $userId = $getuserId -> id;


                        //UserDetail Creation
                        $insertUserDetails = $userDetailsRepo -> getEmptyEntity();
                        $insertUserDetails -> userId = $userId;
                        $insertUserDetails -> name = $rowUser['name'];
                        $insertUserDetails -> surname = $rowUser['surname'];
                        $insertUserDetails -> birthDate = $rowUser['birthDate'];
                        $insertUserDetails -> phone = $rowUser['phone'];

                        $insertUserDetails -> gender = $rowUser['gender'];
                        $insertUserDetails -> regDate = $rowUser['regDate'];
                        $insertUserDetails -> fiscalCode = $rowUser['fiscalCode'];
                        $insertUserDetails -> note = $rowUser['note'];
                        $insertUserDetails -> insert();


                        //UserEmail Creation
                        $insertUserEmail = $userEmailRepo -> getEmptyEntity();
                        $insertUserEmail -> userId = $userId;
                        $insertUserEmail -> address = $rowUser['email'];
                        $insertUserEmail -> isPrimary = '1';
                        $insertUserEmail -> insert();

                    } else {

                        continue;

                    }

                }
                $stmtUserUpdate=$db_con->prepare('UPDATE User SET isImport=1  WHERE isImport IS NULL');
                $stmtUserUpdate->execute();
            } catch (\throwable $e) {
                $this -> report('CImportExternalPickySiteOrderJob', 'error', 'Errore User ' . $e);
            }
            try {

                $stmtUserAddress = $db_con -> prepare('SELECT 
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
                                                          us.fiscalCode as fiscalCode from UserAddress us where us.isImport is null');

                $stmtUserAddress -> execute();
                while ($rowUserAddress = $stmtUserAddress -> fetch(PDO::FETCH_ASSOC)) {
                    $checkUserAddressisBillingIfExist = $userAddressRepo -> findOneBy(['remoteId' => $rowUserAddress['remoteId'], 'remoteUserId' => $rowUserAddress['remoteUserId'], 'remoteShopId' => $shop]);
                    if (null == $checkUserAddressisBillingIfExist) {
                        $findUserInsert = $userRepo -> findOneBy(['remoteId' => $rowUserAddress['remoteUserId'], 'remoteShopId' => $shop]);
                        if ($findUserInsert != null) {
                            $userAddressInsert = $userAddressRepo -> getEmptyEntity();
                            $userAddressId = $findUserInsert -> id;
                            $userAddressInsert -> userId = $userAddressId;
                            $userAddressInsert -> isBilling = $rowUserAddress['isBilling'];
                            $userAddressInsert -> isDefault = $rowUserAddress['isDefault'];
                            $userAddressInsert -> name = $rowUserAddress['name'];
                            $userAddressInsert -> surname = $rowUserAddress['surname'];
                            $userAddressInsert -> company = $rowUserAddress['company'];
                            $userAddressInsert -> address = $rowUserAddress['address'];
                            $userAddressInsert -> extra = $rowUserAddress['extra'];
                            $userAddressInsert -> province = $rowUserAddress['province'];
                            $userAddressInsert -> city = $rowUserAddress['city'];
                            $userAddressInsert -> postcode = $rowUserAddress['postcode'];
                            $userAddressInsert -> countryId = $rowUserAddress['countryId'];
                            $userAddressInsert -> phone = $rowUserAddress['phone'];
                            $userAddressInsert -> lastUsed = $rowUserAddress['lastUsed'];
                            $userAddressInsert -> fiscalCode = $rowUserAddress['fiscalCode'];
                            $userAddressInsert -> remoteId = $rowUserAddress['remoteId'];
                            $userAddressInsert -> remoteUserId = $rowUserAddress['remoteUserId'];
                            $userAddressInsert -> remoteShopId = $shop;
                            $userAddressInsert -> insert();

                        }
                    } else {
                        continue;

                    }

                }
                $stmtUserUpdate=$db_con->prepare('UPDATE UserAddress SET isImport=1  WHERE isImport IS NULL');
                $stmtUserUpdate->execute();
            } catch (\throwable $e) {

                $this -> report('CImportExternalPickySiteOrderJob', 'error', 'Errore User Address' . $e);

            }
            /** inserimento newsletterUser */
            try {
                $stmtNewsletterUser = $db_con->prepare('SELECT id as remoteId, 
                                                                      email as email,
                                                                      isActive as isActive,
                                                                      subscriptionDate as subscriptionDate,
                                                                      unsubscriptionDate as unsubscriptionDate,  
                                                                      userId as remoteUserId,
                                                                      langId as langId,
                                                                      genderNewsletterUser as genderNewsletterUser,
                                                                      nameNewsletter as nameNewsletter,
                                                                      surnameNewsletter as surnameNewsletter
                                                                      FROM NewsletterUser where isImport is null
                                                                    ');
                $stmtNewsletterUser->execute();
                if ($stmtNewsletterUser!=null) {
                    while ($rowNewsletterUser = $stmtNewsletterUser->fetch(PDO::FETCH_ASSOC)) {
                        $checkIfNewsletterUserExist = $newsletterUserRepo->findOneBy(['email' => $rowNewsletterUser['email']]);
                        if ($checkIfNewsletterUserExist == null) {
                            $newsletterUserInsert = $newsletterUserRepo->getEmptyEntity();
                            $newsletterUserInsert->email = $rowNewsletterUser['email'];
                            $newsletterUserInsert->isActive = $rowNewsletterUser['isActive'];
                            $newsletterUserInsert->langId = $rowNewsletterUser['langId'];
                            $newsletterUserInsert->subscriptionDate = $rowNewsletterUser['subscriptionDate'];
                            if ($rowNewsletterUser['remoteUserId'] != null) {
                                $user = $userRepo->findOneBy(['email' => $rowNewsletterUser['email']]);
                                $userId = $user->id;
                                $newsletterUserInsert->userId = $userId;
                            }
                            $newsletterUserInsert->unsubscriptionDate = $rowNewsletterUser['unsubscriptionDate'];
                            $newsletterUserInsert->genderNewsletterUser = $rowNewsletterUser['genderNewsletterUser'];
                            $newsletterUserInsert->nameNewsletter = $rowNewsletterUser['nameNewsletter'];
                            $newsletterUserInsert->surnameNewsletter = $rowNewsletterUser['surnameNewsletter'];
                            $newsletterUserInsert->remoteId = $rowNewsletterUser['remoteId'];
                            $newsletterUserInsert->remoteShopId = $shop;
                            $newsletterUserInsert->insert();
                        } else {
                            continue;
                        }

                    }
                }
                $stmtUpdateNewsletterUser=$db_con->prepare("UPDATE NewsletterUser set isImport=1 WhERE isImport is null");
                $stmtUpdateNewsletterUser->execute();
            }catch (\Throwable $e){
                $this -> report('CImportExternalPickySiteOrderJob', 'error', 'NewsletterUser  ' . $e);
            }


            /** inserimento CouponType */
            try {
                $stmtCouponType = $db_con -> prepare("SELECT
                                           ct.id as remoteId,
                                           `ct`.`name`  as name,
                                           ct.amount as amount,
                                           ct.amountType as amountType,
                                           ct.validity as validity,
                                           ct.validForCartTotal as validForCartTotal,
                                           ct.hasFreeShipping as hasFreeShipping,
                                           ct.hasFreeReturn as hasFreeReturn
                                
                                           FROM CouponType ct WHERE ct.isImport is null ");
                $stmtCouponType -> execute();
                while ($rowCouponType = $stmtCouponType -> fetch(PDO::FETCH_ASSOC)) {
                    $checkCouponTypeIfExist = $couponTypeRepo -> findOneBy(['remoteId' => $rowCouponType['remoteId'], 'remoteShopId' => $shop]);
                    if ($checkCouponTypeIfExist == null) {
                        $couponTypeInsert = $couponTypeRepo -> getEmptyEntity();
                        $couponTypeInsert -> name = $rowCouponType['name'];
                        $couponTypeInsert -> amount = $rowCouponType['amount'];
                        $couponTypeInsert -> amountType = $rowCouponType['amountType'];
                        $couponTypeInsert -> validity = $rowCouponType['validity'];
                        $couponTypeInsert -> validForCartTotal = $rowCouponType['validForCartTotal'];
                        $couponTypeInsert -> hasFreeShipping = $rowCouponType['hasFreeShipping'];
                        $couponTypeInsert -> hasFreeReturn = $rowCouponType['hasFreeReturn'];
                        $couponTypeInsert -> remoteId = $rowCouponType['remoteId'];
                        $couponTypeInsert -> remoteShopId = $shop;
                        $couponTypeInsert -> insert();

                    } else {

                        continue;
                    }

                }
                $stmtCouponTypeUpdate=$db_con->prepare('UPDATE CouponType SET isImport=1  WHERE isImport  IS NULL');
                $stmtCouponTypeUpdate->execute();
            } catch (\throwable $e) {
                $this -> report('CImportExternalPickySiteOrderJob', 'error', 'Coupon Type  ' . $e);
            }

            /** inserimento Coupon Event **/
            try {
                $stmtCouponEvent = $db_con -> prepare('SELECT
                                                      ce.id as remoteId,
                                                      ce.couponTypeId as couponTypeId,
                                                      `ce`.`name` as `name`,
                                                      ce.description as description,
                                                      ce.click as click,
                                                      ce.startDate as startDate,
                                                      ce.endDate as endDate,
                                                      ce.isCatalogue as isCatalogue,
                                                      ce.isAnnounce as isAnnounce,
                                                      ce.rowCataloguePosition as rowCataloguePosition,
                                                      ce.couponText as couponText
                                                      FROM CouponEvent ce WHERE ce.isImport IS NULL');
                $stmtCouponEvent -> execute();
                while ($rowCouponEvent = $stmtCouponEvent -> fetch(PDO::FETCH_ASSOC)) {
                    $checkCouponEventIfExist = $couponEventRepo -> findOneBy(['remoteId' => $rowCouponEvent['remoteId'], 'name' => $rowCouponEvent['name'], 'remoteShopId' => $shop]);
                    if ($checkCouponEventIfExist == null) {
                        $checkCouponTypeIfExist = $couponTypeRepo -> findOneBy(['remoteId' => $rowCouponEvent['couponTypeId'], 'remoteShopId' => $shop]);
                        if ($checkCouponTypeIfExist != null) {
                            $couponEventInsert = $couponEventRepo -> getEmptyEntity();
                            $couponEventInsert -> couponTypeId = $checkCouponTypeIfExist -> id;
                            $couponEventInsert -> name = $rowCouponEvent['name'];
                            $couponEventInsert -> description = $rowCouponEvent['description'];
                            $couponEventInsert -> click = $rowCouponEvent['click'];
                            $couponEventInsert -> startDate = $rowCouponEvent['startDate'];
                            $couponEventInsert -> endDate = $rowCouponEvent['endDate'];
                            $couponEventInsert -> remoteId = $rowCouponEvent['remoteId'];
                            $couponEventInsert -> isCatalogue = $rowCouponEvent['isCatalogue'];
                            $couponEventInsert -> isAnnounce = $rowCouponEvent['isAnnounce'];
                            $couponEventInsert -> rowCataloguePosition = $rowCouponEvent['rowCataloguePosition'];
                            $couponEventInsert -> couponText = $rowCouponEvent['couponText'];
                            $couponEventInsert -> remoteShopId = $shop;
                            $couponEventInsert -> insert();

                        }
                    } else {


                        continue;
                    }

                }
                $stmtCouponEventUpdate=$db_con->prepare('UPDATE CouponEvent SET isImport=1  WHERE isImport IS NULL');
                $stmtCouponEventUpdate->execute();
            } catch (\throwable $e) {
                $this -> report('CImportExternalPickySiteOrderJob', 'error', 'Errore Coupon Event ' . $e);
            }

            /**inserimento Coupon **/
            try {
                $stmtCoupon = $db_con -> prepare(' SELECT 
                                                 co.id as remoteId,
                                                 co.couponTypeId as couponTypeId,
                                                 co.code as code,
                                                 co.issueDate as issueDate,
                                                 co.validThru as validThru,
                                                 co.amount as amount,
                                                 co.userId as userId,
                                                 co.valid as valid,
                                                 co.couponEventId as couponEventId,
                                                 co.sid as sid,
                                                 co.isExtended as isExtended,
                                                  u.email as email 
                                                 from Coupon co left join User u on co.userId=u.id  WHERE co.isImport is null');
                $stmtCoupon -> execute();
                while ($rowCoupon = $stmtCoupon -> fetch(PDO::FETCH_ASSOC)) {
                    $checkCouponIfExist = $couponRepo -> findOneBy(['remoteId' => $rowCoupon['remoteId'], 'remoteShopId' => $shop]);
                    if ($checkCouponIfExist == null) {
                        $couponInsert = $couponRepo -> getEmptyEntity();
                       /* if($rowCoupon['userId']!=null) {
                            $checkUserIfExist = $userRepo->findOneBy(['email' => $rowCoupon['email']]);
                            $couponInsert -> userId = $checkUserIfExist->id;
                        }*/
                            $checkCouponTypeIfExist = $couponTypeRepo -> findOneBy(['remoteId' => $rowCoupon['couponTypeId'], 'remoteShopId' => $shop]);
                           if($rowCoupon['couponEventId']!=null) {
                               $checkCouponEventIfExist = $couponEventRepo->findOneBy(['remoteId' => $rowCoupon['couponEventId'],'remoteShopId' => $shop]);
                               $couponInsert->couponEventId = $checkCouponEventIfExist->id;
                           }

                                    $couponInsert -> couponTypeId = $checkCouponTypeIfExist -> id;
                                    $couponInsert -> code = $rowCoupon['code'];
                                    $couponInsert -> issueDate = $rowCoupon['issueDate'];
                                    $couponInsert -> validThru = $rowCoupon['validThru'];
                                    $couponInsert -> amount = $rowCoupon['amount'];

                                    $couponInsert -> valid = $rowCoupon['valid'];

                                    $couponInsert -> remoteId = $rowCoupon['remoteId'];
                                    $couponInsert -> remoteShopId = $shop;
                                    $couponInsert -> sid=$rowCoupon['sid'];
                                    $couponInsert ->isExtended=$rowCoupon['isExtended'];
                                    $couponInsert -> insert();
                                    //  $res.='inserito il coupon '.$couponInsert->printId().'<br>';



                    } else {

                        continue;
                    }

                }
                $stmtCouponUpdate=$db_con->prepare('UPDATE Coupon SET isImport=1  WHERE isImport IS NULL');
                $stmtCouponUpdate->execute();
            } catch (\throwable $e) {
                $this -> report('CImportExternalPickySiteOrderJob', 'error', 'Errore Coupon ' . $e);
            }


            try {
                $stmtCart = $db_con -> prepare('SELECT 
                                               c.id as remoteCartSellerId,
                                               c.orderPaymentMethodId as orderPaymentMethodId,
                                               c.couponId as couponId,
                                               c.userId as userId,
                                               U.`email` AS `email`,
                                               c.cartTypeId as cartTypeId,
                                               c.pickyCoinsAmountOnCart as pickyCoinsAmountOnCart,
                                               c.billingAddressId as billingAddressId,
                                               c.shipmentAddressId as shipmentAddressId,
                                               c.lastUpdate as lastUpdate,
                                               c.creationDate as creationDate,
                                               c.isParallel as isParallel,
                                               c.isImport as isImport
                                               from Cart c  join `User` U on c.userId = U.id WHERE isParallel is null  AND c.isImport=0  order BY remoteCartSellerId ASC    ');
                $stmtCart -> execute();
                foreach ($stmtCart as $rowCart) {
                    //hile ($rowCart = $stmtCart->fetch(PDO::FETCH_ASSOC)) {
                    $checkCartIfExist = $cartRepo -> findOneBy(['remoteCartSellerId' => $rowCart['remoteCartSellerId'], 'remoteShopSellerId' => $shop]);
                    if (null == $checkCartIfExist) {
                        $userEmailFind = $userRepo -> findOneBy(['email' => $rowCart['email']]);
                        if ($userEmailFind !== null) {
                            $userId = $userEmailFind -> id;
                            $insertCart = $cartRepo -> getEmptyEntity();
                            if ($rowCart['couponId'] != '') {
                                $FindCoupon = $couponRepo -> findOneBy(['remoteId' => $rowCoupon['couponId'], 'remoteShopId' => $shop]);
                                if ($FindCoupon != null) {
                                    $insertCart -> couponId = $FindCoupon -> id;

                                }
                            }
                            $insertCart -> orderPaymentMethodId = $rowCart['orderPaymentMethodId'];
                            $insertCart -> userId = $userId;
                            $insertCart -> cartTypeId = $rowCart['cartTypeId'];
                            $insertCart -> lastUpdate = $rowCart['lastUpdate'];
                            $insertCart -> pickyCoinsAmountOnCart=$rowCart['pickyCoinsAmountOnCart'];
                            $insertCart -> remoteCartSellerId = $rowCart['remoteCartSellerId'];
                            $insertCart -> remoteShopSellerId = $shop;
                            $insertCart -> insert();

                        }

                    } else {

                        continue;
                    }

                }
                $stmtCartUpdate=$db_con->prepare('UPDATE Cart SET isImport=1  WHERE isImport=0 and isImport is null');
                $stmtCartUpdate->execute();
            } catch (\throwable $e) {
                $this->report('CImportExternalPickysiteOrder', 'error', 'Errore Cart ' . $e);
            }

            /***** inserimento righe carrello *********/
            try {
                $stmtCartLine = $db_con -> prepare('SELECT
                                            cl.id as remoteCartLineSellerId,
                                            cl.cartId as remoteCartId,
                                            cl.productId as productId,
                                            cl.productVariantId as productVariantId,
                                            cl.productSizeId as productSizeId,
                                            cl.isParallel as isParallel,
                                            cl.isImport as isImport 
                                            from CartLine cl WHERE isParallel is null AND cl.isImport=0');
                $stmtCartLine -> execute();
                if ($stmtCartLine!=null) {
                    while ($rowCartLineOrder = $stmtCartLine->fetch(PDO::FETCH_ASSOC)) {
                        $findCartLineIdIfExist = $cartLineRepo->findOneBy(['remoteCartLineSellerId' => $rowCartLineOrder['remoteCartLineSellerId'],'remoteShopSellerId' => $shop]);

                        if ($findCartLineIdIfExist == null) {
                            $cartIdFind = $cartRepo->findOneBy(['remoteCartSellerId' => $rowCartLineOrder['remoteCartId'],'remoteShopSellerId' => $shop]);
                            if ($cartIdFind !== null) {
                                $cartId = $cartIdFind->id;
                                $cartLineInsert = $cartLineRepo->getEmptyEntity();
                                $cartLineInsert->cartId = $cartId;
                                $cartLineInsert->productId = $rowCartLineOrder['productId'];
                                $cartLineInsert->productVariantId = $rowCartLineOrder['productVariantId'];
                                $cartLineInsert->productSizeId = $rowCartLineOrder['productSizeId'];
                                $cartLineInsert->remoteCartLineSellerId = $rowCartLineOrder['remoteCartLineSellerId'];
                                $cartLineInsert->remoteShopSellerId = $shop;
                                $cartLineInsert->remoteCartSellerId = $rowCartLineOrder['remoteCartId'];
                                $cartLineInsert->insert();

                            }
                        } else {

                            continue;
                        }

                    }
                }
                $stmtCartLineUpdate=$db_con->prepare('UPDATE CartLine SET isImport=1  WHERE isImport=0');
                $stmtCartLineUpdate->execute();
            } catch (\throwable $e) {
                $this -> report('CImportExternalPickySiteOrderJob', 'error', 'Errore CartLine ' . $e);
            }

            try {
                /***inserimento ordini */
                $stmtOrder = $db_con->prepare('SELECT 
                                               o.id as remoteOrderSellerId,
                                               o.orderPaymentMethodId as orderPaymentMethodId,
                                               o.orderShippingMethodId as orderShippingMethodId,
                                               o.couponId as couponId,
                                               o.userId as userId,
                                               U.`email` as `email`, 
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
                                               o.pickyCoinsMovementAmount as pickyCoinsMovementAmount,
                                               o.hasInvoice as hasInvoice,
                                               o.isParallel as isParallel,
                                               o.isOrderMarketplace as isOrderMarketplace,
                                               o.marketplaceId as marketplaceId,
                                               o.marketplaceOrderId as marketplaceOrderId,
                                               o.orderIDMarketplace as orderIDMarketplace,
                                               o.orderTypeId as orderTypeId,
                                               o.isImport as isImport
                                               from `Order` o join User U on o.userId = U.id WHERE isParallel is null  AND o.isImport=0 ');
                $stmtOrder->execute();
                while ($rowOrder = $stmtOrder->fetch(PDO::FETCH_ASSOC)) {


                    $checkOrderIfExist = $orderRepo->findOneBy(['remoteOrderSellerId' => $rowOrder['remoteOrderSellerId'], 'remoteShopSellerId' => $shop]);

                    if ($checkOrderIfExist == null) {
                        $findUser = $userRepo->findOneBy(['email' => $rowOrder['email']]);
                        if ($findUser !== null) {
                            $userId = $findUser->id;
                            $findCart = $cartRepo->findOneBy(['remoteCartSellerId' => $rowOrder['cartId'], 'remoteShopSellerId' => $shop]);
                            if ($findCart != null) {
                                $cartId = $findCart->id;
                                $insertOrder = $orderRepo->getEmptyEntity();
                                $insertOrder->orderPaymentMethodId = $rowOrder['orderPaymentMethodId'];
                                $insertOrder->orderShippingmethodId = $rowOrder['orderShippingMethodId'];
                                $insertOrder->pickyCoinsMovementAmount = $rowOrder['pickyCoinsMovementAmount'];

                                if ($rowOrder['couponId'] != '') {
                                    $FindCoupon = $couponRepo->findOneBy(['remoteId' => $rowOrder['couponId'], 'remoteShopId' => $shop]);
                                    if ($FindCoupon != null) {
                                        $insertOrder->couponId = $FindCoupon->id;
                                    }
                                }


                                $insertOrder->userId = $userId;
                                $insertOrder->cartId = $cartId;
                                $insertOrder->status = $rowOrder['status'];

                                $findUserAddressId=$userAddressRepo->findOneBy(['userId'=>$userId]);
                                if($findUserAddressId!=null){
                                    $insertOrder->billingAddressId=$findUserAddressId->id;
                                    $insertOrder->shipmentAddressId=$findUserAddressId->id;
                                }
                                $insertOrder->frozenShippingAddress = $rowOrder['frozenShippingAddress'];
                                $insertOrder->frozenBillingAddress = $rowOrder['frozenBillingAddress'];
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
                                $insertOrder->remoteOrderSellerId = $rowOrder['remoteOrderSellerId'];
                                $insertOrder->remoteShopSellerId = $shop;
                                $insertOrder->hasInvoice = $rowOrder['hasInvoice'];
                                $insertOrder->isParallel = $rowOrder['isParallel'];
                                $insertOrder->isOrderMarketplace=$rowOrder['isOrderMarketplace'];
                                $insertOrder->marketplaceOrderId=$rowOrder['marketplaceOrderId'];
                                $insertOrder->orderIDMarketplace=$rowOrder['orderIDMarketplace'];
                                $insertOrder->orderTypeId=$rowOrder['orderTypeId'];
                                $insertOrder->insert();
                                continue;

                            }
                        }
                    } else {

                        continue;

                    }

                }
                $stmtOrderUpdate=$db_con->prepare('UPDATE `Order` SET isImport=1  WHERE isImport=0');
                $stmtOrderUpdate->execute();

            } catch (\throwable $e) {
                $this->report('CImportExternalPickysiteOrderJob', 'error', 'Errore Order' . $e);
            }
            try {
                /**** inserimento righe Ordine*****/
                if ($shop == 1) {
                    $stmtOrderLine = $db_con->prepare(' SELECT ol.id AS remoteOrderLineSellerId,
                                     ol.orderId as remoteOrderSellerId,
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
                                     ol.note as note,
                                     ol.pickyCoinsMovementAmount as pickyCoinsMovementAmount,
                                     ol.isParallel as isParallel,
									 ol.isImport as isImport,
                                     ol.orderTypeId as orderTypeId
                                     FROM OrderLine ol WHERE ol.frozenProduct IS NOT NULL and isParallel is null and ol.isImport=0');
                } else {
                    $stmtOrderLine = $db_con->prepare('SELECT 
                                     ol.id AS remoteOrderLineSellerId,
                                     ol.orderId as remoteOrderSellerId,
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
                                     ol.note as note,
                                     ol.pickyCoinsMovementAmount as pickyCoinsMovementAmount,
                                     ol.isParallel as isParallel,
                                     ol.isImport as isImport,
                                     ol.orderTypeId as orderTypeId
                                     FROM OrderLine ol WHERE ol.frozenProduct IS NOT NULL and isParallel is null  AND isImport=0');
                }
                $stmtOrderLine->execute();
                while ($rowOrderLine = $stmtOrderLine->fetch(PDO::FETCH_ASSOC)) {
                    $checkOrderLineExist = $orderLineRepo->findOneBy(['remoteOrderLineSellerId' => $rowOrderLine['remoteOrderLineSellerId'], 'remoteOrderSellerId' => $rowOrderLine['remoteOrderSellerId'], 'remoteShopSellerId' => $shop]);
                    if ($checkOrderLineExist == null) {

                        $findOrder = $orderRepo->findOneBy(['remoteOrderSellerId' => $rowOrderLine['remoteOrderSellerId'], 'remoteShopSellerId' => $shop]);
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
                                $insertOrderLine->pickyCoinsMovementAmount = $rowOrderLine['pickyCoinsMovementAmount'];
                                $insertOrderLine->remoteOrderLineSellerId = $rowOrderLine['remoteOrderLineSellerId'];
                                $insertOrderLine->remoteShopSellerId = $shop;
                                $insertOrderLine->remoteOrderSellerId = $rowOrderLine['remoteOrderSellerId'];
                                $insertOrderLine->orderTypeId=$rowOrderLine['orderTypeId'];
                                $insertOrderLine->insert();

                            }


                        }
                    } else {

                        continue;

                    }

                }
                $stmtOrderLineUpdate=$db_con->prepare('UPDATE OrderLine SET isImport=1  WHERE isImport=0');
                $stmtOrderLineUpdate->execute();

            } catch (\throwable $e) {
                $this->report('CImportExternalPickysiteOrderJob', 'error', 'Errore OrderLine ' . $e);
            }
            $this -> report('Finish Import Order ', 'Shop:' . $value -> name);
        }





        $this -> report('Finish Procedure Order ', 'End Procedure');


    }


}
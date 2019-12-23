<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CCart;
use bamboo\domain\entities\CEmailTemplate;
use bamboo\domain\entities\CCouponType;
use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CCartLine;
use bamboo\domain\entities\CCartAbandonedEmailParam;
use bamboo\domain\entities\CUser;
use bamboo\core\base\CSerialNumber;
use PDO;
use PDOException;

/**
 * Class CCartAbandonedPlanEmailSendAddAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/12/2019
 * @since 1.0
 */
class CCartAbandonedPlanEmailSendAddAjaxController extends AAjaxController
{
    /**
     * @return int
     * @throws BambooException
     * @throws \Exception
     */
    public function post()
    {

        //prendo i dati passati in input
        $data = \Monkey::app()->router->request()->getRequestData();
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $generateCoupon = $data['generateCoupon'];
        $generateCoupon2 = $data['generateCoupon2'];
        $generateCoupon3 = $data['generateCoupon3'];
        $firstTemplateId = $data['firstTemplateId'];
        $firstTemplateRepo = \Monkey::app()->repoFactory->create('EmailTemplate')->findOneBy(['id' => $firstTemplateId]);
        $firstTemplate = $firstTemplateRepo->template;
        $secondTemplateId = $data['secondTemplateId'];
        $secondTemplateRepo = \Monkey::app()->repoFactory->create('EmailTemplate')->findOneBy(['id' => $secondTemplateId]);
        $secondTemplate = $secondTemplateRepo->template;
        $thirdTemplateId = $data['thirdTemplateId'];
        $thirdTemplateRepo = \Monkey::app()->repoFactory->create('EmailTemplate')->findOneBy(['id' => $thirdTemplateId]);
        $thirdTemplate = $thirdTemplateRepo->template;
        $firstTimeEmailSendDay = $data['firstTimeEmailSendDay'];
        $secondTimeEmailSendDay = $data['secondTimeEmailSendDay'];
        $thirdTimeEmailSendDay = $data['thirdTimeEmailSendDay'];
        $firstTimeEmailSendHour = $data['firstTimeEmailSendHour'];
        $secondTimeEmailSendHour = $data['secondTimeEmailSendHour'];
        $thirdTimeEmailSendHour = $data['thirdTimeEmailSendHour'];
        $selectEmail = "1";
        $shopId = $data['shopId'];
        $shop = $shopRepo->findOneBy(['id' => $shopId]);
        $shopName = $shop->name;
        $db_host = $shop->dbHost;
        $db_name = $shop->dbName;
        $db_user = $shop->dbUsername;
        $db_pass = $shop->dbPassword;
        try {

            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
            $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $res = " connessione ok <br>";
        } catch (PDOException $e) {
            $res = $e->getMessage();
        }
        $name = "CA-SendMail Shop nome- " . $shopName . "-shopId-" . $shopId . "-primo-invio" . "1T-" . $firstTemplateId . "-giorno:" . $firstTimeEmailSendDay . "-ora:" . $firstTimeEmailSendHour . "-secondo-invio" . $secondTemplateId . "-giorno:" . $secondTimeEmailSendDay . "-ora:" . $secondTimeEmailSendHour . "-terzo-invio-3T" . $thirdTemplateId . "-giorno:" . $thirdTimeEmailSendDay . "-ora:" . $thirdTimeEmailSendHour;
        /** var CRepo $cartAbandonedSendEmailParam */
        $cartAbandonedSendEmailParam = \Monkey::app()->repoFactory->create('CartAbandonedEmailParam')->getEmptyEntity();
        if ($generateCoupon == '1') {
            try {
                $typeCoupon = $data['typeCoupon'];
                $amount = $data['amount'];
                $validity = $data['validity'];
                $validForCartTotal = $data['validForCartTotal'];
                $hasFreeShipping = $data['hasFreeShipping'];
                $hasFreeReturn = $data['hasFreeReturn'];
                $nameCoupon = "CA-SendMail " . $shopName . "-shopId-" . $shopId . "-coupon-first-time-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;
                /** @var CRepo $couponTypeRepo */
                $couponTypeRepo = \Monkey::app()->repoFactory->create('CouponType')->getEmptyEntity();
                $couponTypeRepo->name = $nameCoupon;
                $couponTypeRepo->amount = $amount;
                $couponTypeRepo->amountType = $typeCoupon;
                $couponTypeRepo->validity = $validity;
                $couponTypeRepo->validForCartTotal = $validForCartTotal;
                $couponTypeRepo->hasFreeShipping = $hasFreeShipping;
                $couponTypeRepo->hasFreeReturn = $hasFreeReturn;
                $couponTypeRepo->remoteShopId = $shopId;
                $stmtInsertCouponType = $db_con->prepare("INSERT INTO CouponType (`name`,amount,amountType,validity,validForCartTotal,hasFreeShipping,hasFreeReturn,campaignId,isImport) 
                            VALUES(
                                   '" . $nameCoupon . "',
                                   '" . $amount . "',
                                   '" . $typeCoupon . "',
                                   '" . $validity . "',
                                   '" . $validForCartTotal . "',
                                   '" . $hasFreeShipping . "',
                                   '" . $hasFreeReturn . "',
                                   null,
                                   1)
                                    ");
                $stmtInsertCouponType->execute();
                $remoteId = $db_con->lastInsertId();
                $couponTypeRepo->remoteId = $remoteId;
                $couponTypeRepo->smartInsert();

                /** @var CRepo $couponTypeIdRepo */
                $couponTypeIdRepo = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['name' => $nameCoupon]);
                $coupon1TypeId = $couponTypeIdRepo->id;
            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('CCartAbandonedPlainEmailSendAddAjaxController','error','Create Remote coupon1TypeId',$e,'');
            }
        }
        if ($generateCoupon2 == '1') {
            try {
                $typeCoupon = $data['typeCoupon2'];
                $amount = $data['amount2'];
                $validity = $data['validity2'];
                $validForCartTotal = $data['validForCartTotal2'];
                $hasFreeShipping = $data['hasFreeShipping2'];
                $hasFreeReturn = $data['hasFreeReturn2'];
                $nameCoupon2 = "CA-SendMail " . $shopName . "-shopId-" . $shopId . "-coupon-second-time-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;
                /** @var CRepo $couponTypeRepo */
                $couponTypeRepo = \Monkey::app()->repoFactory->create('CouponType')->getEmptyEntity();
                $couponTypeRepo->name = $nameCoupon2;
                $couponTypeRepo->amount = $amount;
                $couponTypeRepo->amountType = $typeCoupon;
                $couponTypeRepo->validity = $validity;
                $couponTypeRepo->validForCartTotal = $validForCartTotal;
                $couponTypeRepo->hasFreeShipping = $hasFreeShipping;
                $couponTypeRepo->hasFreeReturn = $hasFreeReturn;
                $couponTypeRepo->remoteShopId = $shopId;
                $stmtInsertCouponType = $db_con->prepare("INSERT INTO CouponType (`name`,amount,amountType,validity,validForCartTotal,hasFreeShipping,hasFreeReturn,campaignId,isImport) 
                            VALUES(
                                   '" . $nameCoupon . "',
                                   '" . $amount . "',
                                   '" . $typeCoupon . "',
                                   '" . $validity . "',
                                   '" . $validForCartTotal . "',
                                   '" . $hasFreeShipping . "',
                                   '" . $hasFreeReturn . "',
                                   null,
                                   1)
                                    ");
                $stmtInsertCouponType->execute();
                $remoteId = $db_con->lastInsertId();
                $couponTypeRepo->remoteId = $remoteId;
                $couponTypeRepo->smartInsert();

                /** @var CRepo $couponTypeIdRepo */
                $couponTypeIdRepo = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['name' => $nameCoupon2]);
                $coupon2TypeId = $couponTypeIdRepo->id;
            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('CCartAbandonedPlainEmailSendAddAjaxController','error','Create Remote coupon2TypeId',$e,'');
            }

        }
        if ($generateCoupon3 == '1') {
            try {
                $selectEmail = "3";
                $typeCoupon = $data['typeCoupon3'];
                $amount = $data['amount3'];
                $validity = $data['validity3'];
                $validForCartTotal = $data['validForCartTotal3'];
                $hasFreeShipping = $data['hasFreeShipping3'];
                $hasFreeReturn = $data['hasFreeReturn3'];
                $nameCoupon3 = "CA-SendMail " . $shopName . "-shopId-" . $shopId . "-coupon-third-time-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;
                /** @var CRepo $couponTypeRepo */
                $couponTypeRepo = \Monkey::app()->repoFactory->create('CouponType')->getEmptyEntity();
                $couponTypeRepo->name = $nameCoupon3;
                $couponTypeRepo->amount = $amount;
                $couponTypeRepo->amountType = $typeCoupon;
                $couponTypeRepo->validity = $validity;
                $couponTypeRepo->validForCartTotal = $validForCartTotal;
                $couponTypeRepo->hasFreeShipping = $hasFreeShipping;
                $couponTypeRepo->hasFreeReturn = $hasFreeReturn;
                $couponTypeRepo->remoteShopId = $shopId;
                $stmtInsertCouponType = $db_con->prepare("INSERT INTO CouponType (`name`,amount,amountType,validity,validForCartTotal,hasFreeShipping,hasFreeReturn,campaignId,isImport) 
                            VALUES(
                                   '" . $nameCoupon . "',
                                   '" . $amount . "',
                                   '" . $typeCoupon . "',
                                   '" . $validity . "',
                                   '" . $validForCartTotal . "',
                                   '" . $hasFreeShipping . "',
                                   '" . $hasFreeReturn . "',
                                   null,
                                   1)
                                    ");
                $stmtInsertCouponType->execute();
                $remoteId = $db_con->lastInsertId();
                $couponTypeRepo->remoteId = $remoteId;
                $couponTypeRepo->smartInsert();

                /** @var CRepo $couponTypeIdRepo */
                $couponTypeIdRepo = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['name' => $nameCoupon3]);
                $coupon3TypeId = $couponTypeIdRepo->id;
            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('CCartAbandonedPlainEmailSendAddAjaxController','error','Create Remote coupon3TypeId',$e,'');
            }
        }
        $cartAbandonedSendEmailParam->firstTemplateId = $firstTemplateId;
        $cartAbandonedSendEmailParam->firstEmailTemplate = $firstTemplate;
        $cartAbandonedSendEmailParam->name = $name;
        $cartAbandonedSendEmailParam->firstTimeEmailSendDay = $firstTimeEmailSendDay;
        $cartAbandonedSendEmailParam->firstTimeEmailSendHour = $firstTimeEmailSendHour;
        $cartAbandonedSendEmailParam->secondTemplateId = $secondTemplateId;
        $cartAbandonedSendEmailParam->secondEmailTemplate = $secondTemplate;
        $cartAbandonedSendEmailParam->secondTimeEmailSendDay = $secondTimeEmailSendDay;
        $cartAbandonedSendEmailParam->secondTimeEmailSendHour = $secondTimeEmailSendHour;
        $cartAbandonedSendEmailParam->thirdTemplateId = $thirdTemplateId;
        $cartAbandonedSendEmailParam->thirdEmailTemplate = $thirdTemplate;
        $cartAbandonedSendEmailParam->thirdTimeEmailSendDay = $thirdTimeEmailSendDay;
        $cartAbandonedSendEmailParam->thirdTimeEmailSendHour = $thirdTimeEmailSendHour;
        $cartAbandonedSendEmailParam->selectMailCouponSend = $selectEmail;
        $cartAbandonedSendEmailParam->coupon1TypeId=$coupon1TypeId;
        $cartAbandonedSendEmailParam->coupon2TypeId=$coupon2TypeId;
        $cartAbandonedSendEmailParam->coupon3TypeId=$coupon3TypeId;
        $cartAbandonedSendEmailParam->shopId=$shopId;
        $cartAbandonedSendEmailParam->smartInsert();
        $res='Inserimento Regole Carrelli Abbandonati per lo shop '.$shopName;

        return $res;

    }

    public function put()
    {

        //prendo i dati passati in input
        $data = \Monkey::app()->router->request()->getRequestData();
        $res = "";
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $id1 = $data['cartIdEmailParam1'];
        $shopId=$data['shopId'];
        $generateCoupon = $data['generateCoupon'];
        $generateCoupon2 = $data['generateCoupon2'];
        $generateCoupon3 = $data['generateCoupon3'];
        $coupon1TypeId = $data['coupon1TypeId'];
        $coupon2TypeId = $data['coupon2TypeId'];
        $coupon3TypeId = $data['coupon3TypeId'];
        $firstTemplateId = $data['firstTemplateId'];
        $firstTemplateRepo = \Monkey::app()->repoFactory->create('EmailTemplate')->findOneBy(['id' => $firstTemplateId]);
        $firstTemplate = $firstTemplateRepo->template;
        $secondTemplateId = $data['secondTemplateId'];
        $secondTemplateRepo = \Monkey::app()->repoFactory->create('EmailTemplate')->findOneBy(['id' => $secondTemplateId]);
        $secondTemplate = $secondTemplateRepo->template;
        $thirdTemplateId = $data['thirdTemplateId'];
        $thirdTemplateRepo = \Monkey::app()->repoFactory->create('EmailTemplate')->findOneBy(['id' => $thirdTemplateId]);
        $thirdTemplate = $thirdTemplateRepo->template;
        $firstTimeEmailSendDay = $data['firstTimeEmailSendDay'];
        $secondTimeEmailSendDay = $data['secondTimeEmailSendDay'];
        $thirdTimeEmailSendDay = $data['thirdTimeEmailSendDay'];
        $firstTimeEmailSendHour = $data['firstTimeEmailSendHour'];
        $secondTimeEmailSendHour = $data['secondTimeEmailSendHour'];
        $thirdTimeEmailSendHour = $data['thirdTimeEmailSendHour'];
        $shop = $shopRepo->findOneBy(['id' => $shopId]);
        $shopName = $shop->name;
        $db_host = $shop->dbHost;
        $db_name = $shop->dbName;
        $db_user = $shop->dbUsername;
        $db_pass = $shop->dbPassword;
        try {

            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
            $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $res = " connessione ok <br>";
        } catch (PDOException $e) {
            $res = $e->getMessage();
        }
        $name = "CA-SendMail Shop nome- " . $shopName . "-shopId-" . $shopId . "-primo-invio" . "1T-" . $firstTemplateId . "-giorno:" . $firstTimeEmailSendDay . "-ora:" . $firstTimeEmailSendHour . "-secondo-invio" . $secondTemplateId . "-giorno:" . $secondTimeEmailSendDay . "-ora:" . $secondTimeEmailSendHour . "-terzo-invio-3T" . $thirdTemplateId . "-giorno:" . $thirdTimeEmailSendDay . "-ora:" . $thirdTimeEmailSendHour;

        //primo invio
        if ($generateCoupon == '1') {
            $typeCoupon = $data['typeCoupon'];
            $amount = $data['amount'];
            $validity = $data['validity'];
            


            $validForCartTotal = $data['validForCartTotal'];
            $hasFreeShipping = $data['hasFreeShipping'];
            $hasFreeReturn = $data['hasFreeReturn'];
            $nameCoupon = "CA-SendMail " . $shopName . "-shopId-" . $shopId . "-coupon-first-time-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;
            /** @var CRepo $couponTypeRepo */
            $couponTypeRepo = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id' => $coupon1TypeId]);
            $couponTypeRepo->name = $nameCoupon;
            $couponTypeRepo->amount = $amount;
            $couponTypeRepo->amountType = $typeCoupon;
            $couponTypeRepo->validity = $validity;
            $couponTypeRepo->validForCartTotal = $validForCartTotal;
            $couponTypeRepo->hasFreeShipping = $hasFreeShipping;
            $couponTypeRepo->hasFreeReturn = $hasFreeReturn;
            $couponTypeRepo->remoteShopId=$shopId;
            $remoteId=$couponTypeRepo->remoteId;
            $stmtUpdateRemoteCoupounType=$db_con->prepare("Update CouponType set 
                                        `name`='".$nameCoupon."',
                                        amount='".$amount."',
                                        amountType='".$typeCoupon."',
                                        validity='".$validity."',
                                        validForCartTotal='".$validForCartTotal."',
                                        hasFreeShipping='".$hasFreeShipping."',
                                        hasFreeReturn='".$hasFreeReturn."'
                                         WHERE id=".$remoteId);
            $stmtUpdateRemoteCoupounType->execute();
            $couponTypeRepo->update();
        }


        //secondo invio
        if ($generateCoupon2 == '1') {
            $typeCoupon = $data['typeCoupon2'];
            $amount = $data['amount2'];
            $validity = $data['validity2'];
            $validForCartTotal = $data['validForCartTotal2'];
            $hasFreeShipping = $data['hasFreeShipping2'];
            $hasFreeReturn = $data['hasFreeReturn2'];
            $nameCoupon2 = "CA-SendMail " . $shopName . "-shopId-" . $shopId . "-coupon-second-time-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;
            /** @var CRepo $couponTypeRepo */
            $couponTypeRepo = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id' => $coupon2TypeId]);
            $couponTypeRepo->name = $nameCoupon2;
            $couponTypeRepo->amount = $amount;
            $couponTypeRepo->amountType = $typeCoupon;
            $couponTypeRepo->validity = $validity;
            $couponTypeRepo->validForCartTotal = $validForCartTotal;
            $couponTypeRepo->hasFreeShipping = $hasFreeShipping;
            $couponTypeRepo->hasFreeReturn = $hasFreeReturn;
            $couponTypeRepo->remoteShopId=$shopId;
            $remoteId=$couponTypeRepo->remoteId;
            $stmtUpdateRemoteCoupounType=$db_con->prepare("Update CouponType set 
                                        `name`='".$nameCoupon2."',
                                        amount='".$amount."',
                                        amountType='".$typeCoupon."',
                                        validity='".$validity."',
                                        validForCartTotal='".$validForCartTotal."',
                                        hasFreeShipping='".$hasFreeShipping."',
                                        hasFreeReturn='".$hasFreeReturn."'
                                         WHERE id=".$remoteId);
            $stmtUpdateRemoteCoupounType->execute();
            $couponTypeRepo->update();
        }

        //terzo invio
        if ($generateCoupon3 == '1') {
            $typeCoupon = $data['typeCoupon3'];
            $amount = $data['amount3'];
            $validity = $data['validity3'];
            $validForCartTotal = $data['validForCartTotal3'];
            $hasFreeShipping = $data['hasFreeShipping3'];
            $hasFreeReturn = $data['hasFreeReturn3'];
            $nameCoupon3 = "CA-SendMail " . $shopName . "-shopId-" . $shopId . "-coupon-third-time-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;
            /** @var CRepo $couponTypeRepo */
            $couponTypeRepo = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id' => $coupon3TypeId]);
            $couponTypeRepo->name = $nameCoupon3;
            $couponTypeRepo->amount = $amount;
            $couponTypeRepo->amountType = $typeCoupon;
            $couponTypeRepo->validity = $validity;
            $couponTypeRepo->validForCartTotal = $validForCartTotal;
            $couponTypeRepo->hasFreeShipping = $hasFreeShipping;
            $couponTypeRepo->hasFreeReturn = $hasFreeReturn;
            $couponTypeRepo->remoteShopId=$shopId;
            $remoteId=$couponTypeRepo->remoteId;
            $stmtUpdateRemoteCoupounType=$db_con->prepare("Update CouponType set 
                                        `name`='".$nameCoupon3."',
                                        amount='".$amount."',
                                        amountType='".$typeCoupon."',
                                        validity='".$validity."',
                                        validForCartTotal='".$validForCartTotal."',
                                        hasFreeShipping='".$hasFreeShipping."',
                                        hasFreeReturn='".$hasFreeReturn."'
                                         WHERE id=".$remoteId);
            $stmtUpdateRemoteCoupounType->execute();
            $couponTypeRepo->update();
        }
           $cartAbandonedSendEmailParam = \Monkey::app()->repoFactory->create('CartAbandonedEmailParam')->findOneBy(['id' => $id1]);
            $cartAbandonedSendEmailParam->firstTemplateId = $firstTemplateId;
            $cartAbandonedSendEmailParam->firstEmailTemplate = $firstTemplate;
            $cartAbandonedSendEmailParam->name = $name;
            $cartAbandonedSendEmailParam->firstTimeEmailSendDay = $firstTimeEmailSendDay;
            $cartAbandonedSendEmailParam->firstTimeEmailSendHour = $firstTimeEmailSendHour;
            $cartAbandonedSendEmailParam->secondTemplateId = $secondTemplateId;
            $cartAbandonedSendEmailParam->secondEmailTemplate = $secondTemplate;
            $cartAbandonedSendEmailParam->secondTimeEmailSendDay = $secondTimeEmailSendDay;
            $cartAbandonedSendEmailParam->secondTimeEmailSendHour = $secondTimeEmailSendHour;
            $cartAbandonedSendEmailParam->thirdTemplateId = $thirdTemplateId;
            $cartAbandonedSendEmailParam->thirdEmailTemplate = $thirdTemplate;
            $cartAbandonedSendEmailParam->thirdTimeEmailSendDay = $thirdTimeEmailSendDay;
            $cartAbandonedSendEmailParam->thirdTimeEmailSendHour = $thirdTimeEmailSendHour;
            $cartAbandonedSendEmailParam->update();


            $res = $res . "<br>Aggiornamento Regola Eseguito";


        return $res;

    }


}
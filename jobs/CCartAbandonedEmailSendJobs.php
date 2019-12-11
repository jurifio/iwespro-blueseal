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

use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProduct;
use bamboo\core\events\AEventListener;
use DateTime;
use PDO;
use PDOException;


/**
 * Class CCartAbandonedSendEmail
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/07/2018
 * @since 1.0
 */
class CCartAbandonedEmailSendJobs extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)

    {
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $sql = "SELECT
  C.id                                                   AS id,
  C.creationDate                                         AS creationDate,
  C.userId                                               AS userId,
  C.cartTypeId                                           AS carTypeId,
  C.lastUpdate                                           AS lastUpdate,
  C.remoteShopSellerId                                   as remoteShopSellerId
FROM Cart C 
WHERE C.userId <>''
      AND C.cartTypeId IN(1,2) AND creationDate > (NOW()- INTERVAL 7 DAY) AND C.couponId IS NULL 
GROUP BY C.id";
//estraggo tutti i carrelli abbandonati
        /** @var CCartRepo $cartRepo */
        $cartRepo = \Monkey::app()->repoFactory->create('Cart');
        /** @var $listCart CObjectCollection */
        $listCart = $cartRepo->findBySql($sql);
        //colleziono tutti i carrelli
        foreach ($listCart as $cart) {

            $customer = $cart->userId;
            $cartId = $cart->id;
            $cartDate = $cart->creationDate;

            $cartDate = new \DateTime($cartDate);
            $defDate = $cartDate->format('Y-m-d H:i:s');
            $remoteShopSellerId = $cart->remoteShopSellerId;


//seleziono tutti i parametri per la generazione delle email

            $lastUpdate = $cart->lastUpdate;
            /** @var  $sqlCartAbandonedEmailParam */
            $sqlCartAbandonedEmailParam = 'SELECT id ,
                      name, 
                      firstTemplateId,
                      firstEmailTemplate,       
                      firstTimeEmailSendDay,
                      firstTimeEmailSendHour,
                      secondTemplateId,
                      secondEmailTemplate,
                      secondTimeEmailSendDay,
                      secondTimeEmailSendHour,
                      thirdTemplateId,
                      thirdEmailTemplate,
                      thirdTimeEmailSendHour,
                      thirdTimeEmailSendHour,
                      couponTypeId,
                      selectMailCouponSend
   FROM CartAbandonedEmailParam WHERE shopId =' . $remoteShopSellerId;
//collezione email Parametri


            $cartAbandonedEmailParam = \Monkey::app()->repoFactory->create('CartAbandonedEmailParam')->findOneBy(['shopId' => $remoteShopSellerId,'isActive' => 1]);
            if ($cartAbandonedEmailParam == null) {
                \Monkey::app()->applicationLog('CcartAbandonedEmailSendJobs','warning','Find Rules ','any rules for shop ' . $remoteShopSellerId . 'not active or found','');
            }
            $firstTemplateId = $cartAbandonedEmailParam->firstTemplateId;
            $firstEmailTemplate = $cartAbandonedEmailParam->firstEmailTemplate;
            $firstTimeEmailSendDay = $cartAbandonedEmailParam->firstTimeEmailSendDay;
            $firstTimeEmailSendHour = $cartAbandonedEmailParam->firstTimeEmailSendHour;
            $secondTemplateId = $cartAbandonedEmailParam->secondTemplateId;
            $secondEmailTemplate = $cartAbandonedEmailParam->secondEmailTemplate;
            $secondTimeEmailSendDay = $cartAbandonedEmailParam->secondTimeEmailSendDay;
            $secondTimeEmailSendHour = $cartAbandonedEmailParam->firstTimeEmailSendHour;
            $thirdTemplateId = $cartAbandonedEmailParam->thirdTemplateId;
            $thirdEmailTemplate = $cartAbandonedEmailParam->thirdEmailTemplate;
            $thirdTimeEmailSendDay = $cartAbandonedEmailParam->thirdTimeEmailSendDay;
            $thirdTimeEmailSendHour = $cartAbandonedEmailParam->thirdTimeEmailSendHour;
            $coupon1TypeId = $cartAbandonedEmailParam->coupon1TypeId;
            $coupon2TypeId = $cartAbandonedEmailParam->coupon2TypeId;
            $coupon3TypeId = $cartAbandonedEmailParam->coupon3TypeId;
            $shopId = $cartAbandonedEmailParam->shopId;
            /* @var CCartAbandonedSendEmailIfExist $cartAbandonedSendEmailIfExist */
            $cartAbandonedSendEmailIfExist = \Monkey::app()->repoFactory->create('CartAbandonedEmailSend')->findOneBy(['cartId' => $cartId,'coupon1TypeId' => $coupon1TypeId,'coupon2TypeId' => $coupon2TypeId,'coupon3TypeId' => $coupon3TypeId,'shopId' => $shopId]);
            if (empty($cartAbandonedSendEmailIfExist)) {

                // se non esiste la inserisco e e la setto con la fase 0*/
                try {
                    $selectMailCouponSend = 1;
                    $cartAbandonedEmailSendInsert = \Monkey::app()->repoFactory->create('CartAbandonedEmailSend')->getEmptyEntity();
                    $cartAbandonedEmailSendInsert->cartId = $cartId;
                    $cartAbandonedEmailSendInsert->firstTemplateId = $firstTemplateId;
                    $firstEmailSendDate = date('Y-m-d',strtotime('+' . $firstTimeEmailSendDay . ' day ',strtotime($defDate)));
                    $firstEmailSendDate = date('Y-m-d ' . $firstTimeEmailSendHour . ':i:s',strtotime($firstEmailSendDate));
                    $cartAbandonedEmailSendInsert->firstTimeEmailSendDate = $firstEmailSendDate;
                    $cartAbandonedEmailSendInsert->firstSentCheck = 0;
                    $cartAbandonedEmailSendInsert->secondTemplateId = $secondTemplateId;
                    $secondEmailSendDate = date('Y-m-d',strtotime('+' . $secondTimeEmailSendDay . ' day ',strtotime($defDate)));
                    $secondEmailSendDate = date('Y-m-d ' . $secondTimeEmailSendHour . ':i:s',strtotime($secondEmailSendDate));
                    $cartAbandonedEmailSendInsert->secondTimeEmailSendDate = $secondEmailSendDate;
                    $cartAbandonedEmailSendInsert->secondSentCheck = 0;
                    $cartAbandonedEmailSendInsert->thirdTemplateId = $thirdTemplateId;
                    $thirdEmailSendDate = date('Y-m-d',strtotime('+' . $thirdTimeEmailSendDay . ' day ',strtotime($defDate)));
                    $thirdEmailSendDate = date('Y-m-d ' . $thirdTimeEmailSendHour . ':i:s',strtotime($thirdEmailSendDate));
                    $cartAbandonedEmailSendInsert->thirdTimeEmailSendDate = $thirdEmailSendDate;
                    $cartAbandonedEmailSendInsert->thirdSentCheck = 0;
                    $cartAbandonedEmailSendInsert->userId = $customer;
                    $cartAbandonedEmailSendInsert->coupon1TypeId = $cartAbandonedEmailParam->coupon1typeId;
                    $cartAbandonedEmailSendInsert->coupon2TypeId = $cartAbandonedEmailParam->coupon2typeId;
                    $cartAbandonedEmailSendInsert->coupon3TypeId = $cartAbandonedEmailParam->coupon3typeId;
                    $cartAbandonedEmailSendInsert->selectMailCouponSend = $selectMailCouponSend;
                    $cartAbandonedEmailSendInsert->shopId = $remoteShopSellerId;
                    $cartAbandonedEmailSendInsert->smartInsert();
                }catch (\Throwable $e){
                    \Monkey::app()->applicationLog('CcartAbandonedEmailSendJobs',' error','insert program  send error ',$e,'');
                }
            } else {
                // se non esiste controllo su quale fase si trova
                $selectMailCouponSend = $cartAbandonedSendEmailIfExist->selectMailCouponSend;
                $emailUserFind = \Monkey::app()->repoFactory->create('User')->findOneBy(['id' => $cartAbandonedSendEmailIfExist->userId]);
                $emailUser = $emailUserFind->email;
                $emailUserDetails = \Monkey::app()->repoFactory->create('UserDetails')->findOneBy(['userId' => $cartAbandonedSendEmailIfExist->userId]);
                $userDetail = $emailUserDetails->name . " " . $emailUserDetails->surname;
                $stmtUser = $db_con->prepare('SELECT id from User where `email`= \'' . $emailUser . '\'');
                $stmtUser->execute();
                $rowUser = $stmtUser->fetch(PDO::FETCH_ASSOC);
                $remoteUserId = $rowUser['id'];
                // colleziono tutte le linee dei carrelli con il cartId
                /** @var  $listCartLine CObjectCollection */
                $cartPrice = 0;
                $listCartLine = \Monkey::app()->repoFactory->create('CartLine')->findBy(['cartId' => $cartAbandonedSendEmailIfExist->cartId,'remoteShopSellerId' => $cartAbandonedSendEmailIfExist->shopId]);
                $shop = $shopRepo->findOneBy(['id' => $cartAbandonedSendEmailIfExist->shopId]);
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
                foreach ($listCartLine as $line) {
                    $ProductId = $line->productId;
                    $ProductVariantId = $line->productVariantId;
                    $ProductSizeId = $line->productSizeId;
                    //ottengo tutti i prodotti dalla tabella ProductPublicSku
                    /** @var productPublicSkuRepo $productPublicSku */
                    $productPublicSku = \Monkey::app()->repoFactory->create('ProductPublicSku')->findOneBy(['productId' => $ProductId,'productVariantId' => $ProductVariantId,'productSizeId' => $ProductSizeId]);
                    $price = $productPublicSku->price;
                    $salePrice = $productPublicSku->salePrice;
                    // controllo se il prodotto è in saldo
                    /** @var $productIsOnSale CObjectCollection */
                    $productIsOnSale = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $ProductId,'productVariantId' => $ProductVariantId]);
                    $isOnSale = $productIsOnSale->isOnSale;
                    if ($isOnSale == '1') {
                        $cartPrice = $cartPrice + $salePrice;

                    } else {
                        $cartPrice = $cartPrice + $price;
                    }
                }

                switch ($selectMailCouponSend) {

                    case 1:
                        try {
                            $checkDate = new DateTime($cartAbandonedSendEmailIfExist->firsTimeEmailSendDate);
                            $startToday = new DateTime();
                            $endToday = new DateTime('+1 day');
                            if ($checkDate->getTimestamp() > $startToday->getTimestamp() && $checkDate->getTimestamp() < $endToday->getTimestamp()) {
                                //trovo il carrello e tutti i relativi prodotti
                                $coupon1TypeId = $cartAbandonedSendEmailIfExist->coupon1TypeId;

                                if ($coupon1TypeId != null) {
                                    $couponType = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id' => $coupon1TypeId]);

                                    $remoteCouponTypeId = $couponType->remoteId;
                                    $amount = $couponType->amount;
                                    $amountType = $couponType->amountType;
                                    $validity = $couponType->validity;
                                    $validForCartTotal = $couponType->validForCartTotal;

                                    if ($validForCartTotal <= $cartPrice) {
                                        $couponGenerate = \Monkey::app()->repoFactory->create('Coupon')->getEmptyEntity();
                                        $invoiceSiteChar = $shop->siteInvoiceChar;
                                        $couponGenerate->couponTypeId = $coupon1TypeId;
                                        $serial = new CSerialNumber();
                                        $serial->generate();
                                        $code = $invoiceSiteChar . '-' . $serial->__toString();
                                        $couponGenerate->code = $code;
                                        $issueDate = new \DateTime();
                                        $validUntil = new \DateInterval($validity);
                                        $validThru = $issueDate->add($validUntil);
                                        $couponGenerate->issueDate = $issueDate->format('Y-m-d H:i:s');
                                        $couponGenerate->validThru = date_format($validThru,'Y-m-d H:i:s');
                                        if ($amountType == "P") {
                                            $amountCart = $cartPrice / 100 * $amount;
                                        } else {
                                            $amountCart = $amount;
                                        }
                                        $couponGenerate->amount = $amountCart;
                                        $couponGenerate->userId = $customer;
                                        $couponGenerate->valid = "1";
                                        $stmtInsertCoupon = $db_con->prepare("INSERT INTO Coupon (`couponTypeId`,`code`,issueDate,validThru,amount,userId,valid,couponEventId,isImport,sid) 
                            VALUES(
                                   '" . $remoteCouponTypeId . "',
                                   '" . $code . "',
                                   '" . $issueDate->format('Y-m-d H:i:s') . "',
                                   '" . date_format($validThru,'Y-m-d H:i:s') . "',
                                   '" . $amount . "',
                                   '" . $remoteUserId . "',
                                   '1',
                                   null,
                                   1,
                                   null)
                                    ");
                                        $stmtInsertCoupon->execute();
                                        $remoteCouponId = $db_con->lastInsertId();
                                        $couponGenerate->remoteId = $remoteCouponId;
                                        $couponGenerate->remoteShopId = $shopId;
                                        $couponGenerate->smartInsert();
                                        $getcouponId = \Monkey::app()->repoFactory->create('Coupon')->findOneBy(['code' => $code,'userId' => $customer]);
                                        $idCoupon = $getcouponId->id;
                                        $cartAbandonedSendEmailIfExist->couponId = $idCoupon;
                                    }
                                }

                                $from = $shop->emailShop;
                                $subject = 'Completa il tuo Ordine';
                                $cartLineFind = \Monkey::app()->repoFactory->create('CartLine')->findby(['cartId' => $cartId]);
                                $cartAmount = 0;
                                $cartRow = "";
                                foreach ($cartLineFind as $cartLine) {
                                    $productId = $cartLine->productId;
                                    $productVariantId = $cartLine->productVariantId;
                                    $productSizeId = $cartLine->productSizeId;
                                    $productPublicSkuFind = \Monkey::app()->repoFactory->create('productPublicSku')->findOneBy(['productId' => $productId,'productVariantId' => $productVariantId,'productSizeId' => $productSizeId]);
                                    $isOnSaleFind = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $productId,'productVariantId' => $productVariantId]);
                                    $isOnSale = $isOnSaleFind->isOnSale;
                                    $productBrand = $isOnSaleFind->productBrandId;
                                    if ($isOnSale == "1") {
                                        $price = $productPublicSkuFind->salePrice;
                                    } else {
                                        $price = $productPublicSkuFind->price;
                                    }
                                    $dummyPicture = $isOnSaleFind->dummyPicture;
                                    $productBrandFind = \Monkey::app()->repoFactory->create('ProductBrand')->findOneBy(['id' => $productBrand]);
                                    $productBrand = $productBrandFind->name;
                                    $cartAmount = $cartAmount + $price;
                                    $cartRowLine = "<!--riga carrello-->
                                                                        <tr>
                                                        <td valign=\"top\" align=\"center\" class=\"lh-3\"
                                                            style=\"padding: 0px 40px; margin: 0px;\">
                                                            <hr>
                                                        </td>
                                                    </tr>
                                    <tr>
                                    <td class=\"lh-3\" style=\"padding: 0px 40px; margin: 0px;\" align=\"center\" valign=\"top\"><hr /></td>
                                    </tr>
                                    <tr>
                                    <td style=\"padding: 0 20px; margin: 0;\" align=\"center\" valign=\"top\">
                                    <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
                                    <tbody>
                                    <tr>
                                    <td style=\"padding: 0 10px; margin: 0;\" align=\"center\" valign=\"top\">
                                    <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
                                    <tbody>
                                    <tr>
                                    <td class=\"\" style=\"margin: 0px; padding: 10px 0;\" align=\"left\" valign=\"top\" width=\"11%\">
                                    <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
                                    <tbody>
                                    <tr>
                                    <td style=\"padding: 0; margin: 0;\" align=\"center\" valign=\"top\">
                                    <table border=\"0\" width=\"64\" cellspacing=\"0\" cellpadding=\"0\" align=\"left\" data-editable=\"image\" data-mobile-width=\"0\">
                                    <tbody>
                                    <tr>
                                    <td class=\"tdBlock\" style=\"display: inline-block; padding: 0px 0px 0px 40px; margin: 0px;\" align=\"left\" valign=\"top\"><img style=\"font-size: 12px; display: block; border: 0px none transparent;\" src=\"" . $dummyPicture . "\" height=\"100\" border=\"0\" /></td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    <td class=\"\" style=\"padding: 0px; margin: 0px;\" align=\"left\" valign=\"top\" width=\"50%\">
                                    <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" data-editable=\"text\">
                                    <tbody>
                                    <tr>
                                    <td class=\"lh-1\" style=\"padding: 50px 0px; margin: 0px; line-height: 1.15; font-size: 16px; font-family: Times New Roman, Times, serif;\" align=\"center\" valign=\"top\"><span style=\"font-family: Helvetica,Arial,sans-serif; font-size: 14px; font-weight: 300; color: #000000; line-height: 0.5;\">" . $productBrand . "<br> Prodotto:" . $productId . "-" . $productVariantId . "</span></td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    <td class=\"\" style=\"padding: 0px; margin: 0px;\" align=\"left\" valign=\"top\" width=\"30%\">
                                    <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" data-editable=\"text\">
                                    <tbody>
                                    <tr>
                                    <td class=\"lh-3\" style=\"padding: 50px 20px 5px 30px; margin: 0px; line-height: 1.35; font-size: 16px; font-family: Times New Roman, Times, serif;\" align=\"right\" valign=\"top\"><span style=\"font-family: Helvetica,Arial,sans-serif; font-size: 14px; font-weight: 300; color: #000000; line-height: 0.5;\"> <span style=\"font-weight: bold;\">" . $price . "</span> </span></td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    </tr>
                                      <tr>
                                                        <td valign=\"top\" align=\"center\" class=\"lh-3\"
                                                            style=\"padding: 0px 40px; margin: 0px;\">
                                                            <hr>
                                                        </td>
                                                    </tr>
                                    <!-- fine Riga Carrello-->";
                                    $cartRow = $cartRow . $cartRowLine;
                                }
                                if ($coupon1TypeId != null) {
                                    $couponTypeFind = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id' => $coupon1TypeId]);
                                    $amountType = $couponTypeFind->amountType;
                                    $amount = $couponTypeFind->amount;
                                    $hasFreeShipping = $couponTypeFind->hasFreeShipping;
                                    if ($hasFreeShipping == "1") {
                                        $cartTotalAmount = number_format($cartAmount,2) . " + SPEDIZIONE GRATUITA";
                                    } else {
                                        $cartTotalAmount = number_format($cartAmount,2) . "+ SPESE SPEDIZIONE";
                                    }
                                    $couponFind = \Monkey::app()->repoFactory->create('Coupon')->findOneBy(['id' => $couponId]);
                                    $code = $couponFind->code;
                                    if ($amountType == "P") {
                                        $couponFirstRow = "Abbiamo riservato Per TE un Coupon del " . $amount . "% di sconto che potrai utilizzare per completare l'ordine!";
                                    } else {
                                        $couponFirstRow = "Abbiamo riservato Per TE un Coupon del valore di " . $amount . "€ di  sconto che potrai utilizzare per completare l'ordine!";
                                    }
                                    $couponLastRow = "Inserisci il coupon nell'area riservata del tuo carrello.";

                                    $cartAmount = number_format($cartAmount,2);
                                } else {
                                    $cartTotalAmount = number_format($cartAmount,2) . "+ SPESE SPEDIZIONE";
                                    $cartAmount = number_format($cartAmount,2);
                                    $couponFirstRow = '';
                                    $couponLastRow = '';
                                    $code = '';
                                }

                                $ordercheck = \Monkey::app()->repoFactory->create('Order')->findOneBy(['cartId' => $cartId]);
                                if (empty($ordercheck)) {
                                    $message = str_replace('{nome}',$userDetail,$firstEmailTemplate);
                                    $message = str_replace('{emailunsuscriber}',$emailUser,$message);
                                    $message = str_replace('{cartRow}',$cartRow,$message);
                                    $message = str_replace('{cartAmount}',$cartAmount,$message);
                                    $message = str_replace('{couponFirstRow}',$couponFirstRow,$message);
                                    $message = str_replace('{code}',$code,$message);
                                    $message = str_replace('{couponLastRow}',$couponLastRow,$message);
                                    $message = str_replace('{cartTotalAmount}',$cartTotalAmount,$message);
                                    $emailRepo = \Monkey::app()->repoFactory->create('Email');
                                    $res = $emailRepo->newMail($from,[$emailUser],[],[],$subject,$message,null,null,null,'mailGun',false);
                                    $cartAbandonedSendEmailIfExist->firstEmailTemplate = $message;
                                    $cartAbandonedSendEmailIfExist->firstSentCheck = "1";
                                    $cartAbandonedSendEmailIfExist->selectEmailCouponSend = 2;
                                    $cartAbandonedSendEmailIfExist->update();
                                }
                            }
                        }catch (\Throwable $e){
                            \Monkey::app()->applicationLog('CcartAbandonedEmailSendJobs','errot','First Send cannot execute',$e,'');
                        }
                        break;
                    case
                    2:
                        try{
                        $checkDate = new DateTime($cartAbandonedSendEmailIfExist->secondTimeEmailSendDate);
                        $startToday = new DateTime();
                        $endToday = new DateTime('+1 day');
                        if ($checkDate->getTimestamp() > $startToday->getTimestamp() && $checkDate->getTimestamp() < $endToday->getTimestamp()) {
                            $coupon2TypeId = $cartAbandonedSendEmailIfExist->coupon2TypeId;
                            if ($coupon2TypeId != null) {
                                $couponType = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id' => $coupon2TypeId]);

                                $remoteCouponTypeId = $couponType->remoteId;
                                $amount = $couponType->amount;
                                $amountType = $couponType->amountType;
                                $validity = $couponType->validity;
                                $validForCartTotal = $couponType->validForCartTotal;

                                if ($validForCartTotal <= $cartPrice) {
                                    $couponFind = \Monkey::app()->repoFactory->create('Coupon')->findOneBy(['id' => $cartAbandonedSendEmailIfExist->couponId]);
                                    $remoteCouponId=$couponFind->remoteId;
                                    $code = $couponFind->code;
                                    $issueDate = new \DateTime();
                                    $validUntil = new \DateInterval($validity);
                                    $validThru = $issueDate->add($validUntil);
                                    $couponFind->issueDate = $issueDate->format('Y-m-d H:i:s');
                                    $couponFind->validThru = date_format($validThru,'Y-m-d H:i:s');
                                    if ($amountType == "P") {
                                        $amountCart = $cartPrice / 100 * $amount;
                                    } else {
                                        $amountCart = $amount;
                                    }
                                    $couponFind->amount = $amountCart;
                                    $couponFind->valid = "1";
                                    $stmtUpdateCoupon = $db_con->prepare("Update Coupon set  
                                    `couponTypeId`='".$remoteCouponTypeId."',
                                     issueDate='".$issueDate->format('Y-m-d H:i:s')."',
                                     validThru='".date_format($validThru,'Y-m-d H:i:s')."',
                                     amount='". $amount."',
                                     valid='1' WHERE id=".$remoteCouponId);
                                    $stmtUpdateCoupon->execute();
                                    $couponFind->update();
                                    $idCoupon = $cartAbandonedSendEmailIfExist->couponId;
                                }
                            }

                            $from = $shop->emailShop;
                            $subject = 'Completa il tuo Ordine';
                            $cartLineFind = \Monkey::app()->repoFactory->create('CartLine')->findby(['cartId' => $cartId]);
                            $cartAmount = 0;
                            $cartRow = "";
                            foreach ($cartLineFind as $cartLine) {
                                $productId = $cartLine->productId;
                                $productVariantId = $cartLine->productVariantId;
                                $productSizeId = $cartLine->productSizeId;
                                $productPublicSkuFind = \Monkey::app()->repoFactory->create('productPublicSku')->findOneBy(['productId' => $productId,'productVariantId' => $productVariantId,'productSizeId' => $productSizeId]);
                                $isOnSaleFind = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $productId,'productVariantId' => $productVariantId]);
                                $isOnSale = $isOnSaleFind->isOnSale;
                                $productBrand = $isOnSaleFind->productBrandId;
                                if ($isOnSale == "1") {
                                    $price = $productPublicSkuFind->salePrice;
                                } else {
                                    $price = $productPublicSkuFind->price;
                                }
                                $dummyPicture = $isOnSaleFind->dummyPicture;
                                $productBrandFind = \Monkey::app()->repoFactory->create('ProductBrand')->findOneBy(['id' => $productBrand]);
                                $productBrand = $productBrandFind->name;
                                $cartAmount = $cartAmount + $price;
                                $cartRowLine = "<!--riga carrello-->
                                                                        <tr>
                                                        <td valign=\"top\" align=\"center\" class=\"lh-3\"
                                                            style=\"padding: 0px 40px; margin: 0px;\">
                                                            <hr>
                                                        </td>
                                                    </tr>
                                    <tr>
                                    <td class=\"lh-3\" style=\"padding: 0px 40px; margin: 0px;\" align=\"center\" valign=\"top\"><hr /></td>
                                    </tr>
                                    <tr>
                                    <td style=\"padding: 0 20px; margin: 0;\" align=\"center\" valign=\"top\">
                                    <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
                                    <tbody>
                                    <tr>
                                    <td style=\"padding: 0 10px; margin: 0;\" align=\"center\" valign=\"top\">
                                    <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
                                    <tbody>
                                    <tr>
                                    <td class=\"\" style=\"margin: 0px; padding: 10px 0;\" align=\"left\" valign=\"top\" width=\"11%\">
                                    <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
                                    <tbody>
                                    <tr>
                                    <td style=\"padding: 0; margin: 0;\" align=\"center\" valign=\"top\">
                                    <table border=\"0\" width=\"64\" cellspacing=\"0\" cellpadding=\"0\" align=\"left\" data-editable=\"image\" data-mobile-width=\"0\">
                                    <tbody>
                                    <tr>
                                    <td class=\"tdBlock\" style=\"display: inline-block; padding: 0px 0px 0px 40px; margin: 0px;\" align=\"left\" valign=\"top\"><img style=\"font-size: 12px; display: block; border: 0px none transparent;\" src=\"" . $dummyPicture . "\" height=\"100\" border=\"0\" /></td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    <td class=\"\" style=\"padding: 0px; margin: 0px;\" align=\"left\" valign=\"top\" width=\"50%\">
                                    <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" data-editable=\"text\">
                                    <tbody>
                                    <tr>
                                    <td class=\"lh-1\" style=\"padding: 50px 0px; margin: 0px; line-height: 1.15; font-size: 16px; font-family: Times New Roman, Times, serif;\" align=\"center\" valign=\"top\"><span style=\"font-family: Helvetica,Arial,sans-serif; font-size: 14px; font-weight: 300; color: #000000; line-height: 0.5;\">" . $productBrand . "<br> Prodotto:" . $productId . "-" . $productVariantId . "</span></td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    <td class=\"\" style=\"padding: 0px; margin: 0px;\" align=\"left\" valign=\"top\" width=\"30%\">
                                    <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" data-editable=\"text\">
                                    <tbody>
                                    <tr>
                                    <td class=\"lh-3\" style=\"padding: 50px 20px 5px 30px; margin: 0px; line-height: 1.35; font-size: 16px; font-family: Times New Roman, Times, serif;\" align=\"right\" valign=\"top\"><span style=\"font-family: Helvetica,Arial,sans-serif; font-size: 14px; font-weight: 300; color: #000000; line-height: 0.5;\"> <span style=\"font-weight: bold;\">" . $price . "</span> </span></td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    </tr>
                                      <tr>
                                                        <td valign=\"top\" align=\"center\" class=\"lh-3\"
                                                            style=\"padding: 0px 40px; margin: 0px;\">
                                                            <hr>
                                                        </td>
                                                    </tr>
                                    <!-- fine Riga Carrello-->";
                                $cartRow = $cartRow . $cartRowLine;
                            }
                            if ($coupon2TypeId != null) {
                                $couponTypeFind = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id' => $coupon2TypeId]);
                                $amountType = $couponTypeFind->amountType;
                                $amount = $couponTypeFind->amount;
                                $hasFreeShipping = $couponTypeFind->hasFreeShipping;
                                if ($hasFreeShipping == "1") {
                                    $cartTotalAmount = number_format($cartAmount,2) . " + SPEDIZIONE GRATUITA";
                                } else {
                                    $cartTotalAmount = number_format($cartAmount,2) . "+ SPESE SPEDIZIONE";
                                }
                                $couponFind = \Monkey::app()->repoFactory->create('Coupon')->findOneBy(['id' => $couponId]);
                                $code = $couponFind->code;
                                if ($amountType == "P") {
                                    $couponFirstRow = "Abbiamo riservato Per TE un Coupon del " . $amount . "% di sconto che potrai utilizzare per completare l'ordine!";
                                } else {
                                    $couponFirstRow = "Abbiamo riservato Per TE un Coupon del valore di " . $amount . "€ di  sconto che potrai utilizzare per completare l'ordine!";
                                }
                                $couponLastRow = "Inserisci il coupon nell'area riservata del tuo carrello.";

                                $cartAmount = number_format($cartAmount,2);
                            } else {
                                $cartTotalAmount = number_format($cartAmount,2) . "+ SPESE SPEDIZIONE";
                                $cartAmount = number_format($cartAmount,2);
                                $couponFirstRow = '';
                                $couponLastRow = '';
                                $code = '';
                            }

                            $ordercheck = \Monkey::app()->repoFactory->create('Order')->findOneBy(['cartId' => $cartId]);
                            if (empty($ordercheck)) {
                                $message = str_replace('{nome}',$userDetail,$secondEmailTemplate);
                                $message = str_replace('{emailunsuscriber}',$emailUser,$message);
                                $message = str_replace('{cartRow}',$cartRow,$message);
                                $message = str_replace('{cartAmount}',$cartAmount,$message);
                                $message = str_replace('{couponFirstRow}',$couponFirstRow,$message);
                                $message = str_replace('{code}',$code,$message);
                                $message = str_replace('{couponLastRow}',$couponLastRow,$message);
                                $message = str_replace('{cartTotalAmount}',$cartTotalAmount,$message);
                                $emailRepo = \Monkey::app()->repoFactory->create('Email');
                                $res = $emailRepo->newMail($from,[$emailUser],[],[],$subject,$message,null,null,null,'mailGun',false);
                                $cartAbandonedSendEmailIfExist->secondEmailTemplate = $message;
                                $cartAbandonedSendEmailIfExist->secondSentCheck = "1";
                                $cartAbandonedSendEmailIfExist->selectEmailCouponSend = 3;
                                $cartAbandonedSendEmailIfExist->update();
                            }
                        }
                        }catch (\Throwable $e){
                            \Monkey::app()->applicationLog('CcartAbandonedEmailSendJobs','error','second Send cannot execute',$e,'');
                        }
                        break;
                    case 3:
                        try{
                        $checkDate = new DateTime($cartAbandonedSendEmailIfExist->thirdTimeEmailSendDate);
                        $startToday = new DateTime();
                        $endToday = new DateTime('+1 day');
                        if ($checkDate->getTimestamp() > $startToday->getTimestamp() && $checkDate->getTimestamp() < $endToday->getTimestamp()) {
                            $coupon3TypeId = $cartAbandonedSendEmailIfExist->coupon3TypeId;
                            if ($coupon3TypeId != null) {
                                $couponType = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id' => $coupon3TypeId]);

                                $remoteCouponTypeId = $couponType->remoteId;
                                $amount = $couponType->amount;
                                $amountType = $couponType->amountType;
                                $validity = $couponType->validity;
                                $validForCartTotal = $couponType->validForCartTotal;

                                if ($validForCartTotal <= $cartPrice) {
                                    $couponFind = \Monkey::app()->repoFactory->create('Coupon')->findOneBy(['id' => $cartAbandonedSendEmailIfExist->couponId]);
                                    $remoteCouponId = $couponFind->remoteId;
                                    $code = $couponFind->code;
                                    $issueDate = new \DateTime();
                                    $validUntil = new \DateInterval($validity);
                                    $validThru = $issueDate->add($validUntil);
                                    $couponFind->issueDate = $issueDate->format('Y-m-d H:i:s');
                                    $couponFind->validThru = date_format($validThru,'Y-m-d H:i:s');
                                    if ($amountType == "P") {
                                        $amountCart = $cartPrice / 100 * $amount;
                                    } else {
                                        $amountCart = $amount;
                                    }
                                    $couponFind->amount = $amountCart;
                                    $couponFind->valid = "1";
                                    $stmtUpdateCoupon = $db_con->prepare("Update Coupon set  
                                    `couponTypeId`='" . $remoteCouponTypeId . "',
                                     issueDate='" . $issueDate->format('Y-m-d H:i:s') . "',
                                     validThru='" . date_format($validThru,'Y-m-d H:i:s') . "',
                                     amount='" . $amount . "',
                                     valid='1' WHERE id=" . $remoteCouponId);
                                    $stmtUpdateCoupon->execute();
                                    $couponFind->update();
                                    $idCoupon = $cartAbandonedSendEmailIfExist->couponId;
                                }
                            }

                            $from = $shop->emailShop;
                            $subject = 'Completa il tuo Ordine';
                            $cartLineFind = \Monkey::app()->repoFactory->create('CartLine')->findby(['cartId' => $cartId]);
                            $cartAmount = 0;
                            $cartRow = "";
                            foreach ($cartLineFind as $cartLine) {
                                $productId = $cartLine->productId;
                                $productVariantId = $cartLine->productVariantId;
                                $productSizeId = $cartLine->productSizeId;
                                $productPublicSkuFind = \Monkey::app()->repoFactory->create('productPublicSku')->findOneBy(['productId' => $productId,'productVariantId' => $productVariantId,'productSizeId' => $productSizeId]);
                                $isOnSaleFind = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $productId,'productVariantId' => $productVariantId]);
                                $isOnSale = $isOnSaleFind->isOnSale;
                                $productBrand = $isOnSaleFind->productBrandId;
                                if ($isOnSale == "1") {
                                    $price = $productPublicSkuFind->salePrice;
                                } else {
                                    $price = $productPublicSkuFind->price;
                                }
                                $dummyPicture = $isOnSaleFind->dummyPicture;
                                $productBrandFind = \Monkey::app()->repoFactory->create('ProductBrand')->findOneBy(['id' => $productBrand]);
                                $productBrand = $productBrandFind->name;
                                $cartAmount = $cartAmount + $price;
                                $cartRowLine = "<!--riga carrello-->
                                                                        <tr>
                                                        <td valign=\"top\" align=\"center\" class=\"lh-3\"
                                                            style=\"padding: 0px 40px; margin: 0px;\">
                                                            <hr>
                                                        </td>
                                                    </tr>
                                    <tr>
                                    <td class=\"lh-3\" style=\"padding: 0px 40px; margin: 0px;\" align=\"center\" valign=\"top\"><hr /></td>
                                    </tr>
                                    <tr>
                                    <td style=\"padding: 0 20px; margin: 0;\" align=\"center\" valign=\"top\">
                                    <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
                                    <tbody>
                                    <tr>
                                    <td style=\"padding: 0 10px; margin: 0;\" align=\"center\" valign=\"top\">
                                    <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
                                    <tbody>
                                    <tr>
                                    <td class=\"\" style=\"margin: 0px; padding: 10px 0;\" align=\"left\" valign=\"top\" width=\"11%\">
                                    <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
                                    <tbody>
                                    <tr>
                                    <td style=\"padding: 0; margin: 0;\" align=\"center\" valign=\"top\">
                                    <table border=\"0\" width=\"64\" cellspacing=\"0\" cellpadding=\"0\" align=\"left\" data-editable=\"image\" data-mobile-width=\"0\">
                                    <tbody>
                                    <tr>
                                    <td class=\"tdBlock\" style=\"display: inline-block; padding: 0px 0px 0px 40px; margin: 0px;\" align=\"left\" valign=\"top\"><img style=\"font-size: 12px; display: block; border: 0px none transparent;\" src=\"" . $dummyPicture . "\" height=\"100\" border=\"0\" /></td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    <td class=\"\" style=\"padding: 0px; margin: 0px;\" align=\"left\" valign=\"top\" width=\"50%\">
                                    <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" data-editable=\"text\">
                                    <tbody>
                                    <tr>
                                    <td class=\"lh-1\" style=\"padding: 50px 0px; margin: 0px; line-height: 1.15; font-size: 16px; font-family: Times New Roman, Times, serif;\" align=\"center\" valign=\"top\"><span style=\"font-family: Helvetica,Arial,sans-serif; font-size: 14px; font-weight: 300; color: #000000; line-height: 0.5;\">" . $productBrand . "<br> Prodotto:" . $productId . "-" . $productVariantId . "</span></td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    <td class=\"\" style=\"padding: 0px; margin: 0px;\" align=\"left\" valign=\"top\" width=\"30%\">
                                    <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" data-editable=\"text\">
                                    <tbody>
                                    <tr>
                                    <td class=\"lh-3\" style=\"padding: 50px 20px 5px 30px; margin: 0px; line-height: 1.35; font-size: 16px; font-family: Times New Roman, Times, serif;\" align=\"right\" valign=\"top\"><span style=\"font-family: Helvetica,Arial,sans-serif; font-size: 14px; font-weight: 300; color: #000000; line-height: 0.5;\"> <span style=\"font-weight: bold;\">" . $price . "</span> </span></td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </td>
                                    </tr>
                                      <tr>
                                                        <td valign=\"top\" align=\"center\" class=\"lh-3\"
                                                            style=\"padding: 0px 40px; margin: 0px;\">
                                                            <hr>
                                                        </td>
                                                    </tr>
                                    <!-- fine Riga Carrello-->";
                                $cartRow = $cartRow . $cartRowLine;
                            }
                            if ($coupon3TypeId != null) {
                                $couponTypeFind = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id' => $coupon3TypeId]);
                                $amountType = $couponTypeFind->amountType;
                                $amount = $couponTypeFind->amount;
                                $hasFreeShipping = $couponTypeFind->hasFreeShipping;
                                if ($hasFreeShipping == "1") {
                                    $cartTotalAmount = number_format($cartAmount,2) . " + SPEDIZIONE GRATUITA";
                                } else {
                                    $cartTotalAmount = number_format($cartAmount,2) . "+ SPESE SPEDIZIONE";
                                }
                                $couponFind = \Monkey::app()->repoFactory->create('Coupon')->findOneBy(['id' => $couponId]);
                                $code = $couponFind->code;
                                if ($amountType == "P") {
                                    $couponFirstRow = "Abbiamo riservato Per TE un Coupon del " . $amount . "% di sconto che potrai utilizzare per completare l'ordine!";
                                } else {
                                    $couponFirstRow = "Abbiamo riservato Per TE un Coupon del valore di " . $amount . "€ di  sconto che potrai utilizzare per completare l'ordine!";
                                }
                                $couponLastRow = "Inserisci il coupon nell'area riservata del tuo carrello.";

                                $cartAmount = number_format($cartAmount,2);
                            } else {
                                $cartTotalAmount = number_format($cartAmount,2) . "+ SPESE SPEDIZIONE";
                                $cartAmount = number_format($cartAmount,2);
                                $couponFirstRow = '';
                                $couponLastRow = '';
                                $code = '';
                            }

                            $ordercheck = \Monkey::app()->repoFactory->create('Order')->findOneBy(['cartId' => $cartId]);
                            if (empty($ordercheck)) {
                                $message = str_replace('{nome}',$userDetail,$thirdEmailTemplate);
                                $message = str_replace('{emailunsuscriber}',$emailUser,$message);
                                $message = str_replace('{cartRow}',$cartRow,$message);
                                $message = str_replace('{cartAmount}',$cartAmount,$message);
                                $message = str_replace('{couponFirstRow}',$couponFirstRow,$message);
                                $message = str_replace('{code}',$code,$message);
                                $message = str_replace('{couponLastRow}',$couponLastRow,$message);
                                $message = str_replace('{cartTotalAmount}',$cartTotalAmount,$message);
                                $emailRepo = \Monkey::app()->repoFactory->create('Email');
                                $res = $emailRepo->newMail($from,[$emailUser],[],[],$subject,$message,null,null,null,'mailGun',false);
                                $cartAbandonedSendEmailIfExist->thirdEmailTemplate = $message;
                                $cartAbandonedSendEmailIfExist->thirdSentCheck = "1";
                                $cartAbandonedSendEmailIfExist->selectEmailCouponSend = 4;
                                $cartAbandonedSendEmailIfExist->update();
                            }
                        }
                        }catch (\Throwable $e){
                            \Monkey::app()->applicationLog('CcartAbandonedEmailSendJobs','error','third Send cannot execute',$e,'');
                        }
                        break;
                }


            }

        }
    }


}
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
        /*creazione email Send*/


        $sql = "SELECT
  C.id                                                   AS id,
  C.creationDate                                         AS creationDate,
  C.userId                                               AS userId,
  C.cartTypeId                                           AS carTypeId,
  C.lastUpdate                                           AS lastUpdate
FROM Cart C
WHERE C.userId ='17212'
      AND C.cartTypeId = 1 AND C.couponId IS NULL
GROUP BY C.id";

        /** @var CCartRepo $cartRepo */
        $cartRepo = \Monkey::app()->repoFactory->create('Cart');
        /** @var $listCart CObjectCollection */
        $listCart = $cartRepo->findBySql($sql);
        foreach ($listCart as $cart) {
            $cartPrice = 0;
            $customer = $cart->userId;
            $cartId = $cart->id;
            /** @var  $listCartLine CObjectCollection */
            $listCartLine = \Monkey::app()->repoFactory->create('CartLine')->findBy(['cartId' => $cartId]);
            foreach ($listCartLine as $line) {
                $ProductId = $line->productId;
                $ProductVariantId = $line->productVariantId;
                $ProductSizeId = $line->productSizeId;
                /** @var productPublicSkuRepo $productPublicSku */
                $productPublicSku = \Monkey::app()->repoFactory->create('ProductPublicSku')->findOneBy(['productId' => $ProductId, 'productVariantId' => $ProductVariantId, 'productSizeId' => $ProductSizeId]);
                $price = $productPublicSku->price;
                $salePrice = $productPublicSku->salePrice;
                /** @var $productIsOnSale CObjectCollection */
                $productIsOnSale = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $ProductId, 'productVariantId' => $ProductVariantId]);
                $isOnSale = $productIsOnSale->isOnSale;
                if ($isOnSale == '1') {
                    $cartPrice = $cartPrice + $salePrice;

                } else {
                    $cartPrice = $cartPrice + $price;
                }
            }
            $lastUpdate = $cart->lastUpdate;
            $sqlCartAbandonedEmailParam = "SELECT id ,
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
   FROM CartAbandonedEmailParam LIMIT 1";
            /** @var CObjectCollection $cartAbandonedEmailParam */
            $cartAbandonedEmailsParam = \Monkey::app()->repoFactory->create('CartAbandonedEmailParam')->findBySql($sqlCartAbandonedEmailParam);
            foreach ($cartAbandonedEmailsParam as $cartAbandonedEmailParam) {
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
                $couponTypeId = $cartAbandonedEmailParam->couponTypeId;
                $selectMailCouponSend = $cartAbandonedEmailParam->selectMailCouponSend;
            }
            /* @var CCartAbandonedSendEmailIfExist $cartAbandonedSendEmailIfExist */
            $cartAbandonedSendEmailIfExist = \Monkey::app()->repoFactory->create('CartAbandonedEmailSend')->findOneBy(['cartId' => $cartId]);
            if (empty($cartAbandonedSendEmailIfExist)) {
                if ($couponTypeId <> '0') {
                    /** var CCouponType $couponType */
                    $couponType = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id' => $couponTypeId]);
                    $couponName = $couponType->name;
                    $amount = $couponType->amount;
                    $amountType = $couponType->amountType;
                    $validity = $couponType->validity;
                    $validForCartTotal = $couponType->validForCartTotal;
                    $hasFreeShipping = $couponType->hasFreeShipping;
                    $hasFreeReturn = $couponType->hasFreeReturn;

                    if ($validForCartTotal <= $cartPrice) {
                        /**var CCoupon $couponGenerate
                         *
                         *
                         */
                        $couponGenerate = \Monkey::app()->repoFactory->create('Coupon')->getEmptyEntity();
                        $couponGenerate->couponTypeId = $couponTypeId;

                        $serial = new CSerialNumber();
                        $serial->generate();
                        /*  @var CCoupon $couponFind */

                        $couponGenerate->code = $serial->__toString();
                        $issueDate = new \DateTime();
                        $validUntil = new \DateInterval($validity);
                        $validThru = $issueDate->add($validUntil);

                        $couponGenerate->issueDate = date('Y-m-d H:i:s');
                        $couponGenerate->validThru = date_format($validThru, 'Y-m-d H:i:s');
                        if ($amountType == "P") {
                            $amountCart = $cartPrice / 100 * $amount;
                        } else {
                            $amountCart = $amount;
                        }
                        $couponGenerate->amount = $amountCart;
                        $couponGenerate->userId = $customer;
                        $couponGenerate->valid = "1";
                        $couponGenerate->smartInsert();
                        $getcouponId = \Monkey::app()->repoFactory->create('Coupon')->findOneBy(['code' => $serial]);
                        $idCoupon = $getcouponId->id;


                        /** var CCartAbandonedEmailSend $cartAbandonedEmailSend */
                        $cartAbandonedEmailSend = \Monkey::app()->repoFactory->create('CartAbandonedEmailSend')->getEmptyEntity();
                        $cartAbandonedEmailSend->cartId = $cartId;
                        $cartAbandonedEmailSend->userId = $customer;
                        $cartAbandonedEmailSend->firstTemplateId = $firstTemplateId;
                        $cartAbandonedEmailSend->firstEmailTemplate = $firstEmailTemplate;
                        $firstEmailSendDate = date('Y-m-d H:i:s', strtotime('+' . $firstTimeEmailSendDay . ' Day  +' . $firstTimeEmailSendHour . 'Hour', strtotime($lastUpdate)));
                        $cartAbandonedEmailSend->firstTimeEmailSendDate = $firstEmailSendDate;
                        $cartAbandonedEmailSend->secondTemplateId = $secondTemplateId;
                        $cartAbandonedEmailSend->secondEmailTemplate = $secondEmailTemplate;
                        $secondEmailSendDate = date('Y-m-d H:i:s', strtotime('+' . $secondTimeEmailSendDay . ' Day  +' . $secondTimeEmailSendHour . 'Hour', strtotime($lastUpdate)));
                        $cartAbandonedEmailSend->secondTimeEmailSendDate = $secondEmailSendDate;
                        $cartAbandonedEmailSend->thirdTemplateId = $thirdTemplateId;
                        $cartAbandonedEmailSend->thirdEmailTemplate = $thirdEmailTemplate;
                        $thirdEmailSendDate = date('Y-m-d H:i:s', strtotime('+' . $thirdTimeEmailSendDay . ' Day  +' . $thirdTimeEmailSendHour . 'Hour', strtotime($lastUpdate)));
                        $cartAbandonedEmailSend->thirdTimeEmailSendDate = $thirdEmailSendDate;
                        $cartAbandonedEmailSend->couponId = $idCoupon;
                        $cartAbandonedEmailSend->couponTypeId = $couponTypeId;
                        $cartAbandonedEmailSend->selectMailCouponSend = $selectMailCouponSend;
                        $cartAbandonedEmailSend->smartInsert();
                    }


                } else {
                    $cartAbandonedEmailSend = \Monkey::app()->repoFactory->create('CartAbandonedEmailSend')->getEmptyEntity();
                    $cartAbandonedEmailSend->cartId = $cartId;
                    $cartAbandonedEmailSend->userId = $customer;
                    $cartAbandonedEmailSend->firstTemplateId = $firstTemplateId;
                    $cartAbandonedEmailSend->firstEmailTemplate = $firstEmailTemplate;
                    $firstEmailSendDate = date('Y-m-d H:i:s', strtotime('+' . $firstTimeEmailSendDay . ' Day  +' . $firstTimeEmailSendHour . 'Hour', strtotime($lastUpdate)));
                    $cartAbandonedEmailSend->firstTimeEmailSendDate = $firstEmailSendDate;
                    $cartAbandonedEmailSend->secondTemplateId = $secondTemplateId;
                    $cartAbandonedEmailSend->secondEmailTemplate = $secondEmailTemplate;
                    $secondEmailSendDate = date('Y-m-d H:i:s', strtotime('+' . $secondTimeEmailSendDay . ' Day  +' . $secondTimeEmailSendHour . 'Hour', strtotime($lastUpdate)));
                    $cartAbandonedEmailSend->secondTimeEmailSendDate = $secondEmailSendDate;
                    $cartAbandonedEmailSend->thirdTemplateId = $thirdTemplateId;
                    $cartAbandonedEmailSend->thirdEmailTemplate = $thirdEmailTemplate;
                    $thirdEmailSendDate = date('Y-m-d H:i:s', strtotime('+' . $thirdTimeEmailSendDay . ' Day  +' . $thirdTimeEmailSendHour . 'Hour', strtotime($lastUpdate)));
                    $cartAbandonedEmailSend->thirdTimeEmailSendDate = $thirdEmailSendDate;
                    $cartAbandonedEmailSend->couponId = '';
                    $cartAbandonedEmailSend->couponTypeId = '';
                    $cartAbandonedEmailSend->selectMailCouponSend = $selectMailCouponSend;
                    $cartAbandonedEmailSend->smartInsert();


                }
            }

            $sql = "SELECT * FROM CartAbandonedEmailSend WHERE DATE_FORMAT(now(),'%Y%m%d%H%i') >= DATE_FORMAT(firstTimeEmailSendDate, '%Y%m%d%H%i') AND DATE_FORMAT(now(),'%Y%m%d%H%i') >= DATE_FORMAT(secondTimeEmailSendDate
            , '%Y%m%d%H%i')  AND DATE_FORMAT(now(),'%Y%m%d%H%i') >= DATE_FORMAT(thirdTimeEmailSendDate, '%Y%m%d%H%i')";
            /** @var CCartAbandonedEmailSendRepo $cartAbandonedEmailSendRepo */
            $cartAbandonedEmailSendRepo = \Monkey::app()->repoFactory->create('CartAbandonedEmailSend');
            $cartAbandonedEmailsSend = $cartAbandonedEmailSendRepo->findBySql($sql);
            if (empty($cartAbandonedEmailsSend)) return;
            // $this->report('Starting', 'Cart Reinvite to send: ' . count($cartAbandonedEmailsSend));
            foreach ($cartAbandonedEmailsSend as $cartAbandonedEmailSend) {

                $idCartAbandonedEmailSend = $cartAbandonedEmailSend->id;
                $firstTemplateId = $cartAbandonedEmailSend->firstTemplateId;
                $firstEmailTemplate = $cartAbandonedEmailSend->firstEmailTemplate;
                $firstSentCheck = $cartAbandonedEmailSend->firstSentCheck;
                $secondTemplateId = $cartAbandonedEmailSend->secondTemplateId;
                $secondEmailTemplate = $cartAbandonedEmailSend->secondEmailTemplate;
                $secondSentCheck = $cartAbandonedEmailSend->secondSentCheck;
                $thirdTemplateId = $cartAbandonedEmailSend->thirdTemplateId;
                $thirdEmailTemplate = $cartAbandonedEmailSend->thirdEmailTemplate;
                $thirdSentCheck = $cartAbandonedEmailSend->thirdSentCheck;
                $userId = $cartAbandonedEmailSend->userId;
                $couponId = $cartAbandonedEmailSend->couponId;
                $couponTypeId = $cartAbandonedEmailSend->couponTypeId;
                $cartId = $cartAbandonedEmailSend->cartId;
                $selectMailCouponSend = $cartAbandonedEmailSend->selectMailCouponSend;
                $emailUserFind = \Monkey::app()->repoFactory->create('User')->findOneBy(['id' => $userId]);
                $emailUser = $emailUserFind->email;
                $emailUserDetails = \Monkey::app()->repoFactory->create('UserDetails')->findOneBY(['userId' => $userId]);
                $userDetail = $emailUserDetails->name . " " . $emailUserDetails->surname;
                $from = 'no-reply@pickyshop.com';
                $subject = 'Completa il tuo Ordine';
                $cartLineFind = \Monkey::app()->repoFactory->create('CartLine')->findby(['cartId' => $cartId]);
                $cartAmount =0;
                $cartRow = "";
                foreach ($cartLineFind as $cartLine) {
                    $productId = $cartLine->productId;
                    $productVariantId = $cartLine->productVariantId;
                    $productSizeId = $cartLine->productSizeId;
                    $productPublicSkuFind = \Monkey::app()->repoFactory->create('productPublicSku')->findOneBy(['productId' => $productId, 'productVariantId' => $productVariantId, 'productSizeId' => $productSizeId]);
                    $isOnSaleFind = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $productId, 'productVariantId' => $productVariantId]);
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
                    $cartAmount =$cartAmount+$price;
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
                if ($couponTypeId != "0") {
                    $couponTypeFind = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id' => $couponTypeId]);
                    $amountType = $couponTypeFind->amountType;
                    $amount = $couponTypeFind->amount;
                    $hasFreeShipping=$couponTypeFind->hasFreeShipping;
                    if($hasFreeShipping=="1"){
                        $cartTotalAmount= number_format($cartAmount,2)." + SPEDIZIONE GRATUITA";
                    }else{
                        $cartTotalAmount =number_format($cartAmount,2). "+ SPESE SPEDIZIONE";

                    }
                    $couponFind = \Monkey::app()->repoFactory->create('Coupon')->findOneBy(['id' => $couponId]);
                    $code = $couponFind->code;
                    if ($amountType == "P") {
                        $couponFirstRow = "Abbiamo riservato Per TE un Coupon del " . $amount . "% di sconto che potrai utilizzare per completare l'ordine!";
                    } else {
                        $couponFirstRow = "Abbiamo riservato Per TE un Coupon del valore di " . $amount . "€ di  sconto che potrai utilizzare per completare l'ordine!";
                    }

                    $cartRowCoupon = "<!--inizio sezione coupon-->
							<tr>
                                <td valign=\"top\" align=\"left\" class=\"lh-3\"
                                    style=\"padding: 5px 20px; margin: 0px; line-height: 1; font-size: 16px; font-family: Times New Roman, Times, serif;\">
                                    <span style=\"font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;color:#000000; line-height:0.5;\">
                                      " . $couponFirstRow . "
                                    </span>
                                </td>
                            </tr>
							<tr>
                                <td valign=\"top\" align=\"left\" class=\"lh-3\"
                                    style=\"padding: 5px 20px; margin: 0px; line-height: 1; font-size: 16px; font-family: Times New Roman, Times, serif;\">
                                    <span style=\"font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;color:#000000; line-height:0.5;\">
                                     " . $code . "
                                    </span>
                                </td>
                            </tr>
							<tr>
                                <td valign=\"top\" align=\"left\" class=\"lh-3\"
                                    style=\"padding: 5px 20px; margin: 0px; line-height: 1; font-size: 16px; font-family: Times New Roman, Times, serif;\">
                                    <span style=\"font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;color:#000000; line-height:0.5;\">
                                       Inserisci il coupon nell'area riservata del tuo carrello.
                                    </span>
                                </td>
                            </tr>
							<!--fine sezione Coupon-->";
                    $cartAmount=number_format($cartAmount,2);
                    if ($firstSentCheck == '0') {
                        try {

                            $message= str_replace('{nome}', $userDetail, $firstEmailTemplate);
                            $message= str_replace('{emailunsuscriber}', $emailUser, $message);
                            $message= str_replace('{cartRow}', $cartRow, $message);
                            $message = str_replace('{cartAmount}', $cartAmount, $message);
                            $message = str_replace('{cartTotalAmount}', $cartTotalAmount, $message);

                            if ($selectMailCouponSend == "1" || $selectMailCouponSend == "4") {
                                $message = str_replace('{cartRowCoupon}', $cartRowCoupon, $message);
                            }
                            /* @var CEmailRepo $emailRepo */
                            $emailRepo = \Monkey::app()->repoFactory->create('Email');
                            $res = $emailRepo->newMail($from, [$emailUser], [], [], $subject, $message, null, null, null, 'mailGun', false);
                            /* @var CCartAbandonedEmailSend $cartAbandonedEmailSentUpdate */
                            $cartAbandonedEmailSentUpdate = \Monkey::app()->repoFactory->create('CartAbandonedEmailSend')->findOneBy(['id' => $idCartAbandonedEmailSend]);
                            $cartAbandonedEmailSentUpdate->firstSentCheck = "1";
                            $cartAbandonedEmailSentUpdate->update();

                        } catch (\Throwable $e) {
                            $res = false;
                        }
                    } elseif ($secondSentCheck == '0') {
                        try {
                            $message=str_replace('{nome}', $userDetail, $secondEmailTemplate);
                            $message=  str_replace('{emailunsuscriber}', $emailUser, $message);
                            $message= str_replace('{cartRow}', $cartRow, $message);
                            $message = str_replace('{cartAmount}', $cartAmount, $message);
                            $message = str_replace('{cartTotalAmount}', $cartTotalAmount, $message);
                            if ($selectMailCouponSend == "2" || $selectMailCouponSend == "4") {
                                $message = str_replace('{cartRowCoupon}', $cartRowCoupon, $message);
                            }

                            /* @var CEmailRepo $emailRepo */
                            $emailRepo = \Monkey::app()->repoFactory->create('Email');
                            $res = $emailRepo->newMail($from, [$emailUser], [], [], $subject, $message, null, null, null, 'mailGun', false);

                            /* @var CCartAbandonedEmailSend $cartAbandonedEmailSentUpdate */
                            $cartAbandonedEmailSentUpdate = \Monkey::app()->repoFactory->create('CartAbandonedEmailSend')->findOneBy(['id' => $idCartAbandonedEmailSend]);
                            $cartAbandonedEmailSentUpdate->secondSentCheck = "1";
                            $cartAbandonedEmailSentUpdate->update();


                        } catch (\Throwable $e) {
                            $res = false;
                        }

                    } elseif ($thirdSentCheck == '0') {
                        try {
                            $message = str_replace('{nome}', $userDetail, $thirdEmailTemplate);
                            $message = str_replace('{emailunsuscriber}', $emailUser, $message);
                            $message = str_replace('{cartRow}', $cartRow, $message);
                            $message = str_replace('{cartAmount}', $cartAmount, $message);
                            $message = str_replace('{cartTotalAmount}', $cartTotalAmount, $message);
                            if ($selectMailCouponSend == "3" || $selectMailCouponSend == "4") {
                                $message = str_replace('{cartRowCoupon}', $cartRowCoupon, $message);
                            }

                            /* @var CEmailRepo $emailRepo */
                            $emailRepo = \Monkey::app()->repoFactory->create('Email');
                            $res = $emailRepo->newMail($from, [$emailUser], [], [], $subject, $message, null, null, null, 'mailGun', false);

                            /* @var CCartAbandonedEmailSend $cartAbandonedEmailSentUpdate */
                            $cartAbandonedEmailSentUpdate = \Monkey::app()->repoFactory->create('CartAbandonedEmailSend')->findOneBy(['id' => $idCartAbandonedEmailSend]);
                            $cartAbandonedEmailSentUpdate->thirdSentCheck = "1";
                            $cartAbandonedEmailSentUpdate->update();


                        } catch (\Throwable $e) {
                            $res = false;
                        }
                    } else {
                        $res = "Tutte le email per i carrelli abbandonati sono stati inviati per questo utente";
                    }
                } else {

                    $cartTotalAmount =number_format($cartAmount,2). "+ SPESE SPEDIZIONE";
                    if ($firstSentCheck == '0') {
                        try {
                            $message = str_replace('{nome}', $userDetail, $firstEmailTemplate);
                            $message = str_replace('{emailunsuscriber}', $emailUser, $message);
                            $message = str_replace('{cartRow}', $cartRow, $message);
                            $message = str_replace('{cartAmount}', $cartAmount, $message);
                            $message = str_replace('{cartTotalAmount}', $cartTotalAmount, $message);

                            /* @var CEmailRepo $emailRepo */
                            $emailRepo = \Monkey::app()->repoFactory->create('Email');
                            $res = $emailRepo->newMail($from, [$emailUser], [], [], $subject, $message, null, null, null, 'mailGun', false);
                            /* @var CCartAbandonedEmailSend $cartAbandonedEmailSentUpdate */
                            $cartAbandonedEmailSentUpdate = \Monkey::app()->repoFactory->create('CartAbandonedEmailSend')->findOneBy(['id' => $idCartAbandonedEmailSend]);
                            $cartAbandonedEmailSentUpdate->firstSentCheck = "1";
                            $cartAbandonedEmailSentUpdate->update();


                        } catch (\Throwable $e) {
                            $res = false;
                        }
                    } elseif ($secondSentCheck == '0') {
                        try {
                            $message = str_replace('{nome}', $userDetail, $secondEmailTemplate);
                            $message = str_replace('{emailunsuscriber}', $emailUser, $message);
                            $message = str_replace('{cartRow}', $cartRow, $message);
                            $message = str_replace('{cartAmount}', $cartAmount, $message);
                            $message = str_replace('{cartTotalAmount}', $cartTotalAmount, $message);

                            /* @var CEmailRepo $emailRepo */
                            $emailRepo = \Monkey::app()->repoFactory->create('Email');
                            $res = $emailRepo->newMail($from, [$emailUser], [], [], $subject, $message, null, null, null, 'mailGun', false);

                            /* @var CCartAbandonedEmailSend $cartAbandonedEmailSentUpdate */
                            $cartAbandonedEmailSentUpdate = \Monkey::app()->repoFactory->create('CartAbandonedEmailSend')->findOneBy(['id' => $idCartAbandonedEmailSend]);
                            $cartAbandonedEmailSentUpdate->secondSentCheck = "1";
                            $cartAbandonedEmailSentUpdate->update();


                        } catch (\Throwable $e) {
                            $res = false;
                        }

                    } elseif ($thirdSentCheck == '0') {
                        try {
                            $message = str_replace('{Name}', $userDetail, $thirdEmailTemplate);
                            $message = str_replace('{emailunsuscriber}', $emailUser, $message);
                            $message = str_replace('{cartRow}', $cartRow, $message);
                            $message = str_replace('{cartAmount}', $cartAmount, $message);

                            /* @var CEmailRepo $emailRepo */
                            $emailRepo = \Monkey::app()->repoFactory->create('Email');
                            $res = $emailRepo->newMail($from, [$emailUser], [], [], $subject, $message, null, null, null, 'mailGun', false);

                            /* @var CCartAbandonedEmailSend $cartAbandonedEmailSentUpdate */
                            $cartAbandonedEmailSentUpdate = \Monkey::app()->repoFactory->create('CartAbandonedEmailSend')->findOneBy(['id' => $idCartAbandonedEmailSend]);
                            $cartAbandonedEmailSentUpdate->thirdSentCheck = "1";
                            $cartAbandonedEmailSentUpdate->update();


                        } catch (\Throwable $e) {
                            $res = false;
                        }
                    } else {
                        $res = "Tutte le email per i carrelli abbandonati sono stati inviati per questo utente";
                    }

                }
            }





        }
    }
}

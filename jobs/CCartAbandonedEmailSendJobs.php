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
            foreach($cartAbandonedEmailsParam as $cartAbandonedEmailParam) {
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

            $sql = "SELECT * FROM CartAbandonedEmailSend WHERE DATE_FORMAT(now(),'%Y%m%d%H%i') = DATE_FORMAT(firstTimeEmailSendDate, '%Y%m%d%H%i') AND DATE_FORMAT(now(),'%Y%m%d%H%i') = DATE_FORMAT(secondTimeEmailSendDate
            , '%Y%m%d%H%i')  AND DATE_FORMAT(now(),'%Y%m%d%H%i') = DATE_FORMAT(thirdTimeEmailSendDate, '%Y%m%d%H%i')";
            /** @var CCartAbandonedEmailSendRepo $cartAbandonedEmailSendRepo */
            $cartAbandonedEmailSendRepo = \Monkey::app()->repoFactory->create('CartAbandonedEmailSend');
            $cartAbandonedEmailsSend = $cartAbandonedEmailSendRepo->findBySql($sql);
            if (empty($cartAbandonedEmailsSend)) return;
            $this->report('Starting', 'Cart Reinvite to send: ' . count($cartAbandonedEmailsSend));
            foreach ($cartAbandonedEmailsSend as $cartAbandonedEmailSend) {

                $asd = $cartAbandonedEmailSendRepo->cartAbandonedEmailSend($cartAbandonedEmailSend, ENV !== 'dev', true);
                $this->report('Esito Invio: ' . $cartAbandonedEmailSend->id, $asd);

            }
            $this->report('Ending', 'inviati tutti gli inviti al Completamento dei Carrelli abbandonati');
        }


    }
}
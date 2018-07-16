<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CCart;
use bamboo\domain\entities\CCoupon;
use bamboo\domain\entities\CCouponType;
use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CCartLine;
use bamboo\domain\entities\CCartAbandonedEmailParam;
use bamboo\domain\entities\CUser;
use bamboo\core\base\CSerialNumber;

/**
 * Class CNewsletterTemplateManage
 * @package bamboo\controllers\back\ajax
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
        $generateCoupon = $data['generateCoupon'];
        if ($generateCoupon == '1') {
            $firstTemplateId = $data['firstTemplateId'];
            $firstTemplateRepo = \Monkey::app()->repoFactory->create('NewsletterTemplate')->findOneBy(['id' => $firstTemplateId]);
            $firstTemplate = $firstTemplateRepo->template;
            $secondTemplateId = $data['secondTemplateId'];
            $secondTemplateRepo = \Monkey::app()->repoFactory->create('NewsletterTemplate')->findOneBy(['id' => $secondTemplateId]);
            $secondTemplate = $secondTemplateRepo->template;
            $thirdTemplateId = $data['thirdTemplateId'];
            $thirdTemplateRepo = \Monkey::app()->repoFactory->create('NewsletterTemplate')->findOneBy(['id' => $thirdTemplateId]);
            $thirdTemplate = $thirdTemplateRepo->template;
            $firstTimeEmailSendDay = $data['firstTimeEmailSendDay'];
            $secondTimeEmailSendDay = $data['secondTimeEmailSendDay'];
            $thirdTimeEmailSendDay = $data['thirdTimeEmailSendDay'];
            $firstTimeEmailSendHour = $data['firstTimeEmailSendHour'];
            $secondTimeEmailSendHour = $data['secondTimeEmailSendHour'];
            $thirdTimeEmailSendHour = $data['thirdTimeEmailSendHour'];
            $selectEmail =$data['selectEmail'];
            $typeCoupon = $data['typeCoupon'];
            $amount = $data['amount'];
            $validity = $data['validity'];
            $validForCartTotal = $data['validForCartTotal'];
            $hasFreeShipping = $data['hasFreeShipping'];
            $hasFreeReturn = $data['hasFreeReturn'];
            $name = "CA-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;
            /** @var CRepo $couponTypeRepo */
            $couponTypeRepo = \Monkey::app()->repoFactory->create('CouponType')->getEmptyEntity();
            $couponTypeRepo->name = $name;
            $couponTypeRepo->amount = $amount;
            $couponTypeRepo->amountType = $typeCoupon;
            $couponTypeRepo->validity = $validity;
            $couponTypeRepo->validForCartTotal = $validForCartTotal;
            $couponTypeRepo->hasFreeShipping = $hasFreeShipping;
            $couponTypeRepo->hasFreeReturn = $hasFreeReturn;
            $couponTypeRepo->smartInsert();

             /** @var CRepo $couponTypeIdRepo */
            $couponTypeIdRepo = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['name' => $name]);
            $couponTypeId = $couponTypeIdRepo->id;
            /** var CRepo $cartAbandonedSendEmailParam */
            $cartAbandonedSendEmailParam = \Monkey::app()->repoFactory->create('CartAbandonedEmailParam')->getEmptyEntity();
            $cartAbandonedSendEmailParam->firstTemplateId = $firstTemplateId;
            $cartAbandonedSendEmailParam->firstEmailTemplate = $firstTemplate;
            $cartAbandonedSendEmailParam->name =$name;
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
            $cartAbandonedSendEmailParam->couponTypeId = $couponTypeId;
            $cartAbandonedSendEmailParam->selectMailCouponSend =$selectEmail;
            $cartAbandonedSendEmailParam->smartInsert();



$res = "Inserimento Pianificazione  con Regola Generazione Coupon Eseguito";
            return $res;


        } else {
            $firstTemplateId = $data['firstTemplateId'];
            $firstTemplateRepo = \Monkey::app()->repoFactory->create('NewsletterTemplate')->findOneBy(['id' => $firstTemplateId]);
            $firstTemplate = $firstTemplateRepo->id;
            $secondTemplateId = $data['secondTemplateId'];
            $secondTemplateRepo = \Monkey::app()->repoFactory->create('NewsletterTemplate')->findOneBy(['id' => $secondTemplateId]);
            $secondTemplate = $secondTemplateRepo->id;
            $thirdTemplateId = $data['thirdTemplateId'];
            $thirdTemplateRepo = \Monkey::app()->repoFactory->create('NewsletterTemplate')->findOneBy(['id' => $thirdTemplateId]);
            $thirdTemplate = $thirdTemplateRepo->id;
            $firstTimeEmailSendDay = $data['firstTimeEmailSendDay'];
            $secondTimeEmailSendDay = $data['secondTimeEmailSendDay'];
            $thirdTimeEmailSendDay = $data['thirdTimeEmailSendDay'];
            $firstTimeEmailSendHour = $data['firstTimeEmailSendHour'];
            $secondTimeEmailSendHour = $data['secondTimeEmailSendHour'];
            $thirdTimeEmailSendHour = $data['thirdTimeEmailSendHour'];
            $name = "CA-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;

            /** var CRepo $cartAbandonedSendEmailParam */
            $cartAbandonedSendEmailParam = \Monkey::app()->repoFactory->create('CartAbandonedEmailParam')->getEmptyEntity();
            $cartAbandonedSendEmailParam->firstTemplateId = $firstTemplateId;
            $cartAbandonedSendEmailParam->firstEmailTemplate = $firstTemplate;
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
            $cartAbandonedSendEmailParam->couponTypeId = '';
            $cartAbandonedSendEmailParam->selectmailCouponSend = '';
            $cartAbandonedSendEmailParam->name=$name;
            $cartAbandonedSendEmailParam->smartInsert();

            }
            $res = "Inserimento Pianificazione  senza  Generazione Coupon Eseguito";

            return $res;

        }





}
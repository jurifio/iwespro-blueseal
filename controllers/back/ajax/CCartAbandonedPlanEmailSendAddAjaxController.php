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
        $generateCoupon2 = $data['generateCoupon2'];
        $generateCoupon3 = $data['generateCoupon3'];
        $res="";
        //primo invio
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
            $selectEmail ="1";
            $typeCoupon = $data['typeCoupon'];
            $amount = $data['amount'];
            $validity = $data['validity'];
            $validForCartTotal = $data['validForCartTotal'];
            $hasFreeShipping = $data['hasFreeShipping'];
            $hasFreeReturn = $data['hasFreeReturn'];
            $name = "CA-SendMail-first-time-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;
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



            $res =$res."<br>Inserimento Pianificazione Primo Invio con Regola Generazione Coupon Eseguito";



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
            $name = "CA-SendMail-first-time-". "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;

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
            $res =$res."<br>Inserimento Pianificazione Primo Invio  senza  Generazione Coupon Eseguito";
            }

            //secondo invio
        if ($generateCoupon2 == '1') {
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
            $selectEmail ="2";
            $typeCoupon = $data['typeCoupon2'];
            $amount = $data['amount2'];
            $validity = $data['validity2'];
            $validForCartTotal = $data['validForCartTotal2'];
            $hasFreeShipping = $data['hasFreeShipping2'];
            $hasFreeReturn = $data['hasFreeReturn2'];
            $name = "CA-SendMail-second-time-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;
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



            $res =$res."<br>Inserimento Pianificazione Secondo Invio con Regola Generazione Coupon Eseguito";



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
            $name = "CA--SendMail-second-time-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;

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
            $res =$res."Inserimento Pianificazione Secondo Invio  senza  Generazione Coupon Eseguito";

        }

        //terzo invio
        if ($generateCoupon3 == '1') {
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
            $selectEmail ="3";
            $typeCoupon = $data['typeCoupon3'];
            $amount = $data['amount3'];
            $validity = $data['validity3'];
            $validForCartTotal = $data['validForCartTotal3'];
            $hasFreeShipping = $data['hasFreeShipping3'];
            $hasFreeReturn = $data['hasFreeReturn3'];
            $name = "CA-SendMail-third-time-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;
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



            $res =$res."<br>Inserimento Pianificazione Terzo Invio con Regola Generazione Coupon Eseguito";



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
            $name = "CA-SendMail-third-time-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;

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
            $res =$res."<br>Inserimento Pianificazione Terzo Invio  senza  Generazione Coupon Eseguito";

        }



            return $res;

        }
    public function put()
    {

        //prendo i dati passati in input
        $data = \Monkey::app()->router->request()->getRequestData();
        $generateCoupon = $data['generateCoupon'];
        $generateCoupon2 = $data['generateCoupon2'];
        $generateCoupon3 = $data['generateCoupon3'];
        $res="";
        //primo invio
        if ($generateCoupon == '1') {
            $id1=$data['cartIdEmailParam1'];
            $couponTypeId1=$data['couponTypeId1'];
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
            $selectEmail ="1";
            $typeCoupon = $data['typeCoupon'];
            $amount = $data['amount'];
            $validity = $data['validity'];
            $validForCartTotal = $data['validForCartTotal'];
            $hasFreeShipping = $data['hasFreeShipping'];
            $hasFreeReturn = $data['hasFreeReturn'];
            $name = "CA-SendMail-first-time-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;
            /** @var CRepo $couponTypeRepo */
            $couponTypeRepo = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id'=>$couponTypeId1]);
            $couponTypeRepo->name = $name;
            $couponTypeRepo->amount = $amount;
            $couponTypeRepo->amountType = $typeCoupon;
            $couponTypeRepo->validity = $validity;
            $couponTypeRepo->validForCartTotal = $validForCartTotal;
            $couponTypeRepo->hasFreeShipping = $hasFreeShipping;
            $couponTypeRepo->hasFreeReturn = $hasFreeReturn;
            $couponTypeRepo->update();


            /** var CRepo $cartAbandonedSendEmailParam */
            $cartAbandonedSendEmailParam = \Monkey::app()->repoFactory->create('CartAbandonedEmailParam')->findOneBy(['id'=>$id1]);
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
            $cartAbandonedSendEmailParam->couponTypeId = $couponTypeId1;
            $cartAbandonedSendEmailParam->selectMailCouponSend =$selectEmail;
            $cartAbandonedSendEmailParam->update();



            $res =$res."<br>Aggiornamento Pianificazione Primo Invio con Regola Generazione Coupon Eseguito";



        } else {
            $id1=$data['cartIdEmailParam1'];
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
            $name = "CA-SendMail-first-time-". "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;

            /** var CRepo $cartAbandonedSendEmailParam */
            $cartAbandonedSendEmailParam = \Monkey::app()->repoFactory->create('CartAbandonedEmailParam')->findOneBy(['id'=>$id1]);
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
            $cartAbandonedSendEmailParam->update();
            $res =$res."<br>Aggiornamento Pianificazione secondo Invio  senza  Generazione Coupon Eseguito";
        }

        //secondo invio
        if ($generateCoupon2 == '1') {
            $id2=$data['cartIdEmailParam2'];
            $couponTypeId2=$data['couponTypeId2'];
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
            $selectEmail ="2";
            $typeCoupon = $data['typeCoupon2'];
            $amount = $data['amount2'];
            $validity = $data['validity2'];
            $validForCartTotal = $data['validForCartTotal2'];
            $hasFreeShipping = $data['hasFreeShipping2'];
            $hasFreeReturn = $data['hasFreeReturn2'];
            $name = "CA-SendMail-second-time-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;
            /** @var CRepo $couponTypeRepo */
            $couponTypeRepo = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id'=>$couponTypeId2]);
            $couponTypeRepo->name = $name;
            $couponTypeRepo->amount = $amount;
            $couponTypeRepo->amountType = $typeCoupon;
            $couponTypeRepo->validity = $validity;
            $couponTypeRepo->validForCartTotal = $validForCartTotal;
            $couponTypeRepo->hasFreeShipping = $hasFreeShipping;
            $couponTypeRepo->hasFreeReturn = $hasFreeReturn;
            $couponTypeRepo->update();



            /** var CRepo $cartAbandonedSendEmailParam */
            $cartAbandonedSendEmailParam = \Monkey::app()->repoFactory->create('CartAbandonedEmailParam')->findOneBy(['id'=>$id2]);
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
            $cartAbandonedSendEmailParam->couponTypeId = $couponTypeId2;
            $cartAbandonedSendEmailParam->selectMailCouponSend =$selectEmail;
            $cartAbandonedSendEmailParam->update();



            $res =$res."<br>Aggiornamento Pianificazione Secondo Invio con Regola Generazione Coupon Eseguito";



        } else {
            $id2=$data['cartIdEmailParam2'];
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
            $name = "CA--SendMail-second-time-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;

            /** var CRepo $cartAbandonedSendEmailParam */
            $cartAbandonedSendEmailParam = \Monkey::app()->repoFactory->create('CartAbandonedEmailParam')->findOneBy(['id'=>$id2]);
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
            $cartAbandonedSendEmailParam->update();
            $res =$res."<br>Aggiornamento Pianificazione Secondo Invio   senza  Generazione Coupon Eseguito";

        }

        //terzo invio
        if ($generateCoupon3 == '1') {
            $id3=$data['cartIdEmailParam3'];
            $couponTypeId3=$data['couponTypeId3'];
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
            $selectEmail ="3";
            $typeCoupon = $data['typeCoupon3'];
            $amount = $data['amount3'];
            $validity = $data['validity3'];
            $validForCartTotal = $data['validForCartTotal3'];
            $hasFreeShipping = $data['hasFreeShipping3'];
            $hasFreeReturn = $data['hasFreeReturn3'];
            $name = "CA-SendMail-third-time-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;
            /** @var CRepo $couponTypeRepo */
            $couponTypeRepo = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id'=>$couponTypeId3]);
            $couponTypeRepo->name = $name;
            $couponTypeRepo->amount = $amount;
            $couponTypeRepo->amountType = $typeCoupon;
            $couponTypeRepo->validity = $validity;
            $couponTypeRepo->validForCartTotal = $validForCartTotal;
            $couponTypeRepo->hasFreeShipping = $hasFreeShipping;
            $couponTypeRepo->hasFreeReturn = $hasFreeReturn;
            $couponTypeRepo->update();

            /** var CRepo $cartAbandonedSendEmailParam */
            $cartAbandonedSendEmailParam = \Monkey::app()->repoFactory->create('CartAbandonedEmailParam')->findOneBy(['id'=>$id3]);
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
            $cartAbandonedSendEmailParam->couponTypeId = $couponTypeId3;
            $cartAbandonedSendEmailParam->selectMailCouponSend =$selectEmail;
            $cartAbandonedSendEmailParam->update();



            $res =$res."<br>Aggiornamento Pianificazione Terzo Invio con Regola Generazione Coupon Eseguito";



        } else {
            $id3=$data['cartIdEmailParam3'];
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
            $name = "CA-SendMail-third-time-" . "1T-" . $firstTemplateId . "-" . $firstTimeEmailSendDay . "-" . $firstTimeEmailSendHour . "-2T" . $secondTemplateId . "-" . $secondTimeEmailSendDay . "-" . $secondTimeEmailSendHour . "-3T" . $thirdTemplateId . "-" . $thirdTimeEmailSendDay . "-" . $thirdTimeEmailSendHour;

            /** var CRepo $cartAbandonedSendEmailParam */
            $cartAbandonedSendEmailParam = \Monkey::app()->repoFactory->create('CartAbandonedEmailParam')->findOneBy(['id'=>$id3]);
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
            $cartAbandonedSendEmailParam->Update();
            $res =$res."<br>Aggiornamento Pianificazione  Terzo Invio senza  Generazione Coupon Eseguito";

        }



        return $res;

    }





}
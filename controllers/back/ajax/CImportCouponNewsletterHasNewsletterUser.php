<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CCouponHasNewsletterUser;
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\repositories\CWorkCategoryRepo;
use bamboo\domain\repositories\CWorkCategoryStepsRepo;
use PDO;
use PDOException;

/**
 * Class CImportCouponNewsletterHasNewsletterUser
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 20/12/2019
 * @since 1.0
 */
class CImportCouponNewsletterHasNewsletterUser extends AAjaxController
{

    public function get()
    {
        $res = "";
        $shopRepo = \Monkey::app()->repoFactory->create('Shop')->findBy(['hasEcommerce' => 1]);
        $couponHasNewsLetterUserRepo = \Monkey::app()->repoFactory->create('CouponHasNewsletterUser');
        $newsletterUserRepo = \Monkey::app()->repoFactory->create('NewsletterUser');
        $couponRepo = \Monkey::app()->repoFactory->create('Coupon');


        foreach ($shopRepo as $value) {
            /********marketplace********/
            $db_host = $value->dbHost;
            $db_name = $value->dbName;
            $db_user = $value->dbUsername;
            $db_pass = $value->dbPassword;
            $shop = $value->id;
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $res .= " connessione ok <br>";
            } catch (PDOException $e) {
                $res .= $e->getMessage();
            }
            try {
                $stmtCouponHasNewsletterUser = $db_con->prepare('SELECT `chnu`.`id` as remoteId,
                                                                           `c`.`code` AS `code`,
                                                                            `nu`.`email` AS email,
                                                                            `c`.`id` as remoteCouponId,
                                                                            `nu`.`id` as remoteNewsletterUserId,
                                                                            `nu`.`isActive` as isActive,
                                                                            `nu`.`subscriptionDate` as `subscriptionDate`,
                                                                            `nu`.`unsubscriptionDate` as `unsubscriptionDate`,
                                                                            `nu`.`langId` as `langId`,
                                                                            `nu`.`genderNewsletterUser` as genderNewsletteruser,
                                                                            `nu`.`nameNewsletter` as nameNewsletter,
                                                                            `nu`.`surnameNewsletter` as surnameNewsletter
                                                                          
       
            FROM  CouponHasNewsletterUser chnu JOIN Coupon c ON chnu.couponId=c.id JOIN NewsletterUser nu ON chnu.newsletterUserId=nu.id where chnu.isImport is null');
                $stmtCouponHasNewsletterUser->execute();
                while ($rowCouponHasNewsletterUser = $stmtCouponHasNewsletterUser->fetch(PDO::FETCH_ASSOC)) {

                    $newsletterUserIdFind = $newsletterUserRepo->findOneBy(['email' => $rowCouponHasNewsletterUser['email']]);
                    $couponHasNewsletterUser = $couponHasNewsLetterUserRepo->getEmptyEntity();
                    if ($newsletterUserIdFind != null) {
                        $newsletterUserId = $newsletterUserIdFind->id;
                        $couponHasNewsletterUser->newsletterUserId = $newsletterUserId;
                    }else{
                        $newsletterUserInsert=$newsletterUserRepo->getEmptyEntity();
                        $newsletterUserInsert->email=$rowCouponHasNewsletterUser['email'];
                        $newletterUserInsert->isActive=$rowCouponHasNewsletterUser['isActive'];
                        $newsletterUserInsert->subscriptionDate=$rowCouponHasNewsletterUser['subscriptionDate'];
                        $newsletterUserInsert->unsubscriptionDate=$rowCouponHasNewsletterUser['unsubscriptionDate'];
                        $newsletterUserInsert->langId=$rowCouponHasNewsletterUser['langId'];
                        $user=\Monkey::app()->repoFactory->create('User')->findOneBy(['email'=>$rowCouponHasNewsletterUser['email']]);
                        if($user!=null){
                            $newsletterUserInsert->userId=$user->id;
                        }
                        $newsletterUserInsert->genderNewsletterUser=$rowCouponHasNewsletterUser['genderNewsletterUser'];
                        $newsletterUserInsert->nameNewsletter=$rowCouponHasNewsletterUser['nameNewsletter'];
                        $newsletterUserInsert->surnameNewsletter=$rowCouponHasNewsletterUser['surnameNewsletter'];
                        $newsletterUserInsert->remoteId=$rowCouponHasNewsletterUser['remoteNewsletterUserId'];
                        $newsletterUserInsert->remoteShopid=$shop;
                        $newsletterUserInsert->insert();
                        $newsletterUserIdFind=$newsletterUserRepo->findOneBy(['email'=>$rowCouponHasNewsletterUser['email']]);
                        $couponHasNewsletterUser->newsletterUserId=$newsletterUserIdFind->id;
                    }
                    $couponIdFind = $couponRepo->findOneBy(['code' => $rowCouponHasNewsletterUser['code'],'remoteShopId' => $shop]);
                    if ($couponIdFind != null) {
                        $couponId = $couponIdFind->id;
                    }
                    if ($couponId != null && $newsletterUserId != null) {

                        $couponHasNewsletterUser->couponId = $couponId;

                        $couponHasNewsletterUser->remoteId = $rowCouponHasNewsletterUser['remoteId'];
                        $couponHasNewsletterUser->remoteCouponId = $rowCouponHasNewsletterUser['remoteCouponId'];
                        $couponHasNewsletterUser->remoteNewsletterUserId = $rowCouponHasNewsletterUser['remoteNewsletterUserId'];
                        $couponHasNewsletterUser->remoteShopId = $shop;
                        $couponHasNewsletterUser->insert();
                    }
                }
                $stmtCouponHasNewsletterUserUpdate = $db_con->prepare('UPDATE CouponHasNewsletterUser SET isImport=1');
                $stmtCouponHasNewsletterUserUpdate->execute();

            } catch
            (\throwable $e) {
                \Monkey::app()->ApplicationLog('CImportCouponNewsLetterHasNewsletterUser','error','','Errore import shop  ' . $shop,$e);
                return 'errore in allineamento da shop ' . $shop;
            }


            return 'allineamento eseguito';

        }

    }
}
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
 * Class CImportCouponNewsletterHasNewsletterUserJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/12/2019
 * @since 1.0
 */
class CImportCouponNewsletterHasNewsletterUserJob extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
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
                                                                            `nu`.`id` as remoteNewsletterUserId
       
            FROM  CouponHasNewsletterUser chnu JOIN Coupon c ON chnu.couponId=c.id JOIN NewsletterUser nu ON chnu.newsletterUserId=nu.id where chnu.isImport is null');
                $stmtCouponHasNewsletterUser->execute();
                while ($rowCouponHasNewsletterUser = $stmtCouponHasNewsletterUser->fetch(PDO::FETCH_ASSOC)) {
                    $couponHasNewsletterUser = $couponHasNewsLetterUserRepo->getEmptyEntity();
                    $newsletterUserIdFind = $newsletterUserRepo->findOneBy(['email' => $rowCouponHasNewsletterUser['email']]);
                    if ($newsletterUserIdFind != null) {
                        $newsletterUserId = $newsletterUserIdFind->id;
                        $couponHasNewsletterUser->newsletterUserId = $newsletterUserId;
                    }
                    $couponIdFind = $couponRepo->findOneBy(['code' => $rowCouponHasNewsletterUser,'remoteShopId' => $shop]);
                    if ($couponIdFind != null) {
                        $couponId = $couponIdFind->id;
                    }
                    if ($couponIdFind != null && $newsletterUserIdFind != null) {

                        $couponHasNewsletterUser->couponId = $couponId;

                        $couponHasNewsletterUser->remoteId = $rowCouponHasNewsletterUser['remoteId'];
                        $couponHasNewsletterUser->remoteCouponId = $rowCouponHasNewsletterUser['remoteCouponId'];
                        $couponHasNewsletterUser->remoteNewsletterUserId = $rowCouponHasNewsletterUser['remoteNewsletterUserId'];
                        $couponHasNewsletterUser->remoteShopId = $rowCouponHasNewsletterUser['remoteShopId'];
                        $couponHasNewsletterUser->insert();
                    }
                }
                $stmtCouponHasNewsletterUserUpdate = $db_con->prepare('UPDATE CouponHasNewsletterUser SET isImport=1');
                $stmtCouponHasNewsletterUserUpdate->execute();

            } catch
            (\throwable $e) {
                $this -> report('CImportCouponNewsLetterHasNewsletterUser','Errore import shop  ' . $shop,$e);
            }


        }

    }
}



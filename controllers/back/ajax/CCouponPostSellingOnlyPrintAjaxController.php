<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CSerialNumber;
use bamboo\domain\entities\CUserAddress;
use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\utils\time\STimeToolbox;
use DateTime;

/**
 * Class CBillInvoiceOnlyPrintAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 24/02/2020
 * @since 1.0
 */
class CCouponPostSellingOnlyPrintAjaxController extends AAjaxController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "invoice_print";

    public function get()
    {
        $this->page = new CBlueSealPage($this->pageSlug, $this->app);

        $orderId = $this->app->router->request()->getRequestData('orderId');
        $order=\Monkey::app()->repoFactory->create('Order')->findOneBy(['id'=>$orderId]);
        $couponGenerateId=$order=$couponGenerateId;
        $remoteShopSellerId=$order->remoteShopSellerId;
        $remoteOrderSellerId=$order->remoteOrderSellerId;
        $shopFindSeller = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $remoteShopSellerId]);
        $db_hostSeller = $shopFindSeller->dbHost;
        $db_nameSeller = $shopFindSeller->dbName;
        $db_userSeller = $shopFindSeller->dbUsername;
        $db_passSeller = $shopFindSeller->dbPassword;

        $logo = $shopFindSeller->logo;
        $nameShop = $shopFindSeller->name;
        $intestation = $shopFindSeller->intestation;
        $intestation2 = $shopFindSeller->intestation2;
        $address = $shopFindSeller->address;
        $address2 = $shopFindSeller->address2;
        $iva = $shopFindSeller->iva;
        $tel = $shopFindSeller->tel;
        $email = $shopFindSeller->email;
        $fee = $shopFindSeller->paralellFee;
        $emailSeller = $shopFindSeller->emailShop;
        /***sezionali******/
        $invoiceParalUe = $shopFindSeller->invoiceParalUe;
        $invoiceParalExtraUe = $shopFindSeller->invoiceParalExtraUe;
        $receipt = $shopFindSeller->receipt;
        $logothankYou = $shopFindSeller->logoSite;
        $shopSite = $shopFindSeller->urlSite;
        $logoSite = $shopFindSeller->logoSite;
        $siteInvoiceChar = $shopFindSeller->siteInvoiceChar;
        $hasCoupon = $shopFindSeller->hasCoupon;


        try {

            $db_con1 = new PDO("mysql:host={$db_hostSeller};dbname={$db_nameSeller}",$db_userSeller,$db_passSeller);
            $db_con1->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $res = ' connessione ok <br>';
        } catch (PDOException $e) {
            $res = $e->getMessage();
        }
        //instestazione cliente

                $stmtFindRemoteUserId = $db_con1->prepare('select o.userId as userId, u.email as email, `us`.`name` as userName  from `Order` o join `User` u on o.userId=u.id 
                            join UserDetails us on o.userId=us.userId where id=' . $remoteOrderSellerId);
                $stmtFindRemoteUserId->execute();
                $rowFindRemoteUserId = $stmtFindRemoteUserId->fetch(PDO::FETCH_ASSOC);
                $remoteUserId = $rowFindRemoteUserId['userId'];
                $remoteEmailId = $rowFindRemoteUserId['email'];
                $userName = $rowFindRemoteUserId['userName'];

                $stmtFindCouponType = $db_con1->prepare('SELECT id, amountType, amount, validity, validForCartTotal,hasFreeShipping,hasFreeReturn 
                            from CouponType where `name` like \'%PostSelling%%\'');
                $stmtFindCouponType->execute();
                $rowFindCouponType = $stmtFindCouponType->fetch(PDO::FETCH_ASSOC);
                $amountTypeRemote = $rowFindCouponType['amountType'];
                $amountCouponRemote = $rowFindCouponType['amount'];
                $couponTypeIdRemote = $rowFindCouponType = ['id'];
                $validityRemote = $rowFindCouponType = ['validity'];

                $today = new \DateTime();







        $stmtFindCouponGenerate = $db_con1->prepare('select id, `code` ,validThru, amount  from Coupon where `id`='.$couponGenerateId);
        $stmtFindCouponGenerate->execute();
        $rowFindCouponGenerate = $stmtFindCouponGenerate->fetch(PDO::FETCH_ASSOC);
        $couponValidThruEmail = (new \DateTime($rowFindCouponGenerate['validThru']))->format('d-m-Y');
        $couponCode=$rowFindCouponGenerate['code'];
        $amount=$rowFindCouponGenerate['amount'];


        $couponText='<html lang="IT">
<head>
    <meta charset="UTF-8"/>
    <style="* { font-family: "Helvetica Neue", Helvetica, "Droid Sans", Arial, sans-serif;font-size:12px; }
a:link { color:#909090; text-decoration: none; }
a:hover { color:#cbac59; text-decoration: none; }
a:visited { color:#909090; text-decoration: none; }
a:active { color:#cbac59; text-decoration: none; }"></style>
</head>
<body style="margin: 0px">
<table align="center" width="100%" cellpadding="0" cellspacing="0" border="0" data-mobile="true" dir="ltr"
       data-width="600" style="font-size: 14px; background-color:#f3f3f3; font-family:Helvetica,Arial,sans-serif">
    <tbody>
    <tr>
        <td align="center" valign="top" style="margin:0;padding:42px 0;">
            <table align="center" border="" cellspacing="0" cellpadding="0" width="600" bgcolor="white"
                   style="background-position: center top; background-repeat: no-repeat; background-size: cover; width: 660px; border:15px solid #B3B3B3;"
                   class="wrapper">
                <tbody>
                <tr>
                    <td align="center" valign="top" style="margin:0;padding:0;">
                        <table border="0" cellpadding="0" cellspacing="0" align="center" data-editable="image"
                               data-mobile-width="0" width="140">
                            <tbody>
                            <tr>
                                <td valign="top" align="center"
                                    style="display: inline-block; padding: 20px 0px 10px; margin: 0px;" class="tdBlock">
                                    <a href="<?php echo $shopSite ?>" target="_blank">
                                        <img src="/assets/img/'.$logoSite.'" alt="" height="80"
                                             border="0"
                                             style="border-width: 0px; border-style: none; border-color: transparent; font-size: 12px; display: block;"/>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="center" style="padding:10px 10px 0px 10px; margin:0;">
                        <table align="center" width="620px" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0"
                               data-editable="text">
                            <tbody>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 30px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: \'Poppins\', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                        Italiano
                                    </span>
                                </td>
                            </tr>
                            <tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 30px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: \'Poppins\', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                        Ciao  '.$userName.',<br>
                                        Abbiamo il piacere di rilasciare il seguente <b>coupon </b> del valore ';
        if ($amountTypeRemote=='P') {
            $couponText.=   ' del '.$amount.' %';
        }else{
            $couponText.=' di '.$amount.  'Euro';
        }
        $couponText.='</span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 10px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: \'Poppins\', sans-serif; font-size:15px;font-weight:300;color:green; line-height:1.2;">
                                        <h1>'.$couponCode.'</h1>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 10px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: \'Poppins\', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                               Potrai utilizzarlo all \'interno del sito '.$shopSite.' inserendo il codice nel carrello, oppure presentarlo in negozio.
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 20px 10px 0; margin: 0px; line-height: 1.5; font-size: 18px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: \'Poppins\', sans-serif; font-size:15px;font-weight:800;color:#3A3A3A; line-height:1.2;">
                                    il Coupon Vale solo su articoli non in saldo e scadrà il '.$couponValidThruEmail.' 
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 10px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: \'Poppins\', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                   Grazie per aver scelto <span style="font-family: \'Poppins\', sans-serif; font-size:15px;font-weight:300;color:green; line-height:1.2; font-weight: 600">'.strtoupper($nameShop).'</span>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 30px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: \'Poppins\', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                        English
                                    </span>
                                </td>
                            </tr>
                            <tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 30px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: \'Poppins\', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                        Hi  '.$userName.',<br>
                                        We have pleasure to release for you a  <b>coupon</b> with value of ';
        if ($amountTypeRemote=='P') {
            $couponText.=   ' '.$amount.' %';
        }else{
            $couponText.='  '.$amount.  'Euro';
        }
        $couponText.='</span>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 10px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: \'Poppins\', sans-serif; font-size:15px;font-weight:300;color:green; line-height:1.2;">
                                        <h1>'.$couponCode.'</h1>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 10px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: \'Poppins\', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                               You can use in '.$shopSite.' after  putting in basket  application or please present this in our shop to apply.
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 20px 10px 0; margin: 0px; line-height: 1.5; font-size: 18px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: \'Poppins\', sans-serif; font-size:15px;font-weight:800;color:#3A3A3A; line-height:1.2;">
                                    The  Coupon can you only on good not in sale and its expiration will be  '.$couponValidThruEmail.' 
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 10px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: \'Poppins\', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                   Thank You For choosing <span style="font-family: \'Poppins\', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2; font-weight: 600">'.strtoupper($nameShop).'</span>
                                    </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>';







        return $couponText;


    }


}


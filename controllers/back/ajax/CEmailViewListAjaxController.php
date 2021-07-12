<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CUserAddress;
use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CEmail;
use PDO;
use PDOException;

/**
 * Class CEmailViewListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/11/2019
 * @since 1.0
 *
 */
class CEmailViewListAjaxController extends AAjaxController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "email_view";

    public function get()
    {
        $view = new VBase(array());
        $this->page = new CBlueSealPage($this->pageSlug,$this->app);
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths','blueseal') . '/template/email_view.php');
        $htmlBody = 'Email Non Visualizzabile';
        $messaggeId =  $this->app->router->request()->getRequestData('messageId') ;
        $orderId = $this->app->router->request()->getRequestData('orderId');
        $isLocal=$this->app->router->request()->getRequestData('local');
        $order=\Monkey::app()->repoFactory->create('Order')->findOneBy(['id' => $orderId]);

        if ($isLocal!= 1) {
            $shop = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $order->remoteShopSellerId]);

                $db_host = $shop->dbHost;
                $db_name = $shop->dbName;
                $db_user = $shop->dbUsername;
                $db_pass = $shop->dbPassword;
                $shopId = $shop->id;
                try {

                    $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                    $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                    $res = " connessione ok <br>";
                } catch (PDOException $e) {
                    $res = $e->getMessage();
                }
                $stmtEmail = $db_con->prepare('SELECT count(*) as countRecord, htmlBody as htmlBody FROM `Email` where id ='.$messaggeId);
                $stmtEmail->execute();

                while($rowEmail = $stmtEmail->fetch(PDO::FETCH_ASSOC)){
                    $htmlBody = $rowEmail['htmlBody'];
                }




        } else {
           $email=\Monkey::app()->repoFactory->create('Email')->findOneBy(['id' => $messageId]);
            $htmlBody = $email->htmlBody;
        }


        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $pageURL = $blueseal . "email";

        $opera = $blueseal . "email-view";
        $aggiungi = $blueseal . "email-view";
        echo utf8_encode($htmlBody);
        /* return $view->render([
             'app' => new CRestrictedAccessWidgetHelper($this->app),
             'pageURL' => $pageURL,
             'operaURL' => $opera,
             'htmlBody' => $htmlBody,
             'page' => $this->page,
         ]);*/
    }


}


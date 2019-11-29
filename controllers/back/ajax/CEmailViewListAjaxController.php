<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CUserAddress;
use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CUser;
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
    protected $pageSlug = "invoice_print";

    public function get()
    {
        $view = new VBase(array());
        $this->page = new CBlueSealPage($this->pageSlug,$this->app);
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths','blueseal') . '/template/email_view.php');
        $htmlBody = 'Email Non Visualizzabile';
        $messaggeId = '<' . $this->app->router->request()->getRequestData('messageId') . '>';
        $orderId = $this->app->router->request()->getRequestData('orderId');
        $email = \Monkey::app()->repoFactory->create('Email')->findOneBy(['providerEmailId' => $messaggeId]);
        if ($email == null) {
            $shops = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['hasEcommerce' => 1]);
            foreach ($shops as $shop) {
                $db_host = $shop->dbHost;
                $db_name = $shop->dbName;
                $db_user = $shop->dbUsername;
                $db_pass = $shop->dbPassword;
                $shopId = $shop->id;
                try {

                    $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                    $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                    $res .= " connessione ok <br>";
                } catch (PDOException $e) {
                    $res .= $e->getMessage();
                }
                $stmtEmail = $db_con->prepare('SELECT htmlBody FROM Email where providerEmailId LIKE \'%' . $messaggeId . '%\'');
                $stmtEmail->execute();
                $rowEmail = $stmtEmail->fetch('PDO::FETCH_ASSOC');
                if ($rowEmail == null) {
                    continue;
                } else {
                    $htmlBody = $rowEmail['htmlBody'];
                    break;
                }


            }
        } else {
            $htmlBody = $email->htmlBody;
        }


        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $pageURL = $blueseal . "email";

        $opera = $blueseal . "email-view";
        $aggiungi = $blueseal . "email-view";

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'pageURL' => $pageURL,
            'operaURL' => $opera,
            'htmlBody' => $htmlBody,
            'page' => $this->page,
        ]);
    }


}


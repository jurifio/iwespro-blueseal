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

        $messaggeId = $this->app->router->request()->getRequestData('messageId');

        $email = \Monkey::app()->repoFactory->create('Email')->findOneBy(['providerEmailId' => $messaggeId]);
        $htmlBody = $email->htmlBody;
        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $pageURL = $blueseal."email";

        $opera = $blueseal."email-view";
        $aggiungi = $blueseal."email-view";

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'pageURL' =>$pageURL,
            'operaURL' =>$opera,
            'htmlBody' =>$htmlBody,
            'page' => $this->page,
        ]);
    }


}


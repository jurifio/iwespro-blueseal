<?php
namespace bamboo\blueseal\controllers

use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CBlueSealDashboardController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CBlueSealDashboardController extends ARestrictedAccessRootController
{
    protected $fallBack = "home";
    protected $logFallBack = "blueseal";
    protected $pageSlug = "dashboard";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->cfg()->fetch('paths','blueseal').'/template/dashboard.php');

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'data' => $this->request->getUrlPath(),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}
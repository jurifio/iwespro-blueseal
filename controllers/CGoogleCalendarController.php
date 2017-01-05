<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\ecommerce\APaymentGateway;
use bamboo\core\exceptions\BambooException;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class COrderAddController
 * @package bamboo\blueseal\controllers
 */
class CGoogleCalendarController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "google_calendar";

    public function get()
    {

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths', 'blueseal') . '/template/google_calendar.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build(),
        ]);
    }
}
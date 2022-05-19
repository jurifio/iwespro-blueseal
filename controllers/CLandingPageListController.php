<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CTranslationController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since 1.0
 */
class CLandingPageListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "landing_list";
    
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/landing_list.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'previewUrl' => $this->app->router->request()->getScheme().'://'.$this->app->cfg()->fetch('paths','domain').'/it/focus/demo/L',
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}
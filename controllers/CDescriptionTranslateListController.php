<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;



/**
 * Class CDescriptionTranslateListController
 * @package redpanda\blueseal\controllers
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CDescriptionTranslateListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "description_translate_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/description_translate_list.php');
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";
        $url = $this->urls['base']."traduzioni/descrizioni/lingua";

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'url'=>$url,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}
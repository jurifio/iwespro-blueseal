<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;


/**
 * Class CDetailTranslateLangListController
 * @package bamboo\blueseal\controllers
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
class CDetailTranslateLangListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "detail_lang_list";

    /**
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/detail_lang_list.php');
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";
        $langId = $this->app->router->getMatchedRoute()->getComputedFilter('langId');
        $urlAll = $this->urls['base']."traduzioni/dettagli/lingua_list/" .$langId;
        $urlTrans = $this->urls['base']."traduzioni/dettagli/lingua/" .$langId;

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'langId' => $langId,
            'page'=>$this->page,
            'urlAll'=>$urlAll,
            'urlTrans'=>$urlTrans,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}
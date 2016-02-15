<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;


/**
 * Class CDetailTranslateLangAllListController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CDetailTranslateLangAllListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "detail_langall_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->cfg()->fetch('paths','blueseal').'/template/detail_langall_list.php');
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";
        $langId = $this->app->router->getMatchedRoute()->getComputedFilter('langId');
        $urlAll = $this->urls['base']."traduzioni/dettagli/lingua_list/" .$langId;
        $urlTrans = $this->urls['base']."traduzioni/dettagli/lingua/" .$langId;

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'langId' => $langId,
            'page'=>$this->page,
            'urlAll'=>$urlAll,
            'urlTrans'=>$urlTrans,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}
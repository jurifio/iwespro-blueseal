<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\entities\CEntityManager;

/**
 * Class CCategoryListController
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
class CCategoryListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_category_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->cfg()->fetch('paths','blueseal').'/template/category_list.php');

        $blueseal = $this->app->baseUrl(false).'/blueseal';
        $pageURL = $blueseal."/categories";
        /** @var $em CEntityManager **/
        $categories = $this->app->categoryManager->categories()->children(1);
        $modifica = $blueseal."/prodotti/categorie/aggiungi";

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'categories' => $categories,
            'modifica' => $modifica,
            'pageURL' => $pageURL,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function post()
    {

    }
}
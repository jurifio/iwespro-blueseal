<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\entities\CEntityManager;

/**
 * Class CCategoryListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CCategoryTranslationListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "category_translation_list";

    public function get()
    {
        $allShops = \Monkey::app()->getUser()->hasPermission('allShops');
        $isAdmin='2';
        if($allShops){
            $isAdmin='1';
        }
        $addUrl='/blueseal/prodotti/categorie';
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/category_translation_list.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'allShops'=>$allShops,
            'addUrl'=>$addUrl,
            'isAdmin'=>$isAdmin,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}
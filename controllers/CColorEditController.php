<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\utils\slugify\CSlugify;

/**
 * Class CColorManageController
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
class CColorEditController extends CColorManageController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_colorgroup_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/colorgroup_edit.php');

        $colorGroupEdit = [];
        foreach ($this->app->router->request()->getRequestData() as $colorId) {
            $colorGroupEdit = \Monkey::app()->repoFactory->create('ProductColorGroupTranslation', false)->findBy(['productColorGroupId' => $colorId]);

        }

        $em = $this->app->entityManagerFactory->create('Lang');
        $langs = $em->findBy(["isActive"=>1]);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'colorGroupEdit' => $colorGroupEdit,
            'langs' => $langs,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);

    }

}
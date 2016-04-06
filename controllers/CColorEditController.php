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
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
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
            $colorGroupEdit = $this->app->repoFactory->create('ProductColorGroup', false)->findBy(['id' => $colorId]);

        }

        $em = $this->app->entityManagerFactory->create('Lang');
        $langs = $em->findAll("limit 99999", "");

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'colorGroupEdit' => $colorGroupEdit,
            'langs' => $langs,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);

    }

}
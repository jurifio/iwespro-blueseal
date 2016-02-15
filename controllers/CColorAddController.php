<?php
namespace bamboo\controllers;

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
class CColorAddController extends CColorManageController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_colorgroup_add";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->cfg()->fetch('paths', 'blueseal') . '/template/colorgroup_add.php');
        /** @var $em CEntityManager * */
        $colorGroupEdit = null;
        if (isset($_GET['productColorGroupId'])) {
            $em = $this->app->entityManagerFactory->create('ProductColorGroup');
            $colorGroupEdit = $em->findBy(array("id" => $_GET['productColorGroupId']), "", "");
        }

        $em = $this->app->entityManagerFactory->create('Lang');
        $langs = $em->findAll("limit 99999", "");

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'colorGroupEdit' => $colorGroupEdit,
            'langs' => $langs,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}
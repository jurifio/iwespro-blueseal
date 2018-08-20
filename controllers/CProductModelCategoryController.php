<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CProductSheetModelPrototypeCategoryGroup;
use bamboo\domain\entities\CProductSheetModelPrototypeGender;
use bamboo\domain\entities\CProductSheetModelPrototypeMacroCategoryGroup;
use bamboo\domain\entities\CProductSheetModelPrototypeMaterial;
use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;

/**
 * Class CProductListController
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
class CProductModelCategoryController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_model_manage_list";

    public function get()
    {

        /** @var CRepo $psmpRepo */
        $psmpRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype');
        /** @var CRepo $psmpcgRepo */
        $psmpcgRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup');

        //Catch gender
        $gendRes = [];
        /** @var CObjectCollection $genders */
        $genders = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeGender')->findAll();
        /** @var CProductSheetModelPrototypeGender $gender */
        foreach ($genders as $gender){
            $gendRes[$gender->id]['count'] = $psmpRepo->findBy(['genderId'=>$gender->id])->count();
            $gendRes[$gender->id]['name'] = $gender->name;
        }

        //Catch macrocategory
        $macroCatRes = [];
        /** @var CObjectCollection $macros */
        $macros = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeMacroCategoryGroup')->findAll();
        /** @var CProductSheetModelPrototypeMacroCategoryGroup $macro */
        foreach ($macros as $macro){
            $macroCatRes[$macro->id]['count'] = $psmpcgRepo->findBy(['macroCategoryGroupId'=>$macro->id])->count();
            $macroCatRes[$macro->id]['name'] = $macro->name;
        }

        //Catch categoryGroup
        $catGroupRes = [];
        /** @var CObjectCollection $catsGroup */
        $catsGroup = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup')->findAll();
        /** @var CProductSheetModelPrototypeCategoryGroup $catGroup */
        foreach ($catsGroup as $catGroup){
            $catGroupRes[$catGroup->id]['count'] = $psmpRepo->findBy(['categoryGroupId'=>$catGroup->id])->count();
            $catGroupRes[$catGroup->id]['name'] = $catGroup->name;
        }

        //Catch material
        $matRes = [];
        /** @var CObjectCollection $mats */
        $mats = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeMaterial')->findAll();
        /** @var CProductSheetModelPrototypeMaterial $mat */
        foreach ($mats as $mat){
            $matRes[$mat->id]['count'] = $psmpRepo->findBy(['materialId'=>$mat->id])->count();
            $matRes[$mat->id]['name'] = $mat->name;
        }


        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/product_model_manage_list.php');


        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'gendRes' => $gendRes,
            'macroCatRes' => $macroCatRes,
            'catGroupRes' => $catGroupRes,
            'matRes' => $matRes,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}
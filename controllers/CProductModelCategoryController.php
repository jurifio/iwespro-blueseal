<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\base\CObjectCollection;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CProductSheetModelPrototypeCategoryGroup;
use bamboo\domain\entities\CProductSheetModelPrototypeGender;
use bamboo\domain\entities\CProductSheetModelPrototypeMacroCategoryGroup;
use bamboo\domain\entities\CProductSheetModelPrototypeMaterial;
use bamboo\ecommerce\views\VBase;

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

        //Catch gender
        $gendRes = [];
        /** @var CObjectCollection $genders */
        //$genders = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeGender')->findAll();
        $genders = \Monkey::app()->dbAdapter->query('
                                                        SELECT id, name
                                                        FROM ProductSheetModelPrototypeGender
                                                        ORDER BY name',[])->fetchAll();
        //$genders->reorder('name');
        /** @var CProductSheetModelPrototypeGender $gender */
        foreach ($genders as $gender){

            //$gendRes[$gender['id']]['count'] = $psmpRepo->findBy(['genderId'=>$gender["id"]])->count();
            $gendRes[$gender['id']]['count'] = \Monkey::app()->dbAdapter->selectCount('ProductSheetModelPrototype', ['genderId'=>$gender["id"]]);
            $gendRes[$gender['id']]['name'] = $gender['name'];
        }

        //Catch macrocategory
        $macroCatRes = [];
        /** @var CObjectCollection $macros */
        //$macros = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeMacroCategoryGroup')->findAll();
        $macros = \Monkey::app()->dbAdapter->query('
                                                        SELECT id, name
                                                        FROM ProductSheetModelPrototypeMacroCategoryGroup
                                                        ORDER BY name',[])->fetchAll();
        //$macros->reorder('name');
        /** @var CProductSheetModelPrototypeMacroCategoryGroup $macro */
        foreach ($macros as $macro){
            //$macroCatRes[$macro['id']]['count'] = $psmpcgRepo->findBy(['macroCategoryGroupId'=>$macro['id']])->count();
            $macroCatRes[$macro['id']]['count'] = \Monkey::app()->dbAdapter->selectCount('ProductSheetModelPrototypeCategoryGroup', ['macroCategoryGroupId'=>$macro["id"]]);
            $macroCatRes[$macro['id']]['name'] = $macro['name'];
        }

        //Catch categoryGroup
        $catGroupRes = [];
        /** @var CObjectCollection $catsGroup */
        //$catsGroup = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup')->findAll();
        $catsGroup = \Monkey::app()->dbAdapter->query('
                                                        SELECT id, name
                                                        FROM ProductSheetModelPrototypeCategoryGroup
                                                        ORDER BY name',[])->fetchAll();
        //$catsGroup->reorder('name');
        /** @var CProductSheetModelPrototypeCategoryGroup $catGroup */
        foreach ($catsGroup as $catGroup){
            //$catGroupRes[$catGroup['id']]['count'] = $psmpRepo->findBy(['categoryGroupId'=>$catGroup['id']])->count();
            $catGroupRes[$catGroup['id']]['count'] = \Monkey::app()->dbAdapter->selectCount('ProductSheetModelPrototype', ['categoryGroupId'=>$catGroup["id"]]);
            $catGroupRes[$catGroup['id']]['name'] = $catGroup['name'];
        }

        //Catch material
        $matRes = [];
        /** @var CObjectCollection $mats */
        //$mats = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeMaterial')->findAll();
        $mats = \Monkey::app()->dbAdapter->query('
                                                        SELECT id, name
                                                        FROM ProductSheetModelPrototypeMaterial
                                                        ORDER BY name',[])->fetchAll();
        //$mats->reorder('name');
        /** @var CProductSheetModelPrototypeMaterial $mat */
        foreach ($mats as $mat){
            //$matRes[$mat['id']]['count'] = $psmpRepo->findBy(['materialId'=>$mat['id']])->count();
            $matRes[$mat['id']]['count'] = \Monkey::app()->dbAdapter->selectCount('ProductSheetModelPrototype', ['materialId'=>$mat["id"]]);
            $matRes[$mat['id']]['name'] = $mat['name'];
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
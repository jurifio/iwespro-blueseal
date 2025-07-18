<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\nestedCategory\CCategoryManager;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CProductEditController
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
class CProductModelEditController extends CProductManageController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_model_edit";

    /**
     * @throws \Exception
     * @throws \bamboo\core\exceptions\RedPandaDBALException
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $ids = \Monkey::app()->cacheService->getCache('misc')->get('idsModels');
        \Monkey::app()->cacheService->getCache('misc')->delete('idsModels');
        $parPostMultiple = \Monkey::app()->router->request()->getRequestData('modelIds');
        $parUpdateMultiple = \Monkey::app()->router->request()->getRequestData('modifyModelIds');

        $isMultiple = false;
        $isUpdate = false;

        if($parPostMultiple || $parUpdateMultiple){
            $isMultiple = true;

            if($parUpdateMultiple) $isUpdate = true;
        }

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/product_model_edit.php');

		/** LOGICA */

        $productCategories = \Monkey::app()->repoFactory->create('ProductCategory')->findAll();
        $categories = [];
        $idCat = 0;
        foreach($productCategories as $productCategory) {
            $categories[$idCat] = [];
            $categories[$idCat]['id'] = $productCategory->id;
            $categories[$idCat]['name'] = trim($this->app->categoryManager->categories()->getStringPath($productCategory->id," "));
            $idCat++;
        }

        $productDetailsCollection = \Monkey::app()->repoFactory->create('ProductDetailTranslation')->findBy(['langId'=>1]);
        $productDetails = [];

        //gender
        $gends = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeGender')->findAll();
        $genders = [];
        $iGend = 0;
        foreach ($gends as $gend){
            $genders[$iGend] = [];
            $genders[$iGend]['id'] = $gend->id;
            $genders[$iGend]['name'] = $gend->name;
            $iGend++;
        }

        //material
        $material = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeMaterial')->findAll();
        $materials = [];
        $iMat = 0;
        foreach ($material as $mat){
            $materials[$iMat] = [];
            $materials[$iMat]['id'] = $mat->id;
            $materials[$iMat]['name'] = $mat->name;
            $iMat++;
        }
        $ProdCat = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeMacroCategoryGroup')->findAll();
        $prodCats = [];
        $iprodCat = 0;
        foreach ($ProdCat as $pcat){

            $prodCats[$iprodCat]=[];
            $prodCats[$iprodCat]['id']=$pcat->id;
            $prodCats[$iprodCat]['name'] =$pcat->name;
        $iprodCat++;

        }


        foreach ($productDetailsCollection as $detail) {
            try {
                $productDetails[$detail->productDetailId] = $detail->name;
            } catch(\Throwable $e) {

            }
        }
        unset($productDetailsCollection);

        $countM = count(json_decode($ids));

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'categories' => json_encode($categories),
            'genders' => json_encode($genders),
            'materials' => json_encode($materials),
            'sidebar' => $this->sidebar->build(),
            'isMultiple' => $isMultiple,
            'productDetails' => $productDetails,
            'isUpdated' => $isUpdate,
            'prodCats'=>json_encode($prodCats),
            'ids'=>$ids,
            'countM'=>$countM
        ]);
    }

    /**
     * @return string|void
     * @throws \Exception
     * @throws \bamboo\core\exceptions\RedPandaDBALException
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */
    public function post(){
        $ids = \Monkey::app()->router->request()->getRequestData('ids');

        \Monkey::app()->cacheService->getCache('misc')->set('idsModels', $ids);

    }
}
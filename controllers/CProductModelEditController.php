<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\nestedCategory\CCategoryManager;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CProductEditController
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

        foreach ($productDetailsCollection as $detail) {
            try {
                $productDetails[$detail->productDetailId] = $detail->name;
            } catch(\Throwable $e) {

            }
        }
        unset($productDetailsCollection);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'categories' => json_encode($categories),
            'sidebar' => $this->sidebar->build(),
            'productDetails' => $productDetails
        ]);
    }
}
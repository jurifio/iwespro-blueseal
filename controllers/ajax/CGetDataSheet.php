<?php
namespace bamboo\blueseal\controllers\ajax;
use bamboo\ecommerce\views\VBase;

/**
 * Class CGetDataSheet
 * @package bamboo\blueseal\controllers\ajax
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
class CGetDataSheet extends AAjaxController
{
    /**
     * @throws \bamboo\core\exceptions\RedPandaDBALException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/parts/sheetDetails.php');

        $get = $this->app->router->request()->getRequestData();
        $emptyDetails = (false !== $this->app->router->request()->getRequestData('emptyDetails')) ? $this->app->router->request()->getRequestData('emptyDetails') : 1;

        $code = (array_key_exists('code', $get)) ? (($get['code']) ? $get['code'] : false) : false;
        $value = (array_key_exists('value', $get)) ? $get['value'] : false;
        $type = (array_key_exists('type', $get)) ? $get['type'] : false;

        $Pname = '';

        if ($type && ('model' == $type)) {
            $productSheetModelPrototype = $this->app->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $value]);
            $productSheetPrototype = $productSheetModelPrototype->productSheetPrototype;
            $Pname= ($productSheetModelPrototype->productName) ? $productSheetModelPrototype->productName : '';
            $actual = $productSheetModelPrototype->productSheetModelActual;
        } elseif ('change' == $type) {
            $productSheetPrototype = $this->app->repoFactory->create('ProductSheetPrototype')->findOneBy(['id' => $value]);
            $actual = [];
        } else {
            if ($code) {
                $prodCollection = $this->app->repoFactory->create('Product')->findByAnyString($code);
                $product = $prodCollection->getFirst();
                $productSheetPrototype = $product->productSheetPrototype;
                if (null === $productSheetPrototype) $productSheetPrototype = $this->app->repoFactory->create('ProductSheetPrototype')->findOne([33]);

                $productName = $product->productNameTranslation;
                if ($productName) $Pname = $productName->getfirst()->name;
                $actual = $product->productSheetActual;
            } else {
                $productSheetPrototype = $this->app->repoFactory->create('ProductSheetPrototype')->findOneBy(['name' => 'Generica']);
                $actual = [];
            }
        }

        $resActual = [];
        foreach($actual as $v) {
            $resActual[$v->productDetailLabelId] = $v->productDetailId;
        }

        $cats = [];

        if(isset($product)) {
            foreach($product->productCategory as $pc){
                $cats[] = $pc->id;
            };
        }

        $cats = json_encode($cats);

        $em = $this->app->entityManagerFactory->create('ProductSheetPrototype');
        $productSheets = $em->findBySql('SELECT id FROM ProductSheetPrototype ORDER BY `name`');

        return $view->render([
            'emptyDetails' => $emptyDetails,
            'productName' => $Pname,
            'productSheets' => $productSheets,
            'productSheetPrototype' => $productSheetPrototype,
            'actual' => $resActual,
            'actualCount' => count($resActual),
            'categories' => $cats
        ]);
    }

    public function post()
    {
        return $this->get();
    }
}
<?php
namespace bamboo\controllers\back\ajax;
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
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal') . '/template/parts/sheetDetails.php');

        $emptyDetails = (false !== $this->app->router->request()->getRequestData('emptyDetails')) ? $this->app->router->request()->getRequestData('emptyDetails') : 1;

        $code = \Monkey::app()->router->request()->getRequestData('code');
        $value = \Monkey::app()->router->request()->getRequestData('value');
        $type = \Monkey::app()->router->request()->getRequestData('type');

        $isCorrectMultiple = false;
        $Pname = '';

        if ($type && ('model' == $type)) {
            $productSheetModelPrototype = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $value]);
            $productSheetPrototype = $productSheetModelPrototype->productSheetPrototype;
            $Pname= ($productSheetModelPrototype->productName) ? $productSheetModelPrototype->productName : '';
            $actual = $productSheetModelPrototype->productSheetModelActual;
        } elseif ($type &&  ('change' == $type)) {
            $productSheetPrototype = \Monkey::app()->repoFactory->create('ProductSheetPrototype')->findOneBy(['id' => $value]);
            $actual = [];
        } elseif (($code) || ($type && ('product' == $type))) {
            $str = (false != $code) ? $code : $value;
            $prodCollection = \Monkey::app()->repoFactory->create('Product')->findByAnyString($str);
            $product = $prodCollection->getFirst();
            $productSheetPrototype = $product->productSheetPrototype;
            if (null === $productSheetPrototype) $productSheetPrototype = \Monkey::app()->repoFactory->create('ProductSheetPrototype')->findOne([33]);

            $productName = $product->productNameTranslation;
            if ($productName->count()) $Pname = $productName->getfirst()->name;
            $actual = $product->productSheetActual;
        } else if($type && ('models' == $type || 'modifyModels' == $type)){

            $checkSheetArr = [];

            $vs = json_decode($value, true);

            foreach ($vs as $v){
                $productSheetModelPrototype = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $v['id']]);
                $checkSheetArr[] = $productSheetModelPrototype->productSheetPrototype->id;
            }

            $check = array_unique($checkSheetArr);

            if(count($check) !== 1){
                return '<p style="color: orangered">SCHEDE PRODOTTO NON COERENTI</p>';
            } else {
                $productSheetModelPrototype = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $vs[0]['id']]);
                $productSheetPrototype = $productSheetModelPrototype->productSheetPrototype;
                $Pname= ($productSheetModelPrototype->productName) ? $productSheetModelPrototype->productName : '';
                $actual = $productSheetModelPrototype->productSheetModelActual;
                $isCorrectMultiple = true;
            }

        } else {
            $productSheetPrototype = \Monkey::app()->repoFactory->create('ProductSheetPrototype')->findOneBy(['name' => 'Generica']);
            $actual = [];
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
        } elseif (isset($productSheetModelPrototype)) {
            foreach($productSheetModelPrototype->productCategory as $v) {
                $cats[] = $v->id;
            }
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
            'categories' => $cats,
            'correctMultiple'=>$isCorrectMultiple
        ]);
    }

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaDBALException
     */
    public function post()
    {
        return $this->get();
    }
}
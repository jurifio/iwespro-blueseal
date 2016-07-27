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

        $code = (array_key_exists('code', $get)) ? $get['code'] : false;
        $value = (array_key_exists('value', $get)) ? $get['value'] : false;
        $type = (array_key_exists('type', $get)) ? $get['type'] : false;
        $actual = [];

        if ($type && ('model' == $type)) {
            $productSheetModelPrototype = $this->app->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $value]);
            $productSheetPrototype = $productSheetModelPrototype->productSheetPrototype;
            $actual = $productSheetModelPrototype->productSheetModelActual;
        } elseif (!$type || ('model' != $type) || ('change' == $type)) {
            if ($code) {
                list($id, $variantId) = explode('-', $code);
                $productSheetPrototype = $this->app->repoFactory->create('Product')->findOneBy(['id' => $id, 'productVariantId' => $variantId])->productSheetPrototype;
                $actual = $this->app->repoFactory->create('ProductSheetActual')->findBy(['productId' => $id, 'productVariantId' => $variantId]);
            }
        }

        $resActual = [];
        foreach($actual as $v) {
            $resActual[$v->productDetailLabelId] = $v->productDetailId;
        }

        $em = $this->app->entityManagerFactory->create('ProductSheetPrototype');
        $productSheets = $em->findBySql('SELECT id FROM ProductSheetPrototype ORDER BY `name`');


        return $view->render([
            'productSheets' => $productSheets,
            'productSheetPrototype' => $productSheetPrototype,
            'actual' => $resActual,
        ]);
    }

    public function post()
    {
        return $this->get();
    }
}
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
        $id = (array_key_exists('value', $get)) ? $get['value'] : false;
        $idModel = (array_key_exists('idModel', $get)) ? $get['idModel'] : false;
        $type = (array_key_exists('type', $get)) ? $get['type'] : false;
        $actual = [];

        if ($type && ('model' == $type) && $idModel) {
            $productSheetModelPrototype = $this->app->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $idModel]);
            $productSheetPrototype = $productSheetModelPrototype->productSheetPrototype;
            $actual = $productSheetModelPrototype->productSheetModelActual;
        } elseif ((!$type || ('model' != $type) || ('change' == $type)) && ($id)) {
            $productSheetPrototype = $this->app->repoFactory->create('ProductSheetPrototype')->findOneBy(['id' => $id]);
            if ($code) {
                list($id, $variantId) = explode('-', $code);
                $actual = $this->app->repoFactory->create('ProductSheetActual')->findBy(['id' => $id, 'variantId' => $variantId]);
            }
        }

        $resActual = [];
        foreach($actual as $v) {
            $resActual[$v->productDetailLabelId] = $v->productDetailId;
        }

        return $view->render([
            'productSheetPrototype' => $productSheetPrototype,
            'actual' => $resActual,
        ]);
    }

    public function post()
    {
        return $this->get();
    }
}
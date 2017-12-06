<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CDetailProductGetDetails extends AAjaxController
{
    public function get()
    {
        $code = $this->app->router->request()->getRequestData('code');
        $names = [];
        $namesCount = 0;
            $details = [];
            $modelRes = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneByStringId($code);
            $details['id'] = $modelRes->id;
            $details['prototype'] = $modelRes->productSheetPrototypeId;
            $modelDetails = $modelRes->productSheetModelActual;
            //$modelDetails = \Monkey::app()->repoFactory->create('ProductSheetModelActual')->findBy(['productSheetModelPrototypeId' => $modelRes->id]);
            foreach ($modelDetails as $v) {
                $details[$v->productDetailLabelId] = [];
                $details[$v->productDetailLabelId]['labelName'] = \Monkey::app()->repoFactory->create('ProductDetailLabelTranslation')->findOneBy(['langId' => 1, 'productDetailLabelId' => $v->productDetailLabelId]);
                $details[$v->productDetailLabelId]['detailId'] = $v->productDetailId;
            }
            $res = $details;
    }
}
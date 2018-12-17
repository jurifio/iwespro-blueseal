<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CGetDataSheetLoading
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 17/12/2018
 * @since 1.0
 */
class CGetDataSheetLoading extends AAjaxController
{
    /**
     *
     */
    public function post()
    {

        $value = \Monkey::app()->router->request()->getRequestData('value');

        $checkSheetArr = [];

        $vs = json_decode($value, true);

        foreach ($vs as $v){

            $productSheetModelPrototype = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $v['id']]);
            $checkSheetArr[] = $productSheetModelPrototype->productSheetPrototype->id;
        }

        $check = array_unique($checkSheetArr);


        if(count($check) !== 1){
            return 0;
        } else {
            $productSheetModelPrototype = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $vs[0]['id']]);
        }

        return json_encode(['productSheetPrototype' => $productSheetModelPrototype->id, 'count' => count($vs)]);
    }
}
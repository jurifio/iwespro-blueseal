<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductSheetPrototype;


/**
 * Class CProductSheetModelPrototypeForFason
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/05/2018
 * @since 1.0
 */
class CProductSheetModelPrototypeForFason extends AAjaxController
{
    /**
     * @return bool
     */
    public function post()
    {

       $gDate = \Monkey::app()->router->request()->getRequestData('gender');
       $cDate = \Monkey::app()->router->request()->getRequestData('cat');
       $mDate = \Monkey::app()->router->request()->getRequestData('material');


       $gender = explode(', ', $gDate);
       $cat = explode(', ', $cDate);
       $material = explode(', ', $mDate);

       if (!empty($gDate)){
           /** @var CRepo $genPRepo */
           $genPRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeGender');
           foreach ($gender as $val){

               $extistent = $genPRepo->findOneBy(['name'=>ucfirst($val)]);

               if(is_null($extistent)){
                   $genP = $genPRepo->getEmptyEntity();
                   $genP->name = ucfirst($val);
                   $genP->smartInsert();
               }

           }
       }


        if (!empty($cDate)) {
            /** @var CRepo $catCRepo */
            $catCRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup');
            foreach ($cat as $val) {

                $extistent = $catCRepo->findOneBy(['name' => $val]);

                if (is_null($extistent)) {
                    $catP = $catCRepo->getEmptyEntity();
                    $catP->name = $val;
                    $catP->smartInsert();
                }

            }
        }


        if (!empty($mDate)) {
            /** @var CRepo $matRepo */
            $matRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeMaterial');
            foreach ($material as $val) {

                $extistent = $matRepo->findOneBy(['name' => $val]);

                if (is_null($extistent)) {
                    $matP = $matRepo->getEmptyEntity();
                    $matP->name = $val;
                    $matP->smartInsert();
                }

            }
        }

       return true;
    }

    public function put(){

/*
        $id = \Monkey::app()->router->request()->getRequestData('idCopy');


        $psp = \Monkey::app()->repoFactory->create('ProductSheetPrototype');


        $pspDetail = $psp->productDetailLabel;
*/


    }

}
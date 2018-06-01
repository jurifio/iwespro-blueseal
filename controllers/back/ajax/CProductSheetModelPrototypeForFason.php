<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductSheetModelPrototype;
use bamboo\domain\entities\CProductSheetModelPrototypeCategoryGroup;
use bamboo\domain\entities\CProductSheetModelPrototypeGender;
use bamboo\domain\entities\CProductSheetModelPrototypeMaterial;
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

    /**
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function delete(){

        $gS = \Monkey::app()->router->request()->getRequestData('gender');
        $cS = \Monkey::app()->router->request()->getRequestData('cat');
        $mS = \Monkey::app()->router->request()->getRequestData('material');

        /** @var CRepo $psmpR */
        $psmpR = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype');

        $psmpAss = [];

        if($gS){
            /** @var CRepo $psmpGRepo */
            $psmpGRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeGender');
            foreach ($gS as $g){
                /** @var CObjectCollection $psmpGC */
                $psmpGC = $psmpR->findBy(['genderId'=>$g]);

                /** @var CProductSheetModelPrototypeGender $psmpGDel */
                $psmpGDel = $psmpGRepo->findOneBy(['id'=>$g]);

                if($psmpGC->isEmpty()) {
                    $psmpAss[] = $psmpGDel->name.' | Eliminata';
                    $psmpGDel->delete();
                    continue;
                }

                /** @var CProductSheetModelPrototype $psmpG */
                foreach ($psmpGC as $psmpG){
                    $psmpAss[] = $psmpG->id.' - '.$psmpGDel->name.' | Genere';
                }
            }

        }


        if($cS){
            /** @var CRepo $psmpCRepo */
            $psmpCRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeHasProductCategory');
            foreach ($cS as $c){
                /** @var CObjectCollection $psmpCC */
                $psmpCC = $psmpR->findBy(['categoryGroupId'=>$c]);

                /** @var CProductSheetModelPrototypeCategoryGroup $psmpCDel */
                $psmpCDel = $psmpCRepo->findOneBy(['id'=>$c]);

                if($psmpCC->isEmpty()) {
                    $psmpAss[] = $psmpCDel->name.' | Eliminata';
                    $psmpCDel->delete();
                    continue;
                }

                /** @var CProductSheetModelPrototype $psmpC */
                foreach ($psmpCC as $psmpC){
                    $psmpAss[] = $psmpC->id.' - '.$psmpCDel->name.' | Categoria';
                }
            }
        }


        if($mS){
            /** @var CRepo $psmpMRepo */
            $psmpMRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeMaterial');
            foreach ($mS as $m){
                /** @var CObjectCollection $psmpMC */
                $psmpMC = $psmpR->findBy(['materialId'=>$m]);

                /** @var CProductSheetModelPrototypeMaterial $psmpMDel */
                $psmpMDel = $psmpMRepo->findOneBy(['id'=>$m]);

                if($psmpMC->isEmpty()) {
                    $psmpAss[] = $psmpMDel->name.' | Eliminata';
                    $psmpMDel->delete();
                    continue;
                }

                /** @var CProductSheetModelPrototype $psmpM */
                foreach ($psmpMC as $psmpM){
                    $psmpAss[] = $psmpM->id.' - '.$psmpMDel->name.' | Materiale';
                }
            }
        }

        return json_encode($psmpAss);

    }


    /**
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put(){

        $cat = \Monkey::app()->router->request()->getRequestData('cat');
        $name = \Monkey::app()->router->request()->getRequestData('name');

        /** @var CProductSheetModelPrototypeCategoryGroup $c */
        $c = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup')->findOneBy(['id'=>$cat]);
        $c->name = $name;
        $c->update();

        return 'Il nome della categoria è stato modificato con successo.';
    }

}
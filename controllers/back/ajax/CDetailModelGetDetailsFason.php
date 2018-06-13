<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductSheetModelPrototype;
use bamboo\domain\entities\CProductSheetModelPrototypeCategoryGroup;
use bamboo\domain\entities\CProductSheetModelPrototypeMacroCategoryGroup;
use bamboo\domain\entities\CProductSheetModelPrototypeMaterial;

/**
 * Class CDetailModelGetDetailsFason
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 11/05/2018
 * @since 1.0
 */
class CDetailModelGetDetailsFason extends AAjaxController
{
    public function get()
    {
        $step = \Monkey::app()->router->request()->getRequestData('step');
        /** @var CRepo $psmp */
        $psmp = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype');
        if($step == 1){
            $genderId = \Monkey::app()->router->request()->getRequestData('genderId');

            /** @var CObjectCollection $psmpS1 */
            $psmpS1 = $psmp->findBy(['genderId'=>$genderId]);

            if($psmpS1->isEmpty()){
                return false;
            } else {
                $cats = [];

                /** @var CProductSheetModelPrototype $val */
                foreach ($psmpS1 as $val){
                    $cats[] = $val->categoryGroupId;
                }

            }

            $catsU = array_unique($cats);

            /** @var CRepo $catRepo */
            $catRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup');
            $catInfo = [];
            $i = 0;

            $macro = [];
            foreach ($catsU as $cat){
                /** @var CProductSheetModelPrototypeCategoryGroup $sCat */
                $sCat = $catRepo->findOneBy(['id'=>$cat]);

                //prendo la macro corrispondente

                /** @var CProductSheetModelPrototypeMacroCategoryGroup $macroCat */
                $macroCat = $sCat->productSheetModelPrototypeMacroCategoryGroup;
                $macro[$i]['id'] = $macroCat->id;
                $macro[$i]['name'] = $macroCat->name;
                $i++;



                //$catInfo[$i]['id'] = $sCat->id;
                //$catInfo[$i]['name'] = $sCat->name;
                //$i++;
            }
            return json_encode(array_unique($macro, SORT_REGULAR));

        }


        if($step == 2){

            $genderId = \Monkey::app()->router->request()->getRequestData('genderId');
            $macroCategId = \Monkey::app()->router->request()->getRequestData('macroCategId');

            /** @var CObjectCollection $psmpS2 */
            $psmpS2 = $psmp->findBy(['genderId'=>$genderId]);

            if($psmpS2->isEmpty()){
                return false;
            } else {
                $cats1 = [];

                /** @var CProductSheetModelPrototype $val */
                foreach ($psmpS2 as $val){
                    $cats1[] = $val->categoryGroupId;
                }

            }

            $catsU = array_unique($cats1);

            /** @var CRepo $catRepo */
            $catRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup');
            $catInfo1 = [];
            $i = 0;
            foreach ($catsU as $cat){
                /** @var CProductSheetModelPrototypeCategoryGroup $sCat1 */
                $sCat1 = $catRepo->findOneBySql('SELECT * FROM ProductSheetModelPrototypeCategoryGroup p WHERE p.id = ? AND p.macroCategoryGroupId = ?',
                    [$cat, $macroCategId]);

                if(!is_null($sCat1)) {
                    $catInfo1[$i]['id'] = $sCat1->id;
                    $catInfo1[$i]['name'] = $sCat1->name;
                    $catInfo1[$i]['img'] = $sCat1->imageUrl;
                    $catInfo1[$i]['desc'] = $sCat1->description;
                    $i++;
                }
            }
            return json_encode($catInfo1);



        }



        if($step == 3){
            $genderId = \Monkey::app()->router->request()->getRequestData('genderId');
            $categId = \Monkey::app()->router->request()->getRequestData('categId');

            /** @var CObjectCollection $psmpS2 */
            $psmpS2 = $psmp->findBy(['genderId'=>$genderId, 'categoryGroupId'=>$categId]);

            if($psmpS2->isEmpty()){
                return false;
            } else {
                $mats = [];
                /** @var CProductSheetModelPrototype $valS2 */
                foreach ($psmpS2 as $valS2){
                    $mats[] = $valS2->materialId;
                }
            }
            // non ci possono essere due materiali uguali con lo stesso genere e gruppo categoria
            $matsU = array_unique($mats);

            if($matsU === $mats){
                $matInfo = [];
                $y = 0;
                /** @var CRepo $matRepo */
                $matRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeMaterial');

                foreach ($matsU as $mat){
                    /** @var CProductSheetModelPrototypeMaterial $rMat */
                    $rMat = $matRepo->findOneBy(['id'=>$mat]);
                    $matInfo[$y]['id'] = $rMat->id;
                    $matInfo[$y]['name'] = $rMat->name;
                    $y++;
                }

                return json_encode($matInfo);

            } else {
                return false;
            }
        }


        if($step == 4){
            $genderId = \Monkey::app()->router->request()->getRequestData('genderId');
            $categId = \Monkey::app()->router->request()->getRequestData('categId');
            $matId = \Monkey::app()->router->request()->getRequestData('matId');

            /** @var CObjectCollection $psmpS3 */
            $psmpS3 = $psmp->findBy(['genderId'=>$genderId, 'categoryGroupId'=>$categId, 'materialId'=>$matId]);

            //non possono esistere due modelli con le stesse caratteristiche impostate da default per i fason
            if($psmpS3->count() > 1 || $psmpS3->isEmpty()){
                return false;
            } else {
                /** @var CProductSheetModelPrototype $prodSFinal */
                $prodSFinal = $psmpS3->getFirst();
            }

            $pFinal = [];
            $pFinal['id'] = $prodSFinal->id;
            $pFinal['name'] = $prodSFinal->name;

            return json_encode($pFinal);

        }
    }
}
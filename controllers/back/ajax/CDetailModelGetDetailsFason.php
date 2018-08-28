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
        if ($step == 1) {
            $genderId = \Monkey::app()->router->request()->getRequestData('genderId');

            $psmpS1 = \Monkey::app()->cacheService->getCache("misc")->get("AllModelCategory-$genderId");
            if (!$psmpS1) {
                $psmpS1 = \Monkey::app()->dbAdapter->query('SELECT categoryGroupId FROM ProductSheetModelPrototype WHERE genderId = ?', [$genderId])->fetchAll();
                if (empty($psmpS1)) return false;
                $this->app->cacheService->getCache("misc")->set("AllModelCategory-$genderId", $psmpS1, 13000);
            }


            $cats = [];

            /** @var CProductSheetModelPrototype $val */
            foreach ($psmpS1 as $val) {
                $cats[] = $val["categoryGroupId"];
            }


            $catsU = array_unique($cats);

            /** @var CRepo $catRepo */
            $cats = implode(',', $catsU);
            $query = "SELECT pmc.id, pmc.name, pmc.imageUrl AS img, pmc.description AS `desc` FROM ProductSheetModelPrototypeCategoryGroup pc
                                                                  JOIN ProductSheetModelPrototypeMacroCategoryGroup pmc ON pc.macroCategoryGroupId = pmc.id
                                                                  WHERE pc.id in ($cats)";
            $macroCat = \Monkey::app()->dbAdapter->query($query, [])->fetchAll();

            return json_encode(array_unique($macroCat, SORT_REGULAR));
        }


        if ($step == 2) {

            $genderId = \Monkey::app()->router->request()->getRequestData('genderId');
            $macroCategId = \Monkey::app()->router->request()->getRequestData('macroCategId');

            $psmpS2 = \Monkey::app()->cacheService->getCache("misc")->get("AllModelCategory-$genderId");

            if (empty($psmpS2)) {
                return false;
            } else {
                $cats1 = [];

                /** @var CProductSheetModelPrototype $val */
                foreach ($psmpS2 as $val) {
                    $cats1[] = $val['categoryGroupId'];
                }
            }

            $catsU = array_unique($cats1);

            /** @var CRepo $catRepo */
            $cats = implode(',', $catsU);
            $query = "SELECT p.id, p.name, p.imageUrl AS img, p.description AS `desc` 
                      FROM ProductSheetModelPrototypeCategoryGroup p WHERE p.id in ($cats) AND p.macroCategoryGroupId = $macroCategId";
            $catInfo1 = \Monkey::app()->dbAdapter->query($query, [])->fetchAll();

            return json_encode($catInfo1);

        }


        if ($step == 3) {
            $genderId = \Monkey::app()->router->request()->getRequestData('genderId');
            $categId = \Monkey::app()->router->request()->getRequestData('categId');

            $psmpS2 = \Monkey::app()->dbAdapter->query('SELECT materialId FROM ProductSheetModelPrototype WHERE genderId = ? AND categoryGroupId = ?', [$genderId, $categId])->fetchAll();

            if (empty($psmpS2)) {
                return false;
            } else {
                $mats = [];
                /** @var CProductSheetModelPrototype $valS2 */
                foreach ($psmpS2 as $valS2) {
                    $mats[] = $valS2["materialId"];
                }
            }
            // non ci possono essere due materiali uguali con lo stesso genere e gruppo categoria
            $matsU = array_unique($mats);

            if ($matsU === $mats) {

                /** @var CRepo $catRepo */
                $matsS = implode(',', $matsU);
                $query = "SELECT p.id, p.name FROM ProductSheetModelPrototypeMaterial p WHERE p.id in ($matsS)";
                $matInfo = \Monkey::app()->dbAdapter->query($query, [])->fetchAll();

                return json_encode($matInfo);

            } else {
                return false;
            }
        }


        if ($step == 4) {
            $genderId = \Monkey::app()->router->request()->getRequestData('genderId');
            $categId = \Monkey::app()->router->request()->getRequestData('categId');
            $matId = \Monkey::app()->router->request()->getRequestData('matId');

            /** @var CObjectCollection $psmpS3 */
            $psmpS3 = $psmp->findBy(['genderId' => $genderId, 'categoryGroupId' => $categId, 'materialId' => $matId]);

            //non possono esistere due modelli con le stesse caratteristiche impostate da default per i fason
            if ($psmpS3->count() > 1 || $psmpS3->isEmpty()) {
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
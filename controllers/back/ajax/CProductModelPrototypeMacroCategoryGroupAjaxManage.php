<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\RedPandaException;
use bamboo\domain\entities\CProductSheetModelPrototypeCategoryGroup;
use bamboo\domain\entities\CProductSheetModelPrototypeMacroCategoryGroup;

/**
 * Class CProductModelPrototypeMacroCategoryGroupAjaxManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 13/06/2018
 * @since 1.0
 */
class CProductModelPrototypeMacroCategoryGroupAjaxManage extends AAjaxController
{

    public function get(){

        $catName = \Monkey::app()->router->request()->getRequestData('cat');

        $sql = 'SELECT c.id catId, c.name catN, cm.name macroCatName
                  FROM ProductSheetModelPrototypeCategoryGroup c
                  JOIN ProductSheetModelPrototypeMacroCategoryGroup cm ON c.macroCategoryGroupId = cm.id
                  WHERE c.name RLIKE ?';

        $ids = \Monkey::app()->dbAdapter->query($sql, [$catName])->fetchAll();

        return json_encode($ids);

    }

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put()
    {

        $catName = \Monkey::app()->router->request()->getRequestData('cat');
        $mCat = \Monkey::app()->router->request()->getRequestData('macroCat');

        if(empty($catName) || empty($mCat)) return 'Compila tutti i campi';

        $sql = 'SELECT id
                  FROM ProductSheetModelPrototypeCategoryGroup
                  WHERE name RLIKE ?';

        $ids = \Monkey::app()->dbAdapter->query($sql, [$catName])->fetchAll();


        /** @var CRepo $catGR */
        $catGR = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup');

        foreach ($ids as $id){
            /** @var CProductSheetModelPrototypeCategoryGroup $catG */
            $catG = $catGR->findOneBy(['id'=>$id['id']]);
            $catG->macroCategoryGroupId = $mCat;
            $catG->update();
        }

        return 'Macrocategorie associate';
    }


    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post(){

        $type = \Monkey::app()->router->request()->getRequestData('type');

        /** @var CRepo $pmcRepo */
        $pmcRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeMacroCategoryGroup');

        switch ($type){
            case 'name':
                $id = \Monkey::app()->router->request()->getRequestData('macroCatId');
                /** @var CProductSheetModelPrototypeMacroCategoryGroup $pmc */
                $pmc = $pmcRepo->findOneBy(['id'=>$id]);
                $name = \Monkey::app()->router->request()->getRequestData('name');
                if(empty($name)) return 'Inserisci un nome';
                $pmc->name = $name;
                $pmc->update();
                break;
            case 'description':
                $ids = \Monkey::app()->router->request()->getRequestData('macroCatId');
                $desc = \Monkey::app()->router->request()->getRequestData('desc');
                if (empty($desc)) return 'Inserisci una descrizione';
                foreach ($ids as $id) {
                    /** @var CProductSheetModelPrototypeMacroCategoryGroup $pmc */
                    $pmc = $pmcRepo->findOneBy(['id' => $id]);
                    $pmc->description = $desc;
                    $pmc->update();
                }
                break;
            case 'find-sub-name':
                $sub = \Monkey::app()->router->request()->getRequestData('sub_name');
                $find = \Monkey::app()->router->request()->getRequestData('find_name');
                $ids = \Monkey::app()->router->request()->getRequestData('macroCatIds');
                foreach($ids as $id) {
                    /** @var CProductSheetModelPrototypeMacroCategoryGroup $pmc */
                    $pmc = $pmcRepo->findOneBy(['id'=>$id]);
                    $pmc->name = str_ireplace($find, $sub, $pmc->name);
                    $pmc->update();
                }
                break;
        }

        return 'La macrocategoria è stata aggionata con successo';

    }

    public function delete(){

        $macroCatIds = \Monkey::app()->router->request()->getRequestData('macroCatId');

        $del = '';
        $undel = '';
        /** @var CRepo $macroCatR */
        $macroCatR = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeMacroCategoryGroup');

        foreach ($macroCatIds as $macroCatId){

            /** @var CProductSheetModelPrototypeMacroCategoryGroup $macroCat */
            $macroCat = $macroCatR->findOneBy(['id'=>$macroCatId]);

            if($macroCat->productSheetModelPrototypeCategoryGroup->count() === 0) {
                $del .= $macroCat->id . "-" . $macroCat->name . "<br>";
                $macroCat->delete();
            } else {
                $undel .= $macroCat->id . "-" . $macroCat->name . "<br>";
            }
        }

        return "Cancellate:<br> $del <br> Non cancellate:<br> $undel";
    }

}
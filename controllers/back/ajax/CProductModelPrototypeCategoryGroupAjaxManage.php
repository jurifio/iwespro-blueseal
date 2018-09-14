<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\RedPandaException;
use bamboo\domain\entities\CProductSheetModelPrototype;
use bamboo\domain\entities\CProductSheetModelPrototypeCategoryGroup;

/**
 * Class CProductModelPrototypeCategoryGroupAjaxManage
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
class CProductModelPrototypeCategoryGroupAjaxManage extends AAjaxController
{

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put()
    {

        $field = \Monkey::app()->router->request()->getRequestData('field');
        $catId = \Monkey::app()->router->request()->getRequestData('catId');

        switch ($field){
            case 'name':
                /** @var CProductSheetModelPrototypeCategoryGroup $catG */
                $catG = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup')->findOneBy(['id'=>$catId]);
                $name = \Monkey::app()->router->request()->getRequestData('name');
                if(empty($name)) return 'Inserisci un nome';
                $catG->name = $name;
                $catG->update();
                break;
            case 'desc':
                foreach ($catId as $cat) {
                    $catG = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup')->findOneBy(['id'=>$cat]);
                    $desc = \Monkey::app()->router->request()->getRequestData('desc');
                    $catG->description = $desc;
                    $catG->update();
                }
                break;
        }

        return 'Categoria aggiornata con successo';
    }

    public function post(){

        $pcRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup');
        $sub = \Monkey::app()->router->request()->getRequestData('sub_name');
        $find = \Monkey::app()->router->request()->getRequestData('find_name');
        $ids = \Monkey::app()->router->request()->getRequestData('ids');
        foreach($ids as $id) {
            $pmc = $pcRepo->findOneBy(['id'=>$id]);
            $pmc->name = str_ireplace($find, $sub, $pmc->name);
            $pmc->update();
        }

        return "Categorie aggiornate";
    }


    public function delete(){

        $catIds = \Monkey::app()->router->request()->getRequestData('catId');

        /** @var CRepo $catR */
        $catR = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup');

        foreach ($catIds as $catId){

            /** @var CProductSheetModelPrototypeCategoryGroup $cat */
            $cat = $catR->findOneBy(['id'=>$catId]);

            if($cat->productSheetModelPrototype->count() === 0) {
                $cat->delete();
            } else {

                $psmps = $cat->productSheetModelPrototype;
                /** @var CProductSheetModelPrototype $psmp */
                foreach ($psmps as $psmp){
                    $psmp->isVisible = 0;
                    $psmp->categoryGroupId = null;
                    $psmp->update();
                }

                $cat->delete();

            }
        }

        return "Categorie cancellate con successo";
    }
}
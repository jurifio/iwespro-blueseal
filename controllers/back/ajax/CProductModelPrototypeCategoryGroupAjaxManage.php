<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\RedPandaException;
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

        /** @var CProductSheetModelPrototypeCategoryGroup $catG */
        $catG = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup')->findOneBy(['id'=>$catId]);

        switch ($field){
            case 'name':
                $name = \Monkey::app()->router->request()->getRequestData('name');
                if(empty($name)) return 'Inserisci un nome';
                $catG->name = $name;
                break;
            case 'desc':
                $desc = \Monkey::app()->router->request()->getRequestData('desc');
                $catG->description = $desc;
                break;
        }

        $catG->update();

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

        $del = '';
        $undel = '';
        /** @var CRepo $catR */
        $catR = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup');

        foreach ($catIds as $catId){

            /** @var CProductSheetModelPrototypeCategoryGroup $cat */
            $cat = $catR->findOneBy(['id'=>$catId]);

            if($cat->productSheetModelPrototype->count() === 0) {
                $del .= $cat->id . "-" . $cat->name . "<br>";
                $cat->delete();
            } else {
                $undel .= $cat->id . "-" . $cat->name . "<br>";
            }
        }

        return "Cancellate:<br> $del <br> Non cancellate:<br> $undel";
    }
}
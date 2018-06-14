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

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put()
    {

        $ids = \Monkey::app()->router->request()->getRequestData('ids');
        $mCat = \Monkey::app()->router->request()->getRequestData('macroCat');

        /** @var CRepo $catGR */
        $catGR = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup');

        foreach ($ids as $id){
            /** @var CProductSheetModelPrototypeCategoryGroup $catG */
            $catG = $catGR->findOneBy(['id'=>$id]);
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
        $id = \Monkey::app()->router->request()->getRequestData('macroCatId');
        $name = \Monkey::app()->router->request()->getRequestData('name');

        if(empty($name)) return 'Inserisci un nome';

        /** @var CRepo $pmcRepo */
        $pmcRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeMacroCategoryGroup');

        /** @var CProductSheetModelPrototypeMacroCategoryGroup $pmc */
        $pmc = $pmcRepo->findOneBy(['id'=>$id]);
        $pmc->name = $name;
        $pmc->update();

        return 'Il nome è stato cambiato con successo';

    }


}
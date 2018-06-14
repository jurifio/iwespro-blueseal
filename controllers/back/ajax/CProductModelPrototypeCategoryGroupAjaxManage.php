<?php
namespace bamboo\controllers\back\ajax;

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

}
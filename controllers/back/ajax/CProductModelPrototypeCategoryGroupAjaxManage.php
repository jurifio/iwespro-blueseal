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

        $catId = \Monkey::app()->router->request()->getRequestData('catId');
        $desc = \Monkey::app()->router->request()->getRequestData('desc');

        /** @var CProductSheetModelPrototypeCategoryGroup $catG */
        $catG = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup')->findOneBy(['id'=>$catId]);
        $catG->description = $desc;
        $catG->update();

        return 'Descrizione inserita con successo';
    }

}
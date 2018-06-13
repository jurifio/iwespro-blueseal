<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductSheetModelPrototype;


/**
 * Class CProductSheetModelPrototypeManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 11/06/2018
 * @since 1.0
 */
class CProductSheetModelPrototypeManage extends AAjaxController
{
    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put(){


        $ids = \Monkey::app()->router->request()->getRequestData('ids');

        /** @var CRepo $psmpRepo */
        $psmpRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype');


        foreach ($ids as $id) {

            /** @var CProductSheetModelPrototype $psmp */
            $psmp = $psmpRepo->findOneBy(['id'=>$id]);

            $psmp->isVisible = 0;
            $psmp->update();
        }

        return 'Il modello è stato nascosto';

    }

}
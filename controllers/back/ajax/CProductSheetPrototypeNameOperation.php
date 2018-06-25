<?php

namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CProductSheetPrototype;


/**
 * Class CProductSheetModelPrototypeNameOperation
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 25/06/2018
 * @since 1.0
 */
class CProductSheetPrototypeNameOperation extends AAjaxController
{

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put(){

        $id = \Monkey::app()->router->request()->getRequestData('id');
        $name = \Monkey::app()->router->request()->getRequestData('name');

        /** @var CProductSheetPrototype $psp */
        $psp = \Monkey::app()->repoFactory->create('ProductSheetPrototype')->findOneBy(['id'=>$id]);
        $psp->name = $name;
        $psp->update();

        return 'Nome aggiornato con successo';

    }

}
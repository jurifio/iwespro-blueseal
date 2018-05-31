<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductDetailLabel;
use bamboo\domain\entities\CProductDetailLabelTranslation;
use bamboo\domain\entities\CProductSheetModelPrototype;
use bamboo\domain\entities\CProductSheetPrototype;
use bamboo\domain\entities\CProductSheetPrototypeHasProductDetailLabel;


/**
 * Class CProductSheetModelPrototypeOperation
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/05/2018
 * @since 1.0
 */
class CProductSheetModelPrototypeOperation extends AAjaxController
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

        /** @var CRepo $pspRepo */
        $pspRepo = \Monkey::app()->repoFactory->create('ProductSheetPrototype');

        foreach ($ids as $id) {

            /** @var CProductSheetPrototype $psp */
            $psp = $pspRepo->findOneBy(['id'=>$id]);
            $psp->isVisible = 0;
            $psp->update();

        }


        return 'Modelli nascosti con successo';


    }

}
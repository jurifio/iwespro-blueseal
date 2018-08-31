<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CProductBatchHasProductBrand;
use bamboo\domain\entities\CProductBatchHasProductName;

/**
 * Class CProductNameTranslationBatchManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 07/06/2018
 * @since 1.0
 */
class CProductNameTranslationBatchManage extends AAjaxController
{

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post(){
        $names = \Monkey::app()->router->request()->getRequestData('names');
        $lang = \Monkey::app()->router->request()->getRequestData('lang');

        /** @var CRepo $pbhpnRepo */
        $pbhpnRepo = \Monkey::app()->repoFactory->create('ProductBatchHasProductName');

        foreach ($names as $name){
            /** @var CProductBatchHasProductName $pbhpn */
            $pbhpn = $pbhpnRepo->findOneBy(['langId'=>$lang, 'productName'=>$name]);

            $catToChange = $pbhpn->workCategoryStepsId;

            if(!is_null($pbhpn->workCategorySteps->rgt)) {
                $pbhpn->workCategoryStepsId = $pbhpn->workCategorySteps->rgt;
                $pbhpn->update();
            }


            if((($lang == 2 && $catToChange == CProductBatchHasProductName::UNFIT_PRODUCT_NAME_ENG))
            || ($lang == 3 && $catToChange == CProductBatchHasProductName::UNFIT_PRODUCT_NAME_DTC)) {
                /** @var CProductBatch $pb */
                $pb = $pbhpn->productBatch;

                if($pb->isValid() == 'ok'){
                    $pb->isFixed = 1;
                    $pb->unfitDate = date('Y-m-d H:i:s');
                    $pb->update();
                }
            }
        }


        return 'Steps aggiornati con successo';

    }

}
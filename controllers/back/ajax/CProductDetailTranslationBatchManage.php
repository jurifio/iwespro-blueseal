<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CProductBatchHasProductBrand;
use bamboo\domain\entities\CProductBatchHasProductDetail;
use bamboo\domain\entities\CProductBatchHasProductName;
use bamboo\domain\repositories\CProductBatchHasProductDetailRepo;

/**
 * Class CProductDetailTranslationBatchManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/01/2019
 * @since 1.0
 */
class CProductDetailTranslationBatchManage extends AAjaxController
{

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post(){
        $details = \Monkey::app()->router->request()->getRequestData('details');
        $lang = \Monkey::app()->router->request()->getRequestData('lang');
        $pbId = \Monkey::app()->router->request()->getRequestData('batchId');

        /** @var CProductBatchHasProductDetailRepo $pbhpdRepo */
        $pbhpdRepo = \Monkey::app()->repoFactory->create('ProductBatchHasProductDetail');

        foreach ($details as $detail){
            /** @var CProductBatchHasProductDetail $pbhpd */
            $pbhpd = $pbhpdRepo->findOneBy(['productBatchId'=>$pbId, 'productDetailId'=>$detail, 'langId'=>$lang]);

            $catToChange = $pbhpd->workCategoryStepsId;

            if(!is_null($pbhpd->workCategorySteps->rgt)) {
                $pbhpd->workCategoryStepsId = $pbhpd->workCategorySteps->rgt;
                $pbhpd->update();
            }


            if((($lang == 2 && $catToChange == CProductBatchHasProductDetail::UNFIT_PRODUCT_DETAIL_ENG))
            || ($lang == 3 && $catToChange == CProductBatchHasProductDetail::UNFIT_PRODUCT_DETAIL_DTC)) {
                /** @var CProductBatch $pb */
                $pb = $pbhpd->productBatch;

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
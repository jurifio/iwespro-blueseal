<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchHasProductName;
use bamboo\domain\entities\CProductName;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductNameRepo;
use bamboo\domain\repositories\CWorkCategoryStepsRepo;


/**
 * Class CProductBatchNameTranslateManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 06/06/2018
 * @since 1.0
 */
class CProductBatchNameTranslateManage extends AAjaxController
{
    /**
     * @return string
     */
    public function post()
    {
        $pbId = \Monkey::app()->router->request()->getRequestData('productBatchId');
        $langId = \Monkey::app()->router->request()->getRequestData('langId');
        $prNames = \Monkey::app()->router->request()->getRequestData('pNames');

        /** @var CRepo $pbhpnRepo */
        $pbhpnRepo = \Monkey::app()->repoFactory->create('ProductBatchHasProductName');

        /** @var CProductBatchRepo $pbRepo */
        $pbRepo = \Monkey::app()->repoFactory->create('ProductBatch');

        /** @var CProductBatch $pb */
        $pb = $pbRepo->findOneBy(['id'=>$pbId]);

        /** @var CWorkCategoryStepsRepo $workCategoryRepo */
        $workCategoryRepo = \Monkey::app()->repoFactory->create('WorkCategorySteps');
        $initStep = $workCategoryRepo->getFirstStepsFromCategoryId($pb->workCategoryId)->id;

        if(!$pbRepo->checkRightLanguage($pbId, $langId)) return 'Stai cercando di aggiungere le traduzioni di una lingua ad un lotto che prevede un\'altra lavorazione';

        $extInBatch = '';

        foreach ($prNames as $prName) {

            /** @var CProductBatchHasProductName $ext */
            $ext = $pbhpnRepo->findOneBy(['productName'=>$prName, 'langId'=>$langId]);

            if(!is_null($ext)) {
                $extInBatch .= 'Il nome: '.$ext->productName.' è presente nel lotto '.$ext->productBatchId.'<br>';
                continue;
            }

            /** @var CProductBatchHasProductName $pbhpn */
            $pbhpn = $pbhpnRepo->getEmptyEntity();
            $pbhpn->productBatchId = $pbId;
            $pbhpn->productName = $prName;
            $pbhpn->langId = $langId;
            $pbhpn->workCategoryStepsId = $initStep;
            $pbhpn->smartInsert();
        }

        return 'Nomi inseriti con successo nel lotto <br>'.$extInBatch;

    }

}
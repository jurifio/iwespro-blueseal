<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CProductBatchTextManage;
use bamboo\domain\repositories\CProductBatchDetailsRepo;
use bamboo\domain\repositories\CProductBatchRepo;


/**
 * Class CMassiveProductBatchManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 21/05/2018
 * @since 1.0
 */
class CProductBatchTextManageAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put()
    {

        $productBatchTextManageId = \Monkey::app()->router->request()->getRequestData('textManage');
        $note = \Monkey::app()->router->request()->getRequestData('note');
        $type = \Monkey::app()->router->request()->getRequestData('type');

        if (empty($note)) return 'Inserisci il testo della nota';

        /** @var CProductBatchTextManage $productBatchTextManage */
        $productBatchTextManage = \Monkey::app()->repoFactory->create('ProductBatchTextManage')->findOneBy(['id' => $productBatchTextManageId]);


        if ($type == 's' || is_null($productBatchTextManage->note)) {
            $productBatchTextManage->note = $note;
        } else if ($type == 'a') {
            $productBatchTextManage->note = $productBatchTextManage->note . '. ' . $note . '.';
        }

        $productBatchTextManage->workCategoryStepsId = $productBatchTextManage->getUnfitStep();
        $productBatchTextManage->update();

        /** @var CProductBatch $pb */
        $pba = $productBatchTextManage->productBatch;
        $pba->isFixed = 0;
        $pba->update();


        return 'Note inserite con successo';
    }
}
<?php

namespace bamboo\domain\repositories;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CProductBatchHasProductName;
use bamboo\domain\entities\CProductBatchTextManage;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\entities\CWorkCategorySteps;
use bamboo\utils\time\SDateToolbox;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CProductBatchTextManageRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/12/2018
 * @since 1.0
 */
class CProductBatchTextManageRepo extends ARepo
{

    public function insertNewProductBatchTextManage(CProductBatch $productBatch, string $theme, string $description) : CProductBatchTextManage {

        /** @var CWorkCategoryStepsRepo $wksr */
        $wksr = \Monkey::app()->repoFactory->create('WorkCategorySteps');

        $workCategoryId = $productBatch->workCategoryId;

        switch ($workCategoryId){
            case 5:
                $charMin = CProductBatchTextManage::CHARMIN_FASHION_TEXT;
                break;
            case 6:
                $charMin = CProductBatchTextManage::CHARMIN_FASHION_BLOG;
                break;
            case 7:
                $charMin = CProductBatchTextManage::CHARMIN_INFLUENCER;
                break;
            case 8:
                $charMin = CProductBatchTextManage::CHARMIN_PRODUCT_DESCRIPTION;
                break;
            case 9:
                $charMin = CProductBatchTextManage::CHARMIN_BRAND_DESCRIPTION;
                break;
        }

        /** @var CProductBatchTextManage $productBatchTextManage */
        $productBatchTextManage = \Monkey::app()->repoFactory->create('ProductBatchTextManage')->getEmptyEntity();
        $productBatchTextManage->theme = $theme;
        $productBatchTextManage->description = $description;
        $productBatchTextManage->productBatchId = $productBatch->id;
        $productBatchTextManage->charMin = $charMin;
        $productBatchTextManage->workCategoryStepsId = $wksr->getFirstStepsFromCategoryId($workCategoryId)->id;
        $productBatchTextManage->smartInsert();

        return $productBatchTextManage;
    }

    public function goToNextStep($id)
    {
        /** @var CProductBatchTextManage $pbtm */
        $pbtm = $this->findOneBy(['id' => $id]);

        if (!is_null($pbtm->workCategorySteps->rgt)) {
            $pbtm->workCategoryStepsId = ($pbtm->workCategorySteps->rgt);
            $pbtm->update();
        }

        return true;

    }

}
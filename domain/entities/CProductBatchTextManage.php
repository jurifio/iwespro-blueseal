<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\repositories\CWorkCategoryRepo;
use bamboo\domain\repositories\CWorkCategoryStepsRepo;

/**
 * Class CProductBatchTextManage
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/12/2018
 * @since 1.0
 *
 * @property CWorkCategorySteps $workCategorySteps
 * @property CProductBatch $productBatch
 * @property CObjectCollection $productBatchTextManagePhoto
 *
 */
class CProductBatchTextManage extends AEntity
{
    protected $entityTable = 'ProductBatchTextManage';
    protected $primaryKeys = ['id'];

    const CHARMIN_FASHION_TEXT = 100;
    const CHARMIN_FASHION_BLOG = 100;
    const CHARMIN_INFLUENCER = 100;
    const CHARMIN_PRODUCT_DESCRIPTION = 100;
    const CHARMIN_BRAND_DESCRIPTION = 100;
    const CHARMIN_FB = 100;

    public function getUnfitStep() : int {

        $unfit = null;
        switch ($this->productBatch->workCategory->id) {
            case 5:
                $unfit = 16;
                break;
            case 6:
                $unfit = 19;
                break;
            case 7:
                $unfit = 22;
                break;
            case 8:
                $unfit = 25;
                break;
            case 9:
                $unfit = 28;
                break;
            case 10:
                $unfit = 31;
                break;

        }

        return $unfit;
    }

}
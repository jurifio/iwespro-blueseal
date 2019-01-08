<?php

namespace bamboo\domain\entities;

use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CProductBatchHasProductDetail
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 07/01/2019
 * @since 1.0
 *
 * @property CWorkCategorySteps $workCategorySteps
 * @property CProductBatch $productBatch
 * @property CLang $lang
 *
 */
class CProductBatchHasProductDetail extends AEntity
{
    const UNFIT_PRODUCT_DETAIL_ENG = 34;
    const UNFIT_PRODUCT_DETAIL_DTC = 37;

    const LANG_ENG = 2;
    const LANG_DTC = 3;

    protected $entityTable = 'ProductBatchHasProductDetail';
    protected $primaryKeys = ['productBatchId','productDetailId', 'langId'];
}
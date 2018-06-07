<?php

namespace bamboo\domain\entities;

use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CProductBatchHasProductName
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 06/06/2018
 * @since 1.0
 *
 * @property CProductBatch $productBatch
 * @property CWorkCategorySteps $workCategorySteps
 * @property CLang $lang
 *
 */
class CProductBatchHasProductName extends AEntity
{
    const UNFIT_PRODUCT_NAME_ENG = 9;
    const UNFIT_PRODUCT_NAME_DTC = 12;

    protected $entityTable = 'ProductBatchHasProductName';
    protected $primaryKeys = ['productBatchId','productName','langId'];
}
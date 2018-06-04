<?php

namespace bamboo\domain\entities;

use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CProductBatchHasProductBrand
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/06/2018
 * @since 1.0
 *
 * @property CWorkCategorySteps $workCategorySteps
 * @property CProductBatch $productBatch
 *
 */
class CProductBatchHasProductBrand extends AEntity
{
    const UNFIT_BRAND = 6;

    protected $entityTable = 'ProductBatchHasProductBrand';
    protected $primaryKeys = ['productBatchId','productBrandId'];
}
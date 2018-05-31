<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CProductBatchDetails
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/03/2018
 * @since 1.0
 *
 * @property CProductBatch $productBatch
 * @property CWorkCategorySteps $workCategorySteps
 */
class CProductBatchDetails extends AEntity
{

    const UNFIT_NORM = 3;

    protected $entityTable = 'ProductBatchDetails';
    protected $primaryKeys = ['id'];
}
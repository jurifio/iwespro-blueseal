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
 */
class CProductBatchTextManagePhoto extends AEntity
{
    protected $entityTable = 'ProductBatchTextManagePhoto';
    protected $primaryKeys = ['id'];


}
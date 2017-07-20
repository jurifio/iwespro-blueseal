<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CWarehouseShelf
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 * @property CWarehouse $warehouse
 * @property CObjectCollection $warehouseShelfPosition
 */
class CWarehouseShelf extends AEntity
{
    protected $entityTable = 'WarehouseShelf';
    protected $primaryKeys = ['id'];
}
<?php

namespace bamboo\domain\entities;

use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CWarehouseShelfPosition
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
 *
 * @property CWarehouseShelf $warehouseShelf
 * @property COrderLine $orderLine
 */
class CWarehouseShelfPosition extends AEntity
{
    protected $entityTable = 'WarehouseShelfPosition';
    protected $primaryKeys = ['id'];

    /**
     * @return bool
     */
    public function isEmpty() {
        return $this->orderLine === null;
    }
}
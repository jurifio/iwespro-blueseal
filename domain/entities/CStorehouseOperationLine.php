<?php

namespace bamboo\domain\entities;

use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CStorehouseOperationLine
 * @package bamboo\domain\entities
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/08/2016
 * @since 1.0
 */
class CStorehouseOperationLine extends AEntity
{
    protected $entityTable = 'StorehouseOperationLine';
    protected $primaryKeys = ['id','storehouseId','shopId','productId','productVariantId','productSizeId'];
}
<?php

namespace bamboo\domain\entities;

use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CStorehouseOperation
 * @package bamboo\domain\entities
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/08/2016
 * @since 1.0
 */
class CStorehouseOperation extends AEntity
{
    protected $entityTable = 'StorehouseOperation';
    protected $primaryKeys = ['id','shopId','storehouseId'];
}
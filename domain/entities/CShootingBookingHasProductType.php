<?php

namespace bamboo\domain\entities;

use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CShootingBookingHasProductType
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/04/2018
 * @since 1.0
 *
 * @property CShootingProductType $shootingProductType
 *
 */
class CShootingBookingHasProductType extends AEntity
{

    protected $entityTable = 'ShootingBookingHasProductType';
    protected $primaryKeys = ['shootingBookingId','shootingProductTypeId'];
}
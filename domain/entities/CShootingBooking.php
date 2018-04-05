<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CShootingBooking
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
 * @property CObjectCollection $shootingProductType
 * @property CShop $shop
 * @property CObjectCollection $shootingBookingHasProductType
 * @property CShooting $shooting
 *
 *
 */
class CShootingBooking extends AEntity
{

    protected $entityTable = 'ShootingBooking';
    protected $primaryKeys = ['id'];
}
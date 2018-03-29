<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CShooting
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/03/2018
 * @since 1.0
 *
 * @property CObjectCollection $product
 * @property CShop $shop
 */
class CShooting extends AEntity
{

    const PREFIX_ON_DUPLICATE_PRODUCT = 000;

    protected $entityTable = 'Shooting';
    protected $primaryKeys = ['id'];
}
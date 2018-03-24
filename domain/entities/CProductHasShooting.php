<?php

namespace bamboo\domain\entities;

use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CProductHasShooting
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
 */
class CProductHasShooting extends AEntity
{
    protected $entityTable = 'ProductHasShooting';
    protected $primaryKeys = ['productId','productVariantId','shootingId'];
}
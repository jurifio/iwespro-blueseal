<?php

namespace bamboo\domain\entities;

use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class ProductCardPhoto
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 09/05/2018
 * @since 1.0
 */
class CProductCardPhoto extends AEntity
{
    protected $entityTable = 'ProductCardPhoto';
    protected $primaryKeys = ['productId','productVariantId'];
}
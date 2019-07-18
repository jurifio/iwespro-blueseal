<?php

namespace bamboo\domain\entities;

use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CProductBrandHasEan
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 18/07/2019
 * @since 1.0
 */
class CProductBrandHasEan extends AEntity
{
	protected $entityTable = 'ProductBrand';
	protected $primaryKeys = ['id'];
}
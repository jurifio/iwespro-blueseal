<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CEditorialPlanShopAsSocial
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/08/2020
 * @since 1.0
 */
class CEditorialPlanShopAsSocial extends AEntity
{
	protected $entityTable = 'EditorialPlanShopAsSocial';
	protected $primaryKeys = ['id'];
}
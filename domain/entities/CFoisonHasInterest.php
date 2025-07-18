<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CFoisonHasInterest
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 21/08/2018
 * @since 1.0
 *
 * @property CWorkCategory $workCategory
 * @property CFoisonStatus $foisonStatus
 *
 *
 */
class CFoisonHasInterest extends AEntity
{
    protected $entityTable = 'FoisonHasInterest';
    protected $primaryKeys = ['foisonId','workCategoryId'];
}
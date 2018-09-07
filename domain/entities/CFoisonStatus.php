<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CFoisonStatus
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 07/09/2018
 * @since 1.0
 */
class CFoisonStatus extends AEntity
{
    protected $entityTable = 'FoisonStatus';
    protected $primaryKeys = ['id'];
}
<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CMessage
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 31/05/2018
 * @since 1.0
 *
 *
 * @property CObjectCollection $messageHasUser
 *
 */
class CMessage extends AEntity
{
    protected $entityTable = 'Message';
    protected $primaryKeys = ['id'];
}
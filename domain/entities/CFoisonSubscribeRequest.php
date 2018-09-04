<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CFoisonSubscribeRequest
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/09/2018
 * @since 1.0
 *
 *
 *
 * @property CObjectCollection $workCategory
 *
 *
 */
class CFoisonSubscribeRequest extends AEntity
{

    CONST WAITING = "waiting";
    CONST DENIED = "denied";
    CONST ACCEPTED = "accepted";

    protected $entityTable = 'FoisonSubscribeRequest';
    protected $primaryKeys = ['id'];
}
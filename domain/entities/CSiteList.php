<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;
use bamboo\core\db\pandaorm\entities\IEntity;
use bamboo\utils\price\SPriceToolbox;

/**
 * Class CCart
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/07/2018
 * @since 1.0
 */
class CSite extends AEntity
{
    protected $entityTable = 'SiteApi';
    protected $primaryKeys = ['id'];

    /**
     * @return float
     */

}
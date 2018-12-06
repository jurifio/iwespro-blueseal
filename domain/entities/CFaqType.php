<?php

namespace bamboo\domain\entities;

use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CFaqType
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 06/12/2018
 * @since 1.0
 */
class CFaqType extends AEntity
{
    protected $entityTable = 'FaqType';
    protected $primaryKeys = ['id'];



    const FASON = 1;

}
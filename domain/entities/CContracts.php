<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CContracts
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 14/03/2018
 * @since 1.0
 *
 * @property CFoison $foison
 * @property CObjectCollection $contractDetails
 */
class CContracts extends AEntity
{

    CONST DEFAULT_CONTRACT_NAME_REQUEST_FASON = "Condizioni generali per il contratto del Fason: ";

    protected $entityTable = 'Contracts';
    protected $primaryKeys = ['id'];
}
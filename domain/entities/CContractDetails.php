<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CContractDetails
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
 * @property CContracts $contracts
 * @property CWorkCategory $workCategory
 * @property CWorkPriceList $workPriceList
 * @property CObjectCollection $productBatch
 *
 */
class CContractDetails extends AEntity
{
    protected $entityTable = 'ContractDetails';
    protected $primaryKeys = ['id'];
}
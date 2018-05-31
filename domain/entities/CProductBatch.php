<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CProductBatch
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/03/2018
 * @since 1.0
 *
 * @property CContractDetails $contractDetails
 * @property CObjectCollection $productBatchDetails
 * @property CDocument $document
 */
class CProductBatch extends AEntity
{
    protected $entityTable = 'ProductBatch';
    protected $primaryKeys = ['id'];

    public function isComplete(){

        /** @var CObjectCollection $pBdetails */
        $pBdetails = $this->productBatchDetails;

        /** @var CProductBatchDetails $pBdetail */
        foreach ($pBdetails as $pBdetail){
            if(!is_null($pBdetail->workCategorySteps->rgt)) return false;
        }

        return true;

    }
}
<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CEditorialPlan
 * @package bamboo\domain\entities
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/08/2016
 * @since 1.0
 *
 */
class CProductBatchHasProductionImage extends AEntity
{
	protected $entityTable = 'ProductBatchHasProductionImage';
	protected $primaryKeys = ['id'];
    public function isComplete()
    {

        $elems = $this->getElements();

        foreach ($elems as $elem) {
            if (!is_null($elem->workCategorySteps->rgt)) return false;
        }

        return true;

    }
}
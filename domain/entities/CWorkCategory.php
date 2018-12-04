<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CWorkCategory
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
 * @property CWorkCategorySteps $workCategorySteps
 */
class CWorkCategory extends AEntity
{
    const NORM = 1;
    const BRAND = 2;
    const NAME_ENG = 3;
    const NAME_DTC = 4;
    const TXT_FAS = 5;
    const TXT_FAS_BLOG = 6;
    const TXT_INFL = 7;
    const TXT_PRT = 8;
    const TXT_BRAND = 9;

    const SLUG_EMPTY_NORM = 'prodotti';
    const SLUG_EMPTY_BRAND = 'brands';
    const SLUG_EMPTY_TRANS = 'traduzione-nomi';

    protected $entityTable = 'WorkCategory';
    protected $primaryKeys = ['id'];
}
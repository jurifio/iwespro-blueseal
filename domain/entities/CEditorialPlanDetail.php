<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CEditorialPlanDetail
 * @package bamboo\domain\entities
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/08/2016
 * @since 1.0
 * @property  CObjectCollection editorialPlan
 * @property  CObjectCollection editorialPlanSocial
 * @property  CObjectCollection editorialPlanArgument


 */
class CEditorialPlanDetail extends AEntity
{
	protected $entityTable = 'EditorialPlanDetail';
	protected $primaryKeys = ['id'];
}
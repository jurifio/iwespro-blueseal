<?php

namespace bamboo\domain\entities;

use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CEditorialPlanSocial
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
 */
class CEditorialPlanSocial extends AEntity
{
    const FACEBOOK = '#3A5896';
    const TWITTER = '#d92d77';
    const GOOGLE = '#fbbc05';
    const INSTAGRAM = '#d92d77';


	protected $entityTable = 'EditorialPlanSocial';
	protected $primaryKeys = ['id'];
}
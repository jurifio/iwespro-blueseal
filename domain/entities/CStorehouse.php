<?php

namespace bamboo\domain\entities;

use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CStorehouse
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
 * @property CShop $shop
 * @property CDirtySku $dirtySku
 */
class CStorehouse extends AEntity
{
    protected $entityTable = 'Storehouse';
    protected $primaryKeys = ['id','shopId'];
}
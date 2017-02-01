<?php

namespace bamboo\domain\entities;

use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CMarketplaceAccountHasProduct
 * @package bamboo\domain\entities
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/07/2016
 * @since 1.0
 * @property CProduct $product
 * @property CMarketplaceAccount $marketplaceAccount
 */
class CMarketplaceAccountHasProduct extends AEntity
{
    protected $entityTable = 'MarketplaceAccountHasProduct';
	protected $primaryKeys = ['productId','productVariantId','marketplaceId','marketplaceAccountId'];
}
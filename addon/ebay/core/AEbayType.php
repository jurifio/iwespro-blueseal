<?php

namespace bamboo\addon\ebay\core;

/**
 * Class AEbayType
 * @package bamboo\addon\ebay\core
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 02/03/2016
 * @since 1.0
 */
abstract class AEbayType
{
    protected $policy;

    public function __construct(CEbayTypeValidationPolicy $policy)
    {
        $this->policy = $policy;
    }
}
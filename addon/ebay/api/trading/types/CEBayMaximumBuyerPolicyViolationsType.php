<?php

namespace bamboo\addon\ebay\api\trading\calls\types;

use bamboo\addon\ebay\api\trading\enum\CEbayPeriodCodeEnum;

/**
 * Class CEbayMaximumBuyerPolicyViolationsType
 * @package bamboo\addon\ebay\api\trading\calls\types
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/03/2016
 * @since 1.0
 */
class CEbayMaximumBuyerPolicyViolationsType
{
   /** @var int */
   protected $count;
   /** @var CEbayPeriodCodeEnum */
   protected $period;

}
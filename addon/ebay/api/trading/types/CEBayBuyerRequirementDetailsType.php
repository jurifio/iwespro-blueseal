<?php

namespace bamboo\addon\ebay\api\trading\calls\types;

/**
 * Class CEbayBuyerRequirementDetailsType
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
class CEbayBuyerRequirementDetailsType
{
    /** @var bool */
   public $linkedPayPalAccount;
   /** @var  maximumBuyerPolicyViolationsType */
   public $maximumBuyerPolicyViolations;
   /** @var int */
   public $count;
   /** @var periodCodeType */
   public $period;
   /** @var maximumItemRequirementsType */
   public $maximumItemRequirements;
   /** @var maximumUnpaidItemStrikesInfoType */
   public $maximumUnpaidItemStrikesInfo;


}
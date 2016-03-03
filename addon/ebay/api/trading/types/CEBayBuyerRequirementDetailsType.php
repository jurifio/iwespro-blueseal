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
   protected $linkedPayPalAccount;
   /** @var CEbayMaximumBuyerPolicyViolationsType */
   protected $maximumBuyerPolicyViolations;
   /** @var CEbayMaximumItemRequirementsType */
   protected $maximumItemRequirements;
   /** @var CEbayMaximumUnpaidItemStrikesInfoType */
   protected $maximumUnpaidItemStrikesInfo;
   /** @var int */
   protected $minimumFeedbackScore;
   /** @var bool */
   protected $shipToRegistrationCountry;
   /** @var CEbayVerifiedUserRequirementsType */
   protected $verifiedUserRequirements;
   /** @var bool */
   protected $zeroFeedbackScore;


}
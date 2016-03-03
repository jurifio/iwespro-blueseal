<?php

namespace bamboo\addon\ebay\api\trading\calls\types;
use bamboo\addon\ebay\core\IEbayValueType;


/**
 * Class CEbayCharityType
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
class CEbayCharityType
{
    /** @var string */
    protected $charityID;
    /** @var bool */
    protected $charityListing;
    /** @var string */
    protected $charityName;
    /** @var int */
    protected $charityNumber;
    /** @var float */
    protected $donationPercent;
    /** @var string */
    protected $logoURL;
    /** @var string */
    protected $mission;
    protected $status;

}
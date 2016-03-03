<?php

namespace bamboo\addon\ebay\trading\calls;

/**
 * Class CEbayDiscountPriceInfoType
 * @package redpanda\blueseal\ebay\calls\trading\types
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 02/03/2016
 * @since 1.0
 *
 * TODO: Vedi http://developer.ebay.com/Devzone/XML/docs/Reference/ebay/AddFixedPriceItem.html#Request.Item.DiscountPriceInfo
 */
class CEbayDiscountPriceInfoType
{
    /** @var CEbayAmountType */
    protected $madeForOutletComparisonPrice;
    protected $minimumAdvertisedPrice;
    protected $minimumAdvertisedPriceExposure;
    protected $originalRetailPrice;
    protected $pricingTreatment;
    protected $soldOffeBay;
    protected $soldOneBay;
}
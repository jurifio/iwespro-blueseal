<?php

namespace bamboo\addon\ebay\api\trading\calls\types;

use bamboo\addon\ebay\core\AEbayCategory;
use bamboo\addon\ebay\core\AEbaySite;

/**
 * Class CEbayItemType
 * @package redpanda\blueseal\ebay\calls\trading\types
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/03/2016
 * @since 1.0
 */
class CEbayItemType
{
    /** @var string */
    protected $applicationData;
    /** @var bool */
    protected $autoPay;
    /** @var CEbayBuyerRequirementDetailsType */
    protected $buyerRequirementDetails;
    /** @var bool */
    protected $categoryBasedAttributePrefill;
    /** @var bool */
    protected $categoryMappingAllowed;
    /** @var CEbayCharityType */
    protected $charity;
    /** @var string */
    protected $conditionDescription;
    /** @var int */
    protected $conditionId;
    /** @var CEBayCountryCodeEnum */
    protected $country;
    /** @var array (string values) */
    protected $crossBorderTrade;
    /** @var  CEBayCountryCodeEnum */
    protected $currency;
    /** @var string */
    protected $description;
    /** @var CEbayDigitalGoodInfoType */
    protected $digitalGoodInfo;
    /** @var bool */
    protected $disableBuyerRequirements;
    /** @var CEbayDiscountPriceInfoType */
    protected $discountPriceInfo;
    /** @var int */
    protected $dispatchTimeMax;
    /** @var bool */
    protected $eBayNowEligible;
    /** @var bool */
    protected $eBayPlus;
    /** @var int */
    protected $giftIcon;
    /** @var array (contains CEbayGiftServicesCodeEnum) */
    protected $giftServices;
    /** @var CEbayHitCounterCodeEnum */
    protected $hitCounter;
    /** @var bool */
    protected $includeRecommendations;
    /** @var CEbayInventoryTrackingMethodCodeEnum */
    protected $inventoryTrackingMethod;
    /** @var CEbayItemCompatibilityListType */
    protected $itemCompatibilityList;
    /** @var CEbayNameValueListArrayType */
    protected $itemSpecifics;
    /** @var CEbayListingCheckoutRedirectPreferenceType */
    protected $listingCheckoutRedirectPreference;
    /** @var CEbayListingDesignerType */
    protected $listingDesigner;
    /** @var string */
    protected $listingDuration;
    /** @var string */
    protected $location;
    /** @var array (contains CEbayBuyerPaymentMethodCodeEnum) */
    protected $paymentMethods;
    /** @var string */
    protected $payPalEmailAddress;
    /** @var CEbayPickupInStoreDetailsType */
    protected $pickupInStoreDetails;
    /** @var CEbayPictureDetailsType */
    protected $pictureDetails;
    /** @var string */
    protected $postalCode;
    /** @var bool */
    protected $postCheckoutExperienceEnabled;
    /** @var CEbayCategoryType */
    protected $primaryCategory;
    /** @var bool */
    protected $privateListing;
    /** @var string */
    protected $privateNotes;
    /** @var CEbayProductListingDetailsType */
    protected $productListingDetails;
    /** @var int */
    protected $quantity;
    /** @var CEbayQuantityInfoType */
    protected $quantityInfo;
    /** @var CEbayQuantityRestrictionPerBuyerInfoType */
    protected $quantityRestrictionPerBuyer;
    /** @var CEbayReturnPolicyType */
    protected $returnPolicy;
    /** @var string (datetime) */
    protected $scheduleTime;
    /** @var CEbayCategoryType */
    protected $secondaryCategory;
    /** @var CEbaySellerProfilesType */
    protected $sellerProfiles;
    /** @var string */
    protected $sellerProvidedTitle;
    /** @var CEbayShippingDetailsType */
    protected $shippingDetails;
    /** @var CEbayShipPackageDetailsType */
    protected $shippingPackageDetails;
    /** @var CEbayShippingServiceCostOverrideListType */
    protected $shippingServiceCostOverrideList;
    /** @var bool */
    protected $shippingTermsInDescription;
    /** @var array (contains string) */
    protected $shipToLocation;
    /** @var CEbaySiteCodeEnum */
    protected $site;
    /** @var string */
    protected $sku;
    /** @var CEbaySkypeContactOptionCodeEnum */
    protected $skypeContactOption;
    /** @var bool */
    protected $skypeEnabled;
    /** @var string */
    protected $skypeId;
    /** @var CEbayAmountType */
    protected $startPrice;
    /** @var CEbayStoreFrontType */
    protected $storeFront;
    /** @var string */
    protected $subTitle;
    /** @var string */
    protected $taxCategory;
    /** @var bool */
    protected $thirdPartyCheckout;
    /** @var bool */
    protected $thirdPartyCheckoutIntegration;
    /** @var string */
    protected $title;
    /** @var bool */
    protected $useRecommendedProduct;
    /** @var bool */
    protected $useTaxTable;
    /** @var string */
    protected $uuid;
    /** @var CEbayVariationType */
    protected $variations;
    /** @var CEbayVatDetailsType */
    protected $vatDetails;
    /** @var string */
    protected $vin;
    /** @var string */
    protected $vrm;

    /**
     * @param AEbayCategory $category
     */
    public function setConditionId(AEbayCategory $category)
    {
        /**
         * L'id della condizione dipende dalla categoria in cui va inserito
         * il prodotto. Il metodo non va bene, la firma è sbagliata
         * ma lo teniamo come placeholder per ora.
         **/
    }

    /**
     * @param AEbaySite $site
     */
    public function setCrossBorderTrade(AEbaySite $site)
    {
        /**
         * Se il sito è US, UK, Canada e Irlanda può settare questo valore sennò no, si controlla
         * facendo $side->getIdentity() (il metodo è solo a scopo esemplificativo)
         *
         * Quindi provando a settare questo valore per un sito che non lo supporta viene tornato false
         *
         * Oppure anche meglio si potrebbe mettere una lista di valori non settabili (o settabili?
         * quello che si fa prima) all'interno dell'oggetto che estende AEbaySite. Magari un array
         * tipo AEbaySite::notAllowed = ['CrossBorderTrade','bla','bla']; che poi si controlla...
         */
    }
}
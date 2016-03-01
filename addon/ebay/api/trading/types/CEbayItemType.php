<?php

namespace redpanda\blueseal\ebay\calls\trading\types;
use bamboo\addon\ebay\core\AEbaySite;
use redpanda\blueseal\ebay\calls\trading\enum\CEBayCountryCodeEnum;

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
    protected $ApplicationData;
    /** @var bool */
    protected $AutoPay;
    /** @var bool */
    protected $CategoryBasedAttributePrefill;
    /** @var bool */
    protected $CategoryMappingAllowed;
    /** @var string */
    protected $ConditionDescription;
    /** @var int */
    protected $ConditionID;
    /** @var CEBayCountryCodeEnum */
    protected $CountryCodeType;
    /** @var string */
    protected $CrossBorderTrade;

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
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
 */
class CEbayAmountType implements IEbayValueType
{
    /** @var double */
    protected $value;
    /** @var CEBayCurrencyCodeEnum */
    protected $currencyId;

    /** @return float */
    public function getValue()
    {
        return $this->value;
    }

    /** @return array */
    public function getProperties()
    {
        return ['currencyID'=>$this->currencyId->getValue()];
    }
}
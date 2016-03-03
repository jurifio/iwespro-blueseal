<?php

namespace bamboo\addon\ebay\api\trading\calls\types;
use bamboo\addon\ebay\core\IEbayValueType;
use bamboo\addon\ebay\api\trading\enum\CEbayCurrencyCodeEnum;

/**
 * Class CEbayAmountType
 * @package bamboo\addon\ebay\api\trading\calls\types
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
    /** @var CEbayCurrencyCodeEnum */
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
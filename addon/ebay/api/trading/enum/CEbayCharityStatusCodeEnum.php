<?php

namespace bamboo\addon\ebay\api\trading\enum;

use bamboo\core\base\CEnum;

/**
 * Class CEbayCharityStatusCodeEnum
 * @package bamboo\addon\ebay\api\trading\enum
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
class CEbayCharityStatusCodeEnum extends CEnum
{
    const CustomCode = 'Custom Code';
    const NoLongerValid = 'NoLongerValid';
    const Valid = 'Valid';
}
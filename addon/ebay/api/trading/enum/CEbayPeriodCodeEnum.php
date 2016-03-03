<?php

namespace bamboo\addon\ebay\api\trading\enum;

use bamboo\core\base\CEnum;

/**
 * Class CEBayPeriodCodeEnum
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
class CEBayPeriodCodeEnum extends CEnum
{
    const CustomCode = 'Custom Code';
    const Days_1 = 'Days_1';
    const Days_180 = 'Days_180';
    const Days_30 = 'Days_30';
    const Days_360 = 'Days_360';
    const Days_540 = 'Days_540';
}
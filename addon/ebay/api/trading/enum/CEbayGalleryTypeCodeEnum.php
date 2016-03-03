<?php

namespace bamboo\addon\ebay\api\trading\enum;

use bamboo\core\base\CEnum;

/**
 * Class CEbayGalleryTypeCodeEnum
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
class CEbayGalleryTypeCodeEnum extends CEnum
{
    const CustomCode = 'CustomCode';
    const Featured = 'Featured';
    const Gallery = 'Gallery';
    const None = 'None';
    const Plus = 'Plus';
}
<?php

namespace bamboo\addon\ebay\api\trading\enum;

use bamboo\core\base\CEnum;

/**
 * Class CEbayPhotoDisplayCodeEnum
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
class CEbayPhotoDisplayCodeEnum extends CEnum
{
    const CustomCode = 'CustomCode';
    const None = 'None';
    const PicturePack = 'PicturePack';
    const SuperSize = 'SuperSize';
    const SuperSizePictureShow = 'SuperSizePictureShow';
}
<?php

namespace bamboo\addon\ebay\api\trading\enum;

use bamboo\core\base\CEnum;

/**
 * Class CEbayGalleryStatusCodeEnum
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
class CEbayGalleryStatusCodeEnum extends CEnum
{
    const CustomCode = 'CustomCode';
    const ImageNonExistent = 'ImageNonExistent';
    const ImageProcessingError = 'ImageProcessingError';
    const ImageReadTimeOut = 'ImageReadTimeOut';
    const InvalidFile = 'InvalidFile';
    const InvalidFileFormat = 'InvalidFileFormat';
    const InvalidProtocol ='InvalidProtocol';
    const InvalidUrl = 'InvalidUrl';
    const Pending = 'Pending';
    const ServerDown ='ServerDown';
    const Success ='Success';
}
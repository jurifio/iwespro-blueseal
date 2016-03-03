<?php

namespace bamboo\addon\ebay\api\trading\calls\types;

use bamboo\addon\ebay\api\trading\enum\CEbayGalleryStatusCodeEnum;
use bamboo\addon\ebay\api\trading\enum\CEbayGalleryTypeCodeEnum;
use bamboo\addon\ebay\api\trading\enum\CEbayPhotoDisplayCodeEnum;
use bamboo\addon\ebay\api\trading\enum\CEbayPictureSourceCodeEnum;

/**
 * Class CEbayPictureDetailsType
 * @package bamboo\addon\ebay\api\trading\calls\types
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

class CEbayPictureDetailsType
{
   /** @var CEbayExtendedPictureDetailsType */
   protected $extendedPictureDetails;
   /** @var anyURI */
   protected $externalPictureURL;
   /** @var token */
   protected $galleryDuration;
   /** @var string */
   protected $galleryErrorInfo;
   /** @var CEbayGalleryStatusCodeEnum */
   protected $galleryStatus;
   /** @var CEbayGalleryTypeCodeEnum */
   protected $galleryType;
   /** @var  anyURI */
   protected $galleryURL;
   /** @var CEbayPhotoDisplayCodeEnum */
   protected $photoDisplay;
   /** @var CEbayPictureSourceCodeEnum */
   protected $pictureSource;
   /** @var anyURI */
   protected $pictureURL;

}
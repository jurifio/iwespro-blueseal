<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

/**
 * Class CWorkCategory
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 14/03/2018
 * @since 1.0
 *
 * @property CWorkCategorySteps $workCategorySteps
 */
class CWorkCategory extends AEntity
{
    const NORM = 1;
    const BRAND = 2;
    const NAME_ENG = 3;
    const NAME_DTC = 4;
    const TXT_FAS = 5;
    const TXT_FAS_BLOG = 6;
    const TXT_INFL = 7;
    const TXT_PRT = 8;
    const TXT_BRAND = 9;
    const TXT_FB = 10;
    const DET_ENG = 11;
    const DET_DTC = 12;
    const DET_RUS = 14;
    const DET_CHI = 15;
    const DET_FRE = 16;
    const NAME_FRE = 17;
    const NAME_RUS =18;
    const NAME_CHI =19;
    const TXT_COPY_BLOG_POST=20;
    const TXT_COPY_BRAND=21;
    const TXT_FB_CR =22;
    const TXT_FB_VID =23;
    const TXT_IN_PHOTO_FEED =24;
    const TXT_IN_PHOTO_STORY=25;
    const TXT_IN_VIDEO_FEED=26;
    const TXT_IN_VIDEO_STORY=27;
    const POST_YOUTUBE_VIDEO=28;
    const POST_TWITTER_VIDEO=31;
    const POST_TIKTOK_VIDEO=29;
    const POST_WHATSAPP =30;
    const STREAM_TWITCH=32;
    const STREAM_YOUTUBE=33;

    const SLUG_EMPTY_NORM = 'prodotti';
    const SLUG_EMPTY_BRAND = 'brands';
    const SLUG_EMPTY_TRANS = 'traduzione-nomi';

    protected $entityTable = 'WorkCategory';
    protected $primaryKeys = ['id'];
}
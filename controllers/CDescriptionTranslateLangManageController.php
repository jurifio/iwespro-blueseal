<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;
use bamboo\core\utils\slugify\CSlugify;

/**
 * Class CDescriptionTranslateLangManageController
 * @package bamboo\app\controllers
 */
class CDescriptionTranslateLangManageController extends ARestrictedAccessRootController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";
    /**
     * @var string
     */
    protected $pageSlug = "description_translate_lang";

    /**
     *
     */
    public function put()
    {

    }

}
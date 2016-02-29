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
        $marketplaceId = $this->app->router->request()->getRequestData('marketplaceId');
        $brandId = $this->app->router->request()->getRequestData('brandId');
        $langId = $this->app->router->request()->getRequestData('langId');
        $description = $this->app->router->request()->getRequestData('Description');

        try {
            $productDecription = $this->app->repoFactory->create('ProductDescriptionTranslation')->findBy(['marketplaceId'=>$marketplaceId,'langId'=>$langId]);
            foreach($productDecription as $descr) {
                $descr->description = $description;
                $descr->update();
            }

        } catch (\Exception $e) {
            $this->app->router->response()->raiseUnauthorized();
        }
     }

}
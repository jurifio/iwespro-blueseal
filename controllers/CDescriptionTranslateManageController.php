<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\core\traits\TFormInputValidate;


/**
 * Class CDescriptionTranslateManageController
 * @package redpanda\blueseal\controllers
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CDescriptionTranslateManageController extends ARestrictedAccessRootController
{
    use TFormInputValidate;

    protected $fallBack = "blueseal";

    protected $pageSlug = "description_translate_edit";

    public function put()
    {

        $post = $this->app->router->request()->getRequestData();

        $productId = $this->app->router->request()->getRequestData('Product_id');
        $productVariantId = $this->app->router->request()->getRequestData('Product_variantId');
        $productIds = ["id" => $productId, "productVariantId" => $productVariantId];

        /** LOGICHE DI UPDATE*/
        try {
            \Monkey::app()->repoFactory->beginTransaction();
            $descRepo = \Monkey::app()->repoFactory->create('ProductDescriptionTranslation');
            foreach ($post as $key => $val) {
                $k = explode('_', $key);
                if ($k[0] != 'ProductDescription') continue;
                $descEdit = $descRepo->findOneBy(['productId' => $productId, 'productVariantId' => $productVariantId, 'marketplaceId' => 1, 'langId' => $k[1]]);

                if (!is_null($descEdit)) {
                    $descEdit->description = $val;
                    $descEdit->update();
                } else {
                    $descEdit = $this->app->entityManagerFactory->create('ProductDescriptionTranslation')->getEmptyEntity();
                    $descEdit->productId = $productId;
                    $descEdit->productVariantId = $productVariantId;
                    $descEdit->marketplaceId = 1;
                    $descEdit->langId = $k[1];
                    $descEdit->description = $val;
                    $descEdit->insert();
                }            }
            \Monkey::app()->repoFactory->commit();
            return json_encode($productIds);
        } catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();
            throw $e;
        }

    }
}
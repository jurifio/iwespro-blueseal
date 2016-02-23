<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\exceptions\RedPandaException;


/**
 * Class CDescriptionTranslateManageController
 * @package redpanda\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CDescriptionTranslateManageController extends ARestrictedAccessRootController
{

    protected $fallBack = "blueseal";

    protected $pageSlug = "desciption_translate_edit";

    public function put()
    {

        $post = $this->app->router->request()->getRequestData();
        \BlueSeal::dump($post);
        $productId = $this->app->router->getMatchedRoute()->getComputedFilters();;
        $productVariantId = $this->app->router->getMatchedRoute()->getComputedFilters('productVariantId');
        \BlueSeal::dump($productId);
        \BlueSeal::dump($productVariantId);
        throw new \Exception();

        /** LOGICHE DI UPDATE*/
        try {
            $this->app->dbAdapter->beginTransaction();
            $descRepo = $this->app->repoFactory->create('ProductDescriptionTranslation');
            foreach ($post as $key => $val) {
                $k = explode('_', $key);
                if ($k[0] != 'ProductDescription') continue;
                $descEdit = $descRepo->findOne(['productId' => $productId, 'productVariantId' => $productVariantId, 'marketplaceId' => 1, 'langId' => $k[1]]);
                if ($descEdit) {
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
                }
            }
            $this->app->dbAdapter->commit();
            return true;
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
            return false;
        }

    }
}
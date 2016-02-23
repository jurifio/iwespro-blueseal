<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\exceptions\RedPandaAssetException;


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

    protected $pageSlug = "description_translate_edit";

    public function put()
    {

        $post = $this->app->router->request()->getRequestData();

        $productId = $this->app->router->request()->getRequestData('Product_id');
        $productVariantId = $this->app->router->request()->getRequestData('Product_variantId');


        /** LOGICHE DI UPDATE*/
        //try {
            $this->app->dbAdapter->beginTransaction();
            $descRepo = $this->app->repoFactory->create('ProductDescriptionTranslation');
            foreach ($post as $key => $val) {
                $k = explode('_', $key);
                if ($k[0] != 'ProductDescription') continue;
                $descEdit = $descRepo->findOneBy(['productId' => $productId, 'productVariantId' => $productVariantId, 'marketplaceId' => 1, 'langId' => $k[1]]);
                \BlueSeal::dump($descEdit);
                throw new \Exception();
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
            //$this->app->dbAdapter->commit();
            //return true;
        //} catch (\Exception $e) {
          //  $this->app->dbAdapter->rollBack();
            //return false;
        //}

    }
}
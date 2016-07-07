<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;
use bamboo\core\utils\slugify\CSlugify;

/**
 * Class CNameTranslateManageController
 * @package redpanda\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CNameTranslateManageController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";

    public function put()
    {
        $datas = $this->app->router->request()->getRequestData();

        $this->app->dbAdapter->beginTransaction();
        try {

            $trans = [];
            foreach ($datas as $key => $val) {
                $keys = explode('_', $key);
                $transNames[$keys[1]] = trim($val);
            }

            $productNameTranslation = $this->app->repoFactory->create('ProductNameTranslation')->findBy(['name' => $transNames[1], 'langId' => 1]);

            if (iterator_count($productNameTranslation)) {
                foreach ($transNames as $langId => $name) {
                    foreach($productNameTranslation as $dbRow) {
                        if (1 == $langId) {
                            $dbRow->name = $name;
                            $dbRow->update();
                        } else {
                        $productNameAssignTranslation = $this->app->repoFactory->create('ProductNameTranslation')->findBy(
                            [
                                'productId' => $dbRow->productId,
                                'productVariantId' => $dbRow->productVariantId,
                                'langId' => $langId
                            ]);
                            $iterCount = iterator_count($productNameAssignTranslation);
                        if ($iterCount) {
                            foreach ($productNameAssignTranslation as $vAssign) {
                                $vAssign->name = $name;
                                $vAssign->update();
                            }
                        } else {
                            $newName = $this->app->repoFactory->create('ProductNameTranslation')->getEmptyEntity();
                            $newName->name = $name;
                            $newName->productId = $dbRow->productId;
                            $newName->ProductVariantId = $dbRow->productVariantId;
                            $newName->langId = $langId;
                            $newName->insert();
                        }
                        
                    }

                }
            }

        }
            $this->app->dbAdapter->commit();
            return true;
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
            throw new \Exception($e->getMessage());
        }

}

}
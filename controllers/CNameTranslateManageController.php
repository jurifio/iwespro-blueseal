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
            foreach ($datas as $key => $val) {
                if ($key == 'ProductId') continue;
                if ($key == 'ProductVariantId') continue;

                $keys = explode('_', $key);
                $langId = $keys[1];
                $name = $val;


                $productName = $this->app->repoFactory->create('ProductNameTranslation')->findOneBy(['productId'=>$datas['ProductId'], 'productVariantId'=>$datas['ProductVariantId'], 'langId'=>$langId]);

                if (!is_null($productName)) {
                    $productName->name = $name;
                    $productName->update();

                } elseif ($name != "") {
                    $productName = $this->app->repoFactory->create("ProductNameTranslation")->getEmptyEntity();

                    $productName->productId = $datas['ProductId'];
                    $productName->productVariantId = $datas['ProductVariantId'];
                    $productName->langId = $langId;
                    $productName->name = $name;
                    $productName->insert();
                }
            }
            $this->app->dbAdapter->commit();
            return true;
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
        }

    }

}
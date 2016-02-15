<?php
namespace bamboo\controllers;

use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;
use bamboo\core\utils\slugify\CSlugify;

/**
 * Class CDetailTranslateManageController
 * @package bamboo\blueseal\controllers
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
class CDetailTranslateManageController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";

    public function put()
    {
        $blueseal = $this->app->baseUrl(false) . '/blueseal';
        $datas = $this->app->router->request()->getRequestData();

        $this->app->dbAdapter->beginTransaction();
        try {
            foreach ($datas as $key => $val) {
                if ($key == 'ProductDetailId') continue;
                $keys = explode('_', $key);
                $langId = $keys[1];
                $name = $val;

                $productDetail = $this->app->repoFactory->create('ProductDetailTranslation')->findOneBy(['productDetailId'=>$datas['ProductDetailId'], 'langId'=>$langId]);

                if (!is_null($productDetail)) {
                    $productDetail->name = $name;
                    $productDetail->update();

                } elseif ($name != "") {
                    $productDetail = $this->app->repoFactory->create("ProductDetailTranslation")->getEmptyEntity();

                    $productDetail->productDetailId = $datas['ProductDetailId'];
                    $productDetail->langId = $langId;
                    $productDetail->name = $name;
                    $productDetail->insert();
                }
            }
            $this->app->dbAdapter->commit();
            return true;
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
        }

    }

}
<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CObjectCollection;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;

/**
 * Class CSkuManageController
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
class CSkuManageController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_sku";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/sku_add.php');
        /** @var $em CEntityManager * */

        $productEdit = null;
        $productSizeGroup = null;
        $productSkuEdit = null;

        $id = $this->app->router->request()->getRequestData('id');
        $productVariantId = $this->app->router->request()->getRequestData('productVariantId');
        $productEdit = $this->app->repoFactory->create('Product')->findOneBy(['id'=>$id, 'productVariantId' => $productVariantId]);

        $productSizeGroup = $productEdit->productSizeGroup;
        $productSkuEdit = $productEdit->productSku;

        $em = $this->app->entityManagerFactory->create('Shop');
        if (!$this->app->getUser()->hasPermission('allShops')) {
            $shop = $em->findBySql("SELECT id FROM Shop, UserHasShop WHERE id = UserHasShop.shopId AND UserHasShop.userId = ?", array($this->app->getUser()->getId()));
            if ($shop instanceof CObjectCollection) {
                $shop = $shop->getFirst();
            }
        } else {
            try {
                $shop = $em->findOne(array($productEdit->productSku->getFirst()->shopId));
                if ($shop instanceof CObjectCollection) {
                    $shop = $shop->getFirst();
                }
            } catch (\Throwable $e) {
                $shop = null;
            }
        }

        if ($shop == null) {
            $shop = $productEdit->shop->getFirst();
        }

        return $view->render([
            'shop' => $shop,
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'productEdit' => $productEdit,
            'productSizeGroup' => $productSizeGroup,
            'productSkuEdit' => $productSkuEdit,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function post()
    {
        $blueseal = $this->app->baseUrl(false) . '/blueseal/';

        $post = $this->app->router->request()->getRequestData();
        /** @var CMySQLAdapter $mysql */
		$done =0 ;
        foreach ($post as $key => $val) {
            $keys = explode('_', $key);
            if (count($keys) != 3) continue;
            if ($keys[0] != 'ProductSku' || $keys[1] != 'stockQty') continue;
            try {
                $productSku = $this->app->repoFactory->create("ProductSku")->findOneBy(['productId' => $post['id'], 'productVariantId' => $post['productVariantId'], 'shopId' => $post['shopId'], 'productSizeId' => $keys[2]]);

                if (!is_null($productSku)) {
                    $productSku->stockQty = empty($val) ? 0 : $val;
                    $productSku->value = $post['ProductSku_value_' . $keys[2]];
                    $productSku->price = $post['ProductSku_price_' . $keys[2]];
                    $productSku->salePrice = $post['ProductSku_salePrice_' . $keys[2]];
                    $productSku->isOnSale = isset($post['isOnSale']) ? 1 : 0;
	                $productSku->update();
	                $this->app->eventManager->triggerEvent('product.stock.change',['productKeys'=>$productSku->product->printId()]);
	                $done++;
                } else {
                    if (!empty($val)) {
                        $productSku = $this->app->repoFactory->create("ProductSku")->getEmptyEntity();

                        $productSku->productId = $post['id'];
                        $productSku->productVariantId = $post['productVariantId'];
                        $productSku->shopId = $post['shopId'];
                        $productSku->productSizeId = $keys[2];
                        $productSku->stockQty = $val;
                        $productSku->value = $post['ProductSku_value_' . $keys[2]];
                        $productSku->price = $post['ProductSku_price_' . $keys[2]];
                        $productSku->salePrice = $post['ProductSku_salePrice_' . $keys[2]];
                        $productSku->isOnSale = isset($post['isOnSale']) ? 1 : 0;
	                    $productSku->insert();
	                    $done++;
	                    $this->app->eventManager->triggerEvent('product.stock.change',['productKeys'=>$productSku->product->printId()]);
                    }
                }


            } catch (\Throwable $e) {
                $this->app->router->response()->raiseProcessingError();
            }
        }
        return json_encode($done);
    }
}
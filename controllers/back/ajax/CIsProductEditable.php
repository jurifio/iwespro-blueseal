<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\theming\CWidgetHelper;

/**
 * Class CUserList
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/07/2016
 * @since 1.0
 */
class CisProductEditable extends AAjaxController
{
    public function get()
    {
        /** @var CUserRepo */

        $wh = new CWidgetHelper(\Monkey::app());

        $user = $this->app->getUser();
        $worker = $user->hasPermission('worker');
        $get = $this->app->router->request()->getRequestData();
        $ret = [];
        $product = \Monkey::app()->repoFactory->create('Product');
        if (array_key_exists('itemno', $get) && array_key_exists('variantName', $get) && array_key_exists('brandId', $get)) {
            if (!$get['itemno'] || !$get['variantName'] || !$get['brandId']) throw new \Exception("All parameters are needed");
            $res = $this->app->dbAdapter->query(
                'SELECT p.id AS id, p.productVariantId AS productVariantId FROM Product p JOIN ProductVariant pv ON p.productVariantId = pv.id WHERE p.itemno = ? AND pv.name LIKE ? AND p.productBrandId = ?',
                [$get['itemno'], $get['variantName'], $get['brandId']]
            )->fetch();
        } elseif (array_key_exists('id', $get) && array_key_exists('productVariantId', $get)) {
            $prod = $this->rfc('Product')->findOneBy(['id' => $get['id'], 'productVariantid' => $get['productVariantId']]);
            if ($prod) {
                $res = [];
                $res['id'] = $prod->id;
                $res['productVariantId'] = $prod->productVariantId;
            } else {
                $res = false;
            }
        }
        if ($res) {

            $productEdit = $product->findOne($res);
            //controllo lo stato
            $productStatus = $productEdit->productStatusId;

            $userShops = [];
            foreach ($user->shop as $v) {
                $userShops[] = $v->id;
            }
            $productShops = [];
            foreach ($productEdit->shop as $v) {
                $productShops[] = $v->id;
            }

            $productArr = $productEdit->fullTreeToArray();
            $productArr['variantName'] = $productEdit->productVariant->name;
            $productArr['variantDescription'] = $productEdit->productVariant->description;
            $productArr['productColorGroupId'] = $productEdit->productColorGroupId;
            $catIds = [];
            foreach($productEdit->productCategory as $c) {
                $catIds[] = $c->id;
            }
            $productArr['productCategories'] = implode(',', $catIds);
            $name = $productEdit->productNameTranslation->getFirst();
            $productArr['productName'] = ($name) ? $name->name : '' ;

            if (!$user->hasPermission('allShops') && !$worker) {
                foreach ($user->shop as $s) {
                    $shopId = $s->id;
                    break;
                }
                if ($shp = $productEdit->shopHasProduct->findOneByKey('shopId', $shopId)) {
                    $productArr['extId'] = $shp->extId;
                }
            }

            $desc = $productEdit->productDescriptionTranslation->findOneByKey('langId', 1);
            $productArr['productDescription'] = ($desc) ? $desc->description : '';

            $productArr['price'] = "";
            $productArr['value'] = "";

            if (!$user->hasPermission('allShops') && !$worker) {
                $shop = $user->shop;
                $shopId = 0;
                foreach ($shop as $k => $v) {
                    $shopId = $v->id;
                }

                $shp = $this->rfc('ShopHasProduct')->findOneBy(['productId' => $productEdit->id, 'productVariantId' => $productEdit->productVariantId, 'shopId' => $shopId]);
                if ($shp) {
                    $productArr['price'] = ($shp->price) ? $shp->price : '';
                    $productArr['value'] = ($shp->value) ? $shp->value : '';
                    $productArr['extId'] = ($shp->extId) ? $shp->extId : '';
                }
            }
            $editable = false;
            $message = 'Il prodotto è già presente nel nostro cataolgo. Puoi modificarne il prezzo e le quantità';
            if (((count($intersect = array_intersect($userShops, $productShops))) && (($productStatus == 2) || ($productStatus == 11))) || ($this->app->getUser()->hasPermission('allShops') || $this->app->getUser()->hasPermission('worker'))) {
                $editable = true;
                $message = false;
            }

            $productArr['link'] = $wh->productUrl($productEdit);

            $skuEditable = true;
            if ($productEdit->productSku->count()) $skuEditable = false;

            $ret = ['code' => $productEdit->id . '-' . $productEdit->productVariantId, 'product' => $productArr, 'skuEditable' => $skuEditable, 'editable' => $editable, 'repo' => true, 'message' => $message];
        } else {
            $ret = ['editable' => true, 'repo' => true, 'message' => 'Il prodotto non esiste, puoi inserirlo ora.'];
        }
        $this->app->router->response()->setContentType('application/json');
        return json_encode($ret);
    }
}
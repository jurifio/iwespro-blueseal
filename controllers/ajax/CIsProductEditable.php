<?php
namespace bamboo\blueseal\controllers\ajax;

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
        $user = $this->app->getUser();
        $get = $this->app->router->request()->getRequestData();
        $ret = [];
        $product = $this->app->repoFactory->create('Product');
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
            $productArr['productColorGroupId'] = $productEdit->productColorGroup->getFirst()->id;
            $productArr['productName'] = ($name = $productEdit->productNameTranslation->getFirst()->name) ? $name : '';

            $shop = $this->app->getUser()->shop;
            $shopId = 0;
            foreach($shop as $k => $v) {
                $shopId = $v->id;
            }

            $shp = $this->rfc('ShopHasProduct')->findOneBy(['productId' => $productEdit->id, 'productVariantId' => $productEdit->productVariantId, 'shopId' => $shopId]);
            $productArr['price'] = ($shp) ? $shp->price : '';
            $productArr['value'] = ($shp) ? $shp->value : '';

            $editable = false;
            $message = 'Il prodotto è già presente nel nostro cataolgo. Puoi Modificarne le quantità';
            if (((count($intersect = array_intersect($userShops, $productShops))) && ($productStatus == 2)) || ($this->app->getUser()->hasPermission('allShops'))) {
                $editable = true;
                $message = false;
            }
            $ret = ['code' => $productEdit->id . '-' . $productEdit->productVariantId, 'product' => $productArr, 'editable' => $editable, 'repo' => true, 'message' => $message];
        } else {
            $ret = ['editable' => true, 'repo' => true, 'message' => 'Il prodotto non esiste, puoi inserirlo ora.'];
        }
        $this->app->router->response()->setContentType('application/json');
        return json_encode($ret);
    }
}
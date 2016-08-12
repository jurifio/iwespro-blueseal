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
        if (array_key_exists('itemno',$get) && array_key_exists('variantName', $get) && array_key_exists('brandId', $get)) {
            if (!$get['itemno'] || !$get['variantName'] || !$get['brandId']) throw new \Exception("All parameters are needed");
            $res = $this->app->dbAdapter->query(
                'SELECT p.id as id, p.productVariantId as productVariantId FROM Product p JOIN ProductVariant pv on p.productVariantId = pv.id WHERE p.itemno = ? AND pv.name LIKE ? AND p.productBrandId = ?',
                [$get['itemno'], $get['variantName'], $get['brandId']]
            )->fetch();
            if ($res) {

                $productEdit = $product->findOne($res);
                //controllo lo stato
                $productStatus = $productEdit->productStatusId;

                $userShops = [];
                foreach($user->shop as $v) {
                    $userShops[] = $v->id;
                }
                $productShops = [];
                foreach($productEdit->shop as $v) {
                    $productShops[] = $v->id;
                }

                if ((count($intersect = array_intersect($userShops, $productShops))) && ($productStatus == 2)) {
                    $productArr = $productEdit->fullTreeToArray();
                    $productArr['variantName'] = $productEdit->productVariant->name;
                    $productArr['variantDescription'] = $productEdit->productVariant->description;
                    $productArr['productColorGroupId'] = $productEdit->productColorGroup->getFirst()->id;
                    $productArr['productName'] = ($name = $productEdit->productNameTranslation->getFirst()->name) ? $name : '';

                    foreach($productEdit->shopHasProduct as $shp) {
                        if (in_array($shp->shopId, $intersect)) {
                            $productArr['price'] = $shp->price;
                            $productArr['value'] = $shp->value;
                            break;
                        }
                    }
                    //TODO: lettura con gestione di più shop
                    $ret = ['code' => $productArr['id'] . '-' . $productArr['productVariantId'], 'product' => $productArr, 'editable' => true, 'repo' => true];
                } else  {
                    $ret = ['code' => $productEdit->id . '-' . $productEdit->productVariantId, 'editable' => false, 'repo' => true, 'message' => 'Il prodotto è già presente nel nostro cataolgo. Puoi Modificarne le quantità'];
                }
            } else {
                $ret = ['editable' => true, 'repo' => true, 'message' => 'Il prodotto non esiste, puoi inserirlo ora.'];
            }
        } elseif (array_key_exists('id', $get) && (array_key_exists('productVariantId', $get))) {
            $productEdit = $product->findOneBy(['id' => $get['id'], 'productVariantId' => $get['productVariantId']]);
            //TODO
        }
        $this->app->router->response()->setContentType('application/json');
        return json_encode($ret);
    }
}
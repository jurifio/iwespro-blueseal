<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\core\intl\CLang;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\widget\VBase;

/**
 * Class CUserSellRecapController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 2016/04/08
 * @since 1.0
 */
class CProductPriceEdit extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

    public function get() {
        $code = $this->app->router->request()->getRequestData('code');
        try {
            if (!$code) throw new \Exception('Nessun codice pervenuto');

            $prod = $this->rfc('Product')->findOneByStringId($code);
            $shp = $prod->shopHasProduct->toArray();

            $ret = [];
            foreach($prod->productSku as $s) {
                if (!array_key_exists($s['shopId'], $ret)) {
                    $ret[$s['shopId']] = [];
                    $ret[$s['shopId']]['value'] = $s->value;
                    $ret[$s['shopId']]['price'] = $s->price;
                    $ret[$s['shopId']]['salePrice'] = $s->salePrice;
                }
            }

            foreach($shp as $v) {
                if (!array_key_exists($v->shopId, $ret)) {
                    $ret[$v->shopId] = [];
                    $ret[$v->shopId]['value'] = ($v->value) ? $v->value : 0;
                    $ret[$v->shopId]['price'] = ($v->price) ? $v->price : 0;
                    $ret[$v->shopId]['salePrice'] = ($v->salePrice) ? $v->salePrice : 0;
                }
            }
        } catch(\Exception $e) {
            return json_encode($e->getMessage());
        }
        return json_encode($ret);
    }

    public function post() {
        $get = $this->app->router->request()->getRequestData();

        $prices = [];
        //organizzo i dati
        foreach($get as $k => $v) {
            if (("id" !== $k) && ("productVariantId" !== $k)) {
                list($field, $count) = explode('-', $k);
                if (!array_key_exists($count, $prices)) $prices[$count] = [];
                $prices[$count][$field] = $v;
            }
        }

        try {
            $this->app->dbAdapter->beginTransaction();
            $prodRepo = $this->rfc('Product');
            $skuRepo = $this->rfc('ProductSku');
            $shpRepo = $this->rfc('ShopHasProduct');

            if (!$prod = $prodRepo->findOne([$get['id'], $get['productVariantId']])) throw new \Exception('Prodotto non trovato');

            foreach($prices as $v) {
                if ($shp = $shpRepo->findOneBy(['productId' => $get['id'], 'productVariantId' => $get['productVariantId'], 'shopId' => $v['shopId']])) {
                    $shp->price = $v['price'];
                    $shp->value = $v['value'];
                    $shp->salePrice = $v['salePrice'];
                    $shp->update();
                } else {
                    $shp = $shpRepo->getEmptyEntity();
                    $shp->productId = $get['id'];
                    $shp->productVariantId = $get['productVariantId'];
                    $shp->shopId = $v['shopId'];
                    $shp->price = $v['price'];
                    $shp->value = $v['value'];
                    $shp->salePrice = $v['salePrice'];
                    $shp->insert();
                }
                $sku = $skuRepo->findBy(['productId' => $get['id'], 'productVariantId' => $get['productVariantId'], 'shopId' => $v['shopId']]);
                foreach($sku as $s){
                    $s->value = $v['shopId'];
                    $s->price = $v['price'];
                    $s->salePrice = $v['salePrice'];
                    $s->update();
                }
            }
            $this->app->dbAdapter->commit();
        } catch(\Exception $e) {
            $this->app->dbAdapter->rollBack();
            return json_encode($e->getMessage());
        }
        return json_encode(true);
    }
}
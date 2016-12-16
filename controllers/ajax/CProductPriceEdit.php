<?php
namespace bamboo\blueseal\controllers\ajax;

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
            $shopChange = 1; //flag per capire se lo shop può essere modificato o no
            /*foreach($prod->productSku as $s) {
                if (!array_key_exists($s->shopId, $ret)) {
                    $shopChange = 0;
                    $ret[$s->shopId] = [];
                    $ret[$s->shopId]['shopChange'] = $shopChange;
                    $ret[$s->shopId]['extId'] = $s->product->externalId;
                    $ret[$s->shopId]['value'] = $s->value;
                    $ret[$s->shopId]['price'] = $s->price;
                    $ret[$s->shopId]['salePrice'] = $s->salePrice;
                }
            }*/

            foreach($shp as $v) {
                if (!array_key_exists($v->shopId, $ret)) {
                    $ret[$v->shopId] = [];
                    $ret[$v->shopId]['shopChange'] = $shopChange;
                    $ret[$v->shopId]['extId'] = ($v->extId) ? $v->extId : '';
                    $ret[$v->shopId]['value'] = ($v->value) ? str_replace('.', ',', $v->value) : 0;
                    $ret[$v->shopId]['price'] = ($v->price) ? str_replace('.', ',', $v->price) : 0;
                    $ret[$v->shopId]['salePrice'] = ($v->salePrice) ? str_replace('.', ',', $v->salePrice) : 0;
                }
            }
        } catch(\Throwable $e) {
            return json_encode($e->getMessage());
        }
        return json_encode($ret);
    }

    public function post() {
        $get = $this->app->router->request()->getRequestData();

        $prices = $this->parseRequest($get);

        try {
            $this->app->dbAdapter->beginTransaction();
            $prodRepo = $this->rfc('Product');
            $shpRepo = $this->rfc('ShopHasProduct');
            $skuRepo = $this->rfc('ProductSku');

            if (!$prod = $prodRepo->findOne([$get['id'], $get['productVariantId']])) throw new \Exception('Prodotto non trovato');

            foreach($prices as $v) {
                if ($shp = $shpRepo->findOneBy(['productId' => $get['id'], 'productVariantId' => $get['productVariantId'], 'shopId' => $v['shopId']])) {
                    $shp->extId = $v['extId'];
                    $shp->price = $v['price'];
                    $shp->value = $v['value'];
                    $shp->salePrice = $v['salePrice'];
                    $shp->update();
                } else {
                    $shp = $shpRepo->getEmptyEntity();
                    $shp->productId = $get['id'];
                    $shp->productVariantId = $get['productVariantId'];
                    $shp->shopId = $v['shopId'];
                    $shp->extId = $v['extId'];
                    $shp->price = $v['price'];
                    $shp->value = $v['value'];
                    $shp->salePrice = $v['salePrice'];
                    $shp->insert();

                    $shp = $shpRepo->findOneBy(['productId' => $get['id'], 'productVariantId' => $get['productVariantId'], 'shopId' => $v['shopId']]);
                }
                //$shp->updatePrices($v['value'], $v['price'], (array_key_exists('salePrice', $v) ? $v['salePrice'] : 0));
                $skuRepo->updateSkusPrices($shp->productId, $shp->productVariantId, $shp->shopId, $shp->value, $shp->price, $shp->salePrice);
            }
            $this->app->dbAdapter->commit();
            $this->app->eventManager->triggerEvent('product.stock.change',['productKeys'=>$prod->printId()]);
        } catch(\Throwable $e) {
            $this->app->dbAdapter->rollBack();
            return json_encode($e->getMessage());
        }
        return json_encode(true);
    }

    function parseRequest($get) {
        $prices = [];
        //organizzo i dati
        foreach($get as $k => $v) {
            if (("id" !== $k) && ("productVariantId" !== $k)) {
                list($field, $count) = explode('-', $k);
                if (!array_key_exists($count, $prices)) $prices[$count] = [];
                if (('price' == $field) || ('value' == $field) || ('salePrice' == $field)) {
                    $v = str_replace(',', '.', $v);
                }
                $prices[$count][$field] = $v;
            }
        }
        return $prices;
    }

    function delete() {

        $id = $this->app->router->request()->getRequestData('id');
        $productVariantId = $this->app->router->request()->getRequestData('productVariantId');
        $shopId = $this->app->router->request()->getRequestData('shopId');

        $skuRepo = $this->rfc('ProductSku');
        $skuCol = $skuRepo->findBy(['productId' => $id, 'productVariantId' => $productVariantId, 'shopId' => $shopId]);
        if (!$skuCol->count()) {
            $shpRepo = $this->rfc('ShopHasProduct');
            $shp = $shpRepo->findOneBy(['productId' => $id, 'productVariantId' => $productVariantId, 'shopId' => $shopId]);
            if ($shp) $shp->delete();
            return 'ok';
        } else {
            return 'ko';
        }
    }
}
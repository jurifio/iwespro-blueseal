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
class CProductSinglePriceEdit extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

    public function post() {
        $extId = $this->app->router->request()->getRequestData('extId');
        $value = $this->app->router->request()->getRequestData('value');
        $price = $this->app->router->request()->getRequestData('price');
        $id = $this->app->router->request()->getRequestData('id');
        $productVariantId = $this->app->router->request()->getRequestData('productVariantId');

        $user = $this->app->getUser();
        $shop = $user->shop->getFirst();
        try {
            if (!$shop) throw new \Exception('Nessun negozio associato a questo utente');

            $this->app->dbAdapter->beginTransaction();

            $shpRepo = $this->app->repoFactory->create('ShopHasProduct');
            $shp = $shpRepo->findOne([$id, $productVariantId, $shop->id]);

            if (!$shp) {
                $shp = $shpRepo->getEmptyEntity();
                $shp->productId = $id;
                $shp->productVariantId = $productVariantId;
                $shp->shopId = $shop->id;
                $shp->extId = $extId;
                $shp->insert();

            } else {
                $shp->extId = $extId;
                $shp->update();
            }

            $shp = $shpRepo->findOne([$id, $productVariantId, $shop->id]);
            $shp->updatePrices($value, $price);

            $this->app->dbAdapter->commit();
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
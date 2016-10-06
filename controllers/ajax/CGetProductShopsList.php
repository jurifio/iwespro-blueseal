<?php
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CGetProductShopsList extends AAjaxController
{
    public function get()
    {
        $code = $this->app->router->request()->getRequestData('code');

        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $product = $productRepo->findOneByStringId($code);
        if (!$product) return ['status' => 'ko', 'message' => 'Il prodotto cercato non è presente nel catalogo'];

        $friends = [];
        foreach($product->shopHasProduct as $s) {
            $friends[$s->shopId] = [];
            $friends[$s->shopId]['title'] = $s->shop->title;
            $friends[$s->shopId]['price'] = str_replace('.', ',', $s->price);
            $friends[$s->shopId]['salePrice'] = str_replace('.', ',', $s->salePrice);
            $friends[$s->shopId]['value'] = str_replace('.', ',', $s->value);
            $stock = 0;
            foreach($s->productSku as $sku) {
                $stock+= $sku->stockQty + $sku->padding;
            }
            $friends[$s->shopId]['stock'] = $stock;
        }
        $ret = ['status' => 'ok', 'message' => 'Restituito l\'elenco delle categorie', 'friends' => $friends];
        return json_encode($ret);
    }
}
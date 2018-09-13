<?php
namespace bamboo\controllers\back\ajax;



/**
 * Class CCheckProductsToBePublished
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
class CCheckProductsToBePublished extends AAjaxController
{

    public function put()
    {
        $products = \Monkey::app()->repoFactory->create('Product')->findBySql("
          SELECT DISTINCT p.id, p.productVariantId
			FROM Product p,ProductHasProductPhoto phpp,ProductPhoto pp,ProductSku ps,ProductStatus pst
			WHERE p.id = phpp.productId
			  AND p.productVariantId = phpp.productVariantId
              AND p.id = ps.productId
		      AND p.productVariantId = ps.productVariantId
      	      AND p.productStatusId = pst.id
		      AND phpp.productPhotoId = pp.id
		      AND pst.isReady = 1
		      GROUP BY p.id, p.productVariantId HAVING SUM(stockQty) > 0", []);
        $count = 0;
        foreach ($products as $product) {
            $product->productStatusId = 6;

            $count += $product->update();
            /** @var CPrestashopHasProductRepo $phpR */
            $phpR = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
            $phpR->updateProductStatus($product['id'], $product["productVariantId"]);
        }
        return json_encode(
            [
                'bodyMessage' => $count . ' prodotti pubblicati',
                'okButtonLabel' => 'Ok',
                'cancelButtonLabel' => null
            ]);
    }

    public function post()
    {
        $get = $this->app->router->request()->getRequestData();
        $act = $get['action'];
        if (array_key_exists('rows', $get)) $rows = $get['rows'];
        switch ($act) {
            case "updateProductStatus":
                if ($get['productStatusId']) {
                    $count = 0;
                    \Monkey::app()->repoFactory->beginTransaction();
                    try {
                        foreach ($rows as $k => $v) {
                            $product = \Monkey::app()->repoFactory->create('Product')->findOneBy(
                                [
                                    'id' => $v['id'],
                                    'productVariantId' => $v['productVariantId']
                                ]);
                            $product->productStatusId = $get['productStatusId'];
                            $count += $product->update();

                            /** @var CPrestashopHasProductRepo $phpR */
                            $phpR = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
                            $phpR->updateProductStatus($v['id'], $v["productVariantId"]);
                        }
                        \Monkey::app()->repoFactory->commit();
                    } catch (\Throwable $e) {
                        return "Errore nell'aggiornamento dello stato dei prodotti:<br />" .
                            $e->getMessage();
                            "Contattare l'amministratore<br />";
                    }
                return "Aggiornato lo stato di " . $count . " prodotti";
                }
            break;
        }
    }

    public function get()
    {
        $result = $this->app->dbAdapter->query("
         SELECT DISTINCT p.id, p.productVariantId
			FROM Product p,ProductHasProductPhoto phpp,ProductPhoto pp,ProductSku ps,ProductStatus pst
			WHERE p.id = phpp.productId
			  AND p.productVariantId = phpp.productVariantId
              AND p.id = ps.productId
		      AND p.productVariantId = ps.productVariantId
      	      AND p.productStatusId = pst.id
		      AND phpp.productPhotoId = pp.id
		      AND pst.isReady = 1
		      GROUP BY p.id, p.productVariantId HAVING SUM(stockQty) > 0", []);

        $count = count($result->fetchAll());

        if ($count > 0) return json_encode(
            [
                'status' => 'ok',
                'bodyMessage' => 'Sono pronti per la pubblicazione ' . $count . ' nuovi prodotti.',
                'okButtonLabel' => 'Pubblica',
                'cancelButtonLabel' => 'Annulla'
            ]
        );

        return json_encode(
            [
                'status' => 'ko',
                'bodyMessage' => 'Nessun prodotto pubblicabile al momento',
                'okButtonLabel' => 'Ok',
                'cancelButtonLabel' => null
            ]
        );
    }

    public function delete()
    {
        $this->get();
    }
}
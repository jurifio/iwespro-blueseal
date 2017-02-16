<?php
namespace bamboo\blueseal\controllers\ajax;

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
        $products = $this->app->repoFactory->create('Product')->findBySql("
          SELECT DISTINCT Product.id, Product.productVariantId
			FROM Product,ProductHasProductPhoto,ProductPhoto,ProductSku,ProductStatus
			WHERE Product.id = ProductHasProductPhoto.productId
      	      AND Product.productStatusId = ProductStatus.id
		      AND Product.productVariantId = ProductHasProductPhoto.productVariantId
		      AND Product.id = ProductSku.productId
		      AND Product.productVariantId = ProductSku.productVariantId
		      AND ProductHasProductPhoto.productPhotoId = ProductPhoto.id
		      AND ProductStatus.isReady = 1", []);
        $count = 0;
        foreach ($products as $product) {
            $product->productStatusId = 6;
            $count += $product->update();
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
                    $this->app->dbAdapter->beginTransaction();
                    try {
                        foreach ($rows as $k => $v) {
                            $product = $this->app->repoFactory->create('Product')->findOneBy(
                                [
                                    'id' => $v['id'],
                                    'productVariantId' => $v['productVariantId']
                                ]);
                            $product->productStatusId = $get['productStatusId'];
                            $count += $product->update();
                        }
                        $this->app->dbAdapter->commit();
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
         SELECT count(DISTINCT Product.id, Product.productVariantId) as conto
			FROM Product,ProductHasProductPhoto,ProductPhoto,ProductSku,ProductStatus
			WHERE Product.id = ProductHasProductPhoto.productId
      	      AND Product.productStatusId = ProductStatus.id
		      AND Product.productVariantId = ProductHasProductPhoto.productVariantId
		      AND Product.id = ProductSku.productId
		      AND Product.productVariantId = ProductSku.productVariantId
		      AND ProductHasProductPhoto.productPhotoId = ProductPhoto.id
		      AND ProductStatus.isReady = 1", []);

        $count = $result->fetchAll()[0]['conto'];

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
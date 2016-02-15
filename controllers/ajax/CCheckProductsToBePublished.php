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
        $result = $this->app->dbAdapter->query("
          UPDATE Product, ProductHasProductPhoto, ProductPhoto, ProductSku
          SET Product.status = 'P'
          WHERE Product.id = ProductHasProductPhoto.productId
          AND Product.productVariantId = ProductHasProductPhoto.productVariantId
          AND Product.id = ProductSku.productId
          AND Product.productVariantId = ProductSku.productVariantId
          AND ProductHasProductPhoto.productPhotoId = ProductPhoto.id
          AND Product.status IN ('A', 'Q')", []);

        return json_encode(
            [
                'bodyMessage' => $result->countAffectedRows() . ' prodotti pubblicati',
                'okButtonLabel' => 'Ok',
                'cancelButtonLabel' => null
            ]);
    }

    public function post()
    {
        $this->get();
    }

    public function get()
    {
        $result = $this->app->dbAdapter->query("
          SELECT COUNT(DISTINCT Product.id, Product.productVariantId) AS conto
          FROM Product,ProductHasProductPhoto,ProductPhoto,ProductSku
          WHERE Product.id = ProductHasProductPhoto.productId
          AND Product.productVariantId = ProductHasProductPhoto.productVariantId
          AND Product.id = ProductSku.productId
          AND Product.productVariantId = ProductSku.productVariantId
          AND ProductHasProductPhoto.productPhotoId = ProductPhoto.id
          AND Product.status IN ('A', 'Q')", []);

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
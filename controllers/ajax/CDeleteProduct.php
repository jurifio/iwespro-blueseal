<?php
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CDeleteProduct
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
class CDeleteProduct extends AAjaxController
{
    /**
     * @return string
     */
    public function get()
    {
        $em = $this->app->entityManagerFactory->create('Product');

        $ids = [];
        $productVariantId = [];

        $i=0;
        foreach ($this->app->router->request()->getRequestData() as $product) {
            $ids[$i] = explode('__', $product)[0];
            $productVariantId[$i] = explode('__', $product)[1];
            $i++;
        }
        $html = "<table><thead><tr><th>Code</th><th>Immagine</th></tr></thead><tbody>";

        $i=0;
        foreach($ids as $id){
            $conditions = ['id' => $id, 'productVariantId' => $productVariantId[$i]];
            $product = $em->findOneBy($conditions);
            $i++;

            $html .= "<tr><td>" . $product->id . "-" . $product->productVariant->id . "</td><td><img width=\"100\" src=\"/assets/" . $product->dummyPicture . "\"></td></tr>";
        }

        $html .= "</tbody></table>";

        return json_encode(
            [
                'status' => 'ok',
                'bodyMessage' => $html,
                'okButtonLabel' => 'Elimina',
                'cancelButtonLabel' => 'Annulla'
            ]
        );
    }

    /**
     * @return string
     */
    public function delete()
    {
        $em = $this->app->entityManagerFactory->create('Product');

        $id = [];
        $productVariantId = [];
        foreach ($this->app->router->request()->getRequestData() as $product) {
            $id[] = explode('__', $product)[0];
            $productVariantId[] = explode('__', $product)[1];
        }

        $conditions = ['id' => $id, 'productVariantId' => $productVariantId];
        $products = $em->findBy($conditions);

        $deletedProducts['ok'] = [];
        $deletedProducts['ko'] = [];

        foreach ($products as $product) {
            try {
                $product->productStatusId = 8;//'C';
	            $product->update();
                $deletedProducts['ok'][] = $product;
            } catch (\Exception $e) {
                $deletedProducts['ko'][] = $product;
            }
        }

        $html = "<table><thead><tr><th>Code</th><th>Immagine</th><th>Stato</th></tr></thead><tbody>";

        foreach ($deletedProducts['ok'] as $deletedProduct) {
            $html .= "<tr><td>" . $deletedProduct->id . " # " . $deletedProduct->productVariant->id . "</td><td><img width=\"100\" src=\"/assets/" . $deletedProduct->dummyPicture . "\"></td>";
            $html .= "<td>Eliminato</td></tr>";
        }
        foreach ($deletedProducts['ko'] as $deletedProduct) {
            $html .= "<tr><td>" . $deletedProduct->id . " # " . $deletedProduct->productVariant->id . "</td><td><img width=\"100\" src=\"/assets/" . $deletedProduct->dummyPicture . "\"></td>";
            $html .= "<td>Non eliminato</td></tr>";
        }

        $html .= "</tbody></table>";

        return json_encode(
            [
                'status' => 'ok',
                'bodyMessage' => $html,
                'okButtonLabel' => 'Ok',
                'cancelButtonLabel' => null
            ]
        );
    }
}
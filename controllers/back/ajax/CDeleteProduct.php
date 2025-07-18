<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CDeleteProduct
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
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
        $html = "<table><thead><tr><th>Code</th><th>Immagine</th></tr></thead><tbody>";

        $i=0;
        foreach ($this->app->router->request()->getRequestData('id') as $product) {
            $product = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($product);
            $i++;
            $html .= "<tr><td>" . $product->id . "-" . $product->productVariant->id . "</td><td><img width=\"100\" src=\"". $product->getDummyPictureUrl(). "\"></td></tr>";
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
        $deletedProducts['ok'] = [];
        $deletedProducts['ko'] = [];
        foreach ($this->app->router->request()->getRequestData('ids') as $productIds) {
            $product = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($productIds);
            try {
                $product->productStatusId = 8;//'C';
	            $product->update();
                $deletedProducts['ok'][] = $product;
            } catch (\Throwable $e) {
                $deletedProducts['ko'][] = $product;
            }
        }

        $html = "<table><thead><tr><th>Code</th><th>Immagine</th><th>Stato</th></tr></thead><tbody>";

        foreach ($deletedProducts['ok'] as $deletedProduct) {
            $html .= "<tr><td>" . $deletedProduct->id . " # " . $deletedProduct->productVariant->id . "</td><td><img width=\"100\" src=\"" . $deletedProduct->getDummyPictureUrl() . "\"></td>";
            $html .= "<td>Eliminato</td></tr>";
        }
        foreach ($deletedProducts['ko'] as $deletedProduct) {
            $html .= "<tr><td>" . $deletedProduct->id . " # " . $deletedProduct->productVariant->id . "</td><td><img width=\"100\" src=\"" . $deletedProduct->getDummyPictureUrl() . "\"></td>";
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
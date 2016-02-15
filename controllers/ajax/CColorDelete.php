<?php
namespace bamboo\controllers\ajax;

/**
 * Class CBrandDelete
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
class CColorDelete extends AAjaxController
{

    public function get()
    {
        $em = $this->app->entityManagerFactory->create('ProductColorGroup');

        $id = [];

        foreach ($this->app->router->request()->getRequestData() as $colorId) {
            $id[] = explode('__', $colorId)[0];
        }

        $conditions = ['id' => $id];
        $colors = $em->findBy($conditions);
        $html = "<table><thead><tr><th>Nome Colore</th></tr></thead><tbody>";
        foreach ($colors as $color) {
            $html .= "<tr><td>" . $color->name . "</td></tr>";
        }
        $html .= "</tbody></table>";

        return json_encode(
            [
                'status' => 'ok',
                'bodyMessage' => $html,
                'okButtonLabel' => 'Elimina',
                'cancelButtonLabel' => 'Annulla'
            ]);
    }

    /**
     * @return string
     */
    public function delete()
    {
        $em = $this->app->entityManagerFactory->create('ProductColorGroup');
        $id = [];

        foreach ($this->app->router->request()->getRequestData() as $brand) {
            $id[] = explode('__', $brand)[0];
        }

        $conditions = ['id' => $id];
        $products = $em->findBy($conditions);

        $deletedBrands['ok'] = [];
        $deletedBrands['ko'] = [];

        foreach ($products as $brand) {
            try {
                $em->delete($brand);
                $deletedProducts['ok'][] = $brand;
            } catch (\Exception $e) {
                $deletedProducts['ko'][] = $brand;
            }
        }

        $html = "<table><thead><tr><th>Nome Gruppo Colore</th><th>Esito operazione</th></tr></thead><tbody>";

        foreach ($deletedBrands['ok'] as $deletedBrand) {
            $html .= "<tr><td>" . $deletedBrand->nome . "</td>";
            $html .= "<td>Eliminato</td></tr>";
        }
        foreach ($deletedBrands['ko'] as $deletedBrand) {
            $html .= "<tr><td>" . $deletedBrand->nome . "</td>";
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
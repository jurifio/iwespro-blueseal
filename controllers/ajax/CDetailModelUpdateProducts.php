<?php
namespace bamboo\blueseal\controllers\ajax;
use bamboo\core\exceptions\BambooException;

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
class CDetailModelUpdateProducts extends AAjaxController
{
    public function post() {
        $products = $this->app->router->request()->getRequestData('products');
        $idModel = $this->app->router->request()->getRequestData('idModel');
        if ((false === $products) || (false === $idModel)) return 'Non sono stati forniti i prodotti o il modello';

        if (is_string($products)) $products = [$products];

        $pRepo = \Monkey::app()->repoFactory->create('Product');
        $model = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $idModel]);
        try {
            foreach ($products as $p) {
                $product = $pRepo->findOneByStringId($p);
                $product->updateFromModel($model);
            }
        } catch(\Throwable $e){
            return 'OOPS! Errore di sistema:<br />' . $e->getMessage() . '<br />' .
                'Contattare un Amministratore';
        }

        return 'I prodotti sono stati aggiornati!';
    }
}
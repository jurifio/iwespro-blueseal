<?php
namespace bamboo\controllers\back\ajax;
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
        $productName = $this->app->router->request()->getRequestData('productName');
        $prototypeId = $this->app->router->request()->getRequestData('prototypeId');
        $details = $this->app->router->request()->getRequestData('details');
        $category = $this->app->router->request()->getRequestData('category');

        $done = 0;
        if ($idModel) {
            if ((false === $products) || (false === $idModel)) return 'Non sono stati forniti i prodotti o il modello';

            if (is_string($products)) $products = [$products];

            $pRepo = \Monkey::app()->repoFactory->create('Product');
            $model = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $idModel]);
            try {
                foreach ($products as $p) {
                    $pRepo->updateFromModel($model, $pRepo->findOneByStringId($p));
                }
            } catch (\Throwable $e) {
                return 'OOPS! Errore di sistema:<br />' . $e->getMessage() . '<br />' .
                'Contattare un Amministratore';
            }
            $done = 1;
        } elseif ($productName) {
            try {
                $pRepo = \Monkey::app()->repoFactory->create('Product');
                foreach ($products as $p) {
                    $product = $pRepo->findOneByStringId($p);
                    $pRepo->updateDetailsFromData($product, $prototypeId, $details, $productName);
                }
            } catch (\Throwable $e) {
                return 'OOPS! Errore di sistema:<br />' . $e->getMessage() . '<br />' .
                'Contattare un Amministratore';
            }

            $done = 1;
        }

        if (isset($products) and $category) {
            try {
                $phpcRepo = \Monkey::app()->repoFactory->create('ProductHasProductCategory');
                if (is_string($category)) $category = explode(',', $category);
                foreach ($products as $p) {
                    $product = $pRepo->findOneByStringId($p);
                    $phpcOC = $phpcRepo->findBy(['productVariantId' => $product->productVariantId]);
                    foreach ($phpcOC as $phpc) {
                        $phpc->delete();
                    }

                    foreach ($category as $c) {
                        $phpc = $phpcRepo->getEmptyEntity();
                        $phpc->productId = $product->id;
                        $phpc->productVariantId = $product->productVariantId;
                        $phpc->productCategoryId = $c;
                        $phpc->insert();
                    }
                }
            } catch (\Throwable $e) {
            return 'OOPS! Errore di sistema nell\'inserimento delle categorie:<br />' . $e->getMessage() . '<br />' .
            'Contattare un Amministratore';
        }
        }

        if ($done) return 'I prodotti sono stati aggiornati!';
        return 'Fatto niente.';
    }
}
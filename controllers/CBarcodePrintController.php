<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CObjectCollection;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CCategoryListController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CBarcodePrintController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "barcode_print";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/barcode_print.php');

        $this->app->vendorLibraries->load('barcode');
        $generatorSVG = new \Picqer\Barcode\BarcodeGeneratorSVG();

        $productSkus = new CObjectCollection();
        $seen = [];
        switch($this->app->router->request()->getRequestData('source')) {
            case 'movement': {
                foreach ($this->app->router->request()->getRequestData('id') as $storehouseOperationId) {
                    $storehouseOperation = \Monkey::app()->repoFactory->create('StorehouseOperation')->findOneByStringId($storehouseOperationId);
                    foreach ($storehouseOperation->storehouseOperationLine as $storehouseOperationLine) {
                        for($x = 0;$x<abs($storehouseOperationLine->qty);$x++) {
                            $productSkus->add(clone $storehouseOperationLine->productSku);
                        }
                        /*if(array_search($storehouseOperationLine->productSku->printId(),$seen) === false) {

                            $seen[] = $storehouseOperationLine->productSku->printId();
                        }*/
                    }
                }
            }
            break;
            case 'productId': {
                $shopRepo = \Monkey::app()->repoFactory->create('Shop');
                $shopIds = $shopRepo->getAutorizedShopsIdForUser();
                foreach ($this->app->router->request()->getRequestData('id') as $productId) {
                    $product = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($productId);
                    foreach($product->productSku as $sku) {
                        if (in_array($sku->shopId, $shopIds)) {
                            for ($x = 0; $x < abs($sku->stockQty); $x++) {
                                $productSkus->add(clone $sku);
                            }
                        }
                    }
                }
            }
            break;
        }
        $productSkus->reorder('barcode');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'barcodeGenerator' => $generatorSVG,
            'productSkus' => $productSkus
        ]);
    }
}
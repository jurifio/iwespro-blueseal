<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CObjectCollection;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\entities\CEntityManager;

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
                    $storehouseOperation = $this->app->repoFactory->create('StorehouseOperation')->findOneByStringId($storehouseOperationId);
                    foreach ($storehouseOperation->storehouseOperationLine as $storehouseOperationLine) {
                        if(array_search($storehouseOperationLine->productSku->printId(),$seen) === false) {
                            $productSkus->add($storehouseOperationLine->productSku);
                            $seen[] = $storehouseOperationLine->productSku->printId();
                        }
                    }
                }
            }
            break;
            case 'productId': {
                foreach ($this->app->router->request()->getRequestData('id') as $productId) {
                    $product = $this->app->repoFactory->create('Product')->findOneByStringId($productId);
                    foreach($product->productSku as $sku) {
                        if(array_search($sku->printId(),$seen) === false) {
                            $productSkus->add($sku);
                            $seen[] = $sku->printId();
                        }
                    }
                }
            }
            break;
        }


        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'barcodeGenerator' => $generatorSVG,
            'productSkus' => $productSkus
        ]);
    }
}
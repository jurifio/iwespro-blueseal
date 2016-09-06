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
    protected $pageSlug = "product_category_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/barcode_print.php');

        $this->app->vendorLibraries->load('barcode');
        $generatorSVG = new \Picqer\Barcode\BarcodeGeneratorSVG();

        $productSkus = new CObjectCollection();
        $seen = [];
        switch($this->app->router->request()->getRequestData('source')) {
            case 'movements': {
                foreach ($this->app->router->request()->getRequestData('id') as $storageOperationId) {
                    $storageOperation = $this->app->repoFactory->create('StorageOperation')->findOneByStringId($storageOperationId);
                    foreach ($storageOperation->storageOperationLine as $storageOperationLine) {
                        if(array_search($storageOperationLine->productSku->printId(),$seen) === false) {
                            $productSkus->add($storageOperationLine->productSku);
                            $seen[] = $storageOperationLine->productSku->printId();
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
        }


        return $view->render([
            'barcodeGenerator' => $generatorSVG,
            'productSkus' => $productSkus
        ]);
    }
}
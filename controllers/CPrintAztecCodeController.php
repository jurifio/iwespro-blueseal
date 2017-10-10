<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\base\CArrayCollection;
use bamboo\core\base\CObjectCollection;
use bamboo\core\base\CStdCollectibleItem;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CPrintAztecCodeController
 * @package bamboo\blueseal\controllers
 */
class CPrintAztecCodeController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "aztec_print";

    public function get()
    {
        $products = [];

        foreach ($this->app->router->request()->getRequestData('id') as $key => $value) {

            $product = $this->app->repoFactory->create('Product')->findOneByStringId($value);
            $product->aztecCode = base64_encode($product->printId().'__'.$product->productBrand->name.' - '.$product->itemno.' - '.$product->productVariant->name);

            $products[] = $product;
        }

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/aztec_print.php');
        $aztecFactoryEndpoint = $this->app->baseUrl(false).'/blueseal/xhr/GetAztecCode?src=';

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'aztecFactoryEndpoint'=> $aztecFactoryEndpoint,
            'products' => $products,
            'shop' => null,
            'page' => $this->page
        ]);
    }
}
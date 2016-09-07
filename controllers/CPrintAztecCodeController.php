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
        $products =  new CArrayCollection();

        foreach ($this->app->router->request()->getRequestData('id') as $key => $value) {

            $o = new \stdClass();
            \BlueSeal::dump($value);
            $o->product = $this->app->repoFactory->create('Product')->findOneByStringId($value);
            \BlueSeal::dump($o->product);
            $o->aztecCode = base64_encode($o->product->id.'-'.$o->product->productVariantId.'__'.$o->product->productBrand->name.' - '.$o->product->itemno.' - '.$o->product->productVariant->name);

            try {
                $o->shop = $o->product->shop->getFirst()->name;
            } catch (\Exception $e) {
                $o->shop = null;
            }

            $products->add(new CStdCollectibleItem($o));
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
<?php
namespace bamboo\blueseal\controllers;

use bamboo\domain\entities\CProduct;
use bamboo\ecommerce\views\VBase;
use bamboo\core\base\CArrayCollection;
use bamboo\core\base\CObjectCollection;
use bamboo\core\base\CStdCollectibleItem;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CPrintAztecCodeController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 11/04/2018
 * @since 1.0
 */
class CPrintAztecCodeController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "aztec_print";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaException
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function get()
    {
        $products = [];

        foreach ($this->app->router->request()->getRequestData('id') as $key => $value) {

            /** @var CProduct $product */
            $product = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($value);

            $barcodeInt = $product->getBarcodeInt();

            if(is_string($barcodeInt)){
                $product->aztecCode = base64_encode($product->printId().'__'.$product->productBrand->name.' - '.$product->itemno.' - '.$product->productVariant->name.' - '.$barcodeInt);
            } else if($barcodeInt == false){
                $product->aztecCode = base64_encode($product->printId().'__'.$product->productBrand->name.' - '.$product->itemno.' - '.$product->productVariant->name);
            }


            $products[] = $product;
        }

        $temp = \Monkey::app()->router->request()->getRequestData('tmp');


        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/aztec_print.php');
        $aztecFactoryEndpoint = $this->app->baseUrl(false).'/blueseal/xhr/GetAztecCode?src=';

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'aztecFactoryEndpoint'=> $aztecFactoryEndpoint,
            'products' => $products,
            'shop' => null,
            'page' => $this->page,
            'temp' => $temp
        ]);
    }
}
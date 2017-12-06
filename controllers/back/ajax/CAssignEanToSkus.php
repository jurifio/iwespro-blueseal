<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\exceptions\BambooConfigException;

/**
 * Class CAssignEanToSkus
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CAssignEanToSkus extends AAjaxController
{
    public function post()
    {
    	$count = 0;
	    $productsCount = 0;
    	$products = $this->app->router->request()->getRequestData('rows');
	    foreach ($products as $product) {
	    	$one = 0;
	    	$product = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($product);
		    foreach ($product->productSku as $productSku) {
		    	if(empty($productSku->ean)) {
					if(\Monkey::app()->repoFactory->create('ProductSku')->assignNewEan($productSku)) {
						$count++;
						$one++;
					};
			    }
		    }
		    if($one > 0) $productsCount++;
	    }
	    return json_encode(['products'=>$productsCount,'skus'=>$count]);
    }
}
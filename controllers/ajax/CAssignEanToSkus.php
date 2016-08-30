<?php

namespace bamboo\blueseal\controllers\ajax;
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
	    	$product = $this->app->repoFactory->create('Product')->findOneByStringId($product);
		    foreach ($product->productSku as $productSku) {
		    	if(empty($productSku->ean)) {
		    		$ean = $this->app->repoFactory->create('EanBucket')->findOneBy(['isAssigned'=>0]);
				    if(is_null($ean)) throw new BambooConfigException('Could not find an unassigned Ean');
				    $this->app->dbAdapter->beginTransaction();
				    $productSku->ean = $ean->ean;
				    $productSku->update();
				    $ean->isAssigned = 1;
				    $ean->update();
				    $this->app->dbAdapter->commit();
				    $count++;
				    $one++;
			    }
		    }
		    if($one > 0) $productsCount++;
	    }
	    return json_encode(['products'=>$productsCount,'skus'=>$count]);
    }
}
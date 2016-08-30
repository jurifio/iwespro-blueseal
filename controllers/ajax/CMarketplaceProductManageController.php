<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\events\EGenericEvent;
use bamboo\core\intl\CLang;

/**
 * Class CMarketplaceProductManageController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 18/07/2016
 * @since 1.0
 */
class CMarketplaceProductManageController extends AAjaxController
{
	public function get()
	{
		$response = [];
		foreach ($this->app->repoFactory->create('MarketplaceAccount')->findAll() as $account) {
			$modifier = isset($account->config['priceModifier']) ? $account->config['priceModificer'] : 0;
			$response[] = ['id' => $account->printId(), 'name' => $account->name, 'marketplace' => $account->marketplace->name, 'modifier' => $modifier];
		}

		return json_encode($response);
    }

    public function post()
    {
	    $productSample = $this->app->repoFactory->create('Product')->getEmptyEntity();
	    $marketplaceAccount = $this->app->repoFactory->create('MarketplaceAccount')->findOneByStringId($this->app->router->request()->getRequestData('account'));
	    $config = $marketplaceAccount->config;
	    $config['priceModifier'] = $this->app->router->request()->getRequestData('modifier');
	    $i = 0;
	    foreach ($this->app->router->request()->getRequestData('rows') as $row) {
		    $productSample->readId($row);
		    $marketplaceAccountHasProduct = $this->app->repoFactory->create('MarketplaceAccountHasProduct')->getEmptyEntity();
		    $marketplaceAccountHasProduct->productId = $productSample->id;
		    $marketplaceAccountHasProduct->productVariantId = $productSample->productVariantId;
		    $marketplaceAccountHasProduct->marketplaceAccountId = $marketplaceAccount->id;
		    $marketplaceAccountHasProduct->marketplaceId = $marketplaceAccount->marketplaceId;
		    $marketplaceAccountHasProduct->priceModifier = $config['priceModifier'];
		    $marketplaceAccountHasProduct->insert();
		    $this->app->eventManager->trigger((new EGenericEvent('marketplace.product.add',['newProductsKeys'=>$marketplaceAccountHasProduct->printId()])));
		    $i++;
	    }
	    return $i;
    }

    public function put()
    {
    	//RETRY
	    $productSample = $this->app->repoFactory->create('Product')->getEmptyEntity();
	    $i = 0;
	    foreach ($this->app->router->request()->getRequestData('rows') as $row) {
		    $productSample->readId($row);
		    $product = $this->app->repoFactory->create('Product')->findOne($productSample->getIds());
		    foreach ($product->marketplaceAccountHasProduct as $marketplaceAccountHasProduct) {
			    if(1 == $marketplaceAccountHasProduct->hasError || 1 == $marketplaceAccountHasProduct->isToWork) {
				    $this->app->eventManager->trigger((new EGenericEvent('marketplace.product.add',['newProductsKeys'=>$marketplaceAccountHasProduct->printId()])));
				    $i++;
			    }
		    }
	    }

	    return $i;
    }
}
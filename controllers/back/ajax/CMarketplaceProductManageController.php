<?php
namespace bamboo\controllers\back\ajax;
use bamboo\domain\repositories\CMarketplaceAccountHasProductRepo;

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
			$response[] = ['id' => $account->printId(), 'name' => $account->name, 'marketplace' => $account->marketplace->name, 'modifier' => $modifier, 'cpc'=>$account->marketplace->type != 'marketplace'];
		}

		return json_encode($response);
    }

    public function post()
    {
	    $productSample = $this->app->repoFactory->create('Product')->getEmptyEntity();
	    $marketplaceAccount = $this->app->repoFactory->create('MarketplaceAccount')->findOneByStringId($this->app->router->request()->getRequestData('account'));
	    $config = $marketplaceAccount->config;
	    $config['priceModifier'] = $this->app->router->request()->getRequestData('modifier');
	    $config['cpc'] = $this->app->router->request()->getRequestData('cpc');
        if(!$config['cpc']) {
            if(isset($marketplaceAccount->config['defaultCpc'])) {
                $config['cpc'] = $marketplaceAccount->config['defaultCpc'];
            }
        }
	    $i = 0;
        $rows = $this->app->router->request()->getRequestData('rows');
        if($rows == 'all') {
            $query = "select distinct concat(product,'-', variant) as code
                      from vProductSortingView v 
                      where (product, variant) not in (
                        select distinct m.productId, m.productVariantId 
                        from MarketplaceAccountHasProduct m 
                        where m.marketplaceId = ? and m.marketplaceAccountId = ? )";
            $rows = $this->app->dbAdapter->query($query,[$marketplaceAccount->marketplaceId,$marketplaceAccount->id])->fetchAll(\PDO::FETCH_COLUMN,0);
        }
        $this->app->dbAdapter->beginTransaction();
	    try {
            $ids = [];
	        foreach ($rows as $row) {
	            set_time_limit(6);
                $update = false;
                $productSample->readId($row);
                $marketplaceAccountHasProduct = $this->app->repoFactory->create('MarketplaceAccountHasProduct')->getEmptyEntity();
                $marketplaceAccountHasProduct->productId = $productSample->id;
                $marketplaceAccountHasProduct->productVariantId = $productSample->productVariantId;
                $marketplaceAccountHasProduct->marketplaceAccountId = $marketplaceAccount->id;
                $marketplaceAccountHasProduct->marketplaceId = $marketplaceAccount->marketplaceId;
                if($marketplaceAccountHasProduct2 = $this->app->repoFactory->create('MarketplaceAccountHasProduct')->findOneBy($marketplaceAccountHasProduct->getIds())) {
                    $marketplaceAccountHasProduct = $marketplaceAccountHasProduct2;
                    $update = true;
                }

                $marketplaceAccountHasProduct->priceModifier = $config['priceModifier'];
                if($marketplaceAccount->marketplace->type == 'cpc') {
                    $marketplaceAccountHasProduct->fee = $config['cpc'];
                }
                if($update) {
                    $marketplaceAccountHasProduct->isDeleted = 0;
                    $marketplaceAccountHasProduct->isToWork = 1;
                    $marketplaceAccountHasProduct->update();
                } else {
                    $marketplaceAccountHasProduct->insert();
                }
                $i++;
                $this->app->eventManager->triggerEvent('marketplace.product.add',['newProductsKeys'=>[$marketplaceAccountHasProduct->printId()]]);
            }
        } catch (\Throwable $e) {
            $this->app->dbAdapter->rollBack();
            throw $e;
        }
	    $this->app->dbAdapter->commit();
	    return $i;
    }

    public function put()
    {
    	//RETRY
	    $i = 0;
        $revise = [];
	    foreach ($this->app->router->request()->getRequestData('rows') as $row) {
		    $product = $this->app->repoFactory->create('Product')->findOneByStringId($row);
		    foreach ($product->marketplaceAccountHasProduct as $marketplaceAccountHasProduct) {
			    if(1 == $marketplaceAccountHasProduct->hasError || 1 == $marketplaceAccountHasProduct->isToWork) {
				    $this->app->eventManager->triggerEvent('marketplace.product.add',['newProductsKeys'=>$marketplaceAccountHasProduct->printId()]);
				    $i++;
			    } else {
                    $revise[$product->printId()] = $product;
                }
		    }
	    }
	    foreach ($revise as $product) {
            $this->app->eventManager->triggerEvent('product.marketplace.change',['productIds'=>$product->printId()]);
        }

	    return $i;
    }

    /**
     * @return int
     */
    public function delete() {
        $count = 0;
        /** @var CMarketplaceAccountHasProductRepo $repo */
        $repo = $this->app->repoFactory->create('MarketplaceAccountHasProduct');
        foreach ($this->app->router->request()->getRequestData('ids') as $mId) {
            if($repo->deleteProductFromMarketplace($mId)) $count++;
        }
        return $count;
    }
}
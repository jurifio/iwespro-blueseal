<?php
namespace bamboo\blueseal\controllers\ajax;
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
	    $marketplaceAccount = $this->app->repoFactory->create('MarketplaceAccount')->findOneByStringId($this->app->router->request()->getRequestData('account'));
	    $modifier = $this->app->router->request()->getRequestData('modifier');
	    $cpc = $this->app->router->request()->getRequestData('cpc');
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
        /** @var CMarketplaceAccountHasProductRepo $marketplaceAccountHasProductRepo */
        $marketplaceAccountHasProductRepo = $this->app->repoFactory->create('MarketplaceAccountHasProduct');
        $productRepo = $this->app->repoFactory->create('Product');
        $this->app->dbAdapter->beginTransaction();
	    try {
            $ids = [];
	        foreach ($rows as $row) {
	            set_time_limit(6);
                $product = $productRepo->findOneByStringId($row);
                $marketplaceAccountHasProduct = $marketplaceAccountHasProductRepo->addProductToMarketplaceAccount($product,$marketplaceAccount,$cpc,$modifier);
                $i++;

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
	    foreach ($this->app->router->request()->getRequestData('rows') as $row) {
		    $product = $this->app->repoFactory->create('Product')->findOneByStringId($row);
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
            if($repo->deleteProductFromMarketplaceAccount($mId)) $count++;
        }
        return $count;
    }
}
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
        foreach (\Monkey::app()->repoFactory->create('MarketplaceAccount')->findAll() as $account) {
            $modifier = isset($account->config['priceModifier']) ? $account->config['priceModificer'] : 0;
            $response[] = ['id' => $account->printId(), 'name' => $account->name, 'marketplace' => $account->marketplace->name, 'modifier' => $modifier, 'cpc' => $account->marketplace->type != 'marketplace'];
        }

        return json_encode($response);
    }

    public function post()
    {
        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneByStringId($this->app->router->request()->getRequestData('account'));
        $modifier = $this->app->router->request()->getRequestData('modifier');
        $cpc = $this->app->router->request()->getRequestData('cpc');
        $i = 0;
        $rows = $this->app->router->request()->getRequestData('rows');
        if ($rows == 'all') {
            $query = "SELECT DISTINCT concat(product,'-', variant) AS code
                      FROM vProductSortingView v 
                      WHERE (product, variant) NOT IN (
                        SELECT DISTINCT m.productId, m.productVariantId 
                        FROM MarketplaceAccountHasProduct m 
                        WHERE m.marketplaceId = ? AND m.marketplaceAccountId = ? )";
            $rows = $this->app->dbAdapter->query($query, [$marketplaceAccount->marketplaceId, $marketplaceAccount->id])->fetchAll(\PDO::FETCH_COLUMN, 0);
        }
        /** @var CMarketplaceAccountHasProductRepo $marketplaceAccountHasProductRepo */
        $marketplaceAccountHasProductRepo = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct');
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        \Monkey::app()->repoFactory->beginTransaction();
        foreach ($rows as $row) {
            try {
                $ids = [];

                set_time_limit(6);
                $product = $productRepo->findOneByStringId($row);
                $marketplaceAccountHasProduct = $marketplaceAccountHasProductRepo->addProductToMarketplaceAccount($product, $marketplaceAccount, $cpc, $modifier);
                $i++;
                \Monkey::app()->repoFactory->commit();
            } catch
            (\Throwable $e) {
                \Monkey::app()->repoFactory->rollback();
                throw $e;
            }
        }
        return $i;
    }

    public function put()
    {
        //RETRY
        $i = 0;
        foreach ($this->app->router->request()->getRequestData('rows') as $row) {
            $product = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($row);
            $this->app->eventManager->triggerEvent('product.marketplace.change', ['productId' => $product->printId()]);
        }
        return $i;
    }

    /**
     * @return int
     */
    public function delete()
    {
        $count = 0;
        /** @var CMarketplaceAccountHasProductRepo $repo */
        $repo = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct');
        foreach ($this->app->router->request()->getRequestData('ids') as $mId) {
            if ($repo->deleteProductFromMarketplaceAccount($mId)) $count++;
        }
        return $count;
    }
}
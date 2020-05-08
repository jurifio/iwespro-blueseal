<?php
namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CProduct;
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
                $activeAutomatic=isset($account->config['activeAutomatic']) ? $account->config['activeAutomatic'] : 0;
                $modifier = isset($account->config['priceModifier']) ? $account->config['priceModifier'] : 0;
                $cpc = isset($account->config['defaultCpc']) ? $account->config['defaultCpc'] : 0;
                if ($account->marketplace->type == 'cpc') {
                    $response[] = ['id' => $account->printId(), 'name' => $account->name, 'marketplace' => $account->marketplace->name, 'modifier' => $modifier, 'cpc' => $cpc,'activeAutomatic'=>$activeAutomatic];
                }
        }

        return json_encode($response);
    }

    public function post()
    {
        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneByStringId($this->app->router->request()->getRequestData('account'));
        $modifier = $this->app->router->request()->getRequestData('modifier');
        $cpc = $this->app->router->request()->getRequestData('cpc');
        $activeAutomatic = $this->app->router->request()->getRequestData('activeAutomatic');
        $i = 0;
        $rows = $this->app->router->request()->getRequestData('rows');
        if ($rows == 'all') {
            $query = "SELECT DISTINCT concat(id,'-', productVariantId) AS code
                      FROM Product v 
                      WHERE (id, productVariantId) NOT IN (
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
                /** @var  $product CProduct */
                $product = $productRepo->findOneByStringId($row);
                if($product->productBrand->hasAggregator==1) {
                    $marketplaceAccountHasProduct = $marketplaceAccountHasProductRepo->addProductToMarketplaceAccount($product,$marketplaceAccount,$cpc,$modifier,$activeAutomatic);
                }
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
        $marketplaceAccount=explode('-', $this->app->router->request()->getRequestData('account'));
        $marketplaceAccountId=$marketplaceAccount[0];
        $marketplaceId = $marketplaceAccount[1];
        $marketplaceAccountHasProductRepo=\Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct');


        foreach ($this->app->router->request()->getRequestData('rows') as $mId) {
            $product=explode('-',$mId);
            $productId=$product[0];
            $productVariantId=$product[1];
            $marketplaceAccountHasProduct=$marketplaceAccountHasProductRepo->findOneBy(['productId'=>$productId,
                                                                                        'productVariantId'=>$productVariantId,
                                                                                        'marketplaceId'=>$marketplaceId,
                                                                                        'marketplaceAccountId'=>$marketplaceAccountId]);
            if($marketplaceAccountHasProduct!=null){
                $marketplaceAccountHasProduct->delete();
            }
        }
        return $count;
    }
}
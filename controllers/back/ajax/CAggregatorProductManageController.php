<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CProduct;
use bamboo\domain\repositories\CMarketplaceAccountHasProductRepo;
use bamboo \domain\entities\AggregatorHasProduct;
use bamboo \domain\entities\CMarketplaceAccountHasProduct;
use bamboo\core\db\pandaorm\repositories\CRepo;

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
class CAggregatorProductManageController extends AAjaxController
{
    public function get()
    {
        $response = [];
        foreach (\Monkey::app()->repoFactory->create('Marketplace')->findBy(['type' => 'cpc']) as $mp) {
            foreach (\Monkey::app()->repoFactory->create('MarketplaceAccount')->findBy(['marketplaceId' => $mp->id]) as $account) {
                $activeAutomatic = isset($account->config['activeAutomatic']) ? $account->config['activeAutomatic'] : 0;
                $modifier = isset($account->config['priceModifier']) ? $account->config['priceModifier'] : 0;
                $cpc = isset($account->config['defaultCpc']) ? $account->config['defaultCpc'] : 0;
                $cpcM = isset($account->config['defaultCpcM']) ? $account->config['defaultCpcM'] : 0;
                $cpcFM = isset($account->config['defaultCpcFM']) ? $account->config['defaultCpcFM'] : 0;
                $cpcF = isset($account->config['defaultCpcF']) ? $account->config['defaultCpcF'] : 0;


                $response[] = ['id' => $account->printId(),'name' => $account->name,'marketplace' => $account->marketplace->name,'modifier' => $modifier,'cpc' => $cpc,'cpcF' => $cpcF,'cpcM' => $cpcM,'cpcFM' => $cpcFM,'activeAutomatic' => $activeAutomatic];

            }
        }

        return json_encode($response);
    }

    public function post()
    {
        try {
        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneByStringId($this->app->router->request()->getRequestData('account'));
        $aggregatorHasShopId = $marketplaceAccount->config['aggregatorHasShopId'];
        $modifier = $this->app->router->request()->getRequestData('modifier');
        $cpc = $this->app->router->request()->getRequestData('cpc');
        $cpcF = $this->app->router->request()->getRequestData('cpcF');
        $cpcM = $this->app->router->request()->getRequestData('cpcM');
        $cpcFM = $this->app->router->request()->getRequestData('cpcFM');
        /**  @var $aggregatorHasProductRepo CRepo */
        $aggregatorHasProductRepo = \Monkey::app()->repoFactory->create('AggregatorHasProduct');
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $activeAutomatic = $this->app->router->request()->getRequestData('activeAutomatic');
        $i = 0;
        $rows = $this->app->router->request()->getRequestData('rows');

        foreach ($rows as $row) {

            $ids = [];
            $products = explode('-',$row);
            $productId = $products[0];
            $productVariantId = $products[1];

            $product = $productRepo->findOneBy(['id' => $productId,'productVariantId' => $productVariantId]);
            if ($product->productBrand->hasAggregator == 1) {
                /** @var  $ahp CAggregatorHasProduct */
                $ahp = $aggregatorHasProductRepo->findOneBy(['productId' => $productId,'productVariantId' => $productVariantId,'aggregatorHasShopId' => $aggregatorHasShopId]);
                if ($ahp) {
                    $ahp->fee = $cpc;
                    $ahp->feeMobile = $cpcM;
                    $ahp->feeCustomerMobile = $cpcFM;
                    $ahp->feeCustomer = $cpcF;
                    $ahp->productStatusAggregatorId = 3;
                    $ahp->status = 2;
                    $ahp->update();
                } else {
                    $ahpInsert = $aggregatorHasProductRepo->getEmptyEntity();
                    $ahpInsert->productId = $productId;
                    $ahpInsert->productVariantId = $productVariantId;
                    $ahpInsert->status = 0;
                    $ahpInsert->aggregatorHasShopId = $aggregatorHasShopId;
                    $ahpInsert->fee = $cpc;
                    $ahpInsert->feeMobile = $cpcM;
                    $ahpInsert->priceMofidier = 0;
                    $ahpInsert->feeCustomerMobile = $cpcFM;
                    $ahpInsert->feeCustomer = $cpcF;
                    $ahpInsert->productStatusAggregatorId = 3;
                    $ahpInsert->insert();


                }

            }
            $i++;
        }
                \Monkey::app()->repoFactory->commit();
                return 'la Prenotazione dei Prodotti è stata Eseguita con Successo';

            } catch
            (\Throwable $e) {
                \Monkey::app()->repoFactory->rollback();
            return 'Errore Prenotazione Prodotti '.$e;
                throw $e;
            }


    }

    public function put()
    {
        //RETRY
        $i = 0;
        foreach ($this->app->router->request()->getRequestData('rows') as $row) {
            $product = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($row);
            $this->app->eventManager->triggerEvent('product.marketplace.change',['productId' => $product->printId()]);
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
        $marketplaceAccount = explode('-',$this->app->router->request()->getRequestData('account'));
        $marketplaceAccountId = $marketplaceAccount[0];
        $marketplaceId = $marketplaceAccount[1];
        $marketplaceAccountHasProductRepo = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct');
        $aggregatorHasProductRepo = \Monkey::app()->repoFactory->create('AggregatorHasProduct');


        foreach ($this->app->router->request()->getRequestData('rows') as $mId) {
            $product = explode('-',$mId);
            $productId = $product[0];
            $productVariantId = $product[1];
            $marketplaceAccountHasProduct = $marketplaceAccountHasProductRepo->findOneBy(['productId' => $productId,
                'productVariantId' => $productVariantId,
                'marketplaceId' => $marketplaceId,
                'marketplaceAccountId' => $marketplaceAccountId]);

            if ($marketplaceAccountHasProduct != null) {
                $aggregatorHasShopId=$marketplaceAccountHasProduct->aggregatorHasShopId;
                 if($aggregatorHasShopId){
                     $aggregatorHasProduct=$aggregatorHasProductRepo->findOneBy(['productId' => $productId,
                         'productVariantId' => $productVariantId,
                         'aggregatorHasShopId' => $aggregatorHasShopId]);
                     if($aggregatorHasProduct){
                         $aggregatorHasProduct->aggregatorHasShopId=null;
                         $aggregatorHasProduct->productStatusAggregatorId=5;
                         $aggregatorHasProduct->marketplaceProductId=null;
                         $aggregatorHasProduct->update();


                     }
                 }
                $marketplaceAccountHasProduct->delete();

            }

        }
        return $count;
    }
}
<?php

namespace bamboo\blueseal\jobs;

use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CMarketplace;
use bamboo\domain\entities\CMarketplaceAccount;
use bamboo\domain\entities\CAggregatorHasProduct;
use bamboo\domain\entities\CMarketplaceAccountHasProduct;
use bamboo\domain\entities\CAggregatorHasShop;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBrand;


/**
 * Class CPrepareProductForAggregatorJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 27/04/2020
 * @since 1.0
 */
class CPrepareProductForAggregatorJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $marketplaceRepo = \Monkey::app()->repoFactory->create('Marketplace');
        $marketplaceAccountRepo = \Monkey::app()->repoFactory->create('MarketplaceAccount');
        $phphmhsRepo = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct');
        $phsRepo = \Monkey::app()->repoFactory->create('AggregatorHasProduct');
        $mhsRepo = \Monkey::app()->repoFactory->create('AggregatorHasShop');


        try {

                $shops = $shopRepo->findBy(['hasMarketplace' => 1]);
            foreach ($shops as $shop) {
                $mhss = $mhsRepo->findBy(['shopId' => $shop->id]);
                foreach ($mhss as $mhs) {
                    $this->report('CPrepareProductForAggregatorJob','start Preparing',$mhs->id);
                    if ($shop->id != 44) {

                    $sql = '(select p.id as productId, p.productVariantId as productVariantId,p.qty as qty,
                                shp.shopId as shopId from Product p join ShopHasProduct shp on p.id=shp.productId
 and p.productVariantId=shp.productVariantId where p.qty > 0 and p.productStatusId in (6,15) and shp.shopId =' . $shop->id . ') UNION
(select p2.id as productId, p2.productVariantId as productVariantId, p2.qty as qty, shp2.shopIdDestination as shopId from
 Product p2 join ProductHasShopDestination shp2 on p2.id=shp2.productId
 and p2.productVariantId=shp2.productVariantId where p2.qty > 0 and p2.productStatusId in(6,15) and shp2.shopIdDestination =' . $shop->id . ')';
                }else{
                    $sql='select p.id as productId, p.productVariantId as productVariantId,p.qty as qty,
                                shp.shopId as shopId from Product p join ShopHasProduct shp on p.id=shp.productId
 and p.productVariantId=shp.productVariantId where p.qty > 0 and p.productStatusId in (6,15)  AND shopId IN(1,51,61)';
                }
                    $products = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                    foreach ($products as $product) {
                        $phs=$phsRepo->findOneBy(['productId'=>$product['productId'],'productVariantId'=>$product['productVariantId'],'aggregatorHasShopId'=>$mhs->id]);
                        if($phs){
                            if($phs->productStatusAggregatorId =='4'){
                                $phphmhs= $phphmhsRepo->findOneBy(['productId'=>$product['productId'],'productVariantId'=>$product['productVariantId'],'aggregatorHasShopId'=>$mhs->id]);
                                if($phphmhs){
                                    $phphmhs->isDeleted=1;
                                    $phphmhs->isRevised=0;
                                    $phphmhs->isToWork=0;
                                    $phphmhs->update();
                                    \Monkey::app()->applicationLog('CPrepareProductForAggregatorJob','Report','booking Depublish ' . $product['productId'] . '-' . $product['productVariantId'] . ' to marketplace' . $mhs->id,'');
                                }
                            }
                        }else{
                            $phsInsert=$phsRepo->getEmptyEntity();
                            $phsInsert->productId=$product['productId'];
                            $phsInsert->productVariantId=$product['productVariantId'];
                            $phsInsert->aggregatorHasShopId=$mhs->id;
                            $phsInsert->status = 0;
                            $phsInsert->dateUpdate = '2011-01-01 00:00:00';
                            $phsInsert->productStatusAggregatorId = 1;
                            $phsInsert->insert();
                            $this->report('CPrepareProductForAggregatorJob','Report','insert ' . $product['productId'] . '-' . $product['productVariantId'] . ' to aggregator' . $mhs->id);

                        }

                    }
                    $this->report('CPrepareProductForAggregatorJob','Report End ', $mhs->id);
                }
            }
        } catch (\Throwable $e) {
            $this->report('CPrepareProductForAggregatorJob','Error',$e->getMessage().'-'.$e->getLine());
        }


    }

}
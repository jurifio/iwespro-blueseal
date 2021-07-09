<?php

namespace bamboo\blueseal\jobs;

use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CMarketplace;
use bamboo\domain\entities\CMarketplaceAccount;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBrand;


/**
 * Class CPrepareProductForMarketplaceJob
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
class CPrepareProductForMarketplaceJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $marketplaceRepo = \Monkey::app()->repoFactory->create('Marketplace');
        $marketplaceAccountRepo = \Monkey::app()->repoFactory->create('MarketplaceAccount');
        $phphmhsRepo = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');
        $phsRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
        $mhsRepo = \Monkey::app()->repoFactory->create('MarketplaceHasShop');


        try {

            $shops = $shopRepo->findBy(['hasMarketplace' => 1]);
            foreach ($shops as $shop) {
                $mhss = $mhsRepo->findBy(['shopId' => $shop->id]);
                foreach ($mhss as $mhs) {
                    $this->report('CPrepareProductForMarketplaceJob','start Preparing','');
                    $sql = '(select p.id as productId, p.productVariantId as productVariantId,p.qty as qty,
                                shp.shopId as shopId from Product p join ShopHasProduct shp on p.id=shp.productId
 and p.productVariantId=shp.productVariantId where p.qty > 0 and p.productStatusId in (6,15) and   shp.shopId ='.$shop->id.') UNION
(select p2.id as productId, p2.productVariantId as productVariantId, p2.qty as qty, shp2.shopIdDestination as shopId from
 Product p2 join ProductHasShopDestination shp2 on p2.id=shp2.productId
 and p2.productVariantId=shp2.productVariantId where p2.qty > 0 and p2.productStatusId in (6,15) and  shp2.shopIdDestination ='.$shop->id.')';
                    $products = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                    foreach ($products as $product) {
                        $phs=$phsRepo->findOneBy(['productId'=>$product['productId'],'productVariantId'=>$product['productVariantId'],'marketplaceHasShopId'=>$mhs->id]);
                        if($phs){
                          if($phs->productStatusMarketplaceId==4){
                              $phphmhs= $phphmhsRepo->findOneBy(['productId'=>$product['productId'],'productVariantId'=>$product['productVariantId'],'marketplaceHasShopId'=>$mhs->id]);
                              if($phphmhs){
                                  $phphmhs->isPublished=3;
                                  $phphmhs->update();
                                  $this->report('CPrepareProductForMarketplaceJob','Report','booking Depublish ' . $product['productId'] . '-' . $product['productVariantId'] . ' to marketplace' . $mhs->id);
                              }
                          }else{
                              continue;
                          }
                        }else{
                            $phsInsert=$phsRepo->getEmptyEntity();
                            $phsInsert->productId=$product['productId'];
                            $phsInsert->productVariantId=$product['productVariantId'];
                            $phsInsert->marketplaceHasShopId=$mhs->id;
                            $phsInsert->status = 0;
                            $phsInsert->dateUpdate = '2011-01-01 00:00:00';
                            $phsInsert->productStatusMarketplaceId = 1;
                            $phsInsert->insert();
                            $this->report('CPrepareProductForMarketplaceJob','Report','insert ' . $product['productId'] . '-' . $product['productVariantId'] . ' to marketplace' . $mhs->id);

                        }

                    }
                }
            }
        } catch (\Throwable $e) {
            $this->report('CPrepareProductForMarketplaceJob','Error',$e->getMessage().'-'.$e->getLine());
        }


    }

}
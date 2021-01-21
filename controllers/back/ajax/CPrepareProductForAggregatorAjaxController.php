<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CMessage;
use bamboo\domain\entities\CMessageHasUser;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CFoisonRepo;
use PDO;
use PDOException;

/**
 * Class CPrepareProductForAggregatorAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 28/04/2020
 * @since 1.0
 */
class CPrepareProductForAggregatorAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function post()
    {
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $marketplaceRepo = \Monkey::app()->repoFactory->create('Marketplace');
        $marketplaceAccountRepo = \Monkey::app()->repoFactory->create('MarketplaceAccount');
        $phphmhsRepo = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct');
        $phsRepo = \Monkey::app()->repoFactory->create('AggregatorHasProduct');
        $mhsRepo = \Monkey::app()->repoFactory->create('AggregatorHasShop');


        try {
             \Monkey::app()->applicationLog('CPrepareProductForAggregatorAjaxController','start Preparing','');
            $shops = $shopRepo->findBy(['hasMarketplace' => 1]);
            foreach ($shops as $shop) {
                $mhss = $mhsRepo->findBy(['shopId' => $shop->id]);
                foreach ($mhss as $mhs) {
                    $sql = '(select p.id as productId, p.productVariantId as productVariantId,p.qty as qty,
                                shp.shopId as shopId from Product p join ShopHasProduct shp on p.id=shp.productId
 and p.productVariantId=shp.productVariantId where p.qty > 0 and p.productStatusId=6 and shp.shopId =' . $shop->id . ') UNION
(select p2.id as productId, p2.productVariantId as productVariantId, p2.qty as qty, shp2.shopIdDestination as shopId from
 Product p2 join ProductHasShopDestination shp2 on p2.id=shp2.productId
 and p2.productVariantId=shp2.productVariantId where p2.qty > 0 and p2.productStatusId=6 and shp2.shopIdDestination =' . $shop->id . ')';
                    $products = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                    foreach ($products as $product) {
                        $phs=$phsRepo->findOneBy(['productId'=>$product['productId'],'productVariantId'=>$product['productVariantId'],'aggregatorHasShopId'=>$mhs->id]);
                        if($phs){
                            if($phs->productStatusAggregatorId==4){
                                $phphmhs= $phphmhsRepo->findOneBy(['productId'=>$product['productId'],'productVariantId'=>$product['productVariantId'],'aggregatorHasShopId'=>$mhs->id]);
                                if($phphmhs){
                                    $phphmhs->isDeleted=1;
                                    $phphmhs->isRevised=0;
                                    $phphmhs->isToWork=0;
                                    $phphmhs->update();
                                     \Monkey::app()->applicationLog('CPrepareProductForAggregatorAjaxController','Report','booking Depublish ' . $product['productId'] . '-' . $product['productVariantId'] . ' to marketplace' . $mhs->id,'');
                                }
                            }
                        }else{
                            $phsInsert=$phsRepo->getEmptyEntity();
                            $phsInsert->productId=$product['productId'];
                            $phsInsert->productVariantId=$product['productVariantId'];
                            $phsInsert->aggregatorHasShopId=$mhs->id;
                            $phsInsert->status = 0;
                            $phsInsert->lastUpdate = '2011-01-01 00:00:00';
                            $phsInsert->productStatusAggregatorId = 1;
                            $phsInsert->insert();
                             \Monkey::app()->applicationLog('CPrepareProductForAggregatorAjaxController','Report','insert ' . $product['productId'] . '-' . $product['productVariantId'] . ' to marrketplace' . $mhs->id,'');

                        }

                    }
                }
            }
        } catch (\Throwable $e) {
             \Monkey::app()->applicationLog('CPrepareProductForAggregatorAjaxController','Error','Aggregator Prepare',$e->getMessage().'-'.$e->getLine());
        }


    }

}
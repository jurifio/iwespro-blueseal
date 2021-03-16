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
 * Class CPrepareProductForMarketplaceAjaxController
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
class CPrepareProductForMarketplaceAjaxController extends AAjaxController
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
        $phphmhsRepo = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');
        $phsRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
        $mhsRepo = \Monkey::app()->repoFactory->create('MarketplaceHasShop');


        try {

            $shops = $shopRepo->findBy(['hasMarketplace' => 1]);
            foreach ($shops as $shop) {
                $mhss = $mhsRepo->findBy(['shopId' => $shop->id]);
                foreach ($mhss as $mhs) {
                    \Monkey::app()->applicationLog('CPrepareProductForMarketplaceAjaxController','log','start Preparing',$mhs->id);
                    $sql = '(select p.id as productId, p.productVariantId as productVariantId,p.qty as qty,
                                shp.shopId as shopId,shp.isPublished as isPublished from Product p join ShopHasProduct shp on p.id=shp.productId
 and p.productVariantId=shp.productVariantId where p.qty > 0 shp.shopId =' . $shop->id . ' ) UNION
(select p2.id as productId, p2.productVariantId as productVariantId, p2.qty as qty, shp2.shopId as shopId from
 Product p2 join ProductHasShopDestination shp2 on p2.id=shp2.productId
 and p2.productVariantId=shp2.productVariantId where p2.qty > 0 shp2.shopIdDestination =' . $shop->id . ')';
                    $products = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                    foreach ($products as $product) {
                        $phs=$phsRepo->findOneBy(['productId'=>$product['productId'],'productVariantId'=>$product['productVariantId'],'marketplaceHasShopId'=>$mhs->id]);
                        if(!is_null($phs)){
                            if($phs->productStatusMarketplaceId==4){
                                $phphmhs= $phphmhsRepo->findOneBy(['productId'=>$product['productId'],'productVariantId'=>$product['productVariantId'],'marketplaceHasShopId'=>$mhs->id]);
                                if(!is_null($phphmhs)){
                                    $phphmhs->isPublished=3;
                                    $phphmhs->update();
                                   \Monkey::app()->applicationLog('CPrepareProductForMarketplaceAjaxController','Report','booking Depublish ' . $product['productId'] . '-' . $product['productVariantId'] . ' to marrketplace' . $mhs->id,'');
                                   return 'Report booking Depublish ' . $product['productId'] . '-' . $product['productVariantId'] . ' to marketplace' . $mhs->id;
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
                            $phs->insert();
                            \Monkey::app()->applicationLog('CPrepareProductForMarketplaceAjaxController','Report','insert ' . $product['productId'] . '-' . $product['productVariantId'] . ' to marrketplace' . $mhs->id,'');
                            return 'Report booking Depublish ' . $product['productId'] . '-' . $product['productVariantId'] . ' to marrketplace' . $mhs->id;
                        }

                    }
                }
            }
            return 'finish';
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CPrepareProductForMarketplaceJob','Error'. $product['productId'] . '-' . $product['productVariantId'] . ' to marrketplace' . $mhs->id,$e->getMessage().'-'.$e->getLine(),'');
       return 'Error'. $product['productId'] . '-' . $product['productVariantId'] . ' to marrketplace' . $mhs->id . 'see ApplicationLog'.$e->getMessage().'-'.$e->getLine();
        }


    }

}
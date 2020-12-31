<?php

namespace bamboo\blueseal\jobs;

use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CMarketplace;
use bamboo\domain\entities\CMarketplaceAccount;




/**
 * Class CProductHasShopDestinationJob
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
class CProductHasShopDestinationJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $marketplaceRepo = \Monkey::app()->repoFactory->create('Marketplace');
        $pshsdRepo = \Monkey::app()->repoFactory->create('ProductShareHasShopDestination');
        $phsdRepo = \Monkey::app()->repoFactory->create('ProductHasShopDestination');
        $marketplaceAccountRepo = \Monkey::app()->repoFactory->create('MarketplaceAccount');

        try {
            $this->report('CProductHasShopDestinationJob','startSharing','');

            $marketplaces = $marketplaceRepo->findBy(['type' => 'website']);
            foreach ($marketplaces as $marketplace) {
                $marketplaceAccount = $marketplaceAccountRepo->findOneBy(['marketplaceId' => $marketplace->id,'isActive' => 1]);
                if ($marketplaceAccount) {
                    if ($marketplaceAccount->config['isActiveShare'] == 1) {
                        $this->report('CProductHasShopDestinationJob','Working ' . $marketplace->name,'');
                        if ($marketplaceAccount->config['brands'] == 0 || $marketplaceAccount->config['brands'] == '') {
                            $sqlBrandFilter = 'and 1=1';
                        } else {
                            $sqlBrandFilter = 'and p.productCategoryId not in (' . $marketplaceAccount->config['brands'] . ')';
                        }
                        $sql = 'select p.id as productId, p.productVariantId as productVariantId,p.qty as qty, shp.shopId as shopId from Product p join ShopHasProduct shp on p.id=shp.productId
 and p.productVariantId=shp.productVariantId where shp.shopId=' . $marketplaceAccount->config['shop'] . '  ' . $sqlBrandFilter;
                        $products = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                        foreach ($products as $product) {
                            $pshsd = $pshsdRepo->findOneBy(['productId' => $product['productId'],'productVariantId' => $product['productVariantId'],'shopId' => $product['shopId']]);
                            if ($pshsd) {
                                if ($product['qty'] > 0) {
                                    $pshpsd->isPublished = 1;
                                } else {
                                    $pshpsd->isPublished = 0;
                                }
                                $pshpsd->update();
                            } else {
                                if ($product['qty'] > 0) {
                                    $pshsdInsert = $pshsdRepo->getEmptyEntity();
                                    $pshsdInsert->productId = $product['productId'];
                                    $pshsdInsert->productVariantId = $product['productVariantId'];
                                    $pshsdInsert->shopId = $product['shopId'];
                                    $pshsdInsert->isPublished = 1;
                                    $pshsdInsert->insert();
                                }
                            }
                        }
                        $this->report('CProductHasShopDestinationJob','End Work  Sharing From ' . $marketplace->name,'');
                    }
                }
            }

            $this->report('CProductHasShopDestinationJob','End Work Sharing','');
        } catch (\Throwable $e) {
            $this->report('CProductHasShopDestinationJob','ERROR Work Sharing',$e->getMessage() . '-' . $e->getLine());

        }
        try {
            $this->report('CProductHasShopDestinationJob','startPublish','');

            $marketplaces = $marketplaceRepo->findBy(['type' => 'website']);
            foreach ($marketplaces as $marketplace) {
                $marketplaceAccount = $marketplaceAccountRepo->findOneBy(['marketplaceId' => $marketplace->id,'isActive' => 1]);
                if ($marketplaceAccount) {
                    if ($marketplaceAccount->config['isActivePublish'] == 1) {
                        $this->report('CProductHasShopDestinationJob','Working ' . $marketplace->name,'');
                        if ($marketplaceAccount->config['brandParallel'] == 0 || $marketplaceAccount->config['brandParallel'] == '') {
                            $sqlBrandFilter = 'and 1=1';
                        } else {
                            $sqlBrandFilter = 'and p.productCategoryId not in (' . $marketplaceAccount->config['brandParallel'] . ')';
                        }
                        $sql = 'select p.id as productId, p.productVariantId as productVariantId,p.qty as qty, shp.shopId as shopId,shp.isPublished as isPublished from Product p join ProductShareHasShopDestination shp on p.id=shp.productId
 and p.productVariantId=shp.productVariantId where shp.shopId !=' . $marketplaceAccount->config['shop'] . '  ' . $sqlBrandFilter;
                        $products = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                        foreach ($products as $product) {
                            $phsd = $phsdRepo->findOneBy(['productId' => $product['productId'],'productVariantId' => $product['productVariantId'],'shopIdOrigin' => $product['shopId'],'shopIdDestination'=>$marketplaceAccount->config['shop']]);
                            if ($phsd) {
                                if ($product['isPublished'] == 0) {
                                    $phsd->delete();
                                } else {
                                    $phsd->statusId = $marketplaceAccount->config['productStatusId'];
                                    $phsd->update();
                                }
                            } else {
                                if ($product['isPublished'] == 1) {
                                    $phsdInsert = $phsdRepo->getEmptyEntity();
                                    $phsdInsert->productId = $product['productId'];
                                    $phsdInsert->productVariantId = $product['productVariantId'];
                                    $phsdInsert->shopIdOrigin = $product['shopId'];
                                    $phsdInsert->shopIdDestination = $marketplaceAccount->config['shop'];
                                    $phsdInsert->statusId = $marketplaceAccount->config['productStatusId'];
                                    $phsdInsert->insert();
                                }
                            }
                        }
                        $this->report('CProductHasShopDestinationJob','End Work Publish to  ' . $marketplace->name,'');
                    }
                }
            }

            $this->report('CProductHasShopDestinationJob','End Work Publishing Parallel','');
        } catch (\Throwable $e) {
            $this->report('CProductHasShopDestinationJob','ERROR Work Publishing Parallel',$e->getMessage() . '-' . $e->getLine());

        }


    }

}
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
use bamboo\domain\entities\CAggregatorHasProduct;


/**
 * Class CMarketplaceHasProductJob
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
class CAggregatorSocialHasProductJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $marketplaceRepo = \Monkey::app()->repoFactory->create('Marketplace');
        $marketplaceAccountRepo = \Monkey::app()->repoFactory->create('MarketplaceAccount');
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $phphmhsRepo = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct');

        $phsRepo = \Monkey::app()->repoFactory->create('AggregatorHasProduct');


        try {
            $this->report('CMarketplaceHasProductJob','start Preparing','');

            $marketplaces = $marketplaceRepo->findBy(['type' => 'social']);
            foreach ($marketplaces as $marketplace) {
                $marketplaceAccount = $marketplaceAccountRepo->findOneBy(['marketplaceId' => $marketplace->id,'isActive' => 1]);
                if ($marketplaceAccount) {
                    if ($marketplaceAccount->isActive == 1) {
                        $this->report('CAggregatorHasProductJob','Working ' . $marketplace->name,'');

                        $sql = '(select p.id as productId, p.productVariantId as productVariantId,p.qty as qty,
                                shp.shopId as shopId from Product p join ShopHasProduct shp on p.id=shp.productId
 and p.productVariantId=shp.productVariantId where p.qty > 0 and p.productStatusId in (6,15) and shp.shopId =' . $marketplaceAccount->config['shopId'] . ' ) UNION
(select p2.id as productId, p2.productVariantId as productVariantId, p2.qty as qty, shp2.shopIdDestination as shopId from
 Product p2 join ProductHasShopDestination shp2 on p2.id=shp2.productId
 and p2.productVariantId=shp2.productVariantId where p2.qty > 0 and p2.productStatusId in (6,15) and shp2.shopIdDestination =' . $marketplaceAccount->config['shopId'] . ')';

                        $products = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                        foreach ($products as $product) {
                            if ($product['qty'] > 0) {
                                /** @var $pshsd CAggregatorHasProduct */
                                $pshsd = $phsRepo->findOneBy(['productId' => $product['productId'],'productVariantId' => $product['productVariantId'],'aggregatorHasShopId' => $marketplaceAccount->config['aggregatorHasShopId']]);
                                if ($pshsd) {

                                        $pshsd->status = 2;

                                            $prod = $productRepo->findOneBy(['id' => $product['productId'],'productVariantId' => $product['productVariantId']]);
                                            $isOnSale = $prod->isOnSale();
                                            $productSku = \Monkey::app()->repoFactory->create('ProductSku')->findOneBy(['productId' => $product['productId'],'productVariantId' => $product['productVariantId']]);
                                            $price = $productSku->price;
                                            $salePrice = $productSku->salePrice;
                                            if ($isOnSale == 1) {
                                                $activePrice = $salePrice;
                                            } else {
                                                $activePrice = $price;
                                            }
                                            $fee = 0.10;
                                            $feeMobile = 0.10;
                                            $priceModifier = 0.10;
                                            $pshsd->priceModifier = $priceModifier;
                                            $pshsd->fee = $fee;
                                            $pshsd->feeMobile = $feeMobile;
                                            $pshsd->feeCustomer = 0;
                                            $pshsd->feeCustomerMobile = 0.25;
                                            $pshsd->productStatusAggregatorId = 2;
                                            $pshsd->lastUpdate = $marketplaceAccount->config['dateUpdate'];
                                            $pshsd->status = 2;
                                            $pshsd->update();




                                } else {
                                    $pshsdInsert = $phsRepo->getEmptyEntity();
                                    $pshsdInsert->productId = $product['productId'];
                                    $pshsdInsert->productVariantId = $product['productVariantId'];
                                    $pshsdInsert->aggregatorHasShopId = $marketplaceAccount->config['aggregatorHasShopId'];


                                        $prod = $productRepo->findOneBy(['id' => $product['productId'],'productVariantId' => $product['productVariantId']]);
                                        $isOnSale = $prod->isOnSale();
                                        $productSku = \Monkey::app()->repoFactory->create('ProductSku')->findOneBy(['productId' => $product['productId'],'productVariantId' => $product['productVariantId']]);
                                        $price = $productSku->price;
                                        $salePrice = $productSku->salePrice;
                                        if ($isOnSale == 1) {
                                            $activePrice = $salePrice;
                                        } else {
                                            $activePrice = $price;
                                        }


                                        $fee = 0.10;
                                        $feeMobile = 0.10;
                                        $priceModifier = 0.10;


                                        $pshsdInsert->priceModifier = $priceModifier;
                                        $pshsdInsert->fee = $fee;
                                        $pshsdInsert->feeMobile = $feeMobile;
                                        $pshsdInsert->feeCustomer = 0.10;
                                        $pshsdInsert->feeCustomerMobile = 0.10;


                                    $pshsdInsert->status = 0;
                                    $pshsdInsert->lastUpdate = '2011-01-01 00:00:00';
                                    $pshsdInsert->productStatusAggregatorId = 2;
                                    $pshsdInsert->insert();

                                }

                            }
                        }
                        $this->report('CAggregatorHasProductJob','End Work  prepare for publishing From ' . $marketplace->name,'');
                    }
                }
            }

            $this->report('CAggregatorSocialHasProductJob','End Work publishing','');
        } catch (\Throwable $e) {
            $this->report('CAggregatorSocialHasProductJob','ERROR Work publishing',$e->getMessage() . '-' . $e->getLine());

        }
        try {
            $this->report('CAggregatorSocialHasProductJob','startPublish','');

            $marketplaces = $marketplaceRepo->findBy(['type' => 'social']);
            foreach ($marketplaces as $marketplace) {
                $marketplaceAccount = $marketplaceAccountRepo->findOneBy(['marketplaceId' => $marketplace->id,'isActive' => 1]);
                if ($marketplaceAccount) {


                        $this->report('CAggregatorSocialHasProductJob','Working to Select Eligible Products to ' . $marketplace->name,'');
                        $sql = 'select p.id as productId,
                                    p.productVariantId as productVariantId,
                                    p.productBrandId as productBrandId,
                                    p.qty as qty,
                                    p.isOnSale as isOnSale,
                                    shp.aggregatorHasShopId as aggregatorHasShopId,
                                    `shp`.`status` as `status`,
                                    shp.fee as fee,
                                    shp.priceModifier as priceModifier,
                                    shp.feeCustomer as feeCustomer,
                                    shp.feeMobile as feeMobile,
                                    shp.feeCustomerMobile as feeCustomerMobile,
                                    sp.price as price,
                                    sp.salePrice as salePrice,
                                    shp.productStatusAggregatorId as productStatusAggregatorId
                        from Product p join AggregatorHasProduct shp on p.id=shp.productId  
                                                                            
 and p.productVariantId=shp.productVariantId
join ShopHasProduct sp on p.id=sp.productId and p.productVariantId=sp.productVariantId
where  shp.aggregatorHasShopId =' . $marketplaceAccount->config['aggregatorHasShopId'];
                        $products = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                        foreach ($products as $product) {
                            $marketProduct = $phphmhsRepo->findOneBy(['productId' => $product['productId'],'productVariantId' => $product['productVariantId'],'aggregatorHasShopId' => $marketplaceAccount->config['aggregatorHasShopId']]);
                            if ($marketProduct) {
                                if ($product['status'] == 2) {

                                    $marketProduct->istoWork = 1;
                                    $marketProduct->isRevised = 1;
                                    $marketProduct->isDeleted = 0;
                                    $marketProduct->lastUpdate = (new \DateTime())->format('Y-m-d H:i:s');
                                    if ($product['isOnSale'] == 1) {
                                        $marketProduct->titleModified = 1;
                                    } else {
                                        $marketProduct->titleModified = 0;
                                    }
                                    $marketProduct->update();


                                } elseif ($product['status'] == 0) {
                                    $marketProductInsert = $phphmhsRepo->getEmptyEntity();
                                    $marketProductInsert->productId = $product['productId'];
                                    $marketProductInsert->productVariantId = $product['productVariantId'];
                                    $marketProductInsert->insertionDate = (new \DateTime())->format('Y-m-d H:i:s');
                                    $marketProductInsert->istoWork = 1;
                                    $marketProductInsert->isRevised = 0;
                                    $marketProductInsert->isDeleted = 0;
                                    $marketProductInsert->aggregatorHasShopId = $marketplaceAccount->config['aggregatorHasShopId'];
                                    $marketProductInsert->marketplaceAccountId = $marketplaceAccount->id;
                                    $marketProductInsert->marketplaceId = $marketplaceAccount->marketplaceId;
                                    if ($product['isOnSale'] == 1) {
                                        $marketProductInsert->titleModified = 1;
                                    } else {
                                        $marketProductInsert->titleModified = 0;
                                    }
                                    $marketProductInsert->insert();
                                } else {

                                    $marketProduct->istoWork = 1;
                                    $marketProduct->isRevised = 1;
                                    $marketProduct->isDeleted = 0;
                                    $marketProduct->lastUpdate = (new \DateTime())->format('Y-m-d H:i:s');

                                    if ($product['isOnSale'] == 1) {
                                        $marketProduct->titleModified = 1;
                                    } else {
                                        $marketProduct->titleModified = 0;
                                    }
                                    $marketProduct->update();
                                }
                            } else {
                                $marketProductInsert = $phphmhsRepo->getEmptyEntity();
                                $marketProductInsert->productId = $product['productId'];
                                $marketProductInsert->productVariantId = $product['productVariantId'];
                                $marketProductInsert->insertionDate = (new \DateTime())->format('Y-m-d H:i:s');
                                $marketProductInsert->istoWork = 1;
                                $marketProductInsert->isRevised = 0;
                                $marketProductInsert->isDeleted = 0;
                                $marketProductInsert->aggregatorHasShopId = $marketplaceAccount->config['aggregatorHasShopId'];
                                $marketProductInsert->marketplaceAccountId = $marketplaceAccount->id;
                                $marketProductInsert->marketplaceId = $marketplaceAccount->marketplaceId;
                                if ($product['isOnSale'] == 1) {
                                    $marketProductInsert->titleModified = 1;
                                } else {
                                    $marketProductInsert->titleModified = 0;
                                }
                                $marketProductInsert->insert();

                            }
                            $phpUpdate = $phsRepo->findOneBy(['productId' => $product['productId'],'productVariantId' => $product['productVariantId'],'aggregatorHasShopId' => $marketplaceAccount->config['aggregatorHasShopId']]);
                            $phpUpdate->status = 1;
                            $phpUpdate->update();
                            $this->report('CAggregatorSocialHasProductJob','End Work   ' . $product['productId'] . '-' . $product['productVariantId'],'');
                        }

                }

                $this->report('CAggregatorSocialHasProductJob','End Work Publish for  ' . $marketplace->name,'');


            }
            $this->report('CAggregatorSocialHasProductJob','End Work Publishing Eligible Products to Aggregator  Table','');
        } catch
        (\Throwable $e) {
            $this->report('CAggregatorSocialHasProductJob','ERROR Work Publishing Eligible Products to Aggregator',$e->getMessage() . '-' . $e->getLine());

        }


    }

}
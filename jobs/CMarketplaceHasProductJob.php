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
class CMarketplaceHasProductJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $marketplaceRepo = \Monkey::app()->repoFactory->create('Marketplace');
        $marketplaceAccountRepo = \Monkey::app()->repoFactory->create('MarketplaceAccount');
        $phphmhsRepo = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');
        $phsRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');


        try {
            $this->report('CMarketplaceHasProductJob','start Preparing','');

            $marketplaces = $marketplaceRepo->findBy(['type' => 'marketplace','id'=>3]);
            foreach ($marketplaces as $marketplace) {
                $marketplaceAccount = $marketplaceAccountRepo->findOneBy(['marketplaceId' => $marketplace->id,'isActive' => 1]);
                if ($marketplaceAccount) {
                    if ($marketplaceAccount->config['isActivePublish'] == 1) {
                        $this->report('CMarketplaceHasProductJob','Working ' . $marketplace->name,'');
                        if ($marketplaceAccount->config['brands'] == 0 || $marketplaceAccount->config['brands'] == '') {
                            $sqlBrandFilter = 'and 1=1';
                        } else {
                            $sqlBrandFilter = 'and p.productBrandId not in (' . $marketplaceAccount->config['brands'] . ')';
                        }
                        if ($marketplaceAccount->config['brandParallel'] == 0 || $marketplaceAccount->config['brandParallel'] == '') {
                            $sqlBrandParallelFilter = 'and 1=1';
                        } else {
                            $sqlBrandParallelFilter = 'and p2.productBrandId not in (' . $marketplaceAccount->config['brandParallel'] . ')';
                        }
                        $sql = '(select p.id as productId, p.productVariantId as productVariantId,p.qty as qty,
                                shp.shopId as shopId from Product p join ShopHasProduct shp on p.id=shp.productId
 and p.productVariantId=shp.productVariantId where p.qty>0 and  shp.shopId =' . $marketplaceAccount->config['shopId'] . '  ' . $sqlBrandFilter . ' ) UNION
(select p2.id as productId, p2.productVariantId as productVariantId, p2.qty as qty, shp2.shopIdDestination as shopId from
 Product p2 join ProductHasShopDestination shp2 on p2.id=shp2.productId
 and p2.productVariantId=shp2.productVariantId where p2.qty>0 and  shp2.shopIdDestination =' . $marketplaceAccount->config['shopId'] . '  ' . $sqlBrandParallelFilter . ')';

                        $products = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                        foreach ($products as $product) {

                                $pshsd = $phsRepo->findOneBy(['productId' => $product['productId'],'productVariantId' => $product['productVariantId'],'marketplaceHasShopId' => $marketplaceAccount->config['marketplaceHasShopId']]);
                                if ($pshsd) {

                                    if ($pshsd->dateUpdate != $marketplaceAccount->config['dateUpdate']) {
                                        $pshsd->status = 2;
                                        if ($marketplaceAccount->config['activeFullPrice'] == "1") {
                                            $pshsd->modifyType = 'nf';

                                        } else {
                                            if ($marketplaceAccount->config['signFullPrice'] == "1") {
                                                $pshsd->modifyType = '-p';
                                                $pshsd->variantValue = $marketplaceAccount->config['percentFullPrice'];
                                            } else {
                                                $pshsd->modifyType = '+p';
                                                $pshsd->variantValue = $marketplaceAccount->config['percentFullPrice'];
                                            }
                                        }
                                        if ($marketplaceAccount->config['activeSalePrice'] == "1") {
                                            $pshsd->modifyTypeSale = 'nf';

                                        } else {
                                            if ($marketplaceAccount->config['signSale'] == "1") {
                                                $pshsd->modifyTypeSale = '-p';
                                                $pshsd->variantValue = $marketplaceAccount->config['percentSalePrice'];
                                            } else {
                                                $pshsd->modifyTypeSale = '+p';
                                                $pshsd->variantValue = $marketplaceAccount->config['percentSalePrice'];
                                            }
                                        }
                                        $pshsd->maxPercentSalePrice = $marketplaceAccount->config['maxPercentSalePrice'];
                                        $pshsd->lastUpdate = $marketplaceAccount->config['dateUpdate'];
                                    } else {
                                        $pshsd->status = 1;
                                    }
                                    $pshsd->productStatusMarkeplaceId=2;
                                    $pshsd->update();
                                } else {
                                    $pshsdInsert = $phsRepo->getEmptyEntity();
                                    $pshsdInsert->productId = $product['productId'];
                                    $pshsdInsert->productVariantId = $product['productVariantId'];
                                    $pshsdInsert->marketplaceHasShopId = $marketplaceAccount->config['marketplaceHasShopId'];
                                    if ($marketplaceAccount->config['activeFullPrice'] == "1") {
                                        $pshsdInsert->modifyType = 'nf';

                                    } else {
                                        if ($marketplaceAccount->config['signFullPrice'] == "1") {
                                            $pshsdInsert->modifyType = '-p';
                                            $pshsdInsert->variantValue = $marketplaceAccount->config['percentFullPrice'];
                                        } else {
                                            $pshsdInsert->modifyType = '+p';
                                            $pshsdInsert->variantValue = $marketplaceAccount->config['percentFullPrice'];
                                        }
                                    }
                                    if ($marketplaceAccount->config['activeSalePrice'] == "1") {
                                        $pshsdInsert->modifyTypeSale = 'nf';

                                    } else {
                                        if ($marketplaceAccount->config['signSale'] == "1") {
                                            $pshsdInsert->modifyTypeSale = '-p';
                                            $pshsdInsert->variantValue = $marketplaceAccount->config['percentSalePrice'];
                                        } else {
                                            $pshsdInsert->modifyTypeSale = '+p';
                                            $pshsdInsert->variantValue = $marketplaceAccount->config['percentSalePrice'];
                                        }
                                    }
                                    $pshsdInsert->maxPercentSalePrice = $marketplaceAccount->config['maxPercentSalePrice'];
                                    $pshsdInsert->status = 2;
                                    $pshsdInsert->lastUpdate = '2011-01-01 00:00:00';
                                    $pshsdInsert->productStatusMarkeplaceId=2;
                                    $pshsdInsert->insert();

                                }


                        }
                        $this->report('CMarketplaceHasProductJob','End Work  prepare for publishing From ' . $marketplace->name,'');
                    }
                }
            }

            $this->report('CMarketplaceHasProductJob','End Work publishing','');
        } catch (\Throwable $e) {
            $this->report('CMarketplaceHasProductJob','ERROR Work publishing',$e->getMessage() . '-' . $e->getLine());

        }
        try {
            $this->report('CMarketplaceHasProductJob','startPublish','');

            $marketplaces = $marketplaceRepo->findBy(['type' => 'marketplace','id'=>3]);
            foreach ($marketplaces as $marketplace) {
                $marketplaceAccount = $marketplaceAccountRepo->findOneBy(['marketplaceId' => $marketplace->id,'isActive' => 1]);
                if ($marketplaceAccount) {
                    if ($marketplaceAccount->config['isActivePublish'] == 1) {
                        $now = strtotime(date("Y-m-d"));
                        $dateStartPeriod1 = strtotime($marketplaceAccount->config['dateStartPeriod1'] . ' 00:00:00');
                        $dateEndPeriod1 = strtotime($marketplaceAccount->config['dateEndPeriod1'] . ' 23:59:59');
                        $dateStartPeriod2 = strtotime($marketplaceAccount->config['dateStartPeriod2'] . ' 00:00:00');
                        $dateEndPeriod2 = strtotime($marketplaceAccount->config['dateEndPeriod2'] . ' 23:59:59');
                        $dateStartPeriod3 = strtotime($marketplaceAccount->config['dateStartPeriod3'] . ' 00:00:00');
                        $dateEndPeriod3 = strtotime($marketplaceAccount->config['dateEndPeriod3'] . ' 23:59:59');
                        $dateStartPeriod4 = strtotime($marketplaceAccount->config['dateStartPeriod4'] . ' 00:00:00');
                        $dateEndPeriod4 = strtotime($marketplaceAccount->config['dateEndPeriod4'] . ' 23:59:59');

                        $isOnSale = 0;
                        switch (true) {
                            case ($now >= $dateStartPeriod1 && $now <= $dateEndPeriod1):
                                $isOnSale = 1;
                                break;
                            case ($now >= $dateStartPeriod2 && $now <= $dateEndPeriod2):
                                $isOnSale = 1;
                                break;
                            case ($now >= $dateStartPeriod3 && $now <= $dateEndPeriod3):
                                $isOnSale = 1;
                                break;
                            case ($now >= $dateStartPeriod4 && $now <= $dateEndPeriod4):
                                $isOnSale = 1;
                                break;

                        }


                        $brandSaleExclusion = explode(',',$marketplaceAccount->config['brandSaleExclusion']);
                        $this->report('CMarketplaceHasProductJob','Working to Select Eligible Products to ' . $marketplace->name,'');
                        $sql = 'select p.id as productId,
                                    p.productVariantId as productVariantId,
                                    p.productBrandId as productBrandId,
                                    p.qty as qty,
                                    shp.marketplaceHasShopId as marketplaceHasShopId,
                                    `shp`.`status` as `status`,
                                    shp.modifyType as modifyType,
                                    shp.variantValue as variantValue,
                                    shp.modifyTypeSale as modifyTypeSale,
                                    shp.variantValueSale as variantValueSale,
                                    shp.maxPercentSalePrice as maxPercentSalePrice,
                                    sp.price as price,
                                    sp.salePrice as salePrice,
                                    shp.productStatusMarketplaceId as productStatusMarketplaceId
                        from Product p join PrestashopHasProduct shp on p.id=shp.productId  
                                                                            
 and p.productVariantId=shp.productVariantId
join ShopHasProduct sp on p.id=sp.productId and p.productVariantId=sp.productVariantId where p.qty>0 and  shp.productStatusMarketplaceId in (2,3) and shp.marketplaceHasShopId =' . $marketplaceAccount->config['marketplaceHasShopId'];
                        $products = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                        foreach ($products as $product) {
                            $marketProduct = $phphmhsRepo->findOneBy(['productId' => $product['productId'],'productVariantId' => $product['productVariantId'],'marketplaceHasShopId' => $marketplaceAccount->config['marketplaceHasShopId']]);
                            if ($marketProduct) {
                                if ($product['status'] == 2) {
                                    if ($isOnSale == 1) {
                                        if (in_array($product['productBrandId'],$brandSaleExclusion)) {
                                            switch (true) {
                                                case $product['modifyType'] = 'p+':
                                                    $newPrice = $product['price'] + ($product['price'] / 100 * $product['variantValue']);
                                                    break;
                                                case $product['modifyType'] = 'p-':
                                                    $newPrice = $product['price'] - ($product['price'] / 100 * $product['variantValue']);
                                                    break;
                                                default:
                                                    $newPrice = $product['price'];
                                                    break;

                                            }
                                            switch ($marketplaceAccount->config['optradio']) {
                                                case  '-0.5':
                                                    $marketProduct->price = round($newPrice,1,PHP_ROUND_HALF_DOWN);
                                                    break;
                                                case  '+0.5':
                                                    $marketProduct->price = round($newPrice,1,PHP_ROUND_HALF_UP);;
                                                    break;
                                                case  '-1':
                                                    $marketProduct->price = round($newPrice,0,PHP_ROUND_HALF_DOWN);;
                                                    break;
                                                case  '+1':
                                                    $marketProduct->price = round($newPrice,0,PHP_ROUND_HALF_UP);;
                                                    break;
                                                default:
                                                    $marketProduct->price = round($newPrice,1,PHP_ROUND_HALF_DOWN);;

                                            }
                                            switch (true) {
                                                case $product['modifyTypeSale'] = 'p+':
                                                    $newSalePrice = $product['salePrice'] + ($product['salePrice'] / 100 * $product['variantValueSale']);
                                                    break;
                                                case $product['modifyType'] = 'p-':
                                                    if ($product['variantValueSale'] > 30) {
                                                        $newSalePrice = $product['price'] - ($product['price'] / 100 * $product['variantValueSale']);
                                                    } else {
                                                        $newSalePrice = $product['price'] - ($product['price'] / 100 * 30);
                                                    }
                                                    break;
                                                case $product['modifyTypeSale'] = 'nf':
                                                default:
                                                    $newSalePrice = $product['salePrice'];
                                                    break;


                                            }
                                            switch ($marketplaceAccount->config['optradioSalePrice']) {
                                                case  '-0.5':
                                                    $marketProduct->salePrice = round($newSalePrice,1,PHP_ROUND_HALF_DOWN);
                                                    break;
                                                case  '+0.5':
                                                    $marketProduct->salePrice = round($newSalePrice,1,PHP_ROUND_HALF_UP);;
                                                    break;
                                                case  '-1':
                                                    $marketProduct->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_DOWN);;
                                                    break;
                                                case  '+1':
                                                    $marketProduct->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_UP);;
                                                    break;
                                                default:
                                                    $marketProduct->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_DOWN);
                                                    break;
                                            }
                                            $marketProduct->isOnSale = 0;
                                            $marketProduct->titleModified = 0;
                                            $marketProduct->lastUpdate = (new \DateTime())->format('Y-m-d H:i:s');
                                            $marketProduct->isPublished = 1;
                                            $marketProduct->update();
                                        } else {
                                            switch ($product['modifyType']) {
                                                case  'p+':
                                                    $newPrice = $product['price'] + ($product['price'] / 100 * $product['variantValue']);
                                                    break;
                                                case  'p-':
                                                    $newPrice = $product['price'] - ($product['price'] / 100 * $product['variantValue']);
                                                    break;
                                                default:
                                                    $newPrice = $product['price'];
                                                    break;
                                            }
                                            switch ($marketplaceAccount->config['optradio']) {
                                                case  '-0.5':
                                                    $marketProduct->price = round($newPrice,1,PHP_ROUND_HALF_DOWN);
                                                    break;
                                                case  '+0.5':
                                                    $marketProduct->price = round($newPrice,1,PHP_ROUND_HALF_UP);;
                                                    break;
                                                case  '-1':
                                                    $marketProduct->price = round($newPrice,0,PHP_ROUND_HALF_DOWN);;
                                                    break;
                                                case  '+1':
                                                    $marketProduct->price = round($newPrice,0,PHP_ROUND_HALF_UP);;
                                                    break;
                                                default:
                                                    $marketProduct->price = round($newPrice,1,PHP_ROUND_HALF_DOWN);

                                            }
                                            switch ($product['modifyTypeSale']) {
                                                case  'p+':
                                                    $newSalePrice = $product['salePrice'] + ($product['salePrice'] / 100 * $product['variantValueSale']);
                                                    break;
                                                case 'p-':
                                                    if ($product['variantValueSale'] > 30) {
                                                        $newSalePrice = $product['price'] - ($product['price'] / 100 * $product['variantValueSale']);
                                                    } else {
                                                        $newSalePrice = $product['price'] - ($product['price'] / 100 * 30);
                                                    }
                                                    break;
                                                default:
                                                    $newSalePrice = $product['salePrice'];
                                                    break;
                                            }
                                            switch ($marketplaceAccount->config['optradioSalePrice']) {
                                                case  '-0.5':
                                                    $marketProduct->salePrice = round($newSalePrice,1,PHP_ROUND_HALF_DOWN);
                                                    break;
                                                case  '+0.5':
                                                    $marketProduct->salePrice = round($newSalePrice,1,PHP_ROUND_HALF_UP);;
                                                    break;
                                                case  '-1':
                                                    $marketProduct->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_DOWN);;
                                                    break;
                                                case  '+1':
                                                    $marketProduct->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_UP);;
                                                    break;
                                                default:
                                                    $marketProduct->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_DOWN);
                                                    break;
                                            }

                                            $marketProduct->isOnSale = 1;
                                            if ($marketplaceAccount->config['marketplaceHasShopId'] == "2") {
                                                $marketProduct->titleModified = 1;
                                            } else {
                                                $marketProduct->titleModified = 0;
                                            }
                                            $marketProduct->lastUpdate = (new \DateTime())->format('Y-m-d H:i:s');
                                            $marketProduct->isPublished = 1;
                                            $marketProduct->update();
                                        }
                                    } else {
                                        switch ($product['modifyType']) {
                                            case  'nf':
                                                $newPrice = $product['price'];
                                                break;
                                            case  'p+':
                                                $newPrice = $product['price'] + ($product['price'] / 100 * $product['variantValue']);
                                                break;
                                            case  'p-':
                                                $newPrice = $product['price'] - ($product['price'] / 100 * $product['variantValue']);
                                                break;
                                            default :
                                                $newPrice = $product['price'];
                                                break;
                                        }
                                        switch ($marketplaceAccount->config['optradio']) {
                                            case  '-0.5':
                                                $marketProduct->price = round($newPrice,1,PHP_ROUND_HALF_DOWN);
                                                break;
                                            case  '+0.5':
                                                $marketProduct->price = round($newPrice,1,PHP_ROUND_HALF_UP);;
                                                break;
                                            case  '-1':
                                                $marketProduct->price = round($newPrice,0,PHP_ROUND_HALF_DOWN);;
                                                break;
                                            case  '+1':
                                                $marketProduct->price = round($newPrice,0,PHP_ROUND_HALF_UP);;
                                                break;
                                            default:
                                                $marketProduct->price = round($newPrice,1,PHP_ROUND_HALF_DOWN);;

                                        }
                                        switch ($product['modifyTypeSale']) {
                                            case  'p+':
                                                $newSalePrice = $product['salePrice'] + ($product['salePrice'] / 100 * $product['variantValueSale']);
                                                break;
                                            case 'p-':
                                                if ($product['variantValueSale'] > 30) {
                                                    $newSalePrice = $product['price'] - ($product['price'] / 100 * $product['variantValueSale']);
                                                } else {
                                                    $newSalePrice = $product['price'] - ($product['price'] / 100 * 30);
                                                }
                                                break;
                                            default:
                                                $newSalePrice = $product['salePrice'];
                                                break;
                                        }
                                        switch ($marketplaceAccount->config['optradioSalePrice']) {
                                            case  '-0.5':
                                                $marketProduct->salePrice = round($newSalePrice,1,PHP_ROUND_HALF_DOWN);
                                                break;
                                            case  '+0.5':
                                                $marketProduct->salePrice = round($newSalePrice,1,PHP_ROUND_HALF_UP);;
                                                break;
                                            case  '-1':
                                                $marketProduct->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_DOWN);;
                                                break;
                                            case  '+1':
                                                $marketProduct->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_UP);;
                                                break;
                                            default:
                                                $marketProduct->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_DOWN);
                                                break;
                                        }
                                        $marketProduct->isOnSale = 0;
                                        $marketProduct->titleModified = 0;
                                        $marketProduct->lastUpdate = (new \DateTime())->format('Y-m-d H:i:s');
                                        $marketProduct->isPublished = 1;
                                        $marketProduct->update();


                                    }


                                } elseif ($product['status'] == 0) {
                                    $marketProductInsert = $phphmhsRepo->getEmptyEntity();
                                    if ($isOnSale == 1) {
                                        if (in_array($product['productBrandId'],$brandSaleExclusion)) {
                                            switch ($product['modifyType']) {
                                                case  'p+':
                                                    $newPrice = $product['price'] + ($product['price'] / 100 * $product['variantValue']);
                                                    break;
                                                case  'p-':
                                                    $newPrice = $product['price'] - ($product['price'] / 100 * $product['variantValue']);
                                                    break;
                                                default:
                                                    $newPrice = $product['price'];
                                                    break;
                                            }
                                            switch ($marketplaceAccount->config['optradio']) {
                                                case  '-0.5':
                                                    $marketProductInsert->price = round($newPrice,1,PHP_ROUND_HALF_DOWN);
                                                    break;
                                                case  '+0.5':
                                                    $marketProductInsert->price = round($newPrice,1,PHP_ROUND_HALF_UP);;
                                                    break;
                                                case  '-1':
                                                    $marketProductInsert->price = round($newPrice,0,PHP_ROUND_HALF_DOWN);;
                                                    break;
                                                case  '+1':
                                                    $marketProductInsert->price = round($newPrice,0,PHP_ROUND_HALF_UP);;
                                                    break;
                                                default:
                                                    $marketProductInsert->price = round($newPrice,1,PHP_ROUND_HALF_DOWN);

                                            }
                                            switch ($product['modifyTypeSale']) {
                                                case  'p+':
                                                    $newSalePrice = $product['salePrice'] + ($product['salePrice'] / 100 * $product['variantValueSale']);
                                                    break;
                                                case 'p-':
                                                    if ($product['variantValueSale'] > 30) {
                                                        $newSalePrice = $product['price'] - ($product['price'] / 100 * $product['variantValueSale']);
                                                    } else {
                                                        $newSalePrice = $product['price'] - ($product['price'] / 100 * 30);
                                                    }
                                                    break;
                                                default:
                                                    $newSalePrice = $product['salePrice'];
                                                    break;
                                            }
                                            switch ($marketplaceAccount->config['optradioSalePrice']) {
                                                case  '-0.5':
                                                    $marketProductInsert->salePrice = round($newSalePrice,1,PHP_ROUND_HALF_DOWN);
                                                    break;
                                                case  '+0.5':
                                                    $marketProductInsert->salePrice = round($newSalePrice,1,PHP_ROUND_HALF_UP);;
                                                    break;
                                                case  '-1':
                                                    $marketProductInsert->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_DOWN);;
                                                    break;
                                                case  '+1':
                                                    $marketProductInsert->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_UP);;
                                                    break;
                                                default:
                                                    $marketProductInsert->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_DOWN);
                                                    break;
                                            }

                                            $marketProductInsert->isOnSale = 0;
                                            $marketProductInsert->titleModified = 0;
                                            $marketProductInsert->lastUpdate = (new \DateTime())->format('Y-m-d H:i:s');
                                            $marketProductInsert->isPublished = 2;
                                            $marketProductInsert->insert();
                                        } else {
                                            switch ($product['modifyTypeSale']) {
                                                case  'p+':
                                                    $newSalePrice = $product['salePrice'] + ($product['salePrice'] / 100 * $product['variantValueSale']);
                                                    break;
                                                case 'p-':
                                                    if ($product['variantValueSale'] > 30) {
                                                        $newSalePrice = $product['price'] - ($product['price'] / 100 * $product['variantValueSale']);
                                                    } else {
                                                        $newSalePrice = $product['price'] - ($product['price'] / 100 * 30);
                                                    }
                                                    break;
                                                default:
                                                    $newSalePrice = $product['salePrice'];
                                                    break;
                                            }
                                            switch ($marketplaceAccount->config['optradioSalePrice']) {
                                                case  '-0.5':
                                                    $marketProductInsert->salePrice = round($newSalePrice,1,PHP_ROUND_HALF_DOWN);
                                                    break;
                                                case  '+0.5':
                                                    $marketProductInsert->salePrice = round($newSalePrice,1,PHP_ROUND_HALF_UP);;
                                                    break;
                                                case  '-1':
                                                    $marketProductInsert->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_DOWN);;
                                                    break;
                                                case  '+1':
                                                    $marketProductInsert->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_UP);;
                                                    break;
                                                default:
                                                    $marketProductInsert->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_DOWN);
                                                    break;
                                            }
                                            $marketProductInsert->isOnSale = 1;
                                            if ($marketplaceAccount->config['marketplaceHasShopId'] == "2") {
                                                $marketProductInsert->titleModified = 1;
                                            } else {
                                                $marketProductInsert->titleModified = 0;
                                            }
                                            $marketProductInsert->lastUpdate = (new \DateTime())->format('Y-m-d H:i:s');
                                            $marketProductInsert->isPublished = 2;
                                            $marketProductInsert->insert();
                                        }
                                    } else {
                                        switch ($product['modifyType']) {
                                            case  'p+':
                                                $newPrice = $product['price'] + ($product['price'] / 100 * $product['variantValue']);
                                                break;
                                            case  'p-':
                                                $newPrice = $product['price'] - ($product['price'] / 100 * $product['variantValue']);
                                                break;
                                            default:
                                                $newPrice = $product['price'];
                                                break;
                                        }
                                        switch ($marketplaceAccount->config['optradio']) {
                                            case  '-0.5':
                                                $marketProductInsert->price = round($newPrice,1,PHP_ROUND_HALF_DOWN);
                                                break;
                                            case  '+0.5':
                                                $marketProductInsert->price = round($newPrice,1,PHP_ROUND_HALF_UP);;
                                                break;
                                            case  '-1':
                                                $marketProductInsert->price = round($newPrice,0,PHP_ROUND_HALF_DOWN);;
                                                break;
                                            case  '+1':
                                                $marketProductInsert->price = round($newPrice,0,PHP_ROUND_HALF_UP);;
                                                break;
                                            default:
                                                $marketProductInsert->price = round($newPrice,1,PHP_ROUND_HALF_DOWN);
                                        }
                                        switch ($product['modifyTypeSale']) {
                                            case  'p+':
                                                $newSalePrice = $product['salePrice'] + ($product['salePrice'] / 100 * $product['variantValueSale']);
                                                break;
                                            case 'p-':
                                                if ($product['variantValueSale'] > 30) {
                                                    $newSalePrice = $product['price'] - ($product['price'] / 100 * $product['variantValueSale']);
                                                } else {
                                                    $newSalePrice = $product['price'] - ($product['price'] / 100 * 30);
                                                }
                                                break;
                                            default:
                                                $newSalePrice = $product['salePrice'];
                                                break;
                                        }
                                        switch ($marketplaceAccount->config['optradioSalePrice']) {
                                            case  '-0.5':
                                                $marketProductInsert->salePrice = round($newSalePrice,1,PHP_ROUND_HALF_DOWN);
                                                break;
                                            case  '+0.5':
                                                $marketProductInsert->salePrice = round($newSalePrice,1,PHP_ROUND_HALF_UP);;
                                                break;
                                            case  '-1':
                                                $marketProductInsert->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_DOWN);;
                                                break;
                                            case  '+1':
                                                $marketProductInsert->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_UP);;
                                                break;
                                            default:
                                                $marketProductInsert->salePrice = round($newSalePrice,0,PHP_ROUND_HALF_DOWN);
                                                break;
                                        }

                                        $marketProductInsert->isOnSale = 0;
                                        $marketProductInsert->titleModified = 0;
                                        $marketProductInsert->lastUpdate = (new \DateTime())->format('Y-m-d H:i:s');
                                        $marketProductInsert->isPublished = 2;
                                        $marketProductInsert->insert();

                                    }

                                } else {

                                }
                                $phpUpdate = $phsRepo->findOneBy(['productId' => $product['productId'],'productVariantId' => $product['productVariantId'],'marketplaceHasShopId' => $marketplaceAccount->config['marketplaceHasShopId']]);
                                $phpUpdate->status = 1;
                                $phpUpdate->update();
                            }
                            $this->report('CMarketplaceHasProductJob','End Work Publish for  ' . $marketplace->name,'');
                        }
                    }
                }
            }
            $this->report('CMarketplaceHasProductJob','End Work Publishing Eligible Products to marketplace','');
        } catch
        (\Throwable $e) {
            $this->report('CMarketplaceHasProductJob','ERROR Work Publishing Eligible Products to marketplace',$e->getMessage() . '-' . $e->getLine());

        }


    }

}
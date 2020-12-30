<?php

namespace bamboo\blueseal\jobs;

use bamboo\domain\entities\CCartAbandonedEmailSend;
use bamboo\domain\repositories\CCartAbandonedEmailSendRepo;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CCart;
use bamboo\domain\entities\CCartAbandonedEmailParam;
use bamboo\domain\entities\CCouponType;
use bamboo\domain\entities\CCoupon;
use bamboo\domain\entities\CCartLine;
use bamboo\core\base\CSerialNumber;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;

use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProduct;
use bamboo\core\events\AEventListener;


/**
 * Class CPublishProductMarketplaceByRulesJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/10/2018
 * @since 1.0
 */
class CPublishProductMarketplaceByRulesJob extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {
        ini_set('memory_limit','2048M');
        set_time_limit(0);
        try {
            $marketplaceAccountRepo = \Monkey::app()->repoFactory->create('MarketplaceAccount');
            $marketplaceRepo = \Monkey::app()->repoFactory->create('Marketplace');
            $marketplaceProductRepo = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');
            $prestashopHasProduct = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
            $productRepo = \Monkey::app()->repoFactory->create('Product');
            $marketplaces = $marketplaceRepo->findBy(['type' => 'marketplace']);
            $today = (new \DateTime())->format('Y-m-d H:i:s');
            foreach ($marketplaces as $marketplace) {
                switch ($marketplace->id) {
                    case 3:
                        //case Ebay
                        $marketplaceAccounts = $marketplaceAccountRepo->findBy(['marketplaceId' => 3,'isActive' => 1]);
                        foreach ($marketplaceAccounts as $marketplaceAccount) {
                            if ($marketplaceAccount->config['brands'] == '0') {
                                $filterBrands = ' 1 = 1';
                            } else {
                                $filterBrands = 'p.productBrandId not in(' . $marketplaceAccount->config['brands'] . ')';
                            }
                            if ($marketplaceAccount->config['brandParallel'] == '0') {
                                $filterBrandsParallel = ' 1 = 1';
                            } else {
                                $filterBrandsParallel = 'p2.productBrandId not in(' . $marketplaceAccount->config['brandParallel'] . ')';
                            }
                            $sql = '(select p.id as productId,
                                     p.productVariantId as productVariantId,
                                     p.isOnSale as isOnSale,
                                     p.productBrandId as productBrandId,
                                     p.qty as qty,
                                     p.productCategoryId as productCategoryId,
                                     shp.price as price,
                                     shp.salePrice as SalePrice,
                                     p.productStatusId as productStatusId from Product p
                                     join ShopHasProduct shp on p.id=shp.productId and p.productVariantId=shp.productVariantId where  shp.shopId=' . $marketplaceAccount->$config['shopId'] .
                                ' and  ' . $filterBrands . ' AND p.productStatusId IN(6,11,15) and p.qty>0 )
                                    UNION 
                                   (select p2.id as productId,
                                     p2.productVariantId as productVariantId,
                                     p2.isOnSale as isOnSale,
                                     p2.productBrandId as productBrandId,
                                     p2.qty as qty,
                                     shp2.price as price,
                                     shp2.salePrice as SalePrice,
                                      p2.productCategoryId as productCategoryId,
                                     p2.productStatusId as productStatusId from Product p2
                                     join ProductHasShopDestination phsd on p2.id=phsd.productId and p2.productVariantId=phsd.productVariantId
                                      join ShopHasProduct  shp2 on p2.id=shp2.productId and p2.productVariantId=shp2.productVariantId where phsd.shopIdDestination=' . $marketplaceAccount->$config['shopId'] .
                                ' and ' . $filterBrandsParallel . ' AND phsd.statusId IN(6,11,15) and p2.qty>0)';
                            $productsFind = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                            foreach ($productsFind as $result) {
                                $marketplaceProduct = $marketplaceProductRepo->findOneBy(
                                    ['productId' => $result['productId'],
                                        'productVariantId' => $result['productVariantId'],
                                        'marketplaceHasShopId' => $marketplaceAccount->config['marketplaceHasShopId']
                                    ]);
                                if ($marketplaceProduct) {
                                    if ($marketplaceAccount->config['activeFullPrice'] == '1') {
                                        $marketplaceProduct->price = $result['price'];
                                    } else {
                                        if ($marketplaceAccount->config['signFullPrice'] == 1) {
                                            $price = $result['price'] - ($result['price'] / 100 * $marketplaceAccount->config['percentFullPrice']);
                                        } else {
                                            $price = $result['price'] + ($result['price'] / 100 * $marketplaceAccount->config['percentFullPrice']);
                                        }
                                        switch ($marketplaceAccount->config['optradio']) {
                                            case '-0.5':
                                                $marketplaceProduct->price = round($price,1,PHP_ROUND_HALF_DOWN);
                                                break;
                                            case '+0.5':
                                                $marketplaceProduct->price = round($price,1,PHP_ROUND_HALF_UP);
                                                break;
                                            case '-1';
                                                $marketplaceProduct->price = round($price,0,PHP_ROUND_HALF_DOWN);
                                                break;
                                            case '+1':
                                                $marketplaceProduct->price = round($price,0,PHP_ROUND_HALF_UP);
                                                break;
                                        }
                                    }


                                    $brandSaleExclusion = implode(',',$marketplaceAccount->config['brandSaleExclusion']);
                                    if (!in_array($result['productCategoryId'],$brandSaleExclusion)) {
                                        if ($marketplaceAccount->config['activeSalePrice'] == "1") {
                                            $marketplaceProduct->salePrice = $result['salePrice'];
                                            if ($marketplaceAccount->config['checkNameCatalog'] == 1) {
                                                $marketplaceProduct->titleModified = 0;
                                            } else {
                                                $marketplaceProduct->titleModified = 1;
                                            }
                                        } else {
                                            if ($marketplaceAccount->config['signSale'] == 1) {
                                                $salePrice = $result['salePrice'] - ($result['salePrice'] / 100 * $marketplaceAccount->config['percentSalePrice']);
                                            } else {
                                                $salePrice = $result['salePrice'] + ($result['salePrice'] / 100 * $marketplaceAccount->config['percentSalePrice']);
                                            }
                                            switch ($marketplaceAccount->config['optradioSalePrice']) {
                                                case '-0.5':
                                                    $marketplaceProduct->salePrice = round($salePrice,1,PHP_ROUND_HALF_DOWN);
                                                    break;
                                                case '+0.5':
                                                    $marketplaceProduct->salePrice = round($salePrice,1,PHP_ROUND_HALF_UP);
                                                    break;
                                                case '-1';
                                                    $marketplaceProduct->salePrice = round($salePrice,0,PHP_ROUND_HALF_DOWN);
                                                    break;
                                                case '+1':
                                                    $marketplaceProduct->salePrice = round($salePrice,0,PHP_ROUND_HALF_UP);
                                                    break;
                                            }

                                            if ($marketplaceAccount->config['checkNameCatalog'] == 1) {
                                                $marketplaceProduct->titleModified = 0;
                                            } else {
                                                $marketplaceProduct->titleModified = 1;
                                            }

                                        }
                                    } else {
                                        $marketplaceProduct->salePrice = $result['price'];
                                        $marketplaceProduct->titleModified = 0;
                                    }
                                    $marketplaceProduct->isOnSale = $result['isOnSale'];
                                    $marketplaceProduct->lastUpdate = $today;
                                    $marketplaceProduct->isPublished = 1;
                                    $marketplaceProduct->update();


                                } else {
                                    $marketplaceProductInsert = $marketplaceProductRepo->getEmptyEntity();
                                    $marketplaceProductInsert->productId = $result['productId'];
                                    $marketplaceProductInsert->productVariantId = $result['productVariantId'];
                                    $marketplaceProductInsert->marketplaceHasShopId = $marketplaceAccount->config['marketplaceHasShopId'];
                                    if ($marketplaceAccount->config['activeFullPrice'] == '1') {
                                        $marketplaceProductInsert->price = $result['price'];
                                    } else {
                                        if ($marketplaceAccount->config['signFullPrice'] == 1) {
                                            $price = $result['price'] - ($result['price'] / 100 * $marketplaceAccount->config['percentFullPrice']);
                                        } else {
                                            $price = $result['price'] + ($result['price'] / 100 * $marketplaceAccount->config['percentFullPrice']);
                                        }
                                        switch ($marketplaceAccount->config['optradio']) {
                                            case '-0.5':
                                                $marketplaceProductInsert->price = round($price,1,PHP_ROUND_HALF_DOWN);
                                                break;
                                            case '+0.5':
                                                $marketplaceProductInsert->price = round($price,1,PHP_ROUND_HALF_UP);
                                                break;
                                            case '-1';
                                                $marketplaceProductInsert->price = round($price,0,PHP_ROUND_HALF_DOWN);
                                                break;
                                            case '+1':
                                                $marketplaceProductInsert->price = round($price,0,PHP_ROUND_HALF_UP);
                                                break;
                                        }
                                    }


                                    $brandSaleExclusion = implode(',',$marketplaceAccount->config['brandSaleExclusion']);
                                    if (!in_array($result['productCategoryId'],$brandSaleExclusion)) {
                                        if ($marketplaceAccount->config['activeSalePrice'] == "1") {
                                            $marketplaceProductInsert->salePrice = $result['salePrice'];
                                            if ($marketplaceAccount->config['checkNameCatalog'] == 1) {
                                                $marketplaceProductInsert->titleModified = 0;
                                            } else {
                                                $marketplaceProductInsert->titleModified = 1;
                                            }
                                        } else {
                                            if ($marketplaceAccount->config['signSale'] == 1) {
                                                $salePrice = $result['salePrice'] - ($result['salePrice'] / 100 * $marketplaceAccount->config['percentSalePrice']);
                                            } else {
                                                $salePrice = $result['salePrice'] + ($result['salePrice'] / 100 * $marketplaceAccount->config['percentSalePrice']);
                                            }
                                            switch ($marketplaceAccount->config['optradioSalePrice']) {
                                                case '-0.5':
                                                    $marketplaceProductInsert->salePrice = round($salePrice,1,PHP_ROUND_HALF_DOWN);
                                                    break;
                                                case '+0.5':
                                                    $marketplaceProductInsert->salePrice = round($salePrice,1,PHP_ROUND_HALF_UP);
                                                    break;
                                                case '-1';
                                                    $marketplaceProductInsert->salePrice = round($salePrice,0,PHP_ROUND_HALF_DOWN);
                                                    break;
                                                case '+1':
                                                    $marketplaceProductInsert->salePrice = round($salePrice,0,PHP_ROUND_HALF_UP);
                                                    break;
                                            }

                                            if ($marketplaceAccount->config['checkNameCatalog'] == 1) {
                                                $marketplaceProductInsert->titleModified = 0;
                                            } else {
                                                $marketplaceProductInsert->titleModified = 1;
                                            }

                                        }
                                    } else {
                                        $marketplaceProductInsert->salePrice = $result['price'];
                                        $marketplaceProductInsert->titleModified = 0;
                                    }
                                    $marketplaceProductInsert->isOnSale = $result['isOnSale'];
                                    $marketplaceProductInsert->lastUpdate = $today;
                                    $marketplaceProductInsert->isPublished = 1;
                                    $marketplaceProductInsert->insert();

                                }

                            }
                        }
                        break;
                }


            }
        }catch(\Throwable $e){
            $this->report('CPublishProductMarketplaceByRuleJob Error',$e->getMessage(),$e->getLine());
        }

}


}
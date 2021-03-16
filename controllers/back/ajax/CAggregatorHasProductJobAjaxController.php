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
 * Class CAggregatorHasProductJobAjaxController
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
class CAggregatorHasProductJobAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function post()
    {
        $marketplaceRepo = \Monkey::app()->repoFactory->create('Marketplace');
        $marketplaceAccountRepo = \Monkey::app()->repoFactory->create('MarketplaceAccount');
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $phphmhsRepo = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct');
        /** @var CRepo phsRepo */
        $phsRepo = \Monkey::app()->repoFactory->create('AggregatorHasProduct');


        try {
              \Monkey::app()->applicationLog('CMarketplaceHasProductJob','start Preparing','');

            $marketplaces = $marketplaceRepo->finAll();
            if($mp->type != 'marketplace') {
                foreach ($marketplaces as $marketplace) {
                    $marketplaceAccounts = $marketplaceAccountRepo->findBy(['marketplaceId' => $marketplace->id,'isActive' => 1]);
                    foreach ($marketplaceAccounts as $marketplaceAccount) {
                        if ($marketplaceAccount) {
                            if ($marketplaceAccount->config['isActive'] == 1) {
                                \Monkey::app()->applicationLog('CAggregatorHasProductJob','Working ' . $marketplace->name,'');

                                $sql = '(select p.id as productId, p.productVariantId as productVariantId,p.qty as qty,
                                shp.shopId as shopId from Product p join ShopHasProduct shp on p.id=shp.productId
 and p.productVariantId=shp.productVariantId where p.qty>0 and p.productStatusId in(6,15) and  shp.shopId =' . $marketplaceAccount->config['shopId'] . ' ) UNION
(select p2.id as productId, p2.productVariantId as productVariantId, p2.qty as qty, shp2.shopIdDestination as shopId from
 Product p2 join ProductHasShopDestination shp2 on p2.id=shp2.productId
 and p2.productVariantId=shp2.productVariantId where  p2.qty>0 and p2.productStatusId in(6,15) and shp2.shopIdDestination =' . $marketplaceAccount->config['shopId'] . ')';

                                $products = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                                foreach ($products as $product) {
                                    if ($product['qty'] > 0) {
                                        /** @var $pshsd CAggregatorHasProduct */
                                        $pshsd = $phsRepo->findOneBy(['productId' => $product['productId'],'productVariantId' => $product['productVariantId'],'aggregatorHasShopId' => $marketplaceAccount->config['aggregatorHasShopId']]);
                                        if ($pshsd) {

                                            if ($pshsd->dateUpdate != $marketplaceAccount->config['dateUpdate']) {
                                                $pshsd->status = 2;
                                                if ($marketplaceAccount->config['activeAutomatic'] == '0' || $marketplaceAccount->config['activeAutomatic'] == '') {

                                                    $pshsd->priceModifier = $marketplaceAccount->config['priceModifier'];

                                                    if (isset($marketplaceAccount->config['defaultCpcF'])) {
                                                        $pshsd->feeCustomer = $marketplaceAccount->config['defaultCpcF'];
                                                    } else {
                                                        $pshsd->feeCustomer = 0;
                                                    }
                                                    if (isset($marketplaceAccount->config['defaultCpcFM'])) {
                                                        $pshsd->feeCustomerMobile = $marketplaceAccount->config['defaultCpcFM'];
                                                    } else {
                                                        $pshsd->feeCustomerMobile = 0.25;
                                                    }
                                                    if (isset($marketplaceAccount->config['defaultCpc'])) {
                                                        $pshsd->fee = $marketplaceAccount->config['defaultCpc'];
                                                    } else {
                                                        $pshsd->fee = 0.25;
                                                    }
                                                    if (isset($marketplaceAccount->config['defaultCpcM'])) {
                                                        $pshsd->feeMobile = $marketplaceAccount->config['defaultCpcM'];
                                                    } else {
                                                        $pshsd->feeMobile = 0.25;
                                                    }
                                                    $pshsd->status = 2;
                                                    $pshsd->productStatusAggregatorId = 2;
                                                    $pshsd->lastUpdate = (new \DateTime())->format('Y-m-d H:i:s');
                                                    $pshsd->update();


                                                } else {
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
                                                    if ($mp->type == 'cpc') {
                                                        $priceRange1 = explode('-',$marketplaceAccount->config['priceModifierRange1']);
                                                        $priceRange2 = explode('-',$marketplaceAccount->config['priceModifierRange2']);
                                                        $priceRange3 = explode('-',$marketplaceAccount->config['priceModifierRange3']);
                                                        $priceRange4 = explode('-',$marketplaceAccount->config['priceModifierRange4']);
                                                        $priceRange5 = explode('-',$marketplaceAccount->config['priceModifierRange5']);

                                                        switch (true) {
                                                            case $activePrice >= $priceRange1[0] && $activePrice <= $priceRange1[1]:
                                                                $fee = $marketplaceAccount->config['range1Cpc'];
                                                                $feeMobile = $marketplaceAccount->config['range1CpcM'];
                                                                $priceModifier = $marketplaceAccount->config['valueexcept1'];

                                                                break;
                                                            case $activePrice >= $priceRange2[0] && $activePrice <= $priceRange2[1]:
                                                                $fee = $marketplaceAccount->config['range2Cpc'];
                                                                $feeMobile = $marketplaceAccount->config['range2CpcM'];
                                                                $priceModifier = $marketplaceAccount->config['valueexcept2'];
                                                                break;
                                                            case $activePrice >= $priceRange3[0] && $activePrice <= $priceRange3[1]:
                                                                $fee = $marketplaceAccount->config['range3Cpc'];
                                                                $feeMobile = $marketplaceAccount->config['range3CpcM'];
                                                                $priceModifier = $marketplaceAccount->config['valueexcept3'];
                                                                break;
                                                            case $activePrice >= $priceRange4[0] && $activePrice <= $priceRange4[1]:
                                                                $fee = $marketplaceAccount->config['range4Cpc'];
                                                                $feeMobile = $marketplaceAccount->config['range4CpcM'];
                                                                $priceModifier = $marketplaceAccount->config['valueexcept4'];
                                                                break;
                                                            case $activePrice >= $priceRange5[0] && $activePrice <= $priceRange5[1]:
                                                                $fee = $marketplaceAccount->config['range5Cpc'];
                                                                $feeMobile = $marketplaceAccount->config['range5CpcM'];
                                                                $priceModifier = $marketplaceAccount->config['valueexcept5'];
                                                                break;
                                                        }
                                                    } else {
                                                        $priceModifier = 0;
                                                        $fee = 0;
                                                        $feeMobile = 0;
                                                    }
                                                    $pshsd->priceModifier = $priceModifier;
                                                    $pshsd->fee = $fee;
                                                    $pshsd->feeMobile = $feeMobile;
                                                    if (isset($marketplaceAccount->config['defaultCpcF'])) {
                                                        $pshsd->feeCustomer = $marketplaceAccount->config['defaultCpcF'];
                                                    } else {
                                                        $pshsd->feeCustomer = 0;
                                                    }
                                                    if (isset($marketplaceAccount->config['defaultCpcFM'])) {
                                                        $pshsd->feeCustomerMobile = $marketplaceAccount->config['defaultCpcFM'];
                                                    } else {
                                                        $pshsd->feeCustomerMobile = 0.25;
                                                    }
                                                    $pshsd->productStatusAggregatorId = 2;
                                                    $pshsd->lastUpdate = (new \DateTime())->format('Y-m-d H:i:s');
                                                    $pshsd->status = 2;
                                                    $pshsd->update();
                                                }


                                            } else {
                                                $pshsd->status = 1;
                                                $pshsd->productStatusAggregatorId = 2;
                                                $pshsd->update();
                                            }
                                        } else {
                                            $pshsdInsert = $phsRepo->getEmptyEntity();
                                            $pshsdInsert->productId = $product['productId'];
                                            $pshsdInsert->productVariantId = $product['productVariantId'];
                                            $pshsdInsert->aggregatorHasShopId = $marketplaceAccount->config['aggregatorHasShopId'];
                                            if ($marketplaceAccount->config['activeAutomatic'] == '0' || $marketplaceAccount->config['activeAutomatic'] == '') {

                                                $pshsdInsert->priceModifier = $marketplaceAccount->config['priceModifier'];

                                                if (isset($marketplaceAccount->config['defaultCpcF'])) {
                                                    $pshsdInsert->feeCustomer = $marketplaceAccount->config['defaultCpcF'];
                                                } else {
                                                    $pshsdInsert->feeCustomer = 0;
                                                }
                                                if (isset($marketplaceAccount->config['defaultCpcFM'])) {
                                                    $pshsdInsert->feeCustomerMobile = $marketplaceAccount->config['defaultCpcFM'];
                                                } else {
                                                    $pshsdInsert->feeCustomerMobile = 0.25;
                                                }
                                                if (isset($marketplaceAccount->config['defaultCpc'])) {
                                                    $pshsdInsert->fee = $marketplaceAccount->config['defaultCpc'];
                                                } else {
                                                    $pshsdInsert->fee = 0.25;
                                                }
                                                if (isset($marketplaceAccount->config['defaultCpcM'])) {
                                                    $pshsdInsert->feeMobile = $marketplaceAccount->config['defaultCpcM'];
                                                } else {
                                                    $pshsdInsert->feeMobile = 0.25;
                                                }

                                            } else {
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
                                                if ($mp->type == 'cpc') {
                                                    $priceRange1 = explode('-',$marketplaceAccount->config['priceModifierRange1']);
                                                    $priceRange2 = explode('-',$marketplaceAccount->config['priceModifierRange2']);
                                                    $priceRange3 = explode('-',$marketplaceAccount->config['priceModifierRange3']);
                                                    $priceRange4 = explode('-',$marketplaceAccount->config['priceModifierRange4']);
                                                    $priceRange5 = explode('-',$marketplaceAccount->config['priceModifierRange5']);

                                                    switch (true) {
                                                        case $activePrice >= $priceRange1[0] && $activePrice <= $priceRange1[1]:
                                                            $fee = $marketplaceAccount->config['range1Cpc'];
                                                            $feeMobile = $marketplaceAccount->config['range1CpcM'];
                                                            $priceModifier = $marketplaceAccount->config['valueexcept1'];

                                                            break;
                                                        case $activePrice >= $priceRange2[0] && $activePrice <= $priceRange2[1]:
                                                            $fee = $marketplaceAccount->config['range2Cpc'];
                                                            $feeMobile = $marketplaceAccount->config['range2CpcM'];
                                                            $priceModifier = $marketplaceAccount->config['valueexcept2'];
                                                            break;
                                                        case $activePrice >= $priceRange3[0] && $activePrice <= $priceRange3[1]:
                                                            $fee = $marketplaceAccount->config['range3Cpc'];
                                                            $feeMobile = $marketplaceAccount->config['range3CpcM'];
                                                            $priceModifier = $marketplaceAccount->config['valueexcept3'];
                                                            break;
                                                        case $activePrice >= $priceRange4[0] && $activePrice <= $priceRange4[1]:
                                                            $fee = $marketplaceAccount->config['range4Cpc'];
                                                            $feeMobile = $marketplaceAccount->config['range4CpcM'];
                                                            $priceModifier = $marketplaceAccount->config['valueexcept4'];
                                                            break;
                                                        case $activePrice >= $priceRange5[0] && $activePrice <= $priceRange5[1]:
                                                            $fee = $marketplaceAccount->config['range5Cpc'];
                                                            $feeMobile = $marketplaceAccount->config['range5CpcM'];
                                                            $priceModifier = $marketplaceAccount->config['valueexcept5'];
                                                            break;
                                                    }
                                                } else {
                                                    $priceModifier = 0;
                                                    $fee = 0;
                                                    $feeMobile = 0;
                                                }
                                                $pshsdInsert->priceModifier = $priceModifier;
                                                $pshsdInsert->fee = $fee;
                                                $pshsdInsert->feeMobile = $feeMobile;
                                                if (isset($marketplaceAccount->config['defaultCpcF'])) {
                                                    $pshsdInsert->feeCustomer = $marketplaceAccount->config['defaultCpcF'];
                                                } else {
                                                    $pshsdInsert->feeCustomer = 0.25;
                                                }
                                                if (isset($marketplaceAccount->config['defaultCpcFM'])) {
                                                    $pshsdInsert->feeCustomerMobile = $marketplaceAccount->config['defaultCpcFM'];
                                                } else {
                                                    $pshsdInsert->feeCustomerMobile = 0.25;
                                                }

                                            }
                                            $pshsdInsert->status = 0;
                                            $pshsdInsert->lastUpdate = '2011-01-01 00:00:00';
                                            $pshsdInsert->productStatusAggregatorId = 2;
                                            $pshsdInsert->insert();

                                        }

                                    }
                                }
                                \Monkey::app()->applicationLog('CAggregatorHasProductJob','log','End Work  prepare for publishing From ' . $marketplace->name,'','');
                            }
                        }
                    }
                }
            }

              \Monkey::app()->applicationLog('CAggregatorHasProductJob','log','End Work publishing','','');
        } catch (\Throwable $e) {
              \Monkey::app()->applicationLog('CAggregatorHasProductJob','eror','ERROR Work publishing',$e->getMessage() . '-' . $e->getLine());

        }
        try {
              \Monkey::app()->applicationLog('CAggregatorHasProductJob','log','startPublish','','');

            $marketplaces = $marketplaceRepo->findBy(['type' => 'cpc']);
            foreach ($marketplaces as $marketplace) {
                $marketplaceAccounts = $marketplaceAccountRepo->findBy(['marketplaceId' => $marketplace->id,'isActive' => 1]);
                foreach($marketplaceAccounts as $marketplaceAccount) {
                    if ($marketplaceAccount) {
                        if ($marketplaceAccount->isActive == 1) {

                            \Monkey::app()->applicationLog('CAggregatorHasProductJob','error','Working to Select Eligible Products to ' . $marketplace->name,'','');
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
where shp.productStatusAggregatorId=2 and shp.aggregatorHasShopId =' . $marketplaceAccount->config['aggregatorHasShopId'];
                            $products = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                            foreach ($products as $product) {
                                $marketProduct = $phphmhsRepo->findOneBy(['productId' => $product['productId'],'productVariantId' => $product['productVariantId'],'aggregatorHasShopId' => $marketplaceAccount->config['aggregatorHasShopId']]);
                                if ($marketProduct) {
                                    if ($product['status'] == 2) {
                                        $marketProduct->priceModifier = $product['priceModifier'];
                                        $marketProduct->fee = $product['fee'];
                                        $marketProduct->feeCustomer = $product['feeCustomer'];
                                        $marketProduct->feeCustomerMobile = $product['feeCustomerMobile'];
                                        $marketProduct->feeMobile = $product['feeMobile'];
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
                                        $marketProductInsert->priceModifier = $product['priceModifier'];
                                        $marketProductInsert->fee = $product['fee'];
                                        $marketProductInsert->feeCustomer = $product['feeCustomer'];
                                        $marketProductInsert->feeCustomerMobile = $product['feeCustomerMobile'];
                                        $marketProductInsert->feeMobile = $product['feeMobile'];
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
                                        $marketProduct->priceModifier = $product['priceModifier'];
                                        $marketProduct->fee = $product['fee'];
                                        $marketProduct->feeCustomer = $product['feeCustomer'];
                                        $marketProduct->feeCustomerMobile = $product['feeCustomerMobile'];
                                        $marketProduct->feeMobile = $product['feeMobile'];
                                        $marketProduct->istoWork = 0;
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
                                    $marketProductInsert->priceModifier = $product['priceModifier'];
                                    $marketProductInsert->fee = $product['fee'];
                                    $marketProductInsert->feeCustomer = $product['feeCustomer'];
                                    $marketProductInsert->feeCustomerMobile = $product['feeCustomerMobile'];
                                    $marketProductInsert->feeMobile = $product['feeMobile'];
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
                                \Monkey::app()->applicationLog('CAggregatorHasProductJob','log','End Work   ' . $product['productId'] . '-' . $product['productVariantId'],'');
                            }
                        }
                    }

                    \Monkey::app()->applicationLog('CAggregatorHasProductJob','log','End Work Publish for  ' . $marketplace->name,'');

                }
            }
              \Monkey::app()->applicationLog('CAggregatorHasProductJob','log','End Work Publishing Eligible Products to Aggregator  Table','');
            return 'Fine Pubblicazione';
        } catch
        (\Throwable $e) {
              \Monkey::app()->applicationLog('CMarketplaceHasProductJob','error','ERROR Work Publishing Eligible Products to Aggregator',$e->getMessage() . '-' . $e->getLine());
            return 'errore fine pubblicazione '.$e-getMessage();
        }
    }

}
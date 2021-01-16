<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CAggregatorHasProduct;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\repositories\CPrestashopHasProductRepo;

/**
 * Class CShopALikeAggregatorProductListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 14/03/2019
 * @since 1.0
 */
class CShopALikeAggregatorProductListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "
            SELECT
              concat(mahp.productId, '-', mahp.productVariantId) AS productCode,
              mahp.productId,
              mahp.productVariantId,
              pps.price,
              pb.name  as `brand`, 
              p.externalId AS externalId,
              mahp.marketplaceId as marketplaceId,
              mahp.marketplaceAccountId as marketplaceAccountId,     
              if(mahp.titleModified='1','si','no') as titleModified,  
             concat(mahp.marketplaceProductId,' | ',s.name, ' | ', m.name, ' | Fee: ', mahp.fee,' | Fee Mobile: ', mahp.feeMobile, ' | Fee Customer: ', mahp.feeCustomer,' | Fee  Customer Mobile: ', mahp.feeCustomerMobile,' | Price Modifier: ', mahp.priceModifier, ' | Titolo modificato: ', mahp.titleModified,' | Operazione: ',if(mahp.insertionDate=null,'da inserire ','inserito '),if(mahp.lastResponse=null, concat('Eseguito ',mahp.lastResponse),concat('Fallito ',mahp.lastResponse))  ) AS marketplaceAssociation,
              p.isOnSale AS pickySale,
              p.qty as totalQty,
              PS.name as productStatus,    
              mahp.lastRevised as lastRevised,
              if(mahp.hasError='1','si','no') as hasError,     
              ahs.name as aggregatorName,  
              mahp.aggregatorHasShopId as aggregatorHasShopId,
              ahp.status,
              ahp.marketplaceProductId,
              psm.`name` as productStatusAggregatorId,     
              mahp.marketplaceProductId as markeplaceProductId,
              ma.name as marketplaceAccount,
              
              if((isnull(p.dummyPicture) OR (p.dummyPicture = 'bs-dummy-16-9.png')), 'no', 'sì')            AS dummy,
               concat(shop.id, '-', shop.name)                                                                     AS shop,
               concat(pse.name, ' ', pse.year)                                                               AS season,
               psiz.name                                                                                             AS stock
            FROM MarketplaceAccountHasProduct mahp 
            join MarketplaceAccount  ma on mahp.marketplaceId=ma.marketplaceId and mahp.marketplaceAccountId=ma.id 
            JOIN AggregatorHasProduct ahp   ON mahp.productId = ahp.productId AND mahp.productVariantId = ahp.productVariantId
            left JOIN ProductStatusAggregator psm on ahp.productStatusAggregatorId=psm.id
            left JOIN ProductPublicSku pps ON pps.productId = mahp.productId AND pps.productVariantId = mahp.productVariantId
            left JOIN Product p ON mahp.productId = p.id AND mahp.productVariantId = p.productVariantId    
            LEFT JOIN ProductStatus PS on p.productStatusId=PS.id       
            LEFT JOIN ProductBrand pb on p.productBrandId=pb.id
        
            LEFT JOIN AggregatorHasShop ahs ON ahs.id = mahp.aggregatorHasShopId
            LEFT JOIN Shop s ON ahs.shopId = s.id
            LEFT JOIN Marketplace m ON ahs.marketplaceId = m.id
            LEFT JOIN AggregatorHasShop mhs2 ON ahp.aggregatorHasShopId = mhs2.id
            LEFT JOIN Shop s2 ON mhs2.shopId = s2.id
            LEFT JOIN Marketplace m2 ON mhs2.marketplaceId = m2.id
            JOIN ShopHasProduct sp
                    ON (p.id, p.productVariantId) = (sp.productId, sp.productVariantId)
                  JOIN Shop shop ON shop.id = sp.shopId
                   JOIN ProductSeason pse ON p.productSeasonId = pse.id
                    
                   LEFT JOIN (ProductSku psk
                    JOIN ProductSize psiz ON psk.productSizeId = psiz.id)
                    ON (p.id, p.productVariantId) = (psk.productId, psk.productVariantId)
            where p.qty>0  and   mahp.marketplaceId=5  and mahp.isDeleted=0
            GROUP BY mahp.productId, mahp.productVariantId,mahp.marketplaceId,mahp.marketplaceAccountId 
            order by pse.id desc

        ";


        $datatable = new CDataTables($sql,['productId','productVariantId','marketplaceId','marketplaceAccountId'],$_GET,true);

        $datatable->doAllTheThings();

        /** @var CAggregatorHasProduct $phpRepo */
        $phpRepo = \Monkey::app()->repoFactory->create('AggregatorHasProduct');

        /** @var CRepo $mhsRepo */
        $mhsRepo = \Monkey::app()->repoFactory->create('AggregatorHasShop');
        $productStatusAggregatorRepo = \Monkey::app()->repoFactory->create('ProductStatusAggregator');
        $mahpRepo = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct');
        $marepo=\Monkey::app()->repoFactory->create('MarketplaceAccount');
        foreach ($datatable->getResponseSetData() as $key => $row) {


            /** @var CMarketplaceAccountHasProduct $marketplaceAccountHasProduct */
            $php = $mahpRepo->findOneBy($row);
            $row['productCode'] = $php->productId . '-' . $php->productVariantId;
            $associations = '';
            $onSale = '';
            $salePrice = '';
            $refMarketplaceId = '';

            $aggregatorHasShop = \Monkey::app()->repoFactory->create('AggregatorHasShop')->findOneBy(['id' => $php->aggregatorHasShopId]);
            if ($aggregatorHasShop) {
                $shop = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $aggregatorHasShop->shopId]);
                $marketplace = \Monkey::app()->repoFactory->create('Marketplace')->findOneBy(['id' => $aggregatorHasShop->marketplaceId]);

                $associations .= $php->marketplaceProductId . ' | ' . $shop->name . ' | ' . $marketplace->name . ' |<br> Fee Cost: ' . $php->fee . ' ( ' . $php->feeMobile . ' ) | ' . ' |<br> FeeCustomer: ' . $php->feeCustomer . ' ( ' . $php->feeCustomerMobile . ' ) | Price Modifier: ' . $php->priceModifier . ' |<br> Titolo modificato: ' . ($php->titleModified == 0 ? 'No' : 'Yes') . '<br>' . ' |<br> Operazione: ' . $php->lastUpdate . ' ' . ($php->lastResponse == null ? 'Eseguita ' : 'Fallito ') . '<br><hr>';

            }
            $marketplaceAccount=$marepo->findOneBy(['id'=>$php->marketplaceAccountId,'marketplaceId'=>$php->marketplaceId]);


            $row['marketplaceAssociation'] = $associations;
            /** @var CAggregatorHasProduct $ahp */
            $ahp = $phpRepo->findOneBy(['productId' => $php->productId,'productVariantId' => $php->productVariantId,'aggregatorHasShopId' => $php->aggregatorHasShopId]);

            switch ($ahp->status) {
                case 1:
                    $row['status'] = CAggregatorHasProduct::UPDATED;
                    break;
                case 2:
                    $row['status'] = CAggregatorHasProduct::TOUPDATE;
                    break;
                default:
                    $row['status'] = '';
                    break;
            }


            $row['brand'] = $php->product->productBrand->name;
            $row['productStatus'] = $php->product->productStatus->name;
            $isOnSale = $php->product->isOnSale == 0 ? ' Saldo No' : ' Saldo Si';
            $row['price'] = $php->product->getDisplayPrice() . ' (' . $php->product->getDisplaySalePrice() . ')<br>' . $isOnSale;
            $row['marketplaceProductId'] = $php->marketplaceProductId;
            $productStatusAggregator = $productStatusAggregatorRepo->findOneBy(['id' => $ahp->productStatusAggregatorId]);
            if ($productStatusAggregator) {
                $row['productStatusAggregatorId'] = $productStatusAggregator->name;
            } else {
                $row['productStatusAggregatorId'] = '';
            }
            $row['marketplaceAccount']=$marketplaceAccount->name;
            $row['dummy'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $php->product->getDummyPictureUrl() . '" /></a>';
            $row['shop'] = '<span class="small">' . $php->product->getShops('<br />',true) . '</span>';
            $row['season'] = '<span class="small">' . $php->product->productSeason->name . " " . $php->product->productSeason->year . '</span>';
            $row['totalQty'] = '<span class="small">' . $php->product->qty . '</span>';
            $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="' . $php->product->printId() . '"></table>';
            $row['externalId'] = '<span class="small">' . $php->product->itemno . '</span>';


            /** @var CAggregatorHasShop $mhsCron */
            $mhsCron = $mhsRepo->findOneBy(['id' => $php->aggregatorHasShopId]);

            $row['cronjobOperation'] = '';
            $row['cronjobReservation'] = '';




            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }

}
<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\repositories\CPrestashopHasProductRepo;

/**
 * Class CEbayMarketplaceProductListAjaxController
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
class CEbayMarketplaceProductListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "
            SELECT
              concat(php.productId, '-', php.productVariantId) AS productCode,
              php.productId,
              php.productVariantId,
              pps.price,
              pb.name  as `brand`, 
              p.externalId AS externalId,
                   

              group_concat(concat(phphmhs.refMarketplaceId,' | ',s.name, ' | ', m.name, ' | Price: ', phphmhs.price,' | Sale price: ', phphmhs.salePrice,' | Sale: ', phphmhs.isOnSale, ' | Titolo modificato: ', phphmhs.titleModified,' | Aggiornamento: ',if(phphmhs.result=1, concat('Eseguito ',phphmhs.lastTimeOperation),concat('Fallito ',phphmhs.lastTimeOperation))  )) AS marketplaceAssociation,
              p.isOnSale AS pickySale,
              p.qty as totalQty,
              PS.name as productStatus,     
              group_concat(concat(s.name, ' | ', m.name, ' | Sale: ', phphmhs.isOnSale, ' | Titolo modificato: ', phphmhs.titleModified)) AS sale,
              group_concat(concat(s.name, ' | ', m.name, ' | Sale price: ', phphmhs.salePrice)) AS salePrice,
              php.status,
              php.prestaId,
              psm.`name` as productStatusMarketplaceId,     
              phphmhs.refMarketplaceId as refMarketplaceId,
              concat(s2.name, ' | ', m2.name) AS cronjobReservation,
              concat('Type operation: ', php.modifyType, ' | Operation amount: ', php.variantValue) AS cronjobOperation,
              if((isnull(p.dummyPicture) OR (p.dummyPicture = 'bs-dummy-16-9.png')), 'no', 'sì')            AS dummy,
               concat(shop.id, '-', shop.name)                                                                     AS shop,
               concat(pse.name, ' ', pse.year)                                                               AS season,
               psiz.name                                                                                             AS stock
            FROM PrestashopHasProduct php
                join ProductStatusMarketplace psm on php.productStatusMarketplaceId=psm.id
            JOIN ProductPublicSku pps ON pps.productId = php.productId AND pps.productVariantId = php.productVariantId
            left JOIN Product p ON php.productId = p.id AND php.productVariantId = p.productVariantId    
            LEFT JOIN ProductStatus PS on p.productStatusId=PS.id       
            LEFT JOIN ProductBrand pb on p.productBrandId=pb.id
            JOIN PrestashopHasProductHasMarketplaceHasShop phphmhs ON php.productId = phphmhs.productId AND php.productVariantId = phphmhs.productVariantId and php.marketplaceHasShopId=phphmhs.marketplaceHasShopId
            LEFT JOIN MarketplaceHasShop mhs ON mhs.id = phphmhs.marketplaceHasShopId
            LEFT JOIN Shop s ON mhs.shopId = s.id
            LEFT JOIN Marketplace m ON mhs.marketplaceId = m.id
            LEFT JOIN MarketplaceHasShop mhs2 ON php.marketplaceHasShopId = mhs2.id
            LEFT JOIN Shop s2 ON mhs2.shopId = s2.id
            LEFT JOIN Marketplace m2 ON mhs2.marketplaceId = m2.id
            JOIN ShopHasProduct sp
                    ON (p.id, p.productVariantId) = (sp.productId, sp.productVariantId)
                  JOIN Shop shop ON shop.id = sp.shopId
                   JOIN ProductSeason pse ON p.productSeasonId = pse.id
                    
                   LEFT JOIN (ProductSku psk
                    JOIN ProductSize psiz ON psk.productSizeId = psiz.id)
                    ON (p.id, p.productVariantId) = (psk.productId, psk.productVariantId)
            where p.qty>0 and m.id=4
            GROUP BY phphmhs.productId, phphmhs.productVariantId,phphmhs.marketplaceHasShopId  order by phphmhs.marketplaceHasShopId asc
        ";


        $datatable = new CDataTables($sql, ['productId', 'productVariantId'], $_GET, true);

        $datatable->doAllTheThings();

        /** @var CPrestashopHasProductRepo $phpRepo */
        $phpRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');

        /** @var CRepo $mhsRepo */
        $mhsRepo = \Monkey::app()->repoFactory->create('MarketplaceHasShop');
        $productStatusMarketplaceRepo=\Monkey::app()->repoFactory->create('ProductStatusMarketplace');
        foreach ($datatable->getResponseSetData() as $key => $row) {

            /** @var CPrestashopHasProduct $php */
            $php = $phpRepo->findOneBy($row);

            $row['productCode'] = $php->productId . '-' . $php->productVariantId;

            $associations = '';
            $onSale = '';
            $salePrice = '';
            $refMarketplaceId='';




          /** @var CPrestashopHasProductHasMarketplaceHasShop $pHPHmHs */
            foreach ($php->prestashopHasProductHasMarketplaceHasShop as $pHPHmHs) {
                $associations .= $pHPHmHs->refMarketplaceId. ' | '.$pHPHmHs->marketplaceHasShop->shop->name . ' | ' . $pHPHmHs->marketplaceHasShop->marketplace->name . ' |<br> Price: ' . $pHPHmHs->price . ' ( ' . $pHPHmHs->salePrice . ' ) | Saldo: ' . ($pHPHmHs->isOnSale == 0 ? 'No' : 'Si') . ' |<br> Titolo modificato: ' . ($pHPHmHs->titleModified == 0 ? 'No' : 'Yes') . '<br>'. ' |<br> Aggiornamento: ' . ($pHPHmHs->result == 1 ? 'Eseguito '.$pHPHmHs->lastTimeOperation : 'Fallito '.$pHPHmHs->lastTimeOperation) . '<br><hr>';


            }
            $row['marketplaceAssociation'] = $associations;




            switch ($php->status) {
                case 1:
                    $row['status'] = CPrestashopHasProduct::UPDATED;
                    break;
                case 2:
                    $row['status'] = CPrestashopHasProduct::TOUPDATE;
                    break;
                default:
                    $row['status'] = '';
                    break;
            }
            $row['brand']=$php->product->productBrand->name;
            $row['productStatus']=$php->product->productStatus->name;
            $isOnSale=$php->product->isOnSale == 0 ? ' Saldo No' : ' Saldo Si';
            $row['price'] = $php->product->getDisplayPrice() . ' (' . $php->product->getDisplaySalePrice() . ')<br>' .$isOnSale ;
            $row['prestaId'] = $php->prestaId;
            $productStatusMarketplace=$productStatusMarketplaceRepo->findOneBy(['id'=>$php->productStatusMarketplaceId]);
            if($productStatusMarketplace) {
                $row['productStatusMarketplaceId'] = $productStatusMarketplace->name;
            }else{
                $row['productStatusMarketplaceId'] = '';
            }
            $row['dummy'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $php->product->getDummyPictureUrl() . '" /></a>';
            $row['shop'] = '<span class="small">' . $php->product->getShops('<br />', true) . '</span>';
            $row['season'] = '<span class="small">' . $php->product->productSeason->name . " " . $php->product->productSeason->year .  '</span>';
            $row['totalQty'] = '<span class="small">' .$php->product->qty.'</span>';
            $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="'.$php->product->printId().'"></table>';
            $row['externalId']='<span class="small">' . $php->product->itemno .  '</span>';


            /** @var CMarketplaceHasShop $mhsCron */
            $mhsCron = $mhsRepo->findOneBy(['id' => $php->marketplaceHasShopId]);

            $row['cronjobOperation'] = '';
            $row['cronjobReservation'] = '';

            if (!is_null($mhsCron)) {
                $row['cronjobReservation'] = $mhsCron->shop->name . ' | ' . $mhsCron->marketplace->name;
                $row['cronjobOperation'] = 'Type operation: ' . $php->modifyType . ' | Operation amount: ' . $php->variantValue;
            }

            $datatable->setResponseDataSetRow($key, $row);
        }

        return $datatable->responseOut();
    }

}
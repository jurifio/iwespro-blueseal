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
              phphmhs.price as marketplacePrice,
              phphmhs.salePrice as marketplaceSalePrice,
             concat(p.itemno, ' # ', pv.name)                                                              AS cpf,
              if(phphmhs.isOnSale=1,'si','no'),     
             if(phphmhs.titleModified=1,'si','no'),     
              phphmhs.isOnSale AS isOnSale,
              p.qty as totalQty,
              PS.name as productStatus,   
               concat(p.itemno, ' # ', pv.name)                                                              AS title,     
              if(phphmhs.isOnSale=1,phphmhs.salePrice,phphmhs.price) as activePrice,    
              php.status,
              php.prestaId,
              '' as tableSaldi,     
              psm.`name` as productStatusMarketplaceId,     
              phphmhs.refMarketplaceId as refMarketplaceId,
              concat(s2.name, ' | ', m2.name) AS cronjobReservation,
              concat('Type operation: ', php.modifyType, ' | Operation amount: ', php.variantValue) AS cronjobOperation,
              if((isnull(p.dummyPicture) OR (p.dummyPicture = 'bs-dummy-16-9.png')), 'no', 'sì')            AS dummy,
               concat(shop.id, '-', shop.name)                                                                     AS shop,
               concat(pse.name, ' ', pse.year)                                                               AS season,
               psiz.name                                                                                             AS stock,
               mhs.name as marketplaceShopName    
            FROM PrestashopHasProduct php
                join ProductStatusMarketplace psm on php.productStatusMarketplaceId=psm.id
            JOIN ProductPublicSku pps ON pps.productId = php.productId AND pps.productVariantId = php.productVariantId
             join ProductVariant pv on php.productVariantId=pv.id   
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
            where p.qty>0 and m.id=3 and phphmhs.isPublished=1 and phphmhs.refMarketplaceId is not null
            GROUP BY phphmhs.productId, phphmhs.productVariantId,phphmhs.marketplaceHasShopId  order by phphmhs.marketplaceHasShopId asc
        ";


        $datatable = new CDataTables($sql,['productId','productVariantId'],$_GET,true);

        $datatable->doAllTheThings();

        /** @var CPrestashopHasProductRepo $phpRepo */
        $phpRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');

        /** @var CRepo $mhsRepo */
        $mhsRepo = \Monkey::app()->repoFactory->create('MarketplaceHasShop');
        $mpaRepo = \Monkey::app()->repoFactory->create('MarketplaceAccount');
        $productStatusMarketplaceRepo = \Monkey::app()->repoFactory->create('ProductStatusMarketplace');
        foreach ($datatable->getResponseSetData() as $key => $row) {

            /** @var CPrestashopHasProduct $php */
            $php = $phpRepo->findOneBy($row);
            $row['cpf'] = $php->product->itemno . ' # ' . $php->product->productVariant->name;
            $row['productCode'] = $php->productId . '-' . $php->productVariantId;
            $row['refMarketplaceId'] = ($php->prestashopHasProductHasMarketplaceHasShop->refMarketplaceId)?$php->prestashopHasProductHasMarketplaceHasShop->refMarketplaceId:'';
            $row['marketplaceshopName'] = $php->marketplaceHasShop->name;
            $row['marketplacePrice'] = $php->prestashopHasProductHasMarketplaceHasShop->price;
            $row['marketplaceSalePrice'] = $php->prestashopHasProductHasMarketplaceHasShop->salePrice;
            if ($php->prestashopHasProductHasMarketplaceHasShop->isOnSale == 1) {
                $row['activePrice'] = $php->prestashopHasProductHasMarketplaceHasShop->salePrice;
            } else {
                $row['activePrice'] = $php->prestashopHasProductHasMarketplaceHasShop->price;
            }
            $row['titleModified'] = ($php->prestashopHasProductHasMarketplaceHasShop->titleModified == 1) ? 'si' : 'no';
            $row['isOnSale'] = ($php->prestashopHasProductHasMarketplaceHasShop->isOnSale == 1) ? 'si' : 'no';
            if ($php->prestashopHasProductHasMarketplaceHasShop->titleModified == 1 && $php->prestashopHasProductHasMarketplaceHasShop->isOnSale == 1) {
                $percSc = number_format(100 * ($php->prestashopHasProductHasMarketplaceHasShop->price - $php->prestashopHasProductHasMarketplaceHasShop->salePrice) / $php->prestashopHasProductHasMarketplaceHasShop->price,0);
                $name = $php->product->productBrand->name . ' Sconto del ' . $percSc . '% da ' . number_format($php->prestashopHasProductHasMarketplaceHasShop->price,'2','.','') . ' € a ' . number_format($php->prestashopHasProductHasMarketplaceHasShop->salePrice,'2','.','') . ' € ' .
                    $php->product->itemno
                    . ' ' .
                    $php->product->productColorGroup->productColorGroupTranslation->findOneByKey('langId',1)->name;
            } else {
                $name = $php->product->productCategoryTranslation->findOneByKey('langId',1)->name
                    . ' ' .
                    $php->product->productBrand->name
                    . ' ' .
                    $php->product->itemno
                    . ' ' .
                    $php->product->productColorGroup->productColorGroupTranslation->findOneByKey('langId',1)->name;
            }
            $row['title'] = $name;


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
            $row['brand'] = $php->product->productBrand->name;
            $row['productStatus'] = $php->product->productStatus->name;

            $row['price'] = $php->product->getDisplayPrice() . ' (' . $php->product->getDisplaySalePrice() . ')<br>' . $isOnSale;

            $productStatusMarketplace = $productStatusMarketplaceRepo->findOneBy(['id' => $php->productStatusMarketplaceId]);
            if ($productStatusMarketplace) {
                $row['productStatusMarketplaceId'] = $productStatusMarketplace->name;
            } else {
                $row['productStatusMarketplaceId'] = '';
            }
            $row['dummy'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $php->product->getDummyPictureUrl() . '" /></a>';
            $row['shop'] = '<span class="small">' . $php->product->getShops('<br />',true) . '</span>';
            $row['season'] = '<span class="small">' . $php->product->productSeason->name . " " . $php->product->productSeason->year . '</span>';
            $row['totalQty'] = '<span class="small">' . $php->product->qty . '</span>';
            $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="' . $php->product->printId() . '"></table>';
            $row['externalId'] = '<span class="small">' . $php->product->itemno . '</span>';
            $mpas = $mpaRepo->findBy(['marketplaceId' => 3,'isActive' => 1]);
            if($mpas) {
                foreach ($mpas as $mpa) {
                    if ($mpa->config['marketplaceHasShopId'] == $php->marketplaceHasShopId) {
                        $tableSaldi = '<table><tr><td colspan="2">periodi saldi</td></tr><tr><td>dal</td><td>al </td></tr>';
                        $dateStartPeriod1 = ($mpa->config['dateStartPeriod1'] != '') ? (new \DateTime($mpa->config['dateStartPeriod1']))->format('d-m-Y') : 'non definito';
                        $dateEndPeriod1 = ($mpa->config['dateEndPeriod1'] != '') ? (new \DateTime($mpa->config['dateEndPeriod1']))->format('d-m-Y') : 'non definito';
                        $dateStartPeriod2 = ($mpa->config['dateStartPeriod2'] != '') ? (new \DateTime($mpa->config['dateStartPeriod2']))->format('d-m-Y') : "non definito";
                        $dateEndPeriod2 = ($mpa->config['dateEndPeriod2'] != '') ? (new \DateTime($mpa->config['dateEndPeriod2']))->format('d-m-Y') : 'non definito';
                        $dateStartPeriod3 = ($mpa->config['dateStartPeriod3'] != '') ? (new \DateTime($mpa->config['dateStartPeriod3']))->format('d-m-Y') : "non definto";
                        $dateEndPeriod3 = ($mpa->config['dateEndPeriod3'] != '') ? (new \DateTime($mpa->config['dateEndPeriod3']))->format('d-m-Y') : "non definito";
                        $dateStartPeriod4 = ($mpa->config['dateStartPeriod4'] != '') ? (new \DateTime($mpa->config['dateStartPeriod4']))->format('d-m-Y') : 'non definito';
                        $dateEndPeriod4 = ($mpa->config['dateEndPeriod3'] != '') ? (new \DateTime($mpa->config['dateEndPeriod4']))->format('d-m-Y') : 'non definito';
                        $tableSaldi .= '<tr><td>' . $dateStartPeriod1 . '</td><td>' . $dateEndPeriod1 . '</td></tr>';
                        $tableSaldi .= '<tr><td>' . $dateStartPeriod2 . '</td><td>' . $dateEndPeriod2 . '</td></tr>';
                        $tableSaldi .= '<tr><td>' . $dateStartPeriod3 . '</td><td>' . $dateEndPeriod3 . '</td></tr>';
                        $tableSaldi .= '<tr><td>' . $dateStartPeriod4 . '</td><td>' . $dateEndPeriod4 . '</td></tr>';
                        $tableSaldi .= '</table>';
                        break;
                    }
                }
            }
            $row['tableSaldi']=$tableSaldi;

            /** @var CMarketplaceHasShop $mhsCron */
            $mhsCron = $mhsRepo->findOneBy(['id' => $php->marketplaceHasShopId]);

            $row['cronjobOperation'] = '';
            $row['cronjobReservation'] = '';

            if (!is_null($mhsCron)) {
                $row['cronjobReservation'] = $mhsCron->shop->name . ' | ' . $mhsCron->marketplace->name;
                $row['cronjobOperation'] = 'Type operation: ' . $php->modifyType . ' | Operation amount: ' . $php->variantValue;
            }

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }

}
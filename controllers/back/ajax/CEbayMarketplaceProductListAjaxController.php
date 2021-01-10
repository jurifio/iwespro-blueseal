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
                   phphmhs.lastTypeOperation as lastTypeOperation,
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
              phphmhs.marketplaceHasShopId as marketplaceHasShopId,     
              concat(s2.name, ' | ', m2.name) AS cronjobReservation,
              concat('Type operation: ', php.modifyType, ' | Operation amount: ', php.variantValue) AS cronjobOperation,
              if((isnull(p.dummyPicture) OR (p.dummyPicture = 'bs-dummy-16-9.png')), 'no', 'sì')            AS dummy,
               concat(shop.id, '-', shop.name)                                                                     AS shop,
               concat(pse.name, ' ', pse.year)                                                               AS season,
               psiz.name                                                                                             AS stock,
               mhs.name as marketplaceShopName    
            FROM PrestashopHasProductHasMarketplaceHasShop phphmhs join PrestashopHasProduct php ON php.productId = phphmhs.productId AND php.productVariantId = phphmhs.productVariantId and php.marketplaceHasShopId=phphmhs.marketplaceHasShopId
              left  join ProductStatusMarketplace psm on php.productStatusMarketplaceId=psm.id
            JOIN ProductPublicSku pps ON pps.productId = phphmhs.productId AND pps.productVariantId = phphmhs.productVariantId
             join ProductVariant pv on phphmhs.productVariantId=pv.id   
            left JOIN Product p ON phphmhs.productId = p.id AND phphmhs.productVariantId = p.productVariantId    
            LEFT JOIN ProductStatus PS on p.productStatusId=PS.id       
            LEFT JOIN ProductBrand pb on p.productBrandId=pb.id
    
            LEFT JOIN MarketplaceHasShop mhs ON mhs.id = phphmhs.marketplaceHasShopId
            LEFT JOIN Shop s ON mhs.shopId = s.id
            LEFT JOIN Marketplace m ON mhs.marketplaceId = m.id
            LEFT JOIN MarketplaceHasShop mhs2 ON phphmhs.marketplaceHasShopId = mhs2.id
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


        $datatable = new CDataTables($sql,['productId','productVariantId','marketplaceHasShopId'],$_GET,true);

        $datatable->doAllTheThings();


        $phpmsRepo=\Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');

        /** @var CRepo $mhsRepo */
        $mhsRepo = \Monkey::app()->repoFactory->create('MarketplaceHasShop');
        $mpaRepo = \Monkey::app()->repoFactory->create('MarketplaceAccount');
        foreach ($datatable->getResponseSetData() as $key => $row) {

            /** @var CPrestashopHasProductHasMarketplaceHasShop $php */
            $php = $phpmsRepo->findOneBy($row);
            $row['cpf'] = $php->product->itemno . ' # ' . $php->product->productVariant->name;
            $row['productCode'] = $php->productId . '-' . $php->productVariantId;
            $row['refMarketplaceId'] = ($php->refMarketplaceId) ?: '';
            $row['marketplaceShopName'] = $php->marketplaceHasShop->name;
            $row['marketplacePrice'] = $php->price;
            $row['marketplaceSalePrice'] = $php->salePrice;
            switch (true) {
                case $php->lastTypeOperation = 0;
                    $lastTypeOperation = 'da inserire';
                    break;
                case $php->lastTypeOperation = 1;
                    $lastTypeOperation = 'ADD';
                    break;
                case $php->lastTypeOperation = 2;
                    $lastTypeOperation = 'REVISE';
                    break;
            }
            $row['lastTypeOperation'] =$lastTypeOperation. ' '.(new \DateTime($php->lastTimeOperation))->format('d-m-Y H:i:s');
            if ($php->isOnSale == 1) {
                $row['activePrice'] = $php->salePrice;
            } else {
                $row['activePrice'] = $php->price;
            }
            $row['titleModified'] = ($php->titleModified == 1) ? 'si' : 'no';
            $row['isOnSale'] = ($php->isOnSale == 1) ? 'si' : 'no';
            if (($php->titleModified == 1) && ($php->isOnSale == 1)) {
                $percSc = number_format(100 * ($php->price - $php->salePrice) / $php->price,0);
                $name = $php->product->productBrand->name . ' Sconto del ' . $percSc . '% da ' . number_format($php->price,'2','.','') . ' € a ' . number_format($php->salePrice,'2','.','') . ' € ' .
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



            $row['brand'] = $php->product->productBrand->name;
            $row['productStatus'] = $php->product->productStatus->name;




            $row['dummy'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $php->product->getDummyPictureUrl() . '" /></a>';
            $row['shop'] = '<span class="small">' . $php->product->getShops('<br />',true) . '</span>';
            $row['season'] = '<span class="small">' . $php->product->productSeason->name . " " . $php->product->productSeason->year . '</span>';
            $row['totalQty'] = '<span class="small">' . $php->product->qty . '</span>';
            $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="' . $php->product->printId() . '"></table>';
            $row['externalId'] = '<span class="small">' . $php->product->itemno . '</span>';
            $mpas = $mpaRepo->findBy(['marketplaceId' => 3,'isActive' => 1]);
            $tableSaldi='';
            if($mpas) {
                foreach ($mpas as $mpa) {
                    if ($mpa->config['marketplaceHasShopId'] == $php->marketplaceHasShopId) {
                        $tableSaldi = '<table><tr><tr><td>dal</td><td>al </td></tr>';
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





            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }

}
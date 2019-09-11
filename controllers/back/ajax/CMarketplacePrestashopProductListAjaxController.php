<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\repositories\CPrestashopHasProductRepo;

/**
 * Class CMarketplacePrestashopProductListAjaxController
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
class CMarketplacePrestashopProductListAjaxController extends AAjaxController
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
              group_concat(concat(s.name, ' | ', m.name, ' | Price: ', phphmhs.price )) AS marketplaceAssociation,
              p.isOnSale AS pickySale,
              p.qty as totalQty,
              group_concat(concat(s.name, ' | ', m.name, ' | Sale: ', phphmhs.isOnSale, ' | Titolo modificato: ', phphmhs.titleModified)) AS sale,
              group_concat(concat(s.name, ' | ', m.name, ' | Sale price: ', phphmhs.salePrice)) AS salePrice,
              php.status,
              php.prestaId,
              concat(s2.name, ' | ', m2.name) AS cronjobReservation,
              concat('Type operation: ', php.modifyType, ' | Operation amount: ', php.variantValue) AS cronjobOperation,
              if((isnull(p.dummyPicture) OR (p.dummyPicture = 'bs-dummy-16-9.png')), 'no', 'sì')            AS dummy,
               concat(shop.id, '-', shop.name)                                                                     AS shop,
               concat(pse.name, ' ', pse.year)                                                               AS season,
               psiz.name                                                                                             AS stock
            FROM PrestashopHasProduct php
            JOIN ProductPublicSku pps ON pps.productId = php.productId AND pps.productVariantId = php.productVariantId
            JOIN Product p ON php.productId = p.id AND php.productVariantId = p.productVariantId
            LEFT JOIN ProductBrand pb on p.productBrandId=pb.id
            LEFT JOIN PrestashopHasProductHasMarketplaceHasShop phphmhs ON php.productId = phphmhs.productId AND php.productVariantId = phphmhs.productVariantId
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
            GROUP BY php.productId, php.productVariantId 
        ";


        $datatable = new CDataTables($sql, ['productId', 'productVariantId'], $_GET, true);

        $datatable->doAllTheThings();

        /** @var CPrestashopHasProductRepo $phpRepo */
        $phpRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');

        /** @var CRepo $mhsRepo */
        $mhsRepo = \Monkey::app()->repoFactory->create('MarketplaceHasShop');
        foreach ($datatable->getResponseSetData() as $key => $row) {

            /** @var CPrestashopHasProduct $php */
            $php = $phpRepo->findOneBy($row);

            $row['productCode'] = $php->productId . '-' . $php->productVariantId;

            $associations = '';
            $onSale = '';
            $salePrice = '';



          /** @var CPrestashopHasProductHasMarketplaceHasShop $pHPHmHs */
            foreach ($php->prestashopHasProductHasMarketplaceHasShop as $pHPHmHs) {
                $associations .= $pHPHmHs->marketplaceHasShop->shop->name . ' | ' . $pHPHmHs->marketplaceHasShop->marketplace->name . ' | Price: ' . $pHPHmHs->price . '<br>';
                $onSale .= $pHPHmHs->marketplaceHasShop->shop->name . ' | ' . $pHPHmHs->marketplaceHasShop->marketplace->name . ' | Sale: ' . ($pHPHmHs->isOnSale == 0 ? 'No' : 'Yes') . ' | Titolo modificato: ' . ($pHPHmHs->titleModified == 0 ? 'No' : 'Yes') . '<br>';
                $salePrice .= $pHPHmHs->marketplaceHasShop->shop->name . ' | ' . $pHPHmHs->marketplaceHasShop->marketplace->name . ' | Sale price: ' . $pHPHmHs->salePrice . '<br>';
            }
            $row['marketplaceAssociation'] = $associations;
            $row['sale'] = $onSale;
            $row['salePrice'] = $salePrice;


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
            $row['price'] = $php->product->getDisplayPrice() . ' (' . $php->product->getDisplaySalePrice() . ')';
            $row['pickySale'] = $php->product->isOnSale == 0 ? 'No' : 'Yes';
            $row['prestaId'] = $php->prestaId;
            $row['dummy'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $php->product->getDummyPictureUrl() . '" /></a>';
            $row['shop'] = '<span class="small">' . $php->product->getShops('<br />', true) . '</span>';
            $row['season'] = '<span class="small">' . $php->product->productSeason->name . " " . $php->product->productSeason->year .  '</span>';
            $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="'.$php->product->printId().'"></table>';


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
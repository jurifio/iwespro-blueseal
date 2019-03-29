<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProduct;
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
        $sql="
            SELECT
              concat(php.productId, '-', php.productVariantId) AS productCode,
              php.productId,
              php.productVariantId,
              pps.price,
              group_concat(concat(s.name, ' | ', m.name, ' | Price: ', phphmhs.price )) as marketplaceAssociation,
              p.isOnSale as pickySale,
              group_concat(concat(s.name, ' | ', m.name, ' | Sale: ', phphmhs.isOnSale)) as sale,
              php.status,
              php.prestaId
            FROM PrestashopHasProduct php
            JOIN ProductPublicSku pps ON pps.productId = php.productId AND pps.productVariantId = php.productVariantId
            JOIN Product p ON php.productId = p.id AND php.productVariantId = p.productVariantId
            LEFT JOIN PrestashopHasProductHasMarketplaceHasShop phphmhs ON php.productId = phphmhs.productId AND php.productVariantId = phphmhs.productVariantId
            LEFT JOIN MarketplaceHasShop mhs ON mhs.id = phphmhs.marketplaceHasShopId
            LEFT JOIN Shop s ON mhs.shopId = s.id
            LEFT JOIN Marketplace m ON mhs.marketplaceId = m.id
            GROUP BY php.productId, php.productVariantId 
        ";


        $datatable = new CDataTables($sql, ['productId','productVariantId'], $_GET, true);

        $datatable->doAllTheThings();

        /** @var CPrestashopHasProductRepo $phpRepo */
        $phpRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');

        foreach ($datatable->getResponseSetData() as $key => $row) {

            /** @var CPrestashopHasProduct $php */
            $php = $phpRepo->findOneBy($row);

            $row['productCode'] = $php->productId . '-' . $php->productVariantId;

            $associations = '';
            $onSale = '';

            /** @var CPrestashopHasProductHasMarketplaceHasShop $pHPHmHs */
            foreach ($php->prestashopHasProductHasMarketplaceHasShop as $pHPHmHs){
                $associations .= $pHPHmHs->marketplaceHasShop->shop->name . ' | ' . $pHPHmHs->marketplaceHasShop->marketplace->name . ' | Price: ' . $pHPHmHs->price . '<br>';
                $onSale .= $pHPHmHs->marketplaceHasShop->shop->name . ' | ' . $pHPHmHs->marketplaceHasShop->marketplace->name . ' | Sale: ' . ($pHPHmHs->isOnSale === '0' ? 'No' : 'Yes') . '<br>';
            }
            $row['marketplaceAssociation'] = $associations;
            $row['sale'] = $onSale;

            switch ($php->status){
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

            $row['price'] = $php->product->getDisplayPrice();
            $row['pickySale'] = $php->product->isOnSale === '0' ? 'No' : 'Yes';
            $row['prestaId'] = $php->prestaId;
            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }

}
<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CPrestashopHasProduct;
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
              group_concat(concat(s.name, ' | ', m.name)) as marketplaceAssociation,
              php.status,
              php.prestaId
            FROM PrestashopHasProduct php
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
            /** @var CMarketplaceHasShop $marketplaceHasShop */
            foreach ($php->marketplaceHasShop as $marketplaceHasShop){
                $associations .= $marketplaceHasShop->shop->name . ' | ' . $marketplaceHasShop->marketplace->name . '<br>';
            }
            $row['marketplaceAssociation'] = $associations;

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

            $row['prestaId'] = $php->prestaId;

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }

}
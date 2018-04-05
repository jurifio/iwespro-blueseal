<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CProductHasShooting;
use bamboo\domain\entities\CShooting;
use bamboo\domain\repositories\CProductHasShootingRepo;
use bamboo\domain\repositories\CShootingRepo;


/**
 * Class CShootingDetailsListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/03/2018
 * @since 1.0
 */
class CShootingDetailsListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {

        $shootingId = $this->data["shootingid"];

        $sql = "SELECT concat(phs.productId,'-',phs.productVariantId) as DT_RowId,
                  phs.productId,
                  phs.productVariantId,
                  phs.shootingId,
                  phs.creationDate,
                       sp.name as shopName,
                  phs.progressiveLineNumber,
                   concat(ps.name,'-',ps.year) as season,
                   concat(ifnull(p.externalId, ''), '-', ifnull(dp.extId, ''), '-', ifnull(ds.extSkuId, '')) AS externalId,
                    concat(p.itemno, ' # ', pv.name)                                                              AS cpf,
                    pb.name as productBrandName,
                    p.qty as hasQty
                FROM ProductHasShooting phs
                  JOIN Shooting s ON phs.shootingId = s.id
                  JOIN Product p ON phs.productVariantId = p.productVariantId
                  JOIN ProductSeason ps ON p.productSeasonId = ps.id
                  JOIN ProductBrand pb ON p.productBrandId = pb.id
                  JOIN ProductVariant pv ON p.productVariantId = pv.id
                  JOIN ShootingBooking sb ON s.id = sb.shootingId
                  JOIN Shop sp ON sb.shopId = sp.id
                  JOIN ShopHasProduct shophasprod
                    ON (p.id, p.productVariantId) = (shophasprod.productId, shophasprod.productVariantId)
                  LEFT JOIN (DirtyProduct dp
                    JOIN DirtySku ds ON dp.id = ds.dirtyProductId)
                    ON (shophasprod.productId,shophasprod.productVariantId,shophasprod.shopId) = (dp.productId,dp.productVariantId,dp.shopId)
                WHERE s.id = $shootingId
                GROUP BY phs.productId, phs.productVariantId, phs.progressiveLineNumber
               ";

        $datatable = new CDataTables($sql, ['productId','productVariantId', 'progressiveLineNumber'], $_GET, true);

        $datatable->doAllTheThings(false);

        /** @var CShooting $shooting */
        $shooting = \Monkey::app()->repoFactory->create('Shooting')->findOneBy(['id'=>$shootingId]);

        /** @var CProductHasShootingRepo $phsRepo */
        $phsRepo = \Monkey::app()->repoFactory->create('ProductHasShooting');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CProductHasShooting $phs */
            $phs = $phsRepo->findOneBy(['productId'=>$row["productId"], 'productVariantId'=>$row["productVariantId"], 'shootingId'=>$shootingId, 'progressiveLineNumber'=>$row["progressiveLineNumber"]]);
            $row["productId"] = $phs->productId;
            $row["productVariantId"] = $phs->productVariantId;
            $row["progressiveLineNumber"] = $phs->progressiveLineNumber;
            $row["creationDate"] = $phs->creationDate;
            $row["shootingId"] = $phs->shootingId;
            $row["shopName"] = $shooting->shootingBooking->shop->name;
            $row["DT_RowId"] = $phs->productId.'-'.$phs->productVariantId;
            $row["season"] = $phs->product->productSeason->name.'-'.$phs->product->productSeason->year;
            $row["externalId"] = $phs->product->getShopExtenalIds(', ');
            $row["cpf"] = $phs->product->printCpf();
            $row['dummy'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $phs->product->getDummyPictureUrl() . '" /></a>';
            $row["productBrandName"] = $phs->product->productBrand->name;

            $qty = 0;
            foreach ($phs->product->productSku as $sku) {
                $qty += $sku->stockQty;
            }
            $row["hasQty"] = $qty;

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();

    }

}
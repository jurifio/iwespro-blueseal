<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CShooting;
use bamboo\domain\entities\CShootingBooking;
use bamboo\domain\entities\CShootingBookingHasProductType;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\domain\repositories\CShootingRepo;


/**
 * Class CShootingAcceptProductListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 25/10/2018
 * @since 1.0
 */
class CShootingAcceptProductListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $shopsIds = \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser();

        $user = \Monkey::app()->getUser();
        $allShops = $user->hasPermission('allShops');

        $sql = "SELECT
  `p`.`id`                                             AS `id`,
  `p`.`productVariantId`                               AS `productVariantId`,
  concat(`p`.`id`, '-', `p`.`productVariantId`)        AS `code`,
  `pb`.`name`                                          AS `brand`,
  concat(`p`.`itemno`, ' # ', `pv`.`name`)             AS `cpf`,
  `s`.`id`                                             AS `shopId`,
  `s`.`title`                                          AS `shop`,
  concat(phs.shootingId)                               AS shooting,
  concat(doc.number)                                   AS doc_number,
  `p`.`creationDate`                                   AS `creationDate`,
  concat(ifnull(p.externalId, ''), '-', ifnull(dp.extId, ''), '-', ifnull(ds.extSkuId, '')) AS externalId,
  `pss`.`name`                                         AS `status`,
   `PS`.`name` as season,
    `p`.id as qty  
FROM `Product` `p`
  JOIN `ShopHasProduct` `shp` ON (`p`.`id`, `p`.`productVariantId`) = (`shp`.`productId`, `shp`.`productVariantId`)
     JOIN `ProductSeason` `PS` on p.productSeasonId = `PS`.`id`
  LEFT JOIN (DirtyProduct dp
    JOIN DirtySku ds ON dp.id = ds.dirtyProductId)
    ON (shp.productId,shp.productVariantId,shp.shopId) = (dp.productId,dp.productVariantId,dp.shopId)
  JOIN `ProductStatus` `pss` ON `pss`.`id` = `p`.`productStatusId`
 
  JOIN `ProductVariant` `pv` ON `p`.`productVariantId` = `pv`.`id`
  JOIN `ProductBrand` `pb` ON `p`.`productBrandId` = `pb`.`id`
  JOIN `Shop` `s` ON `s`.`id` = `shp`.`shopId`
  LEFT JOIN (
      ProductHasShooting phs
      JOIN Shooting shoot ON phs.shootingId = shoot.id
      LEFT JOIN Document doc ON shoot.friendDdt = doc.id)
    ON p.productVariantId = phs.productVariantId AND p.id = phs.productId
ORDER BY `p`.`creationDate` DESC
               ";

        $datatable = new CDataTables($sql,['id','productVariantId','shopId'],$_GET,true);
        $datatable->addCondition('shopId',$shopsIds);
        if (!$allShops) $datatable->addLikeCondition('status','Fuso',true);

        $datatable->doAllTheThings();

        /** @var CDocumentRepo $docRepo */
        $docRepo = \Monkey::app()->repoFactory->create('Document');

        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');
        $productSizeRepo = \Monkey::app()->repoFactory->create('ProductSize');
        $productSeasonRepo=\Monkey::app()->repoFactory->create('ProductSeason');
        foreach ($datatable->getResponseSetData() as $key => $row) {

            $val = $productRepo->findOneBy($row);
            /** @var CProduct $val */
            $row["DT_RowId"] = $val->printId();
            $row["DT_RowClass"] = 'colore';
            $row['code'] = $val->id . '-' . $val->productVariantId;

            $row["row_shop"] = $val->getShops('|',true);
            $row['shop'] = '<span>';
            $row['shop'] .= $val->getShops('<br />');
            $row['shop'] .= '</span>';


            $row['externalId'] = '<span class="small">';
            $row['externalId'] .= $val->getShopExtenalIds('<br />');
            $row['externalId'] .= '</span>';

            $row['cpf'] = '<span class="small">';
            $row['cpf'] .= $val->itemno . ' # ' . $val->productVariant->name;
            $row['cpf'] .= '</span>';

            $row['brand'] = $val->productBrand->name;

            $row['creationDate'] = $val->creationDate;
            $sids = "";
            $ddtNumbers = "";

            /** @var CShooting $singleShooting */
            foreach ($val->shooting as $singleShooting) {
                $sids .= '<br />' . $singleShooting->id;
                $ddtNumbers .= '<br />' . $docRepo->findShootingFriendDdt($singleShooting);
            }

            $row['disp'] = '<table class="nested-table inner-size-table" data-product-id="' . $val->printId() . '"></table>';
            $row['qty']=$val->qty;
            $findSeason=$productSeasonRepo->findOneBy(['id'=>$val->productSeasonId]);
            $row['season']=$findSeason->name;

            $row["shooting"] = $sids;
            $row["doc_number"] = $ddtNumbers;
            $datatable->setResponseDataSetRow($key,$row);
        }
        return $datatable->responseOut();
    }
}
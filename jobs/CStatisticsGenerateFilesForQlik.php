<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\exceptions\BambooException;
use bamboo\core\jobs\ACronJob;

/**
 * Class CDispatchPreorderToFriend
 * @package bamboo\blueseal\jobs
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CStatisticsGenerateFilesForQlik extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $path = $this->app->rootPath()
            . $this->app->cfg()->fetch('paths', 'exportedStatistics');

        $files = scandir($path);

        foreach($files as $v) {
            if ('.' === $v || '..' === $v) continue;
            unlink($path . $v);
        }

        $sql = [];

        $sql['ordini'] =
            "SELECT 
 `o`.`id` as `numOrd`,
 DATE_FORMAT(`l`.`time` , '%Y-%m-%d') as `DataOrd`,
 DATE_FORMAT(`l`.`time` , '%H:%i:%s') as `OraOrd`,
 `o`.`userId` as `CodUtente`,
 `ol`.`status` as `StatoRiga`,
 `o`.`status` as `StatoOrdine`,
 concat(`ol`.`productId`, '-', `ol`.`productVariantId`) as `CodProd`,
 `ps`.`name` as `Taglia`,
 `shp`.`shopId` as `CodShop`,
 REPLACE(CAST(IFNULL(`ol`.`cost`, 0) as CHAR),'.', ',') as `CostoForn`,
 REPLACE(CAST(IFNULL(`ol`.`friendRevenue`, 0)as CHAR),'.', ',') as `CostoFriend`,
 REPLACE(CAST(IFNULL(`o`.`shippingPrice`, 0 )as CHAR),'.', ',') as `RealTrasp`,
 REPLACE(CAST(IFNULL(`ol`.`activePrice`, 0 )as CHAR),'.', ',') as `PrezzoAtt`,
 REPLACE(CAST(IFNULL(
    IF(`ol`.`couponCharge` < 0 && `ol`.`netPrice`, 0, `ol`.`netPrice`)
    , 0 )as CHAR),'.', ',') as `realizzo`,
 REPLACE(CAST(IFNULL(
    IF(`ol`.`couponCharge` < 0 && `ol`.`netPrice` - `ol`.`vat`, 0, `ol`.`netPrice` - `ol`.`vat`)
    , 0 )as CHAR),'.', ',') as `realizzoNetto`,
 REPLACE(CAST(IFNULL((`ol`.`fullPrice` - `ol`.`activePrice`), 0 )as CHAR),'.', ',') as `sconti`
  FROM
  `Order` as `o`
  JOIN `OrderLine` as `ol` ON `o`.`id` = `ol`.`orderId`
  JOIN `ProductSize` as `ps` ON ol.`productSizeId` = `ps`.`id`
  LEFT JOIN `ShopHasProduct` as `shp` ON `ol`.`shopId` = `shp`.`shopId` AND `ol`.`productVariantId` = `shp`.`productVariantId` AND `ol`.`productId` = `shp`.`productId`
  JOIN `Log` as `l` on `l`.stringId = `ol`.`orderId`
  WHERE `l`.`entityName` = 'Order'";

 /**       $sql['campagne-prodotti'] =
            "SELECT 
c.name as CodAggr,
 date(cv.timestamp) as Data,
  concat(mahp.productId,'-',mahp.productVariantId) as CodProd,
   count(distinct cv.id) as NumVisite,
    count(distinct ol.orderId) as NumConv,
     ifnull(sum(ol.netPrice), 0) as ValVisite
FROM
  MarketplaceAccount ma
  JOIN MarketplaceAccountHasProduct mahp ON ma.id = mahp.marketplaceAccountId AND ma.marketplaceId = mahp.marketplaceId
  JOIN Campaign c ON c.code = concat('MarketplaceAccount', ma.id, '-', ma.marketplaceId)
  JOIN CampaignVisit cv ON c.id = cv.campaignId
  JOIN CampaignVisitHasProduct cvhp
    ON cvhp.campaignId = cv.campaignId AND cvhp.campaignVisitId = cv.id AND cvhp.productId = mahp.productId AND
    cvhp.productVariantId = mahp.productVariantId
  LEFT JOIN (
        CampaignVisitHasOrder cvho
    JOIN OrderLine ol ON cvho.orderId = ol.orderId)
    ON cvho.campaignId = cv.campaignId AND cvho.campaignVisitId = cv.id AND ol.productId = mahp.productId AND
    ol.productVariantId = mahp.productVariantId
GROUP BY mahp.productId, mahp.productVariantId, c.id, date(cv.timestamp)";*/

        $sql['categorie'] = "SELECT DISTINCT concat(p.id, '-', p.productVariantId) CodProd, pc.id as id,
  (SELECT
     max(CASE WHEN c.depth = 1 THEN pct.name end) AS slug1
   FROM ProductCategory AS c JOIN ProductCategoryTranslation as pct on pct.productCategoryId = c.id, ProductCategory as c2
   WHERE pct.langId = 1 AND c.lft <= c2.lft AND c.rght >= c2.rght AND c2.id = pc.id) as livello1,
  (SELECT
     max(CASE WHEN c.depth = 2 THEN pct.name end) AS slug1
   FROM ProductCategory AS c JOIN ProductCategoryTranslation as pct on pct.productCategoryId = c.id, ProductCategory as c2
   WHERE pct.langId = 1 AND c.lft <= c2.lft AND c.rght >= c2.rght AND c2.id = pc.id) as livello2,
  (SELECT
     max(CASE WHEN c.depth = 3 THEN pct.name end) AS slug1
   FROM ProductCategory AS c JOIN ProductCategoryTranslation as pct on pct.productCategoryId = c.id, ProductCategory as c2
   WHERE pct.langId = 1 AND c.lft <= c2.lft AND c.rght >= c2.rght AND c2.id = pc.id) as livello3,
  (SELECT
     max(CASE WHEN c.depth = 4 THEN pct.name end) AS slug1
   FROM ProductCategory AS c JOIN ProductCategoryTranslation as pct on pct.productCategoryId = c.id, ProductCategory as c2
   WHERE pct.langId = 1 AND c.lft <= c2.lft AND c.rght >= c2.rght AND c2.id = pc.id) as livello4,
  (SELECT
     max(CASE WHEN c.depth = 5 THEN pct.name end) AS slug1
   FROM ProductCategory AS c JOIN ProductCategoryTranslation as pct on pct.productCategoryId = c.id, ProductCategory as c2
   WHERE pct.langId = 1 AND c.lft <= c2.lft AND c.rght >= c2.rght AND c2.id = pc.id) as livello5,
  (SELECT
     max(CASE WHEN c.depth = 6 THEN pct.name end) AS slug1
   FROM ProductCategory AS c JOIN ProductCategoryTranslation as pct on pct.productCategoryId = c.id, ProductCategory as c2
   WHERE pct.langId = 1 AND c.lft <= c2.lft AND c.rght >= c2.rght AND c2.id = pc.id) as livello6
FROM ProductCategory as pc
  JOIN ProductHasProductCategory as phpc on phpc.productCategoryId = pc.id
  JOIN Product as p on phpc.productId = p.id AND phpc.productVariantId = p.productVariantId
WHERE pc.id > 1";

        $sql['prodotti'] = "SELECT
  concat(`p`.`id`, '-', `p`.`productVariantId`) as `CodProd`,
  `shp`.`shopId` as `CodShop`,
  REPLACE(CAST(IFNULL(`shp`.`value`, 0)as CHAR),'.',',') as `CostoForn`,
  REPLACE(CAST(IFNULL(IF(`psk`.`isOnSale` = 0,
     IF (`pse`.`isActive` = 1, `shp`.`value` / 100 * `s`.`currentSeasonMultiplier` + `shp`.`value`, `shp`.`value` / 100 * `s`.`pastSeasonMultiplier` + `shp`.`value` ),
     `shp`.`value` / 100 * `s`.`saleMultiplier` + `shp`.`value`
  ), 0) as CHAR), '.',',') as `CostoFriend`,
  REPLACE(CAST(IFNULL(IF(`psk`.`isOnSale` = 0, `psk`.`price`, `psk`.`salePrice`), 0)as CHAR),'.',',') as `PrezzoAtt`,
  `pcg`.`name` as `GruColore`,
  `psg`.`name` as `codTaglia`,
  `pse`.`name` as `Stagione`
FROM `Product` as `p`
  JOIN `ShopHasProduct` as `shp` ON `p`.`productVariantId` = `shp`.`productVariantId` AND `p`.`productVariantId` AND `shp`.`productId` = `p`.`id`
  JOIN `ProductSeason` as `pse` ON `p`.`productSeasonId` = `pse`.`id`
  JOIN `Shop` as `s` on `s`.`id` = `shp`.`shopId`
  JOIN `ProductSizeGroup` as `psg` on `p`.`productSizeGroupId` = `psg`.`id`
  LEFT JOIN (`ProductHasProductColorGroup` as `phpcg`
    JOIN `ProductColorGroup` as `pcg` on `phpcg`.`productColorGroupId` = `pcg`.`id` ) ON `phpcg`.productVariantId = `p`.`productVariantId` AND `phpcg`.`productId` = `p`.`id`
  LEFT JOIN (`ProductHasProductCategory` as `phpc`
    JOIN `ProductCategory` as `pc` on `phpc`.`productCategoryId` = `pc`.`id`
    JOIN `ProductCategoryTranslation` as `pct` on `pc`.`id` = `pct`.`productCategoryId`) ON `phpc`.productVariantId = `p`.`productVariantId` AND `phpc`.`productId` = `p`.`id`
  JOIN `ProductSku` as `psk` on `psk`.`productId` = `p`.`id` AND `psk`.`productVariantId` = `p`.`productVariantId`
WHERE (`pct`.`langId` = 1 OR `pct`.`langId` IS NULL) AND productStatusId in (5,6,11) AND `p`.`dummyPicture` NOT LIKE '%bs-dummy%' AND `p`.`qty` > 0";

        $sql['utenti'] = "
SELECT 
 `u`.`id` as `CodUtente`,
 `u`.`creationDate` as `DataIscrizione`,
 `ua`.`address` as `address`,
 `ua`.`postcode` as `Cap`,
 `ua`.`city` as `Citta`,
 `ua`.`province` as `Provincia`,
 `c`.`name` as `Stato`,
 `ua`.`phone` as `Telefono`
 FROM
`User` as `u`
JOIN `UserAddress` as `ua` ON `u`.`id` = `ua`.`userId`
JOIN `Country` as `c` ON `c`.`id` = `ua`.`countryId`
";
        $sql['movimenti'] = "
SELECT 
  CONCAT(`sl`.`productId`, '-', `sl`.`productVariantId`) as `CodProd`,
  DATE_FORMAT(`s`.`operationDate`, '%Y-%m-%d') as `DataMov`,
  `sc`.`name`         as `Causale`,
  `uhs`.`shopId`        as `ShopId`,
  sum(`sl`.qty)       as `Qty`,
  `psz`.`name`        as `Taglia`,
  sum(`sl`.qty)       as `Qta`,
  REPLACE(CAST(IFNULL(IF(`ps`.`isOnSale` = 0,
     IF (`pse`.`isActive` = 1, `shp`.`value` / 100 * `sh`.`currentSeasonMultiplier` + `shp`.`value`, `shp`.`value` / 100 * `sh`.`pastSeasonMultiplier` + `shp`.`value` ),
     `shp`.`value` / 100 * `sh`.`saleMultiplier` + `shp`.`value`
  ) * sum(`sl`.qty), 0) as CHAR),'.',',') as `ValCosFri`,
  IF(`ps`.`isOnSale` = 0, `ps`.`price`, `ps`.`salePrice`) * sum(`sl`.qty) as `ValPreAtt`
FROM 
`StorehouseOperation` as `s`
JOIN `StorehouseOperationLine` as `sl` ON `s`.`id` = `sl`.`storehouseOperationId`
JOIN `StorehouseOperationCause` as `sc` ON `sc`.`id` = `s`.`storehouseOperationCauseId`
JOIN `UserHasShop` as `uhs` on `s`.`userId` = `uhs`.`userId`
JOIN `ShopHasProduct` as `shp` ON `shp`.`productVariantId` = `sl`.`productVariantId` AND `shp`.`shopId` = `sl`.`shopId`
JOIN `Shop` as `sh` on `sl`.`shopId` = `sh`.`id`
JOIN `ProductSku` as `ps` ON `sl`.`productId` = `ps`.`productId` AND `sl`.`productVariantId` = `ps`.`productVariantId` AND `sl`.`productSizeId` = `ps`.`productSizeId` AND `sl`.`shopId` = `ps`.`shopId`
JOIN `Product` as `p` on `sl`.`productVariantId` = `p`.`productVariantId` AND `sl`.`productId` = `p`.`id`
JOIN `ProductSeason` as `pse` on `pse`.`id` = `p`.`productSeasonId`
JOIN `ProductSize` as `psz` on `psz`.`id` = `sl`.`productSizeId`
WHERE 1 
GROUP BY `s`.`id`, `sl`.`productVariantId`";

        $sql['shop'] = "SELECT * FROM `Shop` WHERE 1";

       /** $sql['pubblicazioni'] = "
SELECT 
concat(`shp`.`productId`, '-', `shp`.`productVariantId`) as `CodProd`,
`l`.`time` as `DataMov`,
`l`.`eventValue` as `Causale`,
1 as `qty`,
FROM `Log` as `l` 
JOIN `ShopHasProduct` as `shp` ON concat(`shp`.`productId`, '-', `shp`.`productVariantId`, '-', `shp`.`shopId`) = `l`.`stringId`
JOIN `C`";*/

        $dba = \Monkey::app()->dbAdapter;
        foreach($sql as $k => $v) {
            $res = $dba->query($v, [])->fetchAll();
            $file = fopen($path . $k . '.csv', 'x');
            if (!$file) throw new BambooException('Can\'t create the file');
            $fieldNames = [];
            foreach($res[0] as $fk => $fv) {
                $fieldNames[] = $fk;
            }
            array_unshift($res, $fieldNames);
            reset($res);
            foreach($res as $fields) {
                fputcsv($file, $fields, ';', '"', "\\");
            }
            $this->report('file statistiche', 'file ' . $k .'.csv creato');
            fclose($file);
        }
    }

}
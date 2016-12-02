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
 DATE_FORMAT(`l`.`time` , '%Y-%m-%D') as `DataOrd`,
 DATE_FORMAT(`l`.`time` , '%H:%i:%s') as `OraOrd`,
 `o`.`userId` as `CodUtente`,
 `ol`.`status` as `StatoRiga`,
 `o`.`status` as `StatoOrdine`,
 concat(`ol`.`productId`, '-', `ol`.`productVariantId`) as `CodProd`,
 `ps`.`name` as `Taglia`,
 `shp`.`shopId` as `CodShop`,
 `ol`.`cost` as `CostoForn`,
 `ol`.`friendRevenue` as `CostoFriend`,
 `o`.`shippingPrice` as `RealTrasp`,
 `ol`.`activePrice` as `PrezzoAtt`,
 `ol`.`netPrice` as `realizzo`,
 REPLACE(CAST(ifnull((`ol`.`fullPrice` - `ol`.`activePrice`) / (`ol`.`fullPrice` / 100),0), CHAR), '.',',') as `sconti`
  FROM
  `Order` as `o`
  JOIN `OrderLine` as `ol` ON `o`.`id` = `ol`.`orderId`
  JOIN `ProductSize` as `ps` ON ol.`productSizeId` = `ps`.`id`
  LEFT JOIN `ShopHasProduct` as `shp` ON `ol`.`shopId` = `shp`.`shopId` AND `ol`.`productVariantId` = `shp`.`productVariantId` AND `ol`.`productId` = `shp`.`productId`
  JOIN `Log` as `l` on `l`.stringId = `ol`.`orderId`
  WHERE `l`.`entityName` = 'Order'";



        $sql['prodotti'] = "SELECT
  concat(`p`.`id`, '-', `p`.`productVariantId`) as `CodProd`,
  `pct`.`name` as `Categoria`,
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
  `s`.`operationDate` as `DataMov`,
  `sc`.`name`         as `Causale`,
  sum(`sl`.qty)       as `Qty`,
  IF(`ps`.`isOnSale` = 0,
  `psz`.`name`        as `Taglia`,
  sum(`sl`.qty)       as `Qta`,
  REPLACE(CAST(IFNULL(IF(`ps`.`isOnSale` = 0,
     IF (`pse`.`isActive` = 1, `shp`.`value` / 100 * `sh`.`currentSeasonMultiplier` + `shp`.`value`, `shp`.`value` / 100 * `sh`.`pastSeasonMultiplier` + `shp`.`value` ),
     `shp`.`value` / 100 * `sh`.`saleMultiplier` + `shp`.`value`
  ) * sum(`sl`.qty), 0) as CHAR), '.', ',') as `ValCosFri`,
  IFNULL(IF(`ps`.`isOnSale` = 0, `ps`.`price`, `ps`.`salePrice`), 0) as `ValPreAtt`
FROM 
`StorehouseOperation` as `s`
JOIN `StorehouseOperationLine` as `sl` ON `s`.`id` = `sl`.`storehouseOperationId`
JOIN `StorehouseOperationCause` as `sc` ON `sc`.`id` = `s`.`storehouseOperationCauseId`
JOIN `ShopHasProduct` as `shp` ON `shp`.`productVariantId` = `sl`.`productVariantId` AND `shp`.`shopId` = `sl`.`shopId`
JOIN `Shop` as `sh` on `sl`.`shopId` = `sh`.`id`
JOIN `ProductSku` as `ps` ON `sl`.`productId` = `ps`.`productId` AND `sl`.`productVariantId` = `ps`.`productVariantId` AND `sl`.`productSizeId` = `ps`.`productSizeId` AND `sl`.`shopId` = `ps`.`shopId`
JOIN `Product` as `p` on `sl`.`productVariantId` = `p`.`productVariantId` AND `sl`.`productId` = `p`.`id`
JOIN `ProductSeason` as `pse` on `pse`.`id` = `p`.`productSeasonId`
JOIN `ProductSize` as `psz` on `psz`.`id` = `sl`.`productSizeId`
WHERE 1 
GROUP BY `s`.`id`, `sl`.`productVariantId`";

        $sql['pubblicazioni'] = "
SELECT 
concat(`shp`.`productId`, '-', `shp`.`productVariantId`) as `CodProd`,
`l`.`time` as `DataMov`,
`l`.`eventValue` as `Causale`,
1 as `qty`,
FROM `Log` as `l` 
JOIN `ShopHasProduct` as `shp` ON concat(`shp`.`productId`, '-', `shp`.`productVariantId`, '-', `shp`.`shopId`) = `l`.`stringId`
JOIN `C`";

        $dba = \Monkey::app()->dbAdapter;
        foreach($sql as $k => $v) {
            $res = $dba->query($v, [])->fetchAll();
            $file = fopen($path . $k . '.csv', 'x');
            if (!$file) throw new BambooException('Can\'t create the file');
            $fieldNames = [];
            foreach($res[0] as $fk => $fv) {
                $fieldNames[] = $k;
            }
            array_unshift($res, $fieldNames);
            reset($res);
            foreach($res as $fields) {
                fputcsv($file, $fields, ',', '"', "\\");
            }
            $this->report('file statistiche', 'file ' . $k .'.csv creato');
            fclose($file);
        }
    }

}
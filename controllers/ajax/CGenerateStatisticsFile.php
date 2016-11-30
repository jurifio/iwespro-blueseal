<?php
namespace bamboo\blueseal\controllers\ajax;
use bamboo\core\exceptions\BambooException;


class CGenerateStatisticsFile extends AAjaxController
{
    public function get()
    {
        $path = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'exportedstatistics');

        $files = scandir($path);

        foreach($files as $v) {
            if ('.' === $v || '..' === $v) continue;
            unlink($v);
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
 `ps`.`shopId` as `CodShop`,
 `ol`.`cost` as `CostoForn`,
 `ol`.`friendRevenue` as `CostoFriend`,
 `o`.`shippingPrice` as `RealTrasp`,
 `ol`.`activePrice` as `PrezzoAtt`,
 `ol`.`netPrice` as `realizzo`,
 (`ol`.`fullPrice` - `ol`.`activePrice`) / (`ol`.`fullPrice` / 100) as `sconti`
  FROM
  `Order` as `o`
  JOIN `OrderLine` as `ol` ON `o`.`id` = `ol`.`orderId`
  JOIN `ProductSize` as `ps` ON ol.`productSizeId` = `ps`.`id`
  JOIN `ShopHasProduct` as `shp` ON `ol`.`shopId` = `shp`.`shopId` AND `ol`.`productVariantId` = `shp`.`productVariantId` AND `ol`.`productId` = `ol`.`productVariantId`
  JOIN `Log` as `l` on `l`.stringId = `ol`.`orderId`
  WHERE `l`.`entityName` = 'Order' AND (`l`.`eventValue` = 'ORD_PENDING' OR `l`.`eventValue` = 'ORD_WAIT')";



  $sql['prodotti'] = "SELECT
  concat(`p`.`id`, '-', `p`.`productVariantId`) as `CodProd`,
  `pct`.`name` as `Categoria`,
  `shp`.`shopId` as `CodShop`,
  `shp`.`value` as `CostoForn`,
  IF(`psk`.`isOnSale` = 0,
     IF (`pse`.`isActive` = 1, `shp`.`value` / 100 * `s`.`currentSeasonMultiplier` + `shp`.`value`, `shp`.`value` / 100 * `s`.`pastSeasonMultiplier` + `shp`.`value` ),
     `shp`.`value` / 100 * `s`.`saleMultiplier` + `shp`.`value`
  ) as `CostoFriend`,
  IF(`psk`.`isOnSale` = 0, `psk`.`price`, `psk`.`salePrice`) as `PrezzoAtt`,
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
     IF (`pse`.`isActive` = 1, `shp`.`value` / 100 * `sh`.`currentSeasonMultiplier` + `shp`.`value`, `shp`.`value` / 100 * `sh`.`pastSeasonMultiplier` + `shp`.`value` ),
     `shp`.`value` / 100 * `sh`.`saleMultiplier` + `shp`.`value`
  ) * sum(`sl`.qty) as `ValCosFri`,
  IF(`ps`.`isOnSale` = 0, `ps`.`price`, `ps`.`salePrice`) as `ValPreAtt`
FROM 
`StorehouseOperation` as `s`
JOIN `StorehouseOperationLine` as `sl` ON `s`.`id` = `sl`.`storehouseOperationId`
JOIN `StorehouseOperationCause` as `sc` ON `sc`.`id` = `s`.`storehouseOperationCauseId`
JOIN `ShopHasProduct` as `shp` ON `shp`.`productVariantId` = `sl`.`productVariantId` AND `shp`.`shopId` = `sl`.`shopId`
JOIN `Shop` as `sh` on `sl`.`shopId` = `sh`.`id`
JOIN `ProductSku` as `ps` ON `sl`.`productId` = `ps`.`productId` AND `sl`.`productVariantId` = `ps`.`productVariantId` AND `sl`.`productSizeId` = `ps`.`productSizeId` AND `sl`.`shopId` = `ps`.`shopId`
JOIN `Product` as `p` on `sl`.`productVariantId` = `p`.`productVariantId` AND `sl`.`productId` = `p`.`id`
JOIN `ProductSeason` as `pse` on `pse`.`id` = `p`.`productSeasonId`
WHERE 1 
GROUP BY `s`.`id`, `sl`.`productVariantId`";


        $dba = \Monkey::app()->dbAdapter;
        foreach($sql as $k => $v) {
            $res = $dba->query($v, [])->fetchAll();
            $file = fopen($path . $k . '.csv', 'x');
            if (!$file) throw new BambooException('Can\'t create the file');
            foreach($res as $fields) {
                    fputcsv($file, $fields);
            }
            fclose($file);
        }


        $zip = new \ZipArchive();
        $filename = $path . "statistics-" . date('Y-m-d-H-i-s');

        if (FALSE === $zip->open($filename, \ZipArchive::OVERWRITE)) {
            throw new BambooException("Cant' crete the zip archive <$filename>\n");
        }

        foreach($sql as $k => $v) {
            if (!$zip->addFile($path . "/" . $k . ".csv")) {
                $zip->close();
                unlink($filename);
                throw new BambooException('can\'t add file to the zip archive');
            }
        }
        $zip->close();

}
}

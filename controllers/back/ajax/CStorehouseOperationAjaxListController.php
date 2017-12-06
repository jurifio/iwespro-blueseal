<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CStorehouseOperation;

/**
 * Class CStorehouseOperationAjaxListController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/08/2016
 * @since 1.0
 */
class CStorehouseOperationAjaxListController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                      `sho`.`id`                                                                                                        AS `id`,
                      `sho`.`storehouseId`                                                                                              AS `storehouseId`,
                      `sho`.`shopId`                                                                                                    AS `shopId`,
                      `s`.`name`                                                                                                        AS `shop`,
                      `sho`.`creationDate`                                                                                              AS `creationDate`,
                      `sho`.`operationDate`                                                                                             AS `operationDate`,
                      `s`.`title`                                                                                                       AS `friend`,
                      `shoc`.`name`                                                                                                     AS `cause`,
                      group_concat(concat_ws('-', `shol`.`productId`, `shol`.`productVariantId`, `shol`.`productSizeId`) SEPARATOR
                                   ',')                                                                                                 AS `code`,
                      group_concat(concat_ws(' # ', `p`.`itemno`, `pv`.`name`) SEPARATOR
                                   ',')                                                                                                 AS `cpf`,
                      sum(
                          `ps`.`value`)                                                                                                 AS `value`,
                      sum(
                          `ps`.`price`)                                                                                                 AS `price`,
                      sum(
                          `shol`.`qty`)                                                                                                 AS `qty`,
                      if(u.id is null, 'Nessuno', concat(ud.name, ' ',ud.surname)) as user
                    FROM (((((((`Shop` `s`
                      JOIN `Storehouse` `sh`) 
                      JOIN `StorehouseOperation` `sho`) 
                      JOIN `StorehouseOperationCause` `shoc`) 
                      JOIN `StorehouseOperationLine` `shol`) 
                      JOIN `ProductSku` `ps`) 
                      JOIN `Product` `p`) 
                      JOIN `ProductVariant` `pv`)
                      LEFT JOIN (User u join UserDetails ud on u.id = ud.userId) on u.id = sho.userId
                    WHERE ((`s`.`id` = `sh`.`shopId`) AND 
                    (`sh`.`id` = `sho`.`storehouseId`) AND 
                    (`sh`.`shopId` = `sho`.`shopId`) AND
                    (`sho`.`storehouseOperationCauseId` = `shoc`.`id`) AND 
                    (`sho`.`id` = `shol`.`storehouseOperationId`) AND
                    (`sho`.`shopId` = `shol`.`shopId`) AND (`sho`.`storehouseId` = `shol`.`storehouseId`) AND
                           (`shol`.`productId` = `ps`.`productId`) AND (`shol`.`productVariantId` = `ps`.`productVariantId`) AND
                           (`shol`.`productSizeId` = `ps`.`productSizeId`) AND (`shol`.`shopId` = `ps`.`shopId`) AND
                           (`p`.`id` = `ps`.`productId`) AND (`p`.`productVariantId` = `ps`.`productVariantId`) AND
                           (`p`.`productVariantId` = `pv`.`id`))
                    GROUP BY `s`.`id`, `sh`.`id`, `sho`.`id`";
        $sql = "SELECT
                    `sho`.`id`                                                              AS `id`,
                    `sho`.`storehouseId`                                                    AS `storehouseId`,
                    `sho`.`shopId`                                                          AS `shopId`,
                    `s`.`name`                                                              AS `shop`,
                    `sho`.`creationDate`                                                    AS `creationDate`,
                    `sho`.`operationDate`                                                   AS `operationDate`,
                    `s`.`title`                                                             AS `friend`,
                    `shoc`.`name`                                                           AS `cause`,
                    concat_ws('-', `shol`.`productId`, `shol`.`productVariantId`, `shol`.`productSizeId`) AS `code`,
                    concat_ws(' # ', `p`.`itemno`, `pv`.`name`) AS `cpf`,
                    if(u.id is null, 'Nessuno', concat(ud.name, ' ',ud.surname)) as user
                  FROM (((((((`Shop` `s`
                    JOIN `Storehouse` `sh`) JOIN `StorehouseOperation` `sho`) JOIN `StorehouseOperationCause` `shoc`) JOIN
                    `StorehouseOperationLine` `shol`) JOIN `ProductSku` `ps`) JOIN `Product` `p`) JOIN `ProductVariant` `pv`)
                    LEFT JOIN (User u join UserDetails ud on u.id = ud.userId) on u.id = sho.userId
                  WHERE ((`s`.`id` = `sh`.`shopId`) AND (`sh`.`id` = `sho`.`storehouseId`) AND (`sh`.`shopId` = `sho`.`shopId`) AND
                         (`sho`.`storehouseOperationCauseId` = `shoc`.`id`) AND (`sho`.`id` = `shol`.`storehouseOperationId`) AND
                         (`sho`.`shopId` = `shol`.`shopId`) AND (`sho`.`storehouseId` = `shol`.`storehouseId`) AND
                         (`shol`.`productId` = `ps`.`productId`) AND (`shol`.`productVariantId` = `ps`.`productVariantId`) AND
                         (`shol`.`productSizeId` = `ps`.`productSizeId`) AND (`shol`.`shopId` = `ps`.`shopId`) AND
                         (`p`.`id` = `ps`.`productId`) AND (`p`.`productVariantId` = `ps`.`productVariantId`) AND
                         (`p`.`productVariantId` = `pv`.`id`))";
        $datatable = new CDataTables($sql, ['id', 'shopId', 'storehouseId'], $_GET,true);
        $shops = \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser();

        $datatable->addCondition('shopId', $shops);
        $datatable->addSearchColumn('code');
        $datatable->addSearchColumn('cpf');

        //var_dump($datatable->getQuery(),$datatable->getParams());

        $operazioni = \Monkey::app()->repoFactory->create('StorehouseOperation')->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('StorehouseOperation')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('StorehouseOperation')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($operazioni as $val) {
            /** @var CStorehouseOperation $val */
            $row = [];
            $row["DT_RowId"] = $val->printId();
            $row["DT_RowClass"] = 'colore';
            $row['id'] = $val->id;

            $sign = ($val->storehouseOperationCause->sign) ? '+' : '-';
            $row['cause'] = $val->storehouseOperationCause->name . ' (' . $sign . ') ';

            $row['operationDate'] = date('d-M-Y H:i', strtotime($val->operationDate));
            $row['creationDate'] = date('d-M-Y H:i', strtotime($val->creationDate));

            $row['friend'] = $val->shop->title;

            $row['movements'] = '<span class="small">' . $val->storehouseOperationLine->count() . ' Elementi movimentati <br />';
            $k = 0;
            $row['qty'] = 0;
            $row['value'] = 0;
            $row['price'] = 0;

            foreach ($val->storehouseOperationLine as $line) {
                $sku = $line->productSku;
                $product = $sku->product;
                $brand = $product->productBrand->name;
                $size = $sku->productSize->name;
                if ($k < 3) {
                    $row['movements'] .= $product->printId() . " / " . $brand . " / " . $size . " / " . $product->printCpf() . ": " . $line->qty . '<br />';
                } elseif ($k == 3 && $val->storehouseOperationLine->count() > 3) {
                    $row['movements'] .= '...';
                }
                $row['qty'] += $line->qty;
                $row['value'] += $sku->value;
                $row['price'] += $sku->price;
                $k++;
            }
            $row['movements'] .= '</span>';
            try {
                $row['user'] = $val->user->getFullName();
            } catch(\Throwable $e) {
                $row['user'] = 'Nessuno';
            }

            $response['data'][] = $row;

        }

        return json_encode($response);
    }
}
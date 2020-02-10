<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CBillRegistryProduct;
use bamboo\domain\entities\CBillRegistryGroupProduct;
use bamboo\domain\entities\CBillRegistryCategoryProduct;

/**
 * Class CBillRegistryProductListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/02/2020
 * @since 1.0
 */
class CBillRegistryProductListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                      `brp`.`id`                                            AS `id`,
                       `brcp`.`name`                                       as `category`,
                      `brp`.`codeProduct`                                         AS `codeProduct`,
                      `brp`.`nameProduct`                                      AS `nameProduct`,
                      `brp`.`um`                                         AS `um`,
                      `brp`.`cost`             AS `cost`,
                      `brp`.`price`             AS `price`,
                      `brgp`.`name`             AS `GroupProduct`,
                      `brtt`.`description`     as `tax`
                    FROM `BillRegistryProduct` `brp`
                      JOIN `BillRegistryGroupProduct` `brgp` on `brp`.`billRegistryGroupProductId`=`brgp`.`id`
                          JOIN `BillRegistryCategoryProduct` `brcp` on `brgp`.`billRegistryCategoryProductId`=`brcp`.`id`
                       join `BillRegistryTypeTaxes` `brtt` on `brp`.`billRegistryTypeTaxesId`=`brtt`.`id`";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings();

        $productEdit = $this->app->baseUrl(false) . "/blueseal/anagrafica/prodotti-modifica?id=";
        /** @var CBillRegistryProduct $billRegistryProductRepo */
        $billRegistryProductRepo = \Monkey::app()->repoFactory->create('BillRegistryProduct');
        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var CBillRegistryGroupProduct $billRegistryGroupProduct */
            $billRegistryProduct = $billRegistryProductRepo->findOne([$row['id']]);
            $row = [];
            $row["DT_RowId"] = 'row__' . $billRegistryProduct->printId();
            $row['id'] = '<a href="'.$productEdit.$billRegistryProduct->id.'">'.$billRegistryProduct->id.'</a>';
            $row['codeProduct'] = $billRegistryProduct->codeProduct;
            $row['nameProduct'] = $billRegistryProduct->nameProduct;
            /** @var CBillRegistryCategoryProduct $brcp */
            $brgp=\Monkey::app()->repoFactory->create('BillRegistryGroupProduct')->findOneBy(['id'=>$billRegistryProduct->billRegistryGroupProductId]);
            $brcp=\Monkey::app()->repoFactory->create('BillRegistryCategoryProduct')->findOneBy(['id'=>$brgp->billRegistryCategoryProductId]);
            $brtt=\Monkey::app()->repoFactory->create('BillRegistryTypeTaxes')->findOneBY(['id'=>$billRegistryProduct->billRegistryTypeTaxesId]);
            $row['cost'] = $billRegistryProduct->cost;
            $row['price'] = $billRegistryProduct->price;
            $row['um'] =$billRegistryProduct->um;
            $row['tax']=$brtt->description;
            $row['GroupProduct'] = $brgp->name;
            $row['category'] = $brcp->name;
            $datatable->setResponseDataSetRow($key, $row);
        }

        return $datatable->responseOut();
    }
}
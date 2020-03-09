<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CBillRegistryProduct;
use bamboo\domain\entities\CBillRegistryGroupProduct;
use bamboo\domain\entities\CBillRegistryCategoryProduct;

/**
 * Class CBillRegistryGroupProductListAjaxController
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
class CBillRegistryGroupProductListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                      `brgp`.`id`                                            AS `id`,
                       `brcp`.`name`                                       as `category`,
                      `brgp`.`codeProduct`                                         AS `codeProduct`,
                      `brgp`.`name`                                      AS `nameProduct`,
                      `brgp`.`um`                                         AS `um`,
                      `brgp`.`cost`             AS `cost`,
                      `brgp`.`price`             AS `price`,
                      `brgp`.`name`             AS `GroupProduct`,
                      `brtt`.`description`     as `tax`
                    FROM `BillRegistryGroupProduct` `brgp`
                          JOIN `BillRegistryCategoryProduct` `brcp` on `brgp`.`billRegistryCategoryProductId`=`brcp`.`id`
                       join `BillRegistryTypeTaxes` `brtt` on `brgp`.`billRegistryTypeTaxesId`=`brtt`.`id`";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings();

        $productEdit = $this->app->baseUrl(false) . "/blueseal/anagrafica/gruppo-prodotti-modifica?id=";
        /** @var CBillRegistryProduct $billRegistryProductRepo */
        $billRegistryGroupProductRepo = \Monkey::app()->repoFactory->create('BillRegistryGroupProduct');
        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var CBillRegistryGroupProduct $billRegistryGroupProduct */
            $billRegistryGroupProduct = $billRegistryGroupProductRepo->findOne([$row['id']]);
            $row = [];
            $row["DT_RowId"] = 'row__' . $billRegistryGroupProduct->printId();
            $row['id'] = '<a href="'.$productEdit.$billRegistryGroupProduct->id.'">'.$billRegistryGroupProduct->id.'</a>';
            $row['codeProduct'] = $billRegistryGroupProduct->codeProduct;
            $row['nameProduct'] = $billRegistryGroupProduct->name;
            /** @var CBillRegistryCategoryProduct $brcp */
            $brcp=\Monkey::app()->repoFactory->create('BillRegistryCategoryProduct')->findOneBy(['id'=>$billRegistryGroupProduct->billRegistryCategoryProductId]);
            $brtt=\Monkey::app()->repoFactory->create('BillRegistryTypeTaxes')->findOneBY(['id'=>$billRegistryGroupProduct->billRegistryTypeTaxesId]);
            $row['cost'] = $billRegistryGroupProduct->cost;
            $row['price'] = $billRegistryGroupProduct->price;
            $row['um'] =$billRegistryGroupProduct->um;
            $row['tax']=$brtt->description;
            $row['category'] = $brcp->name;
            $datatable->setResponseDataSetRow($key, $row);
        }

        return $datatable->responseOut();
    }
}
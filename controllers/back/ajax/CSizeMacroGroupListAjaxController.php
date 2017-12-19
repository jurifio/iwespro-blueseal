<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;



/**
 * Class CProductDetailListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CSizeMacroGroupListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $sql = "SELECT  pmg.id,
                        pmg.name,
                        psg.name as productSizeGroupName,
                        group_concat(ps.name) as sizes,
                        psg.locale,
                        psg.id as idGroupSize,
                        psg.publicName
                FROM ProductSizeMacroGroup pmg
                LEFT JOIN ProductSizeGroup psg ON pmg.id = psg.productSizeMacroGroupId
                LEFT JOIN ProductSizeGroupHasProductSize psghps ON psg.id = psghps.productSizeGroupId
                LEFT JOIN ProductSize ps ON psghps.productSizeId = ps.id
                GROUP BY psg.id, pmg.id";

        $datatable = new CDataTables($sql, ['idGroupSize'], $_GET, true);

        $datatable->doAllTheThings(true);

        $url = \Monkey::app()->baseUrl('false');

        foreach ($datatable->getResponseSetData() as $key=>$row) {


            if(empty($row['idGroupSize'])){
                $row["DT_RowClass"] = "red";
                $row['idGroupSize'] = "---";
                $row['productSizeGroupName'] = "Macrogruppo collegato a nessun gruppo";
                $row['sizes'] = "Macrogruppo collegato a nessun gruppo";
                $row['locale'] = "Macrogruppo collegato a nessun gruppo";
                $row['modifica'] = "<i class='fa fa-exclamation'></i>";

            } else {
                $row['modifica'] = "<a href='".$url . "gruppo-taglie/aggiungi?productSizeGroupId=" . $row['idGroupSize'] . " '><i class='fa fa-pencil-square-o'></i></a>";
                $row['sizes'] = str_replace(',',' | ',$row['sizes']);
            }





            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}
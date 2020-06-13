<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CProductCorrelationListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 13/06/2020
 * @since 1.0
 */
class CProductCorrelationListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                
                  `p`.`id`                                          AS `id`,
                  `p`.`name`                            AS `name`,
                  `p`.`description`                                AS `description`,
                  `p`.`note`                                      AS `note`
                  
                FROM `ProductCorrelation` `p`";

        $datatable = new CDataTables($sql, ['id'], $_GET);

        $productCorrelation = \Monkey::app()->repoFactory->create('ProductCorrelation')->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('ProductCorrelation')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('ProductCorrelation')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());


        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach ($productCorrelation as $v) {
            try {
                $response['data'][$i]["DT_RowId"] =  $v->id;
                $response['data'][$i]['id'] = $v->id;
                $response['data'][$i]['name'] = $v->name;
                $response['data'][$i]['description'] = $v->description;
                $response['data'][$i]['note'] = $v->note;
                $i++;
            } catch (\Throwable $e) {
                throw $e;
            }
        }
        return json_encode($response);
    }
}
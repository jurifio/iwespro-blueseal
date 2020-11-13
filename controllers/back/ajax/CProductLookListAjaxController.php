<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CProductLookListAjaxController
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
class CProductLookListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                
                  `p`.`id`                                          AS `id`,
                  `p`.`name`                                       AS `name`,
                  `p`.`image`                                      AS `image`,
                  `p`.`image2`                                      AS `image2`,
                  `p`.`image3`                                      AS `image3`,
                  `p`.`image4`                                      AS `image4`,
       
       
                   if(`p`.`discountActive`=0,'si','no')                                      AS `discountActive`,
                   if(`p`.`typeDiscount`=1,'percentuale','Fisso') as typeDiscount,
                   `p`.`amount` as amount 
                FROM `ProductLook` `p`";

        $datatable = new CDataTables($sql, ['id'], $_GET);

        $productLook = \Monkey::app()->repoFactory->create('ProductLook')->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('ProductLook')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('ProductLook')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());


        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach ($productLook as $v) {
            try {
                $response['data'][$i]["DT_RowId"] =  $v->id;
                $response['data'][$i]['id'] = $v->id;
                $response['data'][$i]['name'] = $v->name;
                $response['data'][$i]['image'] = ($v->image!=null)? '<img width="50px" src="'.$v->image.'"/>': '';
                $response['data'][$i]['image2'] = ($v->image2!=null)? '<img width="50px" src="'.$v->image2.'"/>': '';
                $response['data'][$i]['image3'] = ($v->image3!=null)? '<img width="50px" src="'.$v->image3.'"/>': '';
                $response['data'][$i]['image4'] = ($v->image3!=null)? '<img width="50px" src="'.$v->image4.'"/>': '';
                $response['data'][$i]['discountActive'] = ($v->discountActive==1)?'si':'no';
                $response['data'][$i]['typeDiscount'] = ($v->typeDiscount==1)?'percentuale':'fisso';
                $response['data'][$i]['amount'] = number_format($v->amount,2,',','');
                $i++;
            } catch (\Throwable $e) {
                throw $e;
            }
        }
        return json_encode($response);
    }
}
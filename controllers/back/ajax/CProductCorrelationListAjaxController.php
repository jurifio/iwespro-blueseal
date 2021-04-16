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
                  `p`.`note`                                      AS `note`,
                  `p`.`code`                                      AS `code`,
                  `p`.`image`                                      AS `image`,
                   `p`.`seo`                                      AS `seo`,
                    `s`.`name` as shopName,
                    `s`.`id` as remoteShopId,
                    `p`.`remoteId` as remoteId
                FROM `ProductCorrelation` `p` join Shop s on s.id=p.remoteShopId";

        $datatable = new CDataTables($sql, ['id'], $_GET);

        $productCorrelation = \Monkey::app()->repoFactory->create('ProductCorrelation')->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
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
                $response['data'][$i]['image'] = ($v->image!=null)? '<img width="50px" src="'.$v->image.'"/>': '';
                $response['data'][$i]['code'] = $v->code;
                $response['data'][$i]['seo'] = $v->seo;
                $shop=$shopRepo->findOneBy(['id'=>$v->remoteShopId]);
                $response['data'][$i]['shopName'] = $shop->name;
                $response['data'][$i]['remoteShopId'] = $v->remoteShopId;
                $response['data'][$i]['remoteId'] = $v->remoteId;
                $i++;
            } catch (\Throwable $e) {
                throw $e;
            }
        }
        return json_encode($response);
    }
}
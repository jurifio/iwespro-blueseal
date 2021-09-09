<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CPriceListListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/09/2021
 * @since 1.0
 */
class CPriceListListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                
                  `p`.`id`                                          AS `id`,
                  `p`.`name`                            AS `name`,
                  `s`.`name`                                AS `shopName`,
                   `p`.shopId as shopId, 
                  if(`p`.`typeVariation`=1,'Sconto','Maggiorazione')                                      AS `typeVariation`,
                  `p`.`variation`                                      AS `variation`,
                  if(`p`.`typeVariationSale`=1,'Sconto','Maggiorazione')                                      AS `typeVariationSale`,
                  `p`.`variationSale`                                      AS `variationSale`,
                  `p`.`dateStart`                                      AS `dateStart`,
                   `p`.`dateEnd`                                      AS `dateEnd`

                
                FROM `PriceList` `p` join Shop s on s.id=p.shopId";

        $datatable = new CDataTables($sql, ['id','shopId'], $_GET);

        $priceList = \Monkey::app()->repoFactory->create('PriceList')->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
        $count = \Monkey::app()->repoFactory->create('PriceList')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('PriceList')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());


        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach ($priceList as $v) {
            try {
                $response['data'][$i]["DT_RowId"] =  $v->id;
                $response['data'][$i]['id'] = $v->id;
                $response['data'][$i]['name'] = '<a href="/blueseal/listini/modifica/'.$v->id.'/'.$v->shopId.'/">'.$v->name.'</a>';
                $response['data'][$i]['typeVariation'] = ($v->typeVariation==1) ? 'Sconto':'Maggiorazione';
                $response['data'][$i]['variation'] = $v->variation;
                $response['data'][$i]['typeVariationSale'] = ($v->typeVariation==1) ? 'Sconto':'Maggiorazione';
                $response['data'][$i]['variationSale'] = $v->variation;
                $shop=$shopRepo->findOneBy(['id'=>$v->shopId]);
                $response['data'][$i]['shopName'] = $shop->name;
                $response['data'][$i]['shopId'] = $v->shopId;
                $response['data'][$i]['dateStart'] = (new \DateTime($v->dateStart))->format('d-m-Y');
                $response['data'][$i]['dateEnd'] = (new \DateTime($v->dateEnd))->format('d-m-Y');
                $i++;
            } catch (\Throwable $e) {
                throw $e;
            }
        }
        return json_encode($response);
    }
}
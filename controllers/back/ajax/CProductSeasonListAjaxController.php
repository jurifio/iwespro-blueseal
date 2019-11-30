<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProductSeason;

/**
 * Class CProductSeasonListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 30/11/2019
 * @since 1.0
 */
class CProductSeasonListAjaxController extends AAjaxController
{
    public function get()
    {
        $datatable = new CDataTables("ProductSeason",['id'],$_GET,false);
        $datatable->addCondition('id',\Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser());

        $seasons = \Monkey::app()->repoFactory->create('ProductSeason')->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('ProductSeason')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('ProductSeason')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];
        /** @var CProductSeason $season */
        foreach($seasons as $season){
            $row = [];
            $row['DT_RowId'] = $season->printId();
            $row['id'] = '<a href="/blueseal/prodotti/season-aggiungi?id='.$season->printId().'">'.$season->printId().'</a>';
            $row['name'] = $season->name;
            $row['year'] = $season->year;
            $row['dateStart'] = $season->dateStart;
            $row['dateEnd'] = $season->dateEnd;
            $row['isActive'] = ($season->isActive==0)? 'no' : 'si';
            $row['order'] = $season->order;
            $response['data'][] = $row;
        }
        return json_encode($response);
    }
}
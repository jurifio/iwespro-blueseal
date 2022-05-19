<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CColorListAjaxController extends AAjaxController
{
    public function get()
    {
        $datatable = new CDataTables('ProductColorGroup',['id'],$_GET,false);

        $prodotti = \Monkey::app()->repoFactory->create('ProductColorGroup')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('ProductColorGroup')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('ProductColorGroup')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $modifica = "/blueseal/prodotti/gruppo-colore/modifica";

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($prodotti as $val){

            $response['data'][$i]["DT_RowId"] = 'row__'.$val->printId();
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['name'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$modifica.'?id='.$val->id.'">'.$val->name.'</a>' : $val->name;
            $response['data'][$i]['slug'] = $val->slug;

            $i++;
        }

        return json_encode($response);
    }
}
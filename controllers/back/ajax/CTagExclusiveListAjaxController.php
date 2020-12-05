<?php
/**
 * Created by PhpStorm.
 * User: Andrea Tesei
 * Modified File: CUserListAjaxController.php
 * Date: 11/02/2016
 * Time: 10:46
 */

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

class CTagExclusiveListAjaxController extends AAjaxController
{
    public function get()
    {
        $datatable = new CDataTables('TagExclusive', ['id'], $_GET, false);

        $tags = \Monkey::app()->repoFactory->create('TagExclusive')->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('TagExclusive')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('TagExclusive')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');
        $editTagLink = "/blueseal/tag-esclusivo/modifica";
        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($tags as $val) {
            $row = [];
            $row["DT_RowId"] = 'row__' . $val->id;
            $row["DT_RowClass"] = 'colore';
            $row['slug'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$editTagLink . '/'.$val->id.'" style="font-family:consolas">' . $val->slug . '</a>' : $val->slug;
            $row['exclusiven']=$val->exclusiven;
            $row['isPublic'] = $val->isPublic == 1 ? 'Visibile' : 'Nascosto';
            $row['isActive'] = $val->isActive == 1 ? 'Attivo' : 'Non Attivo';
            if ($val->shopId!=null) {
                $shop = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $val->shopId]);
                $row['shopId'] = $val->shopId . '-' . $shop->name;
            }else{
                $row['shopId'] = 'non selezionato';
            }
            if($val->storeHouseId!=null){
                $storeHouse=\Monkey::app()->repoFactory->create('StoreHouse')->findOneBy(['id'=>$val->storeHouseId]);
                $row['storeHouseId']=$storeHouse->name;
            }else{
                $row['storeHouseId']='Non Selezionato';
            }
            $translations = [];
            foreach (\Monkey::app()->repoFactory->create('TagExclusiveTranslation')->findBy(['tagExclusiveId'=>$val->id]) as $translation) $translations[] = $translation->name;
            $row['translations'] = implode('<br>',$translations) ?? " ";
            $response['data'][] = $row;
        }

        return json_encode($response);
    }
    public function put()
    {

    }
}

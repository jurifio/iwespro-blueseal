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

class CTagListAjaxController extends AAjaxController
{
    public function get()
    {
        $datatable = new CDataTables('Tag', ['id'], $_GET, false);

        $tags = $this->app->repoFactory->create('Tag')->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = $this->app->repoFactory->create('Tag')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('Tag')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');
        $editTagLink = "/blueseal/tag/modifica";
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
            $row['sortingPriorityId'] = $val->sortingPriorityId;
            $row['isPublic'] = $val->isPublic == 1 ? 'Visibile' : 'Nascosto';
            $translations = [];
            foreach ($this->app->repoFactory->create('TagTranslation')->findBy(['tagId'=>$val->id]) as $translation) $translations[] = $translation->name;
            $row['translations'] = implode('<br>',$translations) ?? " ";
            $response['data'][] = $row;
        }

        return json_encode($response);
    }
    public function put()
    {

    }
}

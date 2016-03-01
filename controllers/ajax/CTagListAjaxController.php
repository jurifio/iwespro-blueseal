<?php
/**
 * Created by PhpStorm.
 * User: Andrea Tesei
 * Modified File: CUserListAjaxController.php
 * Date: 11/02/2016
 * Time: 10:46
 */

namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

class CTagListAjaxController extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

    /**
     * @param $action
     * @return mixed
     */
    public function createAction($action)
    {
        $this->app->setLang(new CLang(1, 'it'));
        $this->urls['base'] = $this->app->baseUrl(false) . "/blueseal/";
        $this->urls['page'] = $this->urls['base'] . "prodotti";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths', 'dummyUrl');

        if ($this->app->getUser()->hasRole('ownerEmployee')) {

        } else if ($this->app->getUser()->hasRole('friendEmployee')) {
            $res = $this->app->dbAdapter->select('UserHasShop', ['userId' => $this->app->getUser()->getId()])->fetchAll();
            foreach ($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->tags = $this->app->entityManagerFactory->create('Tag');

        return $this->{$action}();
    }

    public function get()
    {
        $datatable = new CDataTables('vBluesealTagList', ['id'], $_GET);

        $tags = $this->em->tags->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = $this->em->tags->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->tags->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $editTagLink = $this->urls['base'] . "tag/modifica";

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach ($tags as $val) {

            $response['data'][$i]["DT_RowId"] = 'row__' . $val->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['slug'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$editTagLink . '/'.$val->id.'" style="font-family:consolas">' . $val->slug . '</a>' : $val->slug;
            $response['data'][$i]['priority'] = $val->sortingPriority->priority;

            $i++;
        }

        echo json_encode($response);
    }
    public function put()
    {

    }
}

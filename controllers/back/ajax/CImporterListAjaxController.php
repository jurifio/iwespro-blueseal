<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CImporterListAjaxController
 * @package redpanda\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CImporterListAjaxController extends AAjaxController
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
        $this->app->setLang(new CLang(1,'it'));
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";
        $this->urls['page'] = $this->urls['base']."importatori";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else{
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->shops = $this->app->entityManagerFactory->create('Shop');

        return $this->{$action}();
    }

    public function get()
    {
        $sql = "SELECT
  `s`.`id`          AS `id`,
  `s`.`name`        AS `name`,
  `s`.`title`       AS `title`,
  `s`.`owner`       AS `owner`,
  `j`.`defaultArgs` AS `args`,
  `j`.`isActive`    AS `active`,
  `j`.`isRunning`   AS `running`,
  `j`.`id`          AS `jobId`,
  `j`.`name`        AS `jobName`,
  `j`.`command`     AS `jobCommand`
FROM (`Shop` `s`
  JOIN `Job` `j`)
WHERE (`s`.`id` IN (SELECT DISTINCT `DirtyProduct`.`shopId`
                    FROM `DirtyProduct`) AND (`s`.`id` = `j`.`defaultArgs`))";
        $datatable = new CDataTables($sql,['id'],$_GET,true);

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $shops = \Monkey::app()->repoFactory->create('Shop')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->shops->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->shops->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $this->app->router->request()->getRequestData('draw');
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];


        $i = 0;
        foreach($shops as $shop) {
            $trans = 0;
            $state = '';

            $resB = $this->app->dbAdapter->query("SELECT count(0) AS mancanti
                                                FROM DictionaryBrand d1
                                                WHERE (d1.shopId = ? AND isnull(d1.productBrandId))", [$shop->id])->fetchAll()[0];
            $resC = $this->app->dbAdapter->query("SELECT count(0) AS mancanti
                                                FROM DictionaryCategory d1
                                                WHERE (d1.shopId = ? AND isnull(d1.productCategoryId))", [$shop->id])->fetchAll()[0];
            $resCG = $this->app->dbAdapter->query("SELECT count(0) AS mancanti
                                                FROM DictionaryColorGroup d1
                                                WHERE (d1.shopId = ? AND isnull(d1.productColorGroupId))", [$shop->id])->fetchAll()[0];
            $resS = $this->app->dbAdapter->query("SELECT count(0) AS mancanti
                                                FROM DictionarySeason d1
                                                WHERE (d1.shopId = ? AND isnull(d1.productSeasonId))", [$shop->id])->fetchAll()[0];
            $resZ = $this->app->dbAdapter->query("SELECT count(0) AS mancanti
                                                FROM DictionarySize d1
                                                WHERE (d1.shopId = ? AND isnull(d1.productSizeId))", [$shop->id])->fetchAll()[0];
            $resT = $this->app->dbAdapter->query("SELECT count(0) AS mancanti
                                                FROM DictionaryTag d1
                                                WHERE (d1.shopId = ? AND isnull(d1.tagId))", [$shop->id])->fetchAll()[0];
            $conn = $this->app->dbAdapter->query("SELECT count(0) AS conn
                                                FROM ImporterConnector d1
                                                WHERE (d1.shopId = ?)", [$shop->id])->fetchAll()[0];
            $errorExe = $this->app->dbAdapter->query("SELECT count(0) AS error
                                                FROM JobExecution je, Job j
                                                WHERE (j.defaultArgs = ?) AND je.jobId=j.id
                                                AND je.error = 1 ", [$shop->id])->fetchAll()[0];
            $errorLog = $this->app->dbAdapter->query("SELECT count(0) AS error
                                                FROM Job j, JobLog jl
                                                WHERE (j.defaultArgs = ?) AND jl.jobId=j.id
                                                AND jl.severity = 'ERROR'", [$shop->id])->fetchAll()[0];

            $trans = $resB['mancanti']+$resC['mancanti']+$resCG['mancanti']+$resS['mancanti']+$resZ['mancanti']+$resT['mancanti'];
            $error = $errorLog['error']+$errorExe['error'];

            if ($shop->job->isRunning != 0) {
                $state = 'Working';
            } elseif ($shop->job->isActive == 0) {
                $state = 'Offline';
            } else {
                $state = 'Online';
            }

            $response['data'][$i]["DT_RowId"] = 'row__'.$shop->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['name'] = $shop->title;
            $response['data'][$i]['trans'] = $trans;
            $response['data'][$i]['state'] = $state;
            $response['data'][$i]['error'] = $error;
            $response['data'][$i]['connector'] = $conn['conn'];

            $i++;
        }

        return json_encode($response);
    }
}
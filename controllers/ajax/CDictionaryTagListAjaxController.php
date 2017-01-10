<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CDictionaryTagListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CDictionaryTagListAjaxController extends AAjaxController
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
        $this->urls['page'] = $this->urls['base']."importatori/dizionari/tag";
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
        $editShopLink = $this->urls['base']."importatori/dizionari/tag/modifica";
        $sql = "SELECT
  `d1`.`shopId`                                                      AS `id`,
  `d1`.`shopId`                                                      AS `shopId`,
  count(0)                                                           AS `count`,
  (SELECT count(0) AS `count`
   FROM `DictionaryTag` `d2`
   WHERE ((`d2`.`shopId` = `d1`.`shopId`) AND isnull(`d2`.`tagId`))) AS `mancanti`
FROM `DictionaryTag` `d1`
GROUP BY `d1`.`shopId`";
        $datatable = new CDataTables($sql,['id'],$_GET);

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $shops = $this->app->repoFactory->create('Shop')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->shops->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->shops->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $this->app->router->request()->getRequestData('draw');
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];


        $i = 0;
        foreach($shops as $shop) {
            $res = $this->app->dbAdapter->query("SELECT count(0) AS count1
                                                FROM DictionaryTag d1
                                                WHERE d1.shopId = ?", [$shop->id])->fetchAll();
            $res2 = $this->app->dbAdapter->query("SELECT count(0) AS mancanti
                                                FROM DictionaryTag d1
                                                WHERE (d1.shopId = ? AND isnull(d1.tagId))", [$shop->id])->fetchAll();

            $response['data'][$i]["DT_RowId"] = 'row__'.$shop->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['shopId'] = '<a data-toggle="tooltip" title="traduci tag" data-placement="right" href="'.$editShopLink.'/'.$shop->id.'" >'.$shop->title.'</a>';
            $response['data'][$i]['count'] = $res[0]['count1'];
            $response['data'][$i]['mancanti'] = $res2[0]['mancanti'];
            $response['data'][$i]['id'] = $shop->id;

            $i++;
        }

        return json_encode($response);
    }
}
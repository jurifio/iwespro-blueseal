<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CDictionaryGroupSizeListAjaxController
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
class CDictionaryGroupSizeListAjaxController extends AAjaxController
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
        $this->urls['page'] = $this->urls['base']."importatori/dizionari/gruppitaglie";
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
        $editShopLinkSize = $this->urls['base']."importatori/dizionari/gruppitaglie/modifica";

        $sql = "SELECT
  `d1`.`shopId`                                                              AS `id`,
  `d1`.`shopId`                                                              AS `shopId`,
  count(0)                                                                   AS `count`,
  (SELECT count(0) AS `count`
   FROM `DictionaryGroupSize` `d2`
   WHERE ((`d2`.`shopId` = `d1`.`shopId`) AND isnull(`d2`.`productSizeGroupId`))) AS `mancanti`
FROM `DictionaryGroupSize` `d1` join Shop s on `d1`.`shopId`  = s.id WHERE s.isVisible='1'
GROUP BY `d1`.`shopId`";
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
            $res = $this->app->dbAdapter->query("SELECT count(0) AS count1
                                                FROM DictionaryGroupSize d1
                                                WHERE d1.shopId = ?", [$shop->id])->fetchAll();
            $res2 = $this->app->dbAdapter->query("SELECT count(0) AS mancanti
                                                FROM DictionaryGroupSize d1
                                                WHERE (d1.shopId = ? AND isnull(d1.productSizeId))", [$shop->id])->fetchAll();

            $response['data'][$i]["DT_RowId"] = 'row__'.$shop->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['shopId'] = '<a data-toggle="tooltip" title="traduci Gruppo taglia" data-placement="right" href="'.$editShopLinkSize.'/'.$shop->id.'" >'.$shop->title.'</a>';
            $response['data'][$i]['count'] = $res[0]['count1'];
            $response['data'][$i]['mancanti'] = $res2[0]['mancanti'];
            $response['data'][$i]['id'] = $shop->id;

            $i++;
        }

        return json_encode($response);
    }
}
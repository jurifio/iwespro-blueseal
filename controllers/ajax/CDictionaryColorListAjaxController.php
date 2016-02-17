<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CDictionaryColorListAjaxController
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
class CDictionaryColorListAjaxController extends AAjaxController
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
        $this->urls['page'] = $this->urls['base']."importatori/dizionari/colori";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        if ($this->app->getUser()->hasRole('ownerEmployee')) {

        } else if($this->app->getUser()->hasRole('friendEmployee')){
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
        $editShopLink = $this->urls['base']."importatori/dizionari/colori/modifica";
        $datatable = new CDataTables('vBluesealDictionaryColorList',['id'],$_GET);

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
                                                FROM DictionaryColorGroup d1
                                                WHERE d1.shopId = ?", [$shop->id])->fetchAll();
            $res2 = $this->app->dbAdapter->query("SELECT count(0) AS mancanti
                                                FROM DictionaryColorGroup d1
                                                WHERE (d1.shopId = ? AND isnull(d1.productColorGroupId))", [$shop->id])->fetchAll();

            $response['data'][$i]["DT_RowId"] = 'row__'.$shop->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['shopId'] = '<a data-toggle="tooltip" title="traduci gruppo colore" data-placement="right" href="'.$editShopLink.'/'.$shop->id.'" >'.$shop->title.'</a>';
            $response['data'][$i]['count'] = $res[0]['count1'];
            $response['data'][$i]['mancanti'] = $res2[0]['mancanti'];
            $response['data'][$i]['id'] = $shop->id;

            $i++;
        }

        echo json_encode($response);
    }
}
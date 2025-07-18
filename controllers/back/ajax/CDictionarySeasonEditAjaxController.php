<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CDictionarySeasonEditAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CDictionarySeasonEditAjaxController extends AAjaxController
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
        $this->urls['page'] = $this->urls['base']."importatori/dizionari/stagioni";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else{
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->seasons = $this->app->entityManagerFactory->create('DictionarySeason');

        return $this->{$action}();
    }

    public function get()
    {
        $shopId = $this->app->router->request()->getRequestData('shop');
        $sql = "select `DictionarySeason`.`shopId` AS `shopId`,`DictionarySeason`.`term` AS `term`,`DictionarySeason`.`productSeasonId` AS `foreign` from `DictionarySeason`";
        $datatable = new CDataTables($sql,['shopId','term'],$_GET,true);
        $datatable->addCondition('shopId',[$shopId]);

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $seasons = \Monkey::app()->repoFactory->create('DictionarySeason')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->seasons->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->seasons->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $this->app->router->request()->getRequestData('draw');
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $productSeasons = \Monkey::app()->repoFactory->create('ProductSeason')->findAll("limit 99999", "order by name");

        $i = 0;
        foreach($seasons as $season) {
            $html = '<div class="form-group form-group-default selectize-enabled full-width">';
            $html .= '<select class="full-width selectpicker" placeholder="Seleziona la stagione" data-init-plugin="selectize" data-action="' . $this->urls['base'] .'xhr/DictionarySeasonEditAjaxController" data-pid="' . $season->shopId . '_' . $season->term . '" tabindex="-1" title="seasonId" name="seasonId" id="seasonId">';
            $html .= '<option value="' . null . '" required ></option>';
            foreach ($productSeasons as $productSeason) {
                $html .= '<option value="' . $productSeason->id . '" required ';
                if ((!is_null($season->productSeasonId)) && ($productSeason->id == $season->productSeasonId)) {
                    $html .= 'selected="selected"';
                }
                $html .= '>' . $productSeason->name . ' ' . $productSeason->year . '</option>';
            }
            $html .= '</select>';
            $html .= '</div>';

            $response['data'][$i]["DT_RowId"] = 'row__'.$season->productSeasonId;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['term'] = $season->term;
            $response['data'][$i]['foreign'] = $html;

            $i++;
        }

        return json_encode($response);
    }

    public function put()
    {
        $seasonId = $this->app->router->request()->getRequestData('seasonId');
        $id = $this->app->router->request()->getRequestData('id');
        $names = explode('_', $id);
        $shopId = $names[0];
        $term = $names[1];

        \Monkey::app()->repoFactory->beginTransaction();
        try {
            $productSeason = \Monkey::app()->repoFactory->create('DictionarySeason')->findOneBy(['shopId' => $shopId, 'term' => $term]);

            $productSeason->productSeasonId = $seasonId;
            $productSeason->update();

            \Monkey::app()->repoFactory->commit();
            return true;
        } catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();
        }
    }
}
<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CDictionaryTagEditAjaxController
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
class CDictionaryTagEditAjaxController extends AAjaxController
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
        $this->em->tags = $this->app->entityManagerFactory->create('DictionaryTag');

        return $this->{$action}();
    }

    public function get()
    {
        $shopId = $this->app->router->request()->getRequestData('shop');
        $sql = "select `DictionaryTag`.`shopId` AS `shopId`,`DictionaryTag`.`term` AS `term`,`DictionaryTag`.`tagId` AS `foreign` from `DictionaryTag`";
        $datatable = new CDataTables($sql,['shopId','term'],$_GET,true);
        $datatable->addCondition('shopId',[$shopId]);

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $tags = \Monkey::app()->repoFactory->create('DictionaryTag')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->tags->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->tags->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $this->app->router->request()->getRequestData('draw');
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $productTags = \Monkey::app()->repoFactory->create('Tag')->findAll("limit 99999", "order by slug");

        $i = 0;
        foreach($tags as $tag) {
            $html = '<div class="form-group form-group-default selectize-enabled full-width">';
            $html .= '<select class="full-width selectpicker" placeholder="Seleziona il tag" data-init-plugin="selectize" data-action="' . $this->urls['base'] .'xhr/DictionaryTagEditAjaxController" data-pid="' . $tag->shopId . '_' . $tag->term . '" tabindex="-1" title="tagId" name="tagId" id="tagId">';
            $html .= '<option value="' . null . '" required ></option>';
            foreach ($productTags as $productTag) {
                $html .= '<option value="' . $productTag->id . '" required ';
                if ((!is_null($tag->tagId)) && ($productTag->id == $tag->tagId)) {
                    $html .= 'selected="selected"';
                }
                $html .= '>' . $productTag->slug . '</option>';
            }
            $html .= '</select>';
            $html .= '</div>';

            $response['data'][$i]["DT_RowId"] = 'row__'.$tag->tagId;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['term'] = $tag->term;
            $response['data'][$i]['foreign'] = $html;

            $i++;
        }

        return json_encode($response);
    }

    public function put()
    {
        $tagId = $this->app->router->request()->getRequestData('tagId');
        $id = $this->app->router->request()->getRequestData('id');
        $names = explode('_', $id);
        $shopId = $names[0];
        $term = $names[1];

        \Monkey::app()->repoFactory->beginTransaction();
        try {
            $productTag = \Monkey::app()->repoFactory->create('DictionaryTag')->findOneBy(['shopId' => $shopId, 'term' => $term]);

            $productTag->tagId = $tagId;
            $productTag->update();

            \Monkey::app()->repoFactory->commit();
            return true;
        } catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();
        }
    }
}
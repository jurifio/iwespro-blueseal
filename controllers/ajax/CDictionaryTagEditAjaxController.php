<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CDictionaryTagEditAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
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

        if ($this->app->getUser()->hasRole('ownerEmployee')) {

        } else if($this->app->getUser()->hasRole('friendEmployee')){
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
        $datatable = new CDataTables('vBluesealDictionaryTagEdit',['shopId','term'],$_GET);
        $datatable->addCondition('shopId',[$shopId]);

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $tags = $this->app->repoFactory->create('DictionaryTag')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->tags->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->tags->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $this->app->router->request()->getRequestData('draw');
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $productTags = $this->app->repoFactory->create('Tag')->findAll("limit 99999", "order by slug");

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

        echo json_encode($response);
    }

    public function put()
    {
        $tagId = $this->app->router->request()->getRequestData('tagId');
        $id = $this->app->router->request()->getRequestData('id');
        $names = explode('_', $id);
        $shopId = $names[0];
        $term = $names[1];

        $this->app->dbAdapter->beginTransaction();
        try {
            $productTag = $this->app->repoFactory->create('DictionaryTag')->findOneBy(['shopId' => $shopId, 'term' => $term]);

            $productTag->tagId = $tagId;
            $productTag->update();

            $this->app->dbAdapter->commit();
            return true;
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
        }
    }
}
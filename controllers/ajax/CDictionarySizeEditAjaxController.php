<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CDictionarySizeEditAjaxController
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
class CDictionarySizeEditAjaxController extends AAjaxController
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
        $this->urls['page'] = $this->urls['base']."importatori/dizionari/taglie";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else{
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->sizes = $this->app->entityManagerFactory->create('DictionarySize');

        return $this->{$action}();
    }

    public function get()
    {
        $shopId = $this->app->router->request()->getRequestData('shop');
        $datatable = new CDataTables('vBluesealDictionarySizeEdit',['shopId','term'],$_GET);
        $datatable->addCondition('shopId',[$shopId]);

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $sizes = $this->app->repoFactory->create('DictionarySize')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->sizes->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->sizes->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $this->app->router->request()->getRequestData('draw');
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $productSizes = $this->app->repoFactory->create('ProductSize')->findAll("limit 99999", "order by name");

        $i = 0;
        foreach($sizes as $size) {
            $html = '<div class="form-group form-group-default selectize-enabled full-width">';
            $html .= '<select class="full-width selectpicker" placeholder="Seleziona la taglia" data-init-plugin="selectize" data-action="' . $this->urls['base'] .'xhr/DictionarySizeEditAjaxController" data-pid="' . $size->shopId . '_' . $size->term . '" tabindex="-1" title="sizeId" name="sizeId" id="sizeId">';
            $html .= '<option value="' . null . '" required ></option>';
            foreach ($productSizes as $productSize) {
                $html .= '<option value="' . $productSize->id . '" required ';
                if ((!is_null($size->productSizeId)) && ($productSize->id == $size->productSizeId)) {
                    $html .= 'selected="selected"';
                }
                $html .= '>' . $productSize->name . '</option>';
            }
            $html .= '</select>';
            $html .= '</div>';

            $response['data'][$i]["DT_RowId"] = 'row__'.$size->productSizeId;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['term'] = $size->term;
            $response['data'][$i]['foreign'] = $html;

            $i++;
        }

        return json_encode($response);
    }

    public function put()
    {
        $sizeId = $this->app->router->request()->getRequestData('sizeId');
        $id = $this->app->router->request()->getRequestData('id');
        $names = explode('_', $id);
        $shopId = $names[0];
        $term = $names[1];

        $this->app->dbAdapter->beginTransaction();
        try {
            $productSize = $this->app->repoFactory->create('DictionarySize')->findOneBy(['shopId' => $shopId, 'term' => $term]);

            $productSize->productSizeId = $sizeId;
            $productSize->update();

            $this->app->dbAdapter->commit();
            return true;
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
        }
    }
}
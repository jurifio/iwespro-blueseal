<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CDictionaryBrandEditAjaxController
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
class CDictionaryBrandEditAjaxController extends AAjaxController
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
        $this->urls['page'] = $this->urls['base']."importatori/dizionari/brand";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else{
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->brands = $this->app->entityManagerFactory->create('DictionaryBrand');

        return $this->{$action}();
    }

    public function get()
    {
        $shopId = $this->app->router->request()->getRequestData('shop');
        $sql = "select `DictionaryBrand`.`shopId` AS `shopId`,`DictionaryBrand`.`term` AS `term`,`DictionaryBrand`.`productBrandId` AS `foreign` from `DictionaryBrand`";
        $datatable = new CDataTables($sql,['shopId','term'],$_GET,true);
        $datatable->addCondition('shopId',[$shopId]);

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $brands = $this->app->repoFactory->create('DictionaryBrand')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->brands->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->brands->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $this->app->router->request()->getRequestData('draw');
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $productBrands = $this->app->repoFactory->create('ProductBrand')->findAll("limit 99999", "order by name");

        $i = 0;
        foreach($brands as $brand) {
            $html = '<div class="form-group form-group-default selectize-enabled full-width">';
            $html .= '<select class="full-width selectpicker" placeholder="Seleziona il brand" data-init-plugin="selectize" data-action="' . $this->urls['base'] .'xhr/DictionaryBrandEditAjaxController" data-pid="' . $brand->shopId . '_' . $brand->term . '" tabindex="-1" title="brandId" name="brandId" id="brandId">';
            $html .= '<option value=""></option>';
            foreach ($productBrands as $productBrand) {
                $html .= '<option value="' . $productBrand->id . '" required ';
                if ((!is_null($brand->productBrandId)) && ($productBrand->id == $brand->productBrandId)) {
                    $html .= 'selected="selected"';
                }
                $html .= '>' . $productBrand->name . '</option>';
            }
            $html .= '</select>';
            $html .= '</div>';

            $response['data'][$i]["DT_RowId"] = 'row__'.$brand->productBrandId;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['term'] = $brand->term;
            $response['data'][$i]['foreign'] = $html;

            $i++;
        }

        return json_encode($response);
    }

    public function put()
    {
        $brandId = $this->app->router->request()->getRequestData('brandId');
        $id = $this->app->router->request()->getRequestData('id');
        $names = explode('_', $id);
        $shopId = $names[0];
        $term = $names[1];

        $this->app->dbAdapter->beginTransaction();
        try {
            $productBrand = $this->app->repoFactory->create('DictionaryBrand')->findOneBy(['shopId' => $shopId, 'term' => $term]);

            $productBrand->productBrandId = $brandId;
            $productBrand->update();

            $this->app->dbAdapter->commit();
            return true;
        } catch (\Throwable $e) {
            $this->app->dbAdapter->rollBack();
        }
    }
}
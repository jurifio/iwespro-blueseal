<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CDictionaryColorEditAjaxController
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
class CDictionaryColorEditAjaxController extends AAjaxController
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

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else{
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->colors = $this->app->entityManagerFactory->create('DictionaryColorGroup');

        return $this->{$action}();
    }

    public function get()
    {
        $shopId = $this->app->router->request()->getRequestData('shop');
        $sql = "select `DictionaryColorGroup`.`shopId` AS `shopId`,`DictionaryColorGroup`.`term` AS `term`,`DictionaryColorGroup`.`productColorGroupId` AS `foreign` from `DictionaryColorGroup`";
        $datatable = new CDataTables($sql,['shopId','term'],$_GET,true);
        $datatable->addCondition('shopId',[$shopId]);

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $colors = $this->app->repoFactory->create('DictionaryColorGroup')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->colors->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->colors->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $this->app->router->request()->getRequestData('draw');
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $productColors = $this->app->repoFactory->create('ProductColorGroup')->findBy([],'limit 99999','order by name');

        $i = 0;
        foreach($colors as $color) {
            $html = '<div class="form-group form-group-default selectize-enabled full-width">';
            $html .= '<select class="full-width selectpicker" placeholder="Seleziona il gruppo colore" data-init-plugin="selectize" data-action="' . $this->urls['base'] .'xhr/DictionaryColorEditAjaxController" data-pid="' . $color->shopId . '_' . $color->term . '" tabindex="-1" title="colorId" name="colorId" id="colorId">';
            $html .= '<option value="' . null . '" required ></option>';
            foreach ($productColors as $productColor) {
                $html .= '<option value="' . $productColor->id . '" required ';
                if ((!is_null($color->productColorGroupId)) && ($productColor->id == $color->productColorGroupId)) {
                    $html .= 'selected="selected"';
                }
                $html .= '>' . $productColor->name . '</option>';
            }
            $html .= '</select>';
            $html .= '</div>';

            $response['data'][$i]["DT_RowId"] = 'row__'.$color->productColorGroupId;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['term'] = $color->term;
            $response['data'][$i]['foreign'] = $html;

            $i++;
        }

        return json_encode($response);
    }

    public function put()
    {
        $colorId = $this->app->router->request()->getRequestData('colorId');
        $id = $this->app->router->request()->getRequestData('id');
        $names = explode('_', $id);
        $shopId = $names[0];
        $term = $names[1];

        $this->app->dbAdapter->beginTransaction();
        try {
            $productColor = $this->app->repoFactory->create('DictionaryColorGroup')->findOneBy(['shopId' => $shopId, 'term' => $term]);

            $productColor->productColorGroupId = $colorId;
            $productColor->update();

            $this->app->dbAdapter->commit();
            return true;
        } catch (\Throwable $e) {
            $this->app->dbAdapter->rollBack();
        }
    }
}
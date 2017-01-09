<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CDictionaryCategoryEditAjaxController
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
class CDictionaryCategoryEditAjaxController extends AAjaxController
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
        $this->urls['page'] = $this->urls['base']."importatori/dizionari/categorie";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else{
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->categories = $this->app->entityManagerFactory->create('DictionaryCategory');

        return $this->{$action}();
    }

    public function get()
    {
        $shopId = $this->app->router->request()->getRequestData('shop');
        $sql = "SELECT
  `DictionaryCategory`.`shopId`            AS `shopId`,
  `DictionaryCategory`.`term`              AS `term`,
  `DictionaryCategory`.`productCategoryId` AS `foreign`
FROM `DictionaryCategory`";
        $datatable = new CDataTables($sql,['shopId','term'],$_GET,true);
        $datatable->addCondition('shopId',[$shopId]);

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $categories = $this->app->repoFactory->create('DictionaryCategory')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->categories->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->categories->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $this->app->router->request()->getRequestData('draw');
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $productCategories = $this->app->repoFactory->create('ProductCategory')->findAll("limit 99999", "order by lft");

	    $explainCategories = [];
	    foreach($productCategories as $productCategory) {
			$explainCategories[$productCategory->id] = $this->app->categoryManager->categories()->getStringPath($productCategory->id," ");
	    }


        $i = 0;
        foreach($categories as $category) {
            $html = '<div class="form-group form-group-default selectize-enabled full-width">';
            $html .= '<select class="full-width selectpicker" placeholder="Seleziona la categoria" data-init-plugin="selectize" data-action="' . $this->urls['base'] .'xhr/DictionaryCategoryEditAjaxController" data-pid="' . $category->shopId . '_' . $category->term . '" tabindex="-1" title="categoryId" name="categoryId" id="categoryId">';
            $html .= '<option value="' . null . '" required ></option>';
            foreach ($explainCategories as $id=>$path) {
                $html .= '<option value="' . $id . '" required ';
                if ((!is_null($category->productCategoryId)) && ($id == $category->productCategoryId)) {
                    $html .= 'selected="selected"';
                }
                $html .= '>' . $path . '</option>';
            }
            $html .= '</select>';
            $html .= '</div>';

            $response['data'][$i]["DT_RowId"] = 'row__'.$category->productCategoryId;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['term'] = $category->term;
            $response['data'][$i]['foreign'] = $html;

            $i++;
        }

        return json_encode($response);
    }

    public function put()
    {
        $categoryId = $this->app->router->request()->getRequestData('categoryId');
        $id = $this->app->router->request()->getRequestData('id');
        $names = explode('_', $id);
        $shopId = $names[0];
        $term = $names[1];

        $this->app->dbAdapter->beginTransaction();
        try {
            $productCategory = $this->app->repoFactory->create('DictionaryCategory')->findOneBy(['shopId' => $shopId, 'term' => $term]);

            $productCategory->productCategoryId = $categoryId;
            $productCategory->update();

            $this->app->dbAdapter->commit();
            return true;
        } catch (\Throwable $e) {
            $this->app->dbAdapter->rollBack();
        }
    }
}
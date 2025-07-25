<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CNameTranslateLangAllListAjaxController
 * @package redpanda\blueseal\controllers\ajax
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
class CNameTranslateLangAllListAjaxController extends AAjaxController
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
        $this->urls['page'] = $this->urls['base']."prodotti";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        $this->em = new \stdClass();
        $this->em->productsName = $this->app->entityManagerFactory->create('ProductNameTranslation');

        return $this->{$action}();
    }

    public function get()
    {
        $langId = $this->app->router->request()->getRequestData('lang');
        $sql = "select `pn`.`id` AS `id`,`pnt`.`productId` AS `productId`,`pnt`.`productVariantId` AS `productVariantId`,`pn`.`name` AS `name`,`pn`.`langId` AS `langId`,0 AS `count` from ((((`ProductName` `pn` join `ProductNameTranslation` `pnt` on(((`pn`.`name` = `pnt`.`name`) and (`pn`.`langId` = `pnt`.`langId`)))) left join `Product` `p` on(((`pnt`.`productId` = `p`.`id`) and (`pnt`.`productVariantId` = `p`.`productVariantId`)))) left join (`ProductHasProductCategory` `phpc` join `ProductCategory` `pc` on((`phpc`.`productCategoryId` = `pc`.`id`))) on(((`p`.`id` = `phpc`.`productId`) and (`p`.`productVariantId` = `phpc`.`productVariantId`)))) join `ProductStatus` on((`ProductStatus`.`id` = `p`.`productStatusId`))) where ((`pn`.`langId` = 1) and (`ProductStatus`.`code` in ('P','A')))";
        $datatable = new CDataTables($sql,['productId','productVariantId','langId'],$_GET,true);

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $datatable->addCondition('langId',[1]);
        $datatable->addCondition('name',[''],true);

        $productsName = \Monkey::app()->repoFactory->create('ProductNameTranslation')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->productsName->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->productsName->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $transRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['langId'] = $langId;
        $response ['data'] = [];

        $i = 0;

        foreach($productsName as $val){
            $trans = $transRepo->findOneBy(['productId' => $val->productId, 'productVariantId' => $val->productVariantId, 'langId' => $langId]);
            $name = '<div class="form-group form-group-default full-width">';
            if (($trans->name != '') && $okManage) {
                $name .= '<input type="text" class="form-control full-width" value="'. $trans->name . '" data-lang="' . $langId . '" data-action="' . $this->urls['base'] .'xhr/NameTranslateLangAllListAjaxController" data-pid="' . $val->productId . '_' . $val->productVariantId. '" title="nameId" name="nameId" id="nameId" />';
            } elseif ($okManage) {
                $name .= '<input type="text" class="form-control full-width" value="" data-lang="' . $langId . '" data-action="' . $this->urls['base'] .'xhr/NameTranslateLangAllListAjaxController" data-pid="' . $val->productId . '_' . $val->productVariantId. '" title="nameId" name="nameId" id="nameId" />';
            }
            $name .= '</div>';

            $response['data'][$i]["DT_RowId"] = 'row__' . $val->productId . '_' . $val->productVariantId;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['trans'] = $name;
            $response['data'][$i]['name'] = $val->name . ' - ' . $val->productId . '_' . $val->productVariantId;
            $response['data'][$i]['productId'] = $val->productId;
            $response['data'][$i]['productVariantId'] = $val->productVariantId;

            $i++;
        }

        return json_encode($response);
    }

    public function put()
    {
        $nameId = $this->app->router->request()->getRequestData('nameId');
        $id = $this->app->router->request()->getRequestData('id');
        $names = explode('_', $id);
        $productId = $names[0];
        $productVariantId = $names[1];

        $langId = $this->app->router->request()->getRequestData('lang');

        \Monkey::app()->repoFactory->beginTransaction();
        try {
            $trans = \Monkey::app()->repoFactory->create('ProductNameTranslation')->findOneBy(['productId' => $productId, 'productVariantId' => $productVariantId, 'langId' => $langId]);
            if (!is_null($trans)) {
                $trans->name = $nameId;
                $trans->update();

            } elseif ($nameId != '') {
                $trans = \Monkey::app()->repoFactory->create("ProductNameTranslation")->getEmptyEntity();

                $trans->productId = $productId;
                $trans->productVariantId = $productVariantId;
                $trans->langId = $langId;
                $trans->name = $nameId;
                $trans->insert();
            }
            \Monkey::app()->repoFactory->commit();
            return true;
        } catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();
        }
    }
}
<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CNameTranslateLangListAjaxController
 * @package redpanda\blueseal\controllers\ajax
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
class CNameTranslateLangListAjaxController extends AAjaxController
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
        $datatable = new CDataTables('vBluesealProductNameList',['productId','productVariantId','langId'],$_GET);

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $datatable->addCondition('langId',[1]);
        $datatable->addCondition('name',[''],true);

        $productsName = $this->app->repoFactory->create('ProductNameTranslation')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->productsName->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->productsName->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $transRepo = $this->app->repoFactory->create('ProductNameTranslation');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($productsName as $val){
            $trans = $transRepo->findOneBy(['productId' => $val->productId, 'productVariantId' => $val->productVariantId, 'langId' => $langId]);

            $name = '<div class="form-group form-group-default full-width">';
            if (($trans->name != '') && $okManage) {
                continue;
            } elseif ($okManage) {
                $name .= '<input type="text" class="form-control full-width" data-lang="' . $langId . '" data-action="' . $this->urls['base'] .'xhr/NameTranslateLangListAjaxController" data-pid="' . $val->productId . '_' . $val->productVariantId. '" title="nameId" name="nameId" id="nameId" />';
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

        $this->app->dbAdapter->beginTransaction();
        try {
            $trans = $this->app->repoFactory->create('ProductNameTranslation')->findOneBy(['productId' => $productId, 'productVariantId' => $productVariantId, 'langId' => $langId]);

            if (!is_null($trans)) {
                $trans->name = $nameId;
                $trans->update();

            } elseif ($nameId != '') {
                $trans = $this->app->repoFactory->create("ProductNameTranslation")->getEmptyEntity();

                $trans->productId = $productId;
                $trans->productVariantId = $productVariantId;
                $trans->langId = $langId;
                $trans->name = $nameId;
                $trans->insert();
            }
            $this->app->dbAdapter->commit();
            return true;
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
        }
    }
}
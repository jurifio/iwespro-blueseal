<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CNameTranslateListAjaxController
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
class CNameTranslateListAjaxController extends AAjaxController
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
        $datatable = new CDataTables('vBluesealProductNameList',['productId','productVariantId','langId'],$_GET);
        $modifica = $this->urls['base']."traduzioni/nomi/modifica";

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
        $repo = $this->app->repoFactory->create('Lang');
        $installedLang = $repo->findAll();

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($productsName as $val){
            $html = '';

            foreach ($installedLang as $insLang) {
                $lang = $transRepo->findOneBy(['productId' => $val->productId, 'productVariantId' => $val->productVariantId, 'langId' => $insLang->id]);
                if(!is_null($lang) && ($lang->name != '')) {
                    $html .= '<span class="badge">' . $insLang->lang . '</span>';
                } else {
                    $html .= '<span class="badge badge-red">' . $insLang->lang . '</span>';
                }
            }

            $response['data'][$i]["DT_RowId"] = 'row__' . $val->productId. '_' . $val->productVariantId;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['name'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'. $modifica . '?productId=' . $val->productId . '&productVariantId=' . $val->productVariantId.'">' . $val->name . '</a>' : $val->name;
            $response['data'][$i]['lang'] = $html;

            $i++;
        }

        echo json_encode($response);
    }
}
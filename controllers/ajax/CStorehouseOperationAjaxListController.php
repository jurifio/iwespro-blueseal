<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CStorehouseOperationAjaxListController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/08/2016
 * @since 1.0
 */
class CStorehouseOperationAjaxListController extends AAjaxController
{
   public function get()
    {
        $datatable = new CDataTables('vBluesealCatalogMovements', ['id', 'shopId', 'storehouseId'], $_GET);

        $shops = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
        $datatable->addCondition('shopId', $shops);

        $operazioni = $this->app->repoFactory->create('StorehouseOperation')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->app->repoFactory->create('Product')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('Product')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        //$okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($operazioni as $val)
        {
            $response['data'][$i]["DT_RowId"] = $val->printId();
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['id'] = $val->id;

            $sign = ($val->storehouseOperationCause->sign) ? '+' : '-';
            $response['data'][$i]['cause'] = $val->storehouseOperationCause->name . ' (' . $sign . ') ';

            $response['data'][$i]['operationDate'] = date('d-M-Y', strtotime($val->operationDate));
            $response['data'][$i]['creationDate'] = date('d-M-Y', strtotime($val->creationDate));

            $response['data'][$i]['friend'] = $val->shop->title;

            $response['data'][$i]['movements'] = '<span class="small">'.$val->storehouseOperationLine->count().' Elementi movimentati <br />';
            foreach ($val->storehouseOperationLine as $line) {
                $sku = $line->productSku;
                $product = $sku->product;
                $brand = $product->productBrand->name;
                $size = $sku->productSize->name;
                $response['data'][$i]['movements'].= $product->printId() . " / " . $brand . " / " . $size . " / " . $product->printCpf() . ": " . $line->qty . '<br />';
            }
            $response['data'][$i]['movements'] .= '</span>';
            $i++;
        }

        return json_encode($response);
    }
}
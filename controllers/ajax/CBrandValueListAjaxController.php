<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CBrandValueListAjaxController extends AAjaxController
{
    public function get()
    {
        $shopIds = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser();

        $datatable = new CDataTables('vBluesealProductBrandValueList',['id'],$_GET);
        $datatable->addSearchColumn('shop',$shopIds);

        $brands = $this->app->repoFactory->create('ProductBrand')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->products->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->products->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach($brands as $val){
            $row = [];

            $row["DT_RowId"] = $val->printId();
            $row["DT_RowClass"] = 'colore';
            $row['brand'] =  $val->name;
            $row['season'] =  $val->name;
            $row['shop'] =  $val->name;
            $row['prodotti'] =  $val->name;
            $row['quantita'] =  $val->name;
            $row['valore_al_costo'] =  $val->name;
            $row['valore_al_prezzo'] =  $val->name;
            $row['incasso_friend'] =  $val->name;
            $row['incasso_picky'] =  $val->name;

            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}
<?php

namespace bamboo\controllers\back\ajax;

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
class CBrandListAjaxController extends AAjaxController
{
    /**
     * @return string
     */
    public function get()
    {
        $sql = "SELECT pb.id,
                        pb.name,
                        pb.slug,
                        pb.description,
                        pb.logoUrl,
                        count(distinct p.id, p.productVariantId) as productCount 
                from ProductBrand pb LEFT JOIN 
                (Product p 
                  JOIN ProductStatus ps on p.productStatusId = ps.id and ps.isVisible = 1) 
                    on pb.id = p.productBrandId
                GROUP BY pb.id";
        $datatable = new CDataTables($sql, ['id'], $_GET );

        $prodotti = $this->app->repoFactory->create('ProductBrand')->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = $this->app->repoFactory->create('ProductBrand')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('ProductBrand')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $modifica = $this->app->baseUrl(false) . "/blueseal/prodotti/brand/modifica";

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach ($prodotti as $val) {

            $response['data'][$i]["DT_RowId"] = 'row__' . $val->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['name'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $val->id . '">' . $val->name . '</a>' : $val->name;
            $response['data'][$i]['slug'] = $val->slug;
            $response['data'][$i]['description'] = $val->description;
            $response['data'][$i]['logoUrl'] = $val->logoUrl && !empty($val->logoUrl) ? '<img style="max-height: 50px" src="' . $val->logoUrl . '">' : "";
            $totalProduct = $this->app->dbAdapter->query("SELECT COUNT(*) AS conto FROM Product WHERE productBrandId = ? ", array($val->id))->fetch()['conto'];
            $publishedProduct = $this->app->dbAdapter->query("SELECT COUNT(*) AS conto 
                                                                     FROM Product JOIN ProductStatus ON Product.productStatusId = ProductStatus.id 
                                                                     WHERE isVisible = 1 AND 
                                                                     productBrandId = ? ", [$val->id])->fetch()['conto'];
            $response['data'][$i]['productCount'] = $publishedProduct.' ('.$totalProduct.')';

                $i++;
        }

        return json_encode($response);
    }
}
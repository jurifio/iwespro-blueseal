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
class CProductSlimListAjaxController extends AAjaxController
{
    public function get()
    {
        $shopsIds = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser();

        $datatable = new CDataTables('vBluesealProductSlimList', ['id', 'productVariantId'], $_GET);
        $datatable->addCondition('shopId', $shopsIds);

        $prodotti = $this->app->repoFactory->create('Product')->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = $this->app->repoFactory->create('Product')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('Product')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        $modifica = '/blueseal/friend/prodotti/modifica';

        foreach ($prodotti as $val) {
            $response['data'][$i]["DT_RowId"] = $val->printId();
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $val->id . '&productVariantId=' . $val->productVariantId . '">' . $val->id . '-' . $val->productVariantId . '</a>' : $val->id . '-' . $val->productVariantId;

            $img = strpos($val->dummyPicture, 's3-eu-west-1.amazonaws.com') ? $val->dummyPicture : "/assets/" . $val->dummyPicture;
            if ($val->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
            else $imgs = "";
            $response['data'][$i]['image'] = '<img width="50" src="' . $img . '" />' . $imgs . '<br />';

            $response['data'][$i]['shop'] = '<span>';
            foreach ($val->shop as $shop) {
                if (in_array($shop->id, $shopsIds)) {
                    $response['data'][$i]['shop'] .= $shop->name . '<br />';
                }
            }
            $response['data'][$i]['shop'] .= '</span>';

            $response['data'][$i]['details'] = $val->productSheetActual->count() ? 'Sì' : 'No';

            $ext = [];
            foreach ($val->shopHasProduct as $shp) {
                if (in_array($shp->shopId, $shopsIds)) {
                    if (!empty($shp->extId)) {
                        $ext[] = $shp->extId;
                    }
                    if (!is_null($shp->dirtyProduct)) {
                        if (!empty($shp->dirtyProduct->extId)) {
                            $ext[] = $shp->dirtyProduct->extId;
                        }
                        foreach ($shp->dirtyProduct->dirtySku as $sku) {
                            if (!empty($sku->extSkuId)) {
                                $ext[] = $sku->extSkuId;
                            }
                        }
                    }
                }
            }

            $ext = array_unique($ext);
            $ext = implode('<br>', $ext);

            $response['data'][$i]['externalId'] = '<span class="small">';
            $response['data'][$i]['externalId'] .= empty($ext) ? "" : $ext;
            $response['data'][$i]['externalId'] .= '</span>';

            $response['data'][$i]['cpf'] = '<span class="small">';
            $response['data'][$i]['cpf'] .= $val->itemno . ' # ' . $val->productVariant->name;
            $response['data'][$i]['cpf'] .= '</span>';

            $response['data'][$i]['brand'] = $val->productBrand->name;

            $cats = [];
            foreach ($val->productCategoryTranslation as $cat) {
                $path = $this->app->categoryManager->categories()->getPath($cat->productCategoryId);
                unset($path[0]);
                $cats[] = '<span>' . implode('/', array_column($path, 'slug')) . '</span>';
            }

            $response['data'][$i]['categories'] = '<span class="small">';
            $response['data'][$i]['categories'] .= implode(',<br/>', $cats); //category
            $response['data'][$i]['categories'] .= '</span>';

            $sizes = [];
            $qty = [];

            foreach ($val->productSku as $productSku) {
                if (in_array($productSku->shopId, $shopsIds) && $productSku->stockQty > 0) {
                    $sizes[$productSku->productSizeId] = $productSku->productSize->name;
                    $qty[$productSku->shopId][$productSku->productSizeId] = $productSku->stockQty;
                }
            }
            if (count($sizes) > 0) {
                $table = '<table class="nested-table">';
                $table .= '<thead><tr>';
                if (count($qty) > 1) {
                    $table .= '<th>Shop</th>';
                }
                foreach ($sizes as $sizeId => $name) {
                    $table .= '<th>' . $name . '</th>';
                }
                $table .= '</tr></thead>';
                $table .= '<tbody>';
                foreach ($qty as $shopId => $size) {
                    $table .= '<tr>';
                    if (count($qty) > 1) {
                        $shop = $this->app->repoFactory->create('Shop')->findOne([$shopId]);
                        $table .= '<td>' . $shop->name . '</td>';
                    }
                    foreach ($sizes as $sizeId => $name) {
                        $table .= '<td>' . (isset($size[$sizeId]) ? $size[$sizeId] : 0) . '</td>';
                    }
                    $table .= '</tr>';
                }
                $table .= '</tbody></table>';
            } else {
                $table = 'Quantità non inserite';
            }
            $response['data'][$i]['stock'] = $table;

            $response['data'][$i]['season'] = '<span class="small">';
            $response['data'][$i]['season'] .= ($val->productSeason) ? $val->productSeason->name . " " . $val->productSeason->year : '-';
            $response['data'][$i]['season'] .= '</span>';

            $response['data'][$i]['status'] = $val->productStatus->name;
            $response['data'][$i]['creationDate'] = $val->creationDate;

            $i++;
        }
        return json_encode($response);
    }
}
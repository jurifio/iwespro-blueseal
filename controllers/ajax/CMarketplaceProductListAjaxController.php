<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CMarketplaceProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 18/07/2016
 * @since 1.0
 */
class CMarketplaceProductListAjaxController extends AAjaxController
{
    public function get()
    {
        $sample = $this->app->repoFactory->create('Product')->getEmptyEntity();

        $datatable = new CDataTables('vBluesealMarketplaceProductList', $sample->getPrimaryKeys(), $_GET);

        if ($this->app->router->request()->getRequestData('accountId')) {
            $marketplaceAccountId = $this->app->router->request()->getRequestData('accountId');
            $marketplaceAccount = $this->app->repoFactory->create('MarketplaceAccount')->findOneByStringId($marketplaceAccountId);
            $datatable->addCondition('marketplaceId', [$marketplaceAccount->marketplaceId]);
            $datatable->addCondition('marketplaceAccountId', [$marketplaceAccount->id]);
        } else {
            $marketplaceAccount = false;
        }

        $datatable->addCondition('shopId', $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser());
        $datatable->addSearchColumn('marketplaceProductId');

        $prodotti = $sample->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = $sample->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $sample->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;
        foreach ($prodotti as $val) {

            $img = strpos($val->dummyPicture, 's3-eu-west-1.amazonaws.com') ? $val->dummyPicture : "/assets/" . $val->dummyPicture;
            if ($val->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
            else $imgs = "";

            $shops = [];
            foreach ($val->shop as $shop) {
                $shops[] = $shop->name;
            }

            $response['data'][$i]["DT_RowId"] = $val->printId();
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['code'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="/blueseal/prodotti/modifica?id=' . $val->id . '&productVariantId=' . $val->productVariantId . '">' . $val->id . '-' . $val->productVariantId . '</a>';
            $response['data'][$i]['brand'] = $val->productBrand->name;
            $response['data'][$i]['season'] = $val->productSeason->name;

            $th = "";
            $tr = "";
            $res = $this->app->dbAdapter->query("SELECT s.name, sum(ps.stockQty) stock
                                          FROM ProductSku ps , ProductSize s
                                          WHERE ps.productSizeId = s.id AND
                                              ps.productId = ? AND
                                              ps.productVariantId = ?
                                          GROUP BY ps.productSizeId
                                          HAVING stock > 0 ORDER BY `name`", [$val->id, $val->productVariantId])->fetchAll();
            foreach ($res as $sums) {
                $th .= "<th>" . $sums['name'] . "</th>";
                $tr .= "<td>" . $sums['stock'] . "</td>";
            }
            $response['data'][$i]["stock"] = '<table class="nested-table"><thead><tr>' . $th . "</tr></thead><tbody>" . $tr . "</tbody></table>";

            $response['data'][$i]['shop'] = implode(', ', $shops);
            $response['data'][$i]['dummy'] = '<img width="50" src="' . $img . '" />' . $imgs . '<br />';
            $response['data'][$i]['itemno'] = '<span class="small">';
            $response['data'][$i]['itemno'] .= $val->itemno . ' # ' . $val->productVariant->name;
            $response['data'][$i]['itemno'] .= '</span>';

            $response['data'][$i]['fee'] = 0;
            $marketplaces = [];
            foreach ($val->marketplaceAccountHasProduct as $mProduct) {
                if ($marketplaceAccount &&
                    ($marketplaceAccount->id != $mProduct->marketplaceAccountId ||
                        $marketplaceAccount->marketplaceId != $mProduct->marketplaceId)) continue;
                $style = $mProduct->isToWork == 0 ? ($mProduct->hasError ? 'style="color:red"' : 'style="color:green"') : "";
                $marketplaces[] = '<span ' . $style . '>' . $mProduct->marketplaceAccount->marketplace->name . ' - ' . $mProduct->marketplaceAccount->name . ' (' . $mProduct->marketplaceProductId . ')</span>';
                $response['data'][$i]['fee'] += $mProduct->fee;
            }

            $response['data'][$i]['marketplaceAccountName'] = implode('<br>', $marketplaces);

            $response['data'][$i]['categories'] = $val->productCategory->getFirst()->getLocalizedName();

            $i++;
        }

        return json_encode($response);
    }
}
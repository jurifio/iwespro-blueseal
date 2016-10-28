<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProduct;

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
        $sample = $this->app->repoFactory->create('MarketplaceAccountHasProduct')->getEmptyEntity();
        $datatable = new CDataTables('vBluesealMarketplaceProductList', $sample->getPrimaryKeys(), $_GET);

        $datatable->addCondition('shopId', $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser());
        $datatable->addSearchColumn('marketplaceProductId');

        /*if($this->app->router->request()->getRequestData('accountId')) {
            $marketplaceAccountId = $this->app->router->request()->getRequestData('accountId');
            $marketplaceAccount = $this->app->repoFactory->create('MarketplaceAccount')->findOneByStringId($marketplaceAccountId);
            $datatable->addCondition('marketplaceId', [$marketplaceAccount->marketplaceId]);
            $datatable->addCondition('marketplaceAccountId', [$marketplaceAccount->id]);
        } else {
            $marketplaceAccount = false;
        }*/

        $righe = $this->app->dbAdapter->query($datatable->getQuery(), $datatable->getParams())->fetchAll();
        $count = $sample->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $sample->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($righe as $val) {
            $row = [];
            $marketplaceHasProduct = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct')->findOne($val);
            if (is_null($marketplaceHasProduct)) {
                $product = \Monkey::app()->repoFactory->create('Product')->findOne([$val['productId'], $val['productVariantId']]);
                $row['fee'] = 0;
                $row['marketplaceAccountName'] = "";
            } else {
                $product = $marketplaceHasProduct->product;

                $style = $marketplaceHasProduct->isToWork == 0 ? ($marketplaceHasProduct->hasError ? 'style="color:red"' : 'style="color:green"') : "";
                $row['marketplaceAccountName'] = '<span ' . $style . '>' .
                    $marketplaceHasProduct->marketplaceAccount->marketplace->name . ' - ' .
                    $marketplaceHasProduct->marketplaceAccount->name .
                    (empty ($marketplaceHasProduct->marketplaceProductId) ? "" : ' (' . $marketplaceHasProduct->marketplaceProductId . ')</span>');

                $row['fee'] = $marketplaceHasProduct->fee;
            }
            /** @var CProduct $product */
            if ($product->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
            else $imgs = "";

            $shops = [];
            foreach ($product->shop as $shop) {
                $shops[] = $shop->name;
            }

            $row["DT_RowId"] = $product->printId();
            $row["DT_RowClass"] = 'colore';
            $row['code'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="/blueseal/prodotti/modifica?id=' . $product->id . '&productVariantId=' . $product->productVariantId . '">' . $product->id . '-' . $product->productVariantId . '</a>';
            $row['brand'] = $product->productBrand->name;
            $row['season'] = $product->productSeason->name;

            $th = "";
            $tr = "";
            $res = $this->app->dbAdapter->query("SELECT s.name, sum(ps.stockQty) stock
                                          FROM ProductSku ps , ProductSize s
                                          WHERE ps.productSizeId = s.id AND
                                              ps.productId = ? AND
                                              ps.productVariantId = ?
                                          GROUP BY ps.productSizeId
                                          HAVING stock > 0 ORDER BY `name`", [$product->id, $product->productVariantId])->fetchAll();
            foreach ($res as $sums) {
                $th .= "<th>" . $sums['name'] . "</th>";
                $tr .= "<td>" . $sums['stock'] . "</td>";
            }
            $row["stock"] = '<table class="nested-table"><thead><tr>' . $th . "</tr></thead><tbody>" . $tr . "</tbody></table>";

            $row['shop'] = implode(', ', $shops);
            $row['dummy'] = '<img width="50" src="' . $product->getDummyPictureUrl() . '" />' . $imgs . '<br />';
            $row['itemno'] = '<span class="small">';
            $row['itemno'] .= $product->printCpf();
            $row['itemno'] .= '</span>';

            $row['category'] = $product->getLocalizedProductCategories('<br>');
            $row['creationDate'] = $product->creationDate;
            $response ['data'][] = $row;
        }

        return json_encode($response);
    }
}
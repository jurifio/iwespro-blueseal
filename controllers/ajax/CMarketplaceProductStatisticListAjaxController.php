<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\core\traits\TMySQLTimestamp;
use bamboo\domain\entities\CProduct;

/**
 * Class CMarketplaceProductStatisticListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CMarketplaceProductStatisticListAjaxController extends AMarketplaceAccountAjaxController
{
    use TMySQLTimestamp;

    public function get()
    {
        $marketplaceAccountId = $this->app->router->request()->getRequestData('MarketplaceAccount');
        $marketplaceAccount = $this->app->repoFactory->create('MarketplaceAccount')->findOneByStringId($marketplaceAccountId);

        $query = self::SQL_SELECT_CAMPAING_PRODUCT_STATISTIC_MARKETPLACE_ACCOUNT." order by visits desc";

        //IL PROBLEMA é IL DIOCANE DI TIMESTAMP CHE RIMANE NULL DI MERDA DI DIO
        $timeFrom = new \DateTime($this->app->router->request()->getRequestData('startDate').' 00:00:00');
        $timeTo = new \DateTime($this->app->router->request()->getRequestData('endDate').' 00:00:00');

        $timeFrom = $timeFrom ? $this->time($timeFrom->getTimestamp()) : null;
        $timeTo = $timeTo ? $this->time($timeTo->getTimestamp()) : null;
        $queryParameters = [$timeFrom, $timeTo,$timeFrom, $timeTo,$marketplaceAccount->id, $marketplaceAccount->marketplaceId ];

        $datatable = new CDataTables($query, ['productId','productVariantId'], $_GET, true);
        //$datatable->addCondition('shopId', $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser());
        $datatable->addSearchColumn('marketplaceProductId');

        $prodottiMarks = $this->app->dbAdapter->query($datatable->getQuery(false, true), array_merge($queryParameters, $datatable->getParams()))->fetchAll();
        $count = $this->app->repoFactory->create('Product')->em()->findCountBySql($datatable->getQuery(true), array_merge($queryParameters, $datatable->getParams()));
        $totalCount = $this->app->repoFactory->create('Product')->em()->findCountBySql($datatable->getQuery('full'), array_merge($queryParameters, $datatable->getParams()));

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response['queryParams'] = $queryParameters;
        $response ['data'] = [];

        foreach ($prodottiMarks as $values) {

            $row = [];
            $prodottiMark = $this->app->repoFactory->create('MarketplaceAccountHasProduct')->findOneBy([
                'marketplaceId' => $values['marketplaceId'],
                'marketplaceAccountId' => $values['marketplaceAccountId'],
                'productId' => $values['productId'],
                'productVariantId' => $values['productVariantId'],
            ]);
            /** @var CProduct $val */
            $val = $prodottiMark->product;

            $img = $val->getDummyPictureUrl();
            if ($val->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
            else $imgs = "";

            $row["DT_RowId"] = $val->printId();
            $row["DT_RowClass"] = 'colore';
            $row['codice'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="/blueseal/prodotti/modifica?id=' . $val->id . '&productVariantId=' . $val->productVariantId . '">' . $val->printId() . '</a>';
            $row['marketCode'] = $prodottiMark->printId();
            $row['brand'] = $val->productBrand->name;
            $row['season'] = $val->productSeason->name;

            $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="'.$val->printId().'"></table>';;

            $row['shop'] = $val->getShops('<br>');
            $row['dummy'] = '<img width="50" src="' . $img . '" />' . $imgs . '<br />';
            $row['itemno'] = '<span class="small">';
            $row['itemno'] .= $val->itemno . ' # ' . $val->productVariant->name;
            $row['itemno'] .= '</span>';

            $row['fee'] = $prodottiMark->fee;
            $row['isToWork'] = $prodottiMark->isToWork ? 'sì' : 'no';
            $row['hasError'] = $prodottiMark->hasError ? 'sì' : 'no';
            $row['isDeleted'] = $prodottiMark->isDeleted ? 'sì' : 'no';
            $row['marketplaceAccountName'] = $prodottiMark->marketplaceAccount->marketplace->name;
            $row['creationDate'] = $val->creationDate;
            $row['categories'] = $val->getLocalizedProductCategories("<br>");
            $row['conversions'] = $values['conversions'];
            $row['pConversions'] = $values['pConversions'];
            $row['visits'] = $values['visits'];
            $row['visitsCost'] = $values['visitsCost'];
            $row['conversionsValue'] = $values['conversionsValue'];
            $row['pConversionsValue'] = $values['pConversionsValue'];
            $row['activePrice'] = $values['activePrice'];
            $row['ordersIds'] = $values['ordersIds'];

            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}
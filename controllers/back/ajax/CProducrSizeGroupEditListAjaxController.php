<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CProduct;

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
class CProducrSizeGroupEditListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "S";

        $shootingCritical = \Monkey::app()->router->request()->getRequestData('shootingCritical');
        if ($shootingCritical)  $sql .= " AND `p`.`dummyPicture` not like '%dummy%' AND `p`.`productStatusId` in (4,5,11)";
        $productDetailCritical = \Monkey::app()->router->request()->getRequestData('detailsCritical');
        if ($productDetailCritical) $sql .= " AND `p`.`dummyPicture` not like '%dummy%' AND `p`.`productStatusId` in (4,5,11) HAVING `hasDetails` = 'no'";

        $datatable = new CDataTables($sql, ['id', 'productVariantId'], $_GET,true);
        $shopIds = \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
        $datatable->addCondition('shopId', $shopIds);

        $em = $this->app->entityManagerFactory->create('ProductStatus');
        $productStatuses = $em->findAll('limit 99', '');

        $statuses = [];
        foreach ($productStatuses as $status) {
            $statuses[$status->code] = $status->name;
        }

        $modifica = $this->app->baseUrl(false) . "/blueseal/friend/prodotti/modifica";
        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');
        $productRepo = $this->app->repoFactory->create('Product');
        $datatable->doAllTheThings();

        foreach ($datatable->getResponseSetData() as $key=>$row) {
            /** @var $val CProduct */
            $val = $productRepo->findOneBy($row);

            $row["DT_RowId"] = $val->printId();
            $row["DT_RowClass"] = $val->productStatus->isVisible == 1 ? 'verde' : (
            $val->productStatus->isReady == 1 ? 'arancione' : ""
            );

            $row['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $val->id . '&productVariantId=' . $val->productVariantId . '">' . $val->id . '-' . $val->productVariantId . '</a>' : $val->id . '-' . $val->productVariantId;
            $row['dummy'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $val->getDummyPictureUrl() . '" /></a>';
            $row['productSizeGroup'] = ($val->productSizeGroup) ? '<span class="small">' . $val->productSizeGroup->locale . '-' . explode("-", $val->productSizeGroup->macroName)[0] . '</span>' : '';

            $row['details'] = "";
            foreach ($val->productSheetActual as $k => $v) {
                if ($trans = $v->productDetail->productDetailTranslation->getFirst()) {
                    $row['details'] .= '<span class="small">' . $trans->name . "</span><br />";
                }
            }

            $row['hasPhotos'] = ($val->productPhoto->count()) ? 'sì' : 'no';
            $row['hasDetails'] = (2 < $val->productSheetActual->count()) ? 'sì' : 'no';
            $row['season'] = '<span class="small">' . $val->productSeason->name . " " . $val->productSeason->year . '</span>';

            $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="'.$val->printId().'"></table>';
            $row['externalId'] = '<span class="small">'.$val->getShopExtenalIds('<br />').'</span>';

            $row['cpf'] = $val->printCpf();

            $row['colorGroup'] = '<span class="small">' . (!is_null($val->productColorGroup) ? $val->productColorGroup->productColorGroupTranslation->getFirst()->name : "[Non assegnato]") . '</span>';
            $row['brand'] = isset($val->productBrand) ? $val->productBrand->name : "";
            $row['categoryId'] = '<span class="small">' . $val->getLocalizedProductCategories(" ", "<br>") . '</span>';
            $row['description'] = '<span class="small">' . ($val->productDescriptionTranslation->getFirst() ? $val->productDescriptionTranslation->getFirst()->description : "") . '</span>';

            $row['productName'] = $val->productNameTranslation->getFirst() ? $val->productNameTranslation->getFirst()->name : "";
            $row['tags'] = '<span class="small">' . $val->getLocalizedTags('<br>', false) . '</span>';
            $row['status'] = $val->productStatus->name;
            $row['productPriority'] = $val->sortingPriorityId;

            $qty = 0;
            $shopz = [];
            $mup = [];
            $isOnSale = $val->isOnSale();
            foreach ($val->productSku as $sku) {
                $qty += $sku->stockQty;
                $iShop = $sku->shop->name;
                if (!in_array($iShop, $shopz)) {
                    $shopz[] = $iShop;

                    $price = $isOnSale ? $sku->salePrice : $sku->price;

                    if ((float)$price) {
                        $multiplier = ($val->productSeason->isActive) ? (($isOnSale) ? $sku->shop->saleMultiplier : $sku->shop->currentSeasonMultiplier) : $sku->shop->pastSeasonMultiplier;
                        $value = $sku->value;
                        $friendRevenue = $value + $value * $multiplier / 100;
                        $priceNoVat = $price / 1.22;
                        $mup[] = number_format(($priceNoVat - $friendRevenue) / $priceNoVat * 100, 2, ",", ".");
                    } else {
                        $mup[] = '-';
                    }
                }
            }
            $row['hasQty'] = $qty;
            $row['activePrice'] = $val->getDisplayActivePrice() ? $val->getDisplayActivePrice() : 'Non Assegnato';

            $row['marketplaces'] = $val->getMarketplaceAccountsName(' - ','<br>',true);

            $row['shop'] = '<span class="small">'.$val->getShops('<br />', true).'</span>';

            $row['mup'] = '<span class="small">';
            $row['mup'] .= implode('<br />', $mup);
            $row['mup'] .= '</span>';

            $row['friendPrices'] = [];
            $row['friendValues'] = [];
            $row['friendSalePrices'] = [];
            foreach ($val->shopHasProduct as $shp) {
                $row['friendPrices'][] = $shp->price;
                $row['friendValues'][] = $shp->value;
                $row['friendSalePrices'][] = $shp->salePrice;
            }

            $row['friendPrices'] = implode('<br />',$row['friendPrices']);
            $row['friendValues'] = implode('<br />',$row['friendValues']);
            $row['friendSalePrices'] = implode('<br />',$row['friendSalePrices']);

            $row['colorNameManufacturer'] = $val->productVariant->description;

            $row['isOnSale'] = $val->isOnSale();
            $row['creationDate'] = (new \DateTime($val->creationDate))->format('d-m-Y H:i');
            $row['processing'] = ($val->processing) ? $val->processing : '-';
            $datatable->setResponseDataSetRow($key,$row);
        }
        return $datatable->responseOut();
    }
}
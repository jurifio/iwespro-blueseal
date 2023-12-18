<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CShooting;
use bamboo\domain\repositories\CDocumentRepo;

/**
 * Class CProductPackingListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 18/12/2023
 * @since 1.0
 */
class CProductPackingListAjaxController extends AAjaxController
{
    public function get()
    {
        try {
            $sql = "SELECT
                  concat(p.id, '-', pv.id)                                                                      AS code,
                  p.id                                                                                              AS id,
                  p.productVariantId                                                                                AS productVariantId,
                  concat(pse.name, ' ', pse.year)                                                               AS season,
                  pse.isActive                                                                                      AS isActive,
                  concat(p.itemno, ' # ', pv.name)                                                              AS cpf,
                  pv.description                                                                                    AS colorNameManufacturer,
                  concat(s.id, '-', s.name)                                                                     AS shop,
                  concat(ifnull(p.externalId, ''), '-', ifnull(dp.extId, ''), '-', ifnull(ds.extSkuId, '')) AS externalId,
                  pb.name                                                                                           AS brand,
                  ps.name                                                                                           AS status,
                  concat(psg.locale, ' - ',
                         psmg.name)                                                                                 AS productSizeGroup,
                  p.creationDate                                                                                    AS creationDate,
                  p.sortingPriorityId                                                                               AS productPriority,
                  s.id                                                                                              AS shopId,
                  s.name                                                                                            AS row_shop,
                  concat(phs.shootingId)                                                             AS shooting,
                  concat(doc.number)                                                             AS doc_number,
                  (SELECT count(*)
                   FROM ShopHasProduct
                   WHERE (ShopHasProduct.productId, ShopHasProduct.productVariantId) = (p.id, p.productVariantId))      AS shops,
                  if(((SELECT count(0)
                       FROM ProductSheetActual
                       WHERE ((ProductSheetActual.productId = p.id) AND
                              (ProductSheetActual.productVariantId = p.productVariantId))) > 2), 'sì', 'no')    AS hasDetails,
                  if((isnull(p.dummyPicture) OR (p.dummyPicture = 'bs-dummy-16-9.png')), 'no', 'sì')            AS dummy,
                  if((p.id, p.productVariantId) IN (SELECT
                                                              ProductHasProductPhoto.productId,
                                                              ProductHasProductPhoto.productVariantId
                                                            FROM ProductHasProductPhoto), 'sì', 'no')                 AS hasPhotos,
                  pc.id                                                                                             AS categoryId,
                  pcg.name                                                                                          AS colorGroup,
                  p.isOnSale                                                                                        AS isOnSale,
                  psiz.name                                                                                             AS stock,
                  ifnull(p.processing, '-')                                                                         AS processing,
                  #(((if((p.isOnSale = 0), psk.price, psk.salePrice) / 1.22) - (psk.value + ((psk.value * if(
                   #   (pse.isActive = 0), s.pastSeasonMultiplier,
                   #   if((p.isOnSale = 1), s.saleMultiplier, s.currentSeasonMultiplier))) / 100))) /
                   #(if((p.isOnSale = 0), psk.price, psk.salePrice) / 1.22)) * 100                           AS mup,
                  p.qty                                                                                             AS hasQty,
                  (SELECT group_concat(DISTINCT t.name)
                   FROM ProductHasTag pht
                     JOIN TagTranslation t ON pht.tagId = t.tagId
                   WHERE langId = 1 AND pht.productId = p.id AND pht.productVariantId = p.productVariantId)   AS tags,
                  (SELECT min(if(ProductSku.stockQty > 0, if(p.isOnSale = 0, ProductSku.price, ProductSku.salePrice), NULL))
                   FROM ProductSku
                   WHERE ProductSku.productId = p.id AND ProductSku.productVariantId = p.productVariantId)              AS activePrice,
                   (SELECT ifnull(group_concat(distinct ma.name), '')
                   FROM Marketplace m
                     JOIN MarketplaceAccount ma ON m.id = ma.marketplaceId
                     JOIN MarketplaceAccountHasProduct mahp ON (ma.id,ma.marketplaceId) = (mahp.marketplaceAccountId,mahp.marketplaceId)
                   WHERE mahp.productId = p.id AND
                         mahp.productVariantId = p.productVariantId AND mahp.isDeleted != 1)                            AS marketplaces,
                         
                         
                if(isnull(prHp.productId), 'no', 'si') inPrestashop,
                CONCAT('https://cdn.iwes.it/',pb.slug,'/',`pp`.`name`) AS photograph
                FROM Product p
                  JOIN ProductSeason pse ON p.productSeasonId = pse.id
                  JOIN ProductVariant pv ON p.productVariantId = pv.id
                  JOIN ProductBrand pb ON p.productBrandId = pb.id
                  JOIN ProductStatus ps ON ps.id = p.productStatusId
                    JOIN ProductHasProductPhoto php ON p.id=php.productId AND p.productVariantId=php.productVariantId
                  join ProductPhoto pp ON php.productPhotoId=pp.id AND `pp`.`order`=1
                 
                  JOIN ShopHasProduct sp
                    ON (p.id, p.productVariantId) = (sp.productId, sp.productVariantId)
                  JOIN Shop s ON s.id = sp.shopId
                   JOIN (ProductSizeGroup psg
                              JOIN ProductSizeMacroGroup psmg ON psg.productSizeMacroGroupId = psmg.id)
                            ON p.productSizeGroupId = psg.id
                   JOIN (ProductSku psk
                    JOIN ProductSize psiz ON psk.productSizeId = psiz.id)
                    ON (p.id, p.productVariantId) = (psk.productId, psk.productVariantId)
                   JOIN (ProductHasProductCategory ppc
                              JOIN ProductCategory pc ON ppc.productCategoryId = pc.id
                    ) ON (p.id, p.productVariantId) = (ppc.productId,ppc.productVariantId)
                   JOIN ProductColorGroup pcg ON p.productColorGroupId = pcg.id
                   JOIN (DirtyProduct dp
                              JOIN DirtySku ds ON dp.id = ds.dirtyProductId)
                    ON (sp.productId,sp.productVariantId,sp.shopId) = (dp.productId,dp.productVariantId,dp.shopId)
                    
                    WHERE 1=1 and   pse.isActive=1
                                 AND p.externalId IN ('0020600',
'0020663',
'210000267277',
'0020807',
'0021016',
'0021016',
'210000317904',
'0020944',
'0020619',
'0020656',
'0020053',
'0020053',
'0020656',
'0020055',
'0020055',
'0020055',
'0020209',
'0020208',
'0020044',
'0020044',
'0020047',
'0020047',
'0020590',
'0020590',
'0019990',
'0019990',
'0019990',
'0019970',
'0019970',
'0019970',
'0020839',
'0020839',
'0020181',
'0020840',
'0020840',
'0020840',
'0020168',
'0020208',
'0020578',
'0020578',
'0019969',
'0019969',
'0019968',
'0019973',
'0019707',
'0019708',
'0020923',
'0020941',
'0020037',
'0020048',
'0020947',
'0020175',
'0020173',
'0020601',
'0020595',
'0021109',
'0021018',
'0020632',
'0020229',
'0020228',
'0020919',
'0020793',
'0020793',
'0020670',
'0020750',
'0020754',
'0021017',
'0020893',
'0021016',
'0020759',
'0020810',
'0021015',
'0020950',
'0020558',
'0020763',
'0020556',
'0020546',
'0020612',
'0018191',
'0019984',
'0020402',
'0019972',
'0019979',
'0019979',
'0019981',
'0020696',
'0020659',
'0020764',
'0021024',
'0020946',
'0020049',
'0020048',
'0020054',
'0020052',
'0020819',
'0020822',
'0020822',
'0020589',
'0020586',
'0019991',
'0020038',
'0021110',
'0021108',
'0021110',
'0020979',
'0020979',
'0020971',
'0020220',
'0020891',
'0020891',
'0020698',
'0020698',
'0019992',
'0019983',
'0020978',
'0020918',
'0020918',
'0019971',
'0019331',
'0020605',
'0019989',
'0020807',
'0020938',
'0020599',
'0020995',
'0020995',
'0020177',
'0020755',
'0020616',
'0019989',
'0019974',
'0020171',
'0019968',
'0020221',
'0020791',
'0020825',
'0020795',
'0020796',
'0020796',
'0020797',
'0020802',
'0020795',
'0020215',
'0020801',
'0020588',
'0020215',
'0020792',
'0020792',
'0020792',
'0020410',
'0020405',
'0020409',
'0020940',
'0020895',
'0020657',
'0020658',
'0020917',
'0020917',
'0020917',
'0020685',
'0020894',
'0020945',
'0020598',
'0020751',
'0020662',
'0020669',
'0020657',
'0020755',
'0020748',
'0020772',
'0019865',
'0020407',
'0020411',
'0020605',
'0020612',
'0020689',
'0020694',
'0021019',
'0020591',
'0020041',
'0020227',
'0020227',
'0020002',
'0020004',
'0020003',
'0020920',
'0021107',
'',
'0019602',
'0020599',
'0020598',
'0020607',
'',
'0020689',
'0020695',
'0020692',
'0020798',
'0020693',
'0020688',
'0020687',
'0020943',
'0020938',
'0020561',
'0020559',
'0020555',
'0020822',
'0020668',
'0020548',
'0020542',
'0020597',
'0020952',
'0020442',
'0020441',
'0020450',
'0020440',
'0020457',
'0020473',
'0020463',
'0020472',
'0020446',
'0020446',
'0020472',
'0020458',
'0020453',
'0020463',
'0020463',
'0020462',
'0020461',
'0020445',
'0020436',
'0020466',
'0020466',
'0020445',
'0020464',
'0020464',
'0020454',
'0020441',
'0020444',
'0020444',
'0020444',
'0020463',
'0020463',
'0020470',
'0020435',
'0020434',
'0020464',
'0020464',
'0020464',
'0020464',
'0020463',
'0020439',
'0020438',
'0020469',
'0020464',
'0020450',
'0020441',
'0020442',
'0020449',
'0020449',
'0020465',
'0020468',
'0020463',
'0020472',
'0020450',
'0020471',
'0020466',
'0020469',
'0020435',
'0020437',
'0020469',
'0020469',
'0020472',
'0020455',
'0020454',
'0020450',
'0020450',
'0020450',
'0020472',
'0020470',
'0020470',
'0020469'
)";

            $shootingCritical = \Monkey::app()->router->request()->getRequestData('shootingCritical');
            if ($shootingCritical) $sql .= " AND `p`.`dummyPicture` not like '%dummy%' AND `p`.`productStatusId` in (4,5,11)";
            $productDetailCritical = \Monkey::app()->router->request()->getRequestData('detailsCritical');
            if ($productDetailCritical) $sql .= " AND `p`.`dummyPicture` not like '%dummy%' AND `p`.`productStatusId` in (4,5,11) HAVING `hasDetails` = 'no'";


            $datatable = new CDataTables($sql, ['id', 'productVariantId'], $_GET, true);
            $shopIds = \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
            $datatable->addCondition('shopId', $shopIds);

            $em = $this->app->entityManagerFactory->create('ProductStatus');
            $productStatuses = $em->findAll('limit 99', '');

            $statuses = [];
            foreach ($productStatuses as $status) {
                $statuses[$status->code] = $status->name;
            }

            $modifica = $this->app->baseUrl(false) . "/blueseal/prodotti/modifica";
            $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');
            $productRepo = \Monkey::app()->repoFactory->create('Product');

            /** @var CDocumentRepo $docRepo */
            $docRepo = \Monkey::app()->repoFactory->create('Document');
            $datatable->doAllTheThings();

            foreach ($datatable->getResponseSetData() as $key => $row) {
                /** @var $val CProduct */
                $val = $productRepo->findOneBy($row);

                $row["DT_RowId"] = $val->printId();
                $row["DT_RowClass"] = $val->productStatus->isVisible == 1 ? 'verde' : (
                $val->productStatus->isReady == 1 ? 'arancione' : ""
                );

                $row['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $val->id . '&productVariantId=' . $val->productVariantId . '">' . $val->id . '-' . $val->productVariantId . '</a>' : $val->id . '-' . $val->productVariantId;
                $row['dummy'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $val->getDummyPictureUrl() . '" /></a>';
                $row['productSizeGroup'] = ($val->productSizeGroup) ? '<span class="small">' . $val->productSizeGroup->locale . '-' . explode("-", $val->productSizeGroup->productSizeMacroGroup->name)[0] . '</span>' : '';

                $row['details'] = "";
                foreach ($val->productSheetActual as $k => $v) {
                    if (!is_null($v->productDetail) && !$v->productDetail->productDetailTranslation->isEmpty()) {
                        $row['details'] .= '<span class="small">' . $v->productDetail->productDetailTranslation->getFirst()->name . "</span><br />";
                    }
                }

                $photo = $val->productPhoto->findOneBy(['size' => '281', 'order' => 1]);
                $row['photograph'] = '<img src="https://cdn.iwes.it/' . $val->productBrand->slug . '/' . $photo->name . '"/>' >
                    $row['hasDetails'] = (2 < $val->productSheetActual->count()) ? 'sì' : 'no';
                $row['season'] = '<span class="small">' . $val->productSeason->name . " " . $val->productSeason->year . '</span>';

                $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="' . $val->printId() . '"></table>';
                $row['externalId'] = '<span class="small">' . $val->getShopExtenalIds('<br />') . '</span>';

                $row['cpf'] = $val->printCpf();

                $row['colorGroup'] = '<span class="small">' . (!is_null($val->productColorGroup) ? $val->productColorGroup->productColorGroupTranslation->getFirst()->name : "[Non assegnato]") . '</span>';
                $row['brand'] = isset($val->productBrand) ? $val->productBrand->name : "";
                $row['categoryId'] = '<span class="small">' . $val->getLocalizedProductCategories('<br>', '/') . '</span>';
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

                //$row['marketplaces'] = $val->getMarketplaceAccountsName(' - ','<br>',true);
                $row['marketplaces'] = "";
                $row["row_shop"] = $val->getShops('|', true);
                $row['shop'] = '<span class="small">' . $val->getShops('<br />', true) . '</span>';
                $row['shops'] = $val->shopHasProduct->count();


                //$row['mup'] = '<span class="small">';
                //$row['mup'] .= implode('<br />', $mup);
                //$row['mup'] .= '</span>';

                $row['friendPrices'] = [];
                $row['friendValues'] = [];
                $row['friendSalePrices'] = [];
                foreach ($val->shopHasProduct as $shp) {
                    $row['friendPrices'][] = $shp->price;
                    $row['friendValues'][] = $shp->value;
                    $row['friendSalePrices'][] = $shp->salePrice;
                }

                $row['friendPrices'] = implode('<br />', $row['friendPrices']);
                $row['friendValues'] = implode('<br />', $row['friendValues']);
                $row['friendSalePrices'] = implode('<br />', $row['friendSalePrices']);

                $row['colorNameManufacturer'] = $val->productVariant->description;

                $row['isOnSale'] = $val->isOnSale();
                $row['creationDate'] = (new \DateTime($val->creationDate))->format('d-m-Y H:i');
                $row['processing'] = ($val->processing) ? $val->processing : '-';


                $datatable->setResponseDataSetRow($key, $row);
            }
            return $datatable->responseOut();

        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CProductPackingListAjaxController','error','Lista Packing',$e->getLine().'-'.$e->getMessage(),$e->getTraceAsString());
        }

    }
}
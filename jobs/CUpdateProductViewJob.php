<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CProductView;
use PDO;
use bamboo\domain\entities\CShooting;
use bamboo\domain\repositories\CDocumentRepo;


/**
 * Class CAmazonAddProductJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 11/05/2020
 * @since 1.0
 */
class CUpdateProductViewJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->updateProductView();
        \Monkey::app()->vendorLibraries->load('amazonMWS');
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    private function updateProductView()
    {
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
                  p.
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
                  p.lastUpdate as lastUpdate,  
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
                         
                if(isnull(prHp.productId), 'no', 'si') inPrestashop
                FROM Product p
                  JOIN ProductSeason pse ON p.productSeasonId = pse.id
                  JOIN ProductVariant pv ON p.productVariantId = pv.id
                  JOIN ProductBrand pb ON p.productBrandId = pb.id
                  JOIN ProductStatus ps ON ps.id = p.productStatusId
                  LEFT JOIN PrestashopHasProduct prHp ON p.id = prHp.productId AND p.productVariantId = prHp.productVariantId
                  JOIN ShopHasProduct sp
                    ON (p.id, p.productVariantId) = (sp.productId, sp.productVariantId)
                  JOIN Shop s ON s.id = sp.shopId
                  LEFT JOIN (ProductSizeGroup psg
                              JOIN ProductSizeMacroGroup psmg ON psg.productSizeMacroGroupId = psmg.id)
                            ON p.productSizeGroupId = psg.id
                  LEFT JOIN (ProductSku psk
                    JOIN ProductSize psiz ON psk.productSizeId = psiz.id)
                    ON (p.id, p.productVariantId) = (psk.productId, psk.productVariantId)
                  LEFT JOIN (ProductHasProductCategory ppc
                              JOIN ProductCategory pc ON ppc.productCategoryId = pc.id
                    ) ON (p.id, p.productVariantId) = (ppc.productId,ppc.productVariantId)
                  LEFT JOIN ProductColorGroup pcg ON p.productColorGroupId = pcg.id
                  LEFT JOIN (DirtyProduct dp
                              JOIN DirtySku ds ON dp.id = ds.dirtyProductId)
                    ON (sp.productId,sp.productVariantId,sp.shopId) = (dp.productId,dp.productVariantId,dp.shopId)
                    LEFT JOIN (
                    ProductHasShooting phs 
                      JOIN Shooting shoot ON phs.shootingId = shoot.id
                        LEFT JOIN Document doc ON shoot.friendDdt = doc.id) 
                                ON p.productVariantId = phs.productVariantId AND p.id = phs.productId ";
        $em = $this->app->entityManagerFactory->create('ProductStatus');
        $productStatuses = $em->findAll('limit 99','');

        $statuses = [];
        foreach ($productStatuses as $status) {
            $statuses[$status->code] = $status->name;
        }

        $row = [];
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $productViewRepo = \Monkey::app()->repoFactory->create('ProductView');
        $productSheetActualRepo = \Monkey::app()->repoFactory->create('ProductSheetActual');
        $productDetailTranslationRepo = \Monkey::app()->repoFactory->create('ProductDetailTranslation');
        $productNameTranslationRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');
        $res = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
        foreach ($res as $result) {
            $findProductView = $productViewRepo->findOneBy(['productId' => $result['id'],'productVariantId' => $result['productVariantId']]);
            if ($findProductView == null) {
                $productViewInsert->productViewRepo - getEmptyEntity();
                /** @var $val CProduct */
                $val = $productRepo->findOneBy(['id' => $result['id'],'productVariantId' => $result['productVariantId']]);

                $productViewInsert->productId = $val->id;
                $productViewInsert->productVariantId = $val->productVariantId;

                $productViewInsert->dummy = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $val->getDummyPictureUrl() . '" /></a>';
                $productViewInsert->productSizeGroup = ($val->productSizeGroup) ? '<span class="small">' . $val->productSizeGroup->locale . '-' . explode("-",$val->productSizeGroup->productSizeMacroGroup->name)[0] . '</span>' : '';

                $details = "";
                foreach ($val->productSheetActual as $k => $v) {
                    if (!is_null($v->productDetail) && !$v->productDetail->productDetailTranslation->isEmpty()) {
                        $details .= '<span class="small">' . $v->productDetail->productDetailTranslation->getFirst()->name . "</span><br />";
                    }
                }
                $productViewInsert->details = $details;

                $productViewInsert->hasPhotos = ($val->productPhoto->count()) ? 'sì' : 'no';
                $productViewInsert->hasDetails = (2 < $val->productSheetActual->count()) ? 'sì' : 'no';
                $productViewInsert->season = '<span class="small">' . $val->productSeason->name . " " . $val->productSeason->year . '</span>';

                $productViewInsert->stock = '<table class="nested-table inner-size-table" data-product-id="' . $val->printId() . '"></table>';
                $productViewInsert->externalId = '<span class="small">' . $val->getShopExtenalIds('<br />') . '</span>';

                $productViewInsert->cpf = $val->printCpf();

                $productViewInsert->colorGroup = '<span class="small">' . (!is_null($val->productColorGroup) ? $val->productColorGroup->productColorGroupTranslation->getFirst()->name : "[Non assegnato]") . '</span>';
                $productViewInsert->brand = isset($val->productBrand) ? $val->productBrand->name : "";
                $productViewInsert->categoryId = '<span class="small">' . $val->getLocalizedProductCategories('<br>','/') . '</span>';
                $productViewInsert->description = '<span class="small">' . ($val->productDescriptionTranslation->getFirst() ? $val->productDescriptionTranslation->getFirst()->description : "") . '</span>';

                $productViewInsert->productName = $val->productNameTranslation->getFirst() ? $val->productNameTranslation->getFirst()->name : "";
                $productViewInsert->tags = '<span class="small">' . $val->getLocalizedTags('<br>',false) . '</span>';
                $productViewInsert->status = $val->productStatus->name;
                $productViewInsert->productPriority = $val->sortingPriorityId;
                $productViewInsert->lastUpdate = $val->lastUpdate;

                $qty = 0;
                $shopz = [];
                $mup = [];
                $isOnSale = $val->isOnSale();
                foreach ($val->productSku as $sku) {
                    $qty += $sku->stockQty;
                    $iShop = $sku->shop->name;
                    if (!in_array($iShop,$shopz)) {
                        $shopz[] = $iShop;

                        $price = $isOnSale ? $sku->salePrice : $sku->price;

                        if ((float)$price) {
                            $multiplier = ($val->productSeason->isActive) ? (($isOnSale) ? $sku->shop->saleMultiplier : $sku->shop->currentSeasonMultiplier) : $sku->shop->pastSeasonMultiplier;
                            $value = $sku->value;
                            $friendRevenue = $value + $value * $multiplier / 100;
                            $priceNoVat = $price / 1.22;
                            $mup[] = number_format(($priceNoVat - $friendRevenue) / $priceNoVat * 100,2,",",".");
                        } else {
                            $mup[] = '-';
                        }
                    }
                }
                $productViewInsert->hasQty = $qty;
                $productViewInsert->activePrice = $val->getDisplayActivePrice() ? $val->getDisplayActivePrice() : 'Non Assegnato';

                //$row['marketplaces'] = $val->getMarketplaceAccountsName(' - ','<br>',true);
                $productViewInsert->marketplaces = "";
                $productViewInsert->shop = '<span class="small">' . $val->getShops('<br />',true) . '</span>';
                $productViewInsert->shops = $val->shopHasProduct->count();


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

                $productViewInsert->friendPrices = implode('<br />',$row['friendPrices']);
                $productViewInsert->friendValues = implode('<br />',$row['friendValues']);
                $productViewInsert->friendSalePrices = implode('<br />',$row['friendSalePrices']);

                $productViewInsert->colorNameManufacturer = $val->productVariant->description;

                $productViewInsert->isOnSale = $val->isOnSale();
                $productViewInsert->creationDate = (new \DateTime($val->creationDate))->format('d-m-Y H:i');
                $productViewInsert->processing = ($val->processing) ? $val->processing : '-';

                $sids = "";
                $ddtNumbers = "";
                /** @var CShooting $singleShooting */
                foreach ($val->shooting as $singleShooting) {
                    $sids .= '<br />' . $singleShooting->id;
                    $ddtNumbers .= '<br />' . $docRepo->findShootingFriendDdt($singleShooting);
                }
                $productViewInsert->shooting = $sids;
                $productViewInsert->doc_number = $ddtNumbers;
                $productViewInsert->inPrestashop = is_null($val->prestashopHasProduct) ? 'no' : 'si';
                $productViewInsert->insert();
            } else {
                // $lastUpdate=(new \DateTime($result[$lastUpdate]))->format('d-m-Y H:i');


                $lastUpdate = strtotime($findProductView->lastUpdate);
                $currentUpdate = strtotime($result['lastUpdate']);
                if ($currentUpdate > $lastUpdate) {
                    /** @var $val CProduct */
                    $val = $productRepo->findOneBy(['id' => $result['id'],'productVariantId' => $result['productVariantId']]);

                    $productView->dummy = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $val->getDummyPictureUrl() . '" /></a>';
                    $productView->productSizeGroup = ($val->productSizeGroup) ? '<span class="small">' . $val->productSizeGroup->locale . '-' . explode("-",$val->productSizeGroup->productSizeMacroGroup->name)[0] . '</span>' : '';

                    $details = "";
                    foreach ($val->productSheetActual as $k => $v) {
                        if (!is_null($v->productDetail) && !$v->productDetail->productDetailTranslation->isEmpty()) {
                            $details .= '<span class="small">' . $v->productDetail->productDetailTranslation->getFirst()->name . "</span><br />";
                        }
                    }
                    $productView->details = $details;

                    $productView->hasPhotos = ($val->productPhoto->count()) ? 'sì' : 'no';
                    $productView->hasDetails = (2 < $val->productSheetActual->count()) ? 'sì' : 'no';
                    $productView->season = '<span class="small">' . $val->productSeason->name . " " . $val->productSeason->year . '</span>';

                    $productView->stock = '<table class="nested-table inner-size-table" data-product-id="' . $val->printId() . '"></table>';
                    $productView->externalId = '<span class="small">' . $val->getShopExtenalIds('<br />') . '</span>';

                    $productView->cpf = $val->printCpf();

                    $productView->colorGroup = '<span class="small">' . (!is_null($val->productColorGroup) ? $val->productColorGroup->productColorGroupTranslation->getFirst()->name : "[Non assegnato]") . '</span>';
                    $productView->brand = isset($val->productBrand) ? $val->productBrand->name : "";
                    $productView->categoryId = '<span class="small">' . $val->getLocalizedProductCategories('<br>','/') . '</span>';
                    $productView->description = '<span class="small">' . ($val->productDescriptionTranslation->getFirst() ? $val->productDescriptionTranslation->getFirst()->description : "") . '</span>';

                    $productView->productName = $val->productNameTranslation->getFirst() ? $val->productNameTranslation->getFirst()->name : "";
                    $productView->tags = '<span class="small">' . $val->getLocalizedTags('<br>',false) . '</span>';
                    $productView->status = $val->productStatus->name;
                    $productView->productPriority = $val->sortingPriorityId;
                    $productView->lastUpdate = $val->lastUpdate;

                    $qty = 0;
                    $shopz = [];
                    $mup = [];
                    $isOnSale = $val->isOnSale();
                    foreach ($val->productSku as $sku) {
                        $qty += $sku->stockQty;
                        $iShop = $sku->shop->name;
                        if (!in_array($iShop,$shopz)) {
                            $shopz[] = $iShop;

                            $price = $isOnSale ? $sku->salePrice : $sku->price;

                            if ((float)$price) {
                                $multiplier = ($val->productSeason->isActive) ? (($isOnSale) ? $sku->shop->saleMultiplier : $sku->shop->currentSeasonMultiplier) : $sku->shop->pastSeasonMultiplier;
                                $value = $sku->value;
                                $friendRevenue = $value + $value * $multiplier / 100;
                                $priceNoVat = $price / 1.22;
                                $mup[] = number_format(($priceNoVat - $friendRevenue) / $priceNoVat * 100,2,",",".");
                            } else {
                                $mup[] = '-';
                            }
                        }
                    }
                    $productView->hasQty = $qty;
                    $productView->activePrice = $val->getDisplayActivePrice() ? $val->getDisplayActivePrice() : 'Non Assegnato';

                    //$row['marketplaces'] = $val->getMarketplaceAccountsName(' - ','<br>',true);
                    $productView->marketplaces = "";
                    $productView->shop = '<span class="small">' . $val->getShops('<br />',true) . '</span>';
                    $productView->shops = $val->shopHasProduct->count();


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

                    $productView->friendPrices = implode('<br />',$row['friendPrices']);
                    $productView->friendValues = implode('<br />',$row['friendValues']);
                    $productView->friendSalePrices = implode('<br />',$row['friendSalePrices']);

                    $productView->colorNameManufacturer = $val->productVariant->description;

                    $productView->isOnSale = $val->isOnSale();
                    $productView->creationDate = (new \DateTime($val->creationDate))->format('d-m-Y H:i');
                    $productView->processing = ($val->processing) ? $val->processing : '-';

                    $sids = "";
                    $ddtNumbers = "";
                    /** @var CShooting $singleShooting */
                    foreach ($val->shooting as $singleShooting) {
                        $sids .= '<br />' . $singleShooting->id;
                        $ddtNumbers .= '<br />' . $docRepo->findShootingFriendDdt($singleShooting);
                    }
                    $productView->shooting = $sids;
                    $productView->doc_number = $ddtNumbers;
                    $productView->inPrestashop = is_null($val->prestashopHasProduct) ? 'no' : 'si';
                    $productView->update();

                }

            }


        }
    }
}



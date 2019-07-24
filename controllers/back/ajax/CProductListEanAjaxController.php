<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductEan;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CShooting;
use bamboo\domain\repositories\CDocumentRepo;

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
class CProductListEanAjaxController extends AAjaxController
{
    public function get()
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
                  s.id                                                                                              AS shopId,
                  s.name                                                                                            AS row_shop,
                  
                
                  (SELECT count(*)
                   FROM ShopHasProduct
                   WHERE (ShopHasProduct.productId, ShopHasProduct.productVariantId) = (p.id, p.productVariantId))      AS shops,
                 
                  if((isnull(p.dummyPicture) OR (p.dummyPicture = 'bs-dummy-16-9.png')), 'no', 'sì')            AS dummy,
                 
                 
                  pcg.name                                                                                          AS colorGroup,
                  p.isOnSale                                                                                        AS isOnSale,
                  psiz.name                                                                                             AS stock,
                  ifnull(p.processing, '-')                                                                         AS processing,
                  #(((if((p.isOnSale = 0), psk.price, psk.salePrice) / 1.22) - (psk.value + ((psk.value * if(
                   #   (pse.isActive = 0), s.pastSeasonMultiplier,
                   #   if((p.isOnSale = 1), s.saleMultiplier, s.currentSeasonMultiplier))) / 100))) /
                   #(if((p.isOnSale = 0), psk.price, psk.salePrice) / 1.22)) * 100                           AS mup,
                  p.qty                                                                                             AS hasQty,
                 
                  (SELECT min(if(ProductSku.stockQty > 0, if(p.isOnSale = 0, ProductSku.price, ProductSku.salePrice), NULL))
                   FROM ProductSku
                   WHERE ProductSku.productId = p.id AND ProductSku.productVariantId = p.productVariantId)              AS activePrice,
                 (SELECT ifnull(group_concat(concat(m.name, ' - ', ma.name)), '')
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
where p.productStatusId=6";

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
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $eanRepo=\Monkey::app()->repoFactory->create('ProductEan');

        /** @var CDocumentRepo $docRepo */
        $docRepo = \Monkey::app()->repoFactory->create('Document');
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
            $row['productSizeGroup'] = ($val->productSizeGroup) ? '<span class="small">' . $val->productSizeGroup->locale . '-' . explode("-", $val->productSizeGroup->productSizeMacroGroup->name)[0] . '</span>' : '';





            $row['season'] = '<span class="small">' . $val->productSeason->name . " " . $val->productSeason->year . '</span>';
            $row['marketplaces'] = $val->getMarketplaceAccountsName(' - ','<br>',true);
            $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="'.$val->printId().'"></table>';
            $row['externalId'] = '<span class="small">'.$val->getShopExtenalIds('<br />').'</span>';

            $row['cpf'] = $val->printCpf();

            $row['colorGroup'] = '<span class="small">' . (!is_null($val->productColorGroup) ? $val->productColorGroup->productColorGroupTranslation->getFirst()->name : "[Non assegnato]") . '</span>';
            $row['brand'] = isset($val->productBrand) ? $val->productBrand->name : "";



            $row['productName'] = $val->productNameTranslation->getFirst() ? $val->productNameTranslation->getFirst()->name : "";

            $row['status'] = $val->productStatus->name;


            $qty = 0;
            $shopz = [];
            $mup = [];
            $isOnSale = $val->isOnSale();

            $barcode="";
            $ean="";
            foreach ($val->productSku as $sku) {
                $qty += $sku->stockQty;
                $iShop = $sku->shop->name;
                $barcode.=$sku->productSize->name."|".$sku->barcode."<br>";
                $ean =  $sku->productSize->name . "|" . $sku->ean . "<br>";



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

            $row['shop'] = '<span class="small">'.$val->getShops('<br />', true).'</span>';
           /* $row['barcode']=$barcode;
            $row['ean']=$ean;*/




            //$row['mup'] = '<span class="small">';
            //$row['mup'] .= implode('<br />', $mup);
            //$row['mup'] .= '</span>';



            $row['friendSalePrices'] = [];
            foreach ($val->shopHasProduct as $shp) {


                $row['friendSalePrices'][] = $shp->salePrice;
            }


            $row['friendSalePrices'] = implode('<br />',$row['friendSalePrices']);

            $row['colorNameManufacturer'] = $val->productVariant->description;

            $row['isOnSale'] = $val->isOnSale();
            $row['creationDate'] = (new \DateTime($val->creationDate))->format('d-m-Y H:i');
            $row['processing'] = ($val->processing) ? $val->processing : '-';
            $row['barcode']=$barcode;
            $row['ean']=$ean;


            $row["inPrestashop"] = is_null($val->prestashopHasProduct) ? 'no' : 'si';

            $datatable->setResponseDataSetRow($key,$row);
        }
        return $datatable->responseOut();
    }
}
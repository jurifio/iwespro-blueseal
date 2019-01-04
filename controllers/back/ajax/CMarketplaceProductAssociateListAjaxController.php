<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProduct;

/**
 * Class CMarketplaceProductListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/09/2018
 * @since 1.0
 */
class CMarketplaceProductAssociateListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT p.id AS id,
       concat(`pps`.`productId`,'-',`pps`.`productVariantId`) AS `code`,
       s.name AS  shop,
       pb.name AS brand,
       phpc.productCategoryId AS category,
       `p`.`itemno` AS `itemno`,
       '' AS stock,
       pss.name   AS season,
       '' AS dummy,
       pps.productId AS productId,
       pps.productVariantId AS productVariantId,
       shp.shopId AS shopId,
       '' AS statusPublished,


       p.creationDate AS creationDate,
       '' AS associatePrestashopMarketPlace
       



FROM ProductPublicSku pps
  JOIN ProductSku psk ON pps.productId=psk.productId AND pps.productVariantId=psk.productVariantId

  JOIN ShopHasProduct shp ON pps.productId=shp.productId AND pps.productVariantId =shp.productVariantId
  JOIN Shop s ON s.id =shp.shopId
  JOIN Product p ON pps.productId=p.id AND pps.productVariantId=p.productVariantId
  JOIN ProductSeason pss ON pss.id =p.productSeasonId
  JOIN ProductHasProductPhoto PHPP ON pps.productId = PHPP.productId AND pps.productVariantId = PHPP.productVariantId
  JOIN ProductBrand pb ON p.productBrandId =pb.id
  JOIN `ProductHasProductCategory` `phpc` ON  pps.`productId` = `phpc`.`productId` AND `pps`.`productVariantId` = `phpc`.`productVariantId`
 

  JOIN `ProductStatus` `ps` ON((`p`.`productStatusId` = `ps`.`id`))

WHERE (`p`.`qty` > 0) AND (p.productStatusId=6)
GROUP BY productId, productVariantId";

        $sql = " SELECT p.id AS id,
       concat(`pps`.`productId`,'-',`pps`.`productVariantId`) AS `code`,
       s.name AS  shop,
       pb.name AS brand,
       phpc.productCategoryId AS category,
       `p`.`itemno` AS `itemno`,
       '' AS stock,
       pss.name   AS season,
       '' AS dummy,
       pps.productId AS productId,
       pps.productVariantId AS productVariantId,
       shp.shopId AS shopId,
       mhpa.statusPublished AS statusPublished,
       mhpa.typeRetouchPrice AS TypeRetouchPrice,
       mhpa.marketPlaceHasShopId AS marketplaceHasShopId,
       mhpa.price AS priceMarketplace,
       mhpa.id AS prestashopProductId,
       mhpa.isOnSale AS isOnSale,
       mhpa.priceSale AS priceSale,
       mhpa.typeSale AS typeSale,
       mhpa.percentSale AS percentSale,
       mhpa.titleTextSale AS titleTextSale,


       p.creationDate AS creationDate,
       '' AS associatePrestashopMarketPlace,
       mhpa.marketplaceProductId AS marketplaceProductId
       



FROM ProductPublicSku pps
  JOIN ProductSku psk ON pps.productId=psk.productId AND pps.productVariantId=psk.productVariantId
  JOIN MarketplaceHasProductAssociate mhpa ON pps.productId= mhpa.productId AND pps.productVariantId=pps.productVariantId

  JOIN ShopHasProduct shp ON pps.productId=shp.productId AND pps.productVariantId =shp.productVariantId
  JOIN Shop s ON s.id =shp.shopId
  JOIN Product p ON pps.productId=p.id AND pps.productVariantId=p.productVariantId
  JOIN ProductSeason pss ON pss.id =p.productSeasonId
  JOIN ProductHasProductPhoto PHPP ON pps.productId = PHPP.productId AND pps.productVariantId = PHPP.productVariantId
  JOIN ProductBrand pb ON p.productBrandId =pb.id
  JOIN `ProductHasProductCategory` `phpc` ON  pps.`productId` = `phpc`.`productId` AND `pps`.`productVariantId` = `phpc`.`productVariantId`
 

  JOIN `ProductStatus` `ps` ON((`p`.`productStatusId` = `ps`.`id`))

WHERE (`p`.`qty` > 0) AND (p.productStatusId=6)  GROUP BY productId, productVariantId";

        /** inizio query**/
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings('true');
        foreach ($datatable->getResponseSetData() as $key => $row) {
            $resproductFindEan = \Monkey::app()->repoFactory->create('ProductEan')->findOneBy(['productId' => $row['productId'], 'productVariantId' => $row['productVariantId'], 'productSizeId' => '0', 'used' => '1', 'usedForParent' => '1']);
            if ($resproductFindEan != null) {
                $row['ean'] = $resproductFindEan->ean;

            } else {
                $row['ean'] = 'Ean non presente';
            }
            $product = \Monkey::app()->repoFactory->create('Product')->findOne([$row['productId'], $row['productVariantId']]);
            $row['code'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="/blueseal/prodotti/modifica?id=' . $product->id . '&productVariantId=' . $product->productVariantId . '">' . $product->id . '-' . $product->productVariantId . '</a>';


            $row["DT_RowId"] = $product->printId();
            if ($product->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
            else $imgs = "";
            $productcategory = $row['category'];
            $row['category'] = $product->getLocalizedProductCategories('<br>');
            $row['dummy'] = '<img width="50" src="' . $product->getDummyPictureUrl() . '" />' . $imgs . '<br />';
            $res = $this->app->dbAdapter->query("SELECT s.name, sum(ps.stockQty) stock
                                          FROM ProductSku ps , ProductSize s
                                          WHERE ps.productSizeId = s.id AND
                                              ps.productId = ? AND
                                              ps.productVariantId = ?
                                          GROUP BY ps.productSizeId
                                          HAVING stock > 0 ORDER BY `name`", [$product->id, $product->productVariantId])->fetchAll();
            $th = "";
            $tr = "";
            foreach ($res as $sums) {
                $th .= "<th>" . $sums['name'] . "</th>";
                $tr .= "<td>" . $sums['stock'] . "</td>";
            }
            $row["stock"] = '<table class="nested-table"><thead><tr>' . $th . "</tr></thead><tbody>" . $tr . "</tbody></table>";

            $row['creationDate'] = $product->creationDate;
            $rowtablemarketplace = "";
            $resmarketplaceHasProductAssociate = \Monkey::app()->repoFactory->create('MarketplaceHasProductAssociate')->findOneBy(['productId' => $product->id, 'productVariantId' => $product->productVariantId]);
            if (null == $resmarketplaceHasProductAssociate) {
                $row['associatePrestashopMarketPlace'] = 'non associato';
                $row['typePrice'] = 'non applicato';
                $row['price'] = 'non calcolato';
                $row['statusPublished'] = 'non lavorato';

            } else {
                $resmarketplacearray = $this->app->dbAdapter->query("SELECT m.name AS name,s.name AS nameShop, mphpa.typeRetouchPrice AS typeRetouchPrice, mphpa.amount AS amount,mphpa.price AS price, 
                             mphpa.priceMarketplace AS priceMarketplace,
                             mphpa.percentSale AS percentSale,
                             mphpa.marketplaceProductId AS marketplaceProductId
                            ,mphpa.isOnSale AS isOnSale, mphpa.typeSale AS typeSale, mphpa.priceSale AS priceSale, mphs.imgMarketPlace AS icon, mphpa.statusPublished AS statusPublished
                                          FROM Marketplace m JOIN MarketplaceHasProductAssociate mphpa
                                          ON mphpa.marketplaceId =m.id
                                           JOIN Shop s ON mphpa.shopId=s.id
                                           JOIN MarketplaceHasShop mphs ON mphpa.marketPlaceHasShopId=mphs.id
                                          WHERE 
                                              mphpa.productId = ? AND
                                              mphpa.productVariantId = ? 
                                            ORDER BY m.`name`", [$product->id, $product->productVariantId])->fetchAll();
                $imgMarketPlacePath = \Monkey::app()->baseUrl(FALSE) . "/images/imgorder/";
                $status = $row['statusPublished'];
                foreach ($resmarketplacearray as $marketplaces) {
                    switch ($marketplaces['typeRetouchPrice']) {
                        case 1:
                            $typeRetouchPrice = "prezzo maggiorato del " . $marketplaces['amount'] . "%";
                            break;

                        case 2:
                            $typeRetouchPrice = "prezzo scontato del " . $marketplaces['amount'] . "%";
                            break;

                        case 3:
                            $typeRetouchPrice = "prezzo maggiorato di Euro " . $marketplaces['amount'];
                            break;

                        case 4:
                            $typeRetouchPrice = "prezzo scontato di Euro " . $marketplaces['amount'];
                            break;
                        case 5:
                            $typeRetouchPrice = "non applicato ";
                            break;

                    }
                    switch ($marketplaces['statusPublished']) {
                        case 0:
                            $status = 'In Attesa di Pubblicazione';
                            break;
                        case 1:
                            $status = 'Esportato';
                            break;
                        case 2:
                            $status = 'Allineamento Programmato';
                            break;
                        case 3:
                            $status = 'Cancellato';
                            break;
                        case 4:
                            $status = 'Prodotto In Saldo';
                            break;
                        default:
                            $status = 'da Lavorare';
                    }
                    switch ($marketplaces['isOnSale']) {
                        case 0:
                            $row['isOnSale'] = 'Prodotto non in Saldo';
                            break;
                        case 1:
                            $row['isOnSale'] = 'Prodotto in Saldo';
                            break;

                    }
                    switch ($marketplaces['typeSale']) {
                        case 1:
                            $row['typeSale'] = 'Saldo Da Sito';
                            break;
                        case 2:
                            $row['typeSale'] = 'Saldo Dedicato';
                            break;
                        case 0:
                            $row['typeSale'] = 'non Applicato';
                            break;
                    }
                    if ($marketplaces['isOnSale'] == 1) {
                        $row["percentSale"] = $marketplaces['percentSale'] . " %";
                        $rowtablemarketplace .= "<tr><td align='center'><img width='80' src='" . $imgMarketPlacePath . $marketplaces['icon'] . "'</img></td align='center'><td>" . $marketplaces['nameShop'] . "-" . $marketplaces['name'] . "</td><td align='center'>" . $marketplaces['marketplaceProductId'] . "</td><td align='center'>" . $typeRetouchPrice . "</td align='center'><td align='center'>" . $marketplaces['priceSale'] . "</td></tr>";
                    } else {
                        $rowtablemarketplace .= "<tr><td align='center'><img width='80' src='" . $imgMarketPlacePath . $marketplaces['icon'] . "'</img></td><td align='center'>" . $marketplaces['nameShop'] . "-" . $marketplaces['name'] . "</td><td align='center'>" . $marketplaces['marketplaceProductId'] . "</td><td align='center'>" . $typeRetouchPrice . "</td><td align='center'>" . $marketplaces['priceMarketplace'] . "</td></tr>";
                        $row["percentSale"] = 'non Applicato';
                    }
                }
                $row["associatePrestashopMarketPlace"] = '<table class="nested-table"><thead><th colspan="2" align="center">MarketPlace</th><th align="center">IdMarketPlace</th><th align="center">Tipo ricalcolo</th><th align="center">Prezzo MarketPlace</th></thead><tbody>' . $rowtablemarketplace . '</tbody></table>';
                $row["statusPublished"] = $status;

            }


            $resprice = \Monkey::app()->repoFactory->create('ProductPublicSku')->findOneBy(['productId' => $product->id, 'productVariantId' => $product->productVariantId]);
            $row['price'] = $resprice->price;

            $datatable->setResponseDataSetRow($key, $row);
        }

        return $datatable->responseOut();
    }
}
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
        $sample = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct')->getEmptyEntity();
        $sql = "

select concat(`p`.`id`,'-',`p`.`productVariantId`) AS `code`,
       `p`.`id` AS `productId`,
       `p`.`productVariantId` AS `productVariantId`,
       `p`.`itemno` AS `itemno`,
       concat(`pss`.`name`,`pss`.`year`) AS `season`,
       `pb`.`name` AS `brand`,
       if(`p`.`qty`,'sì','no') AS `stock`,
       `p`.`creationDate` AS `creationDate`,
       concat(ifnull(`m`.`name`,''),' - ',ifnull(`ma`.`name`,'')) AS `marketplaceAccountName`,
       `s`.`name` AS `shop`,`s`.`id` AS `shopId`,
       `mahp`.`marketplaceProductId` AS `marketplaceProductId`,
       `mahp`.`marketplaceId` AS `marketplaceId`,
       `mahp`.`marketplaceAccountId` AS `marketplaceAccountId`,
       `mahp`.`fee` AS `fee`,
       `phpc`.`productCategoryId` AS `category`,
       if(isnull(mphpa.marketplaceId),'non associato','associato')  as associatePrestashopMarketPlace,
       if(isnull(mphpa.marketplaceId),'non associato',
          concat(if(mphpa.typeRetouchPrice=1,'importo','percentuale'), ' ',mphpa.amount)
       ) as  typePrice,
      '' as status
from ((((((((`Product` `p` join `ProductStatus` `ps` on((`p`.`productStatusId` = `ps`.`id`)))
  join `ShopHasProduct` `shp` on(((`p`.`id` = `shp`.`productId`) and (`p`.`productVariantId` = `shp`.`productVariantId`))))
  join `Shop` `s` on((`s`.`id` = `shp`.`shopId`))) join `ProductSeason` `pss` on((`pss`.`id` = `p`.`productSeasonId`)))
  join `ProductBrand` `pb` on((`p`.`productBrandId` = `pb`.`id`)))
  join `ProductHasProductPhoto` `phpp` on(((`p`.`id` = `phpp`.`productId`) and (`p`.`productVariantId` = `phpp`.`productVariantId`))))
  join `ProductHasProductCategory` `phpc` on(((`p`.`id` = `phpc`.`productId`) and (`p`.`productVariantId` = `phpc`.`productVariantId`))))
  left join ((`MarketplaceAccountHasProduct` `mahp` join `MarketplaceAccount` `ma` on(((`ma`.`marketplaceId` = `mahp`.`marketplaceId`) and (`ma`.`id` = `mahp`.`marketplaceAccountId`))))
    join `Marketplace` `m` on((`m`.`id` = `ma`.`marketplaceId`))) on(((`mahp`.`productId` = `p`.`id`) and (`mahp`.`productVariantId` = `p`.`productVariantId`)))
  left join ((`MarketplaceHasProductAssociate` `mphpa` join `MarketplaceHasShop` `mpahs` on(((`mphpa`.`marketplaceId` = `mpahs`.`marketplaceId`) )))
    join `Marketplace` `q` on((`q`.`id` = `mpahs`.`marketplaceId`))) on(((`mphpa`.`productId` = `p`.`id`) and (`mphpa`.`productVariantId` = `p`.`productVariantId`)))

)
where (((`ps`.`isReady` = 1) and (`p`.`qty` > 0)) or (`m`.`id` is not null))";
        $datatable = new CDataTables($sql, $sample->getPrimaryKeys(), $_GET,true);

        $datatable->addCondition('shopId', \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser());
        $datatable->addSearchColumn('marketplaceProductId');

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
              //  $row['status'] = $marketplaceHasProduct->isToWork == 0 ? "lavorato" : "".",<br>".
               // $marketplaceHasProduct->hasError == 1 ? "errore" : "".",<br>".
               // $marketplaceHasProduct->isDeleted == 1 ? "cancellato" : "";
                $row['fee'] = $marketplaceHasProduct->fee;
            }
            /** @var CProduct $product */
            if ($product->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
            else $imgs = "";

            $shops = [];
            foreach ($product->shop as $shop) {
                $shops[] = $shop->name;
            }
            $shopsId=[];
            foreach ($product->shop as $shopsi) {
                $shopsId[] = $shopsi->id;
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
            $shopsfilter=implode(', ', $shopsId);
            $row['shop'] = implode(', ', $shops);
            $row['dummy'] = '<img width="50" src="' . $product->getDummyPictureUrl() . '" />' . $imgs . '<br />';
            $row['itemno'] = '<span class="small">';
            $row['itemno'] .= $product->printCpf();
            $row['itemno'] .= '</span>';

            $row['category'] = $product->getLocalizedProductCategories('<br>');
            $row['creationDate'] = $product->creationDate;
            $rowtablemarketplace="";
            $resmarketplaceHasProductAssociate=\Monkey::app()->repoFactory->create('MarketplaceHasProductAssociate')->findOneBy(['productId'=>$product->id,'productVariantId'=>$product->productVariantId]);
            if(null == $resmarketplaceHasProductAssociate){
                $row['associatePrestashopMarketPlace']='non associato';
                $row['typePrice']='non applicato';
                $row['price']='non calcolato';
            }else{
                $resmarketplacearray=$this->app->dbAdapter->query("SELECT m.name as name,s.name as nameShop, mphpa.typeRetouchPrice as typeRetouchPrice, mphpa.amount as amount,mphpa.price as price,mphs.imgMarketPlace as icon
                                          FROM Marketplace m join MarketplaceHasProductAssociate mphpa
                                          on mphpa.marketplaceId =m.id
                                           join Shop s on mphpa.shopId=s.id
                                           join MarketplaceHasShop mphs on mphpa.marketPlaceHasShopId=mphs.id
                                          WHERE 
                                              mphpa.productId = ? AND
                                              mphpa.productVariantId = ? 
                                            ORDER BY m.`name`", [$product->id, $product->productVariantId])->fetchAll();
                $imgMarketPlacePath=\Monkey::app()->baseUrl(FALSE)."/images/imgorder/";

                foreach ($resmarketplacearray as $marketplaces) {
                    switch($marketplaces['typeRetouchPrice']){
                        case 1:
                            $typeRetouchPrice="prezzo maggiorato del ".$marketplaces['amount']."%";
                            break;

                        case 2:
                            $typeRetouchPrice="prezzo scontato del ".$marketplaces['amount']."%";
                            break;

                        case 3:
                            $typeRetouchPrice="prezzo maggiorato di Euro ".$marketplaces['amount'];
                            break;

                        case 4:
                            $typeRetouchPrice="prezzo scontato di Euro ".$marketplaces['amount'];
                            break;
                        default:
                            $typeRetouchPrice="non applicato ";
                            break;

                    }


                    $rowtablemarketplace .= "<tr><td><img width='80' src='".$imgMarketPlacePath.$marketplaces['icon']."'</img></td><td>".$marketplaces['nameShop'] ."-". $marketplaces['name'] . "</td><td>".$typeRetouchPrice."</td><td>".$marketplaces['price'] . "</td></tr>";
                }
                $row["associatePrestashopMarketPlace"] = '<table class="nested-table"><thead><th colspan="2">MarketPlace</th><th>Tipo ricalcolo</th><th>Prezzo Ricalcolato</th></thead><tbody>' . $rowtablemarketplace . '</tbody></table>';
              if(null==$resmarketplaceHasProductAssociate->statusPublished){
                  $row['status']='Da Lavorare';
              }else {
                  switch ($resmarketplaceHasProductAssociate->statusPublished) {
                      case 0:
                          $row['status'] = 'In Attesa di Pubblicazione';
                          break;
                      case 1:
                          $row['status'] = 'Pubblicato';
                          break;
                      case 2:
                          $row['status'] = 'Allineamento Programmato';
                          break;
                      case 3:
                          $row['status'] = 'Cancellato';
                          break;
                      default:
                          $row['status'] = 'da Lavorare';
                  }
              }
            }
            $resprice=\Monkey::app()->repoFactory->create('ProductPublicSku')->findOneBy(['productId'=>$product->id,'productVariantId'=>$product->productVariantId]);
            $row['price']=$resprice->price;

            $response ['data'][] = $row;
        }

        return json_encode($response);
    }
}
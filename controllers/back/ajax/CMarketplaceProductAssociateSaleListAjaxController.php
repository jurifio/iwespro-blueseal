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
class CMarketplaceProductAssociateSaleListAjaxController extends AAjaxController
{
    public function get()
    {
        $idmarketplaceshop = $this->data['rightid'];

        $sql="SELECT p.id as id,
       concat(`pps`.`productId`,'-',`pps`.`productVariantId`) AS `code`,
       s.name as  shop,
       pb.name as brand,
       phpc.productCategoryId as category,
       `p`.`itemno` AS `itemno`,
       '' as stock,
       pss.name   as season,
       '' as dummy,
       pps.productId as productId,
       pps.productVariantId as productVariantId,
       shp.shopId as shopId,
       mhpa.statusPublished as statusPublished,
       mhpa.typeRetouchPrice as TypeRetouchPrice,
       mhpa.marketPlaceHasShopId as marketplaceHasShopId,
       mhpa.price as priceMarketplace,


       p.creationDate as creationDate,
       '' as associatePrestashopMarketPlace



from ProductPublicSku pps
  join ProductSku psk on pps.productId=psk.productId and pps.productVariantId=psk.productVariantId
  join MarketplaceHasProductAssociate mhpa on pps.productId= mhpa.productId and pps.productVariantId=pps.productVariantId

  join ShopHasProduct shp on pps.productId=shp.productId and pps.productVariantId =shp.productVariantId
  join Shop s on s.id =shp.shopId
  join Product p on pps.productId=p.id and pps.productVariantId=p.productVariantId
  join ProductSeason pss on pss.id =p.productSeasonId
  join ProductHasProductPhoto PHPP ON pps.productId = PHPP.productId AND pps.productVariantId = PHPP.productVariantId
  join ProductBrand pb on p.productBrandId =pb.id
  join `ProductHasProductCategory` `phpc` on  pps.`productId` = `phpc`.`productId` and `pps`.`productVariantId` = `phpc`.`productVariantId`
 

  join `ProductStatus` `ps` on((`p`.`productStatusId` = `ps`.`id`))

where (`p`.`qty` > 0) and (p.productStatusId=6) and marketplaceHasShopId=".$idmarketplaceshop." 
group by productId, productVariantId";


        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings('true');
        foreach ($datatable->getResponseSetData() as $key => $row) {
           $resproductFindEan=\Monkey::app()->repoFactory->create('ProductEan')->findOneBy(['productId'=>$row['productId'],'productVariantId'=>$row['productVariantId'],'productSizeId'=>'0','used'=>'1','usedForParent'=>'1'])  ;
            if($resproductFindEan!=null){
                $row["ean"] = $resproductFindEan->ean;

            }else{
                $row["ean"]='Ean non presente';
            }
            $product = \Monkey::app()->repoFactory->create('Product')->findOne([$row['productId'], $row['productVariantId']]);
            $row['code'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="/blueseal/prodotti/modifica?id=' . $product->id . '&productVariantId=' . $product->productVariantId . '">' . $product->id . '-' . $product->productVariantId . '</a>';



            $row["DT_RowId"] = $product->printId();
            if ($product->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
            else $imgs = "";
            $productcategory=$row['category'];
            $row['category']= $product->getLocalizedProductCategories('<br>');
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
            $rowtablemarketplace="";
            $resmarketplaceHasProductAssociate=\Monkey::app()->repoFactory->create('MarketplaceHasProductAssociate')->findOneBy(['productId'=>$product->id,'productVariantId'=>$product->productVariantId]);
            if(null == $resmarketplaceHasProductAssociate){
                $row['associatePrestashopMarketPlace']='non associato';
                $row['typePrice']='non applicato';
                $row['price']='non calcolato';
                $row['statusPublished']='non lavorato';

            }else{
                $resmarketplacearray=$this->app->dbAdapter->query("SELECT m.name as name,s.name as nameShop, mphpa.typeRetouchPrice as typeRetouchPrice, mphpa.amount as amount,mphpa.price as price,mphs.imgMarketPlace as icon, mphpa.statusPublished as statusPublished
                                          FROM Marketplace m join MarketplaceHasProductAssociate mphpa
                                          on mphpa.marketplaceId =m.id
                                           join Shop s on mphpa.shopId=s.id
                                           join MarketplaceHasShop mphs on mphpa.marketPlaceHasShopId=mphs.id
                                          WHERE 
                                              mphpa.productId = ? AND
                                              mphpa.productVariantId = ? 
                                            ORDER BY m.`name`", [$product->id, $product->productVariantId])->fetchAll();
                $imgMarketPlacePath=\Monkey::app()->baseUrl(FALSE)."/images/imgorder/";
$status=$row['statusPublished'];
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


                    $rowtablemarketplace .= "<tr><td><img width='80' src='".$imgMarketPlacePath.$marketplaces['icon']."'</img></td><td>".$marketplaces['nameShop'] ."-". $marketplaces['name'] . "</td><td>".$typeRetouchPrice."</td><td>".$marketplaces['price'] . "</td></tr>";
                }
                $row["associatePrestashopMarketPlace"] = '<table class="nested-table"><thead><th colspan="2">MarketPlace</th><th>Tipo ricalcolo</th><th>Prezzo Ricalcolato</th></thead><tbody>' . $rowtablemarketplace . '</tbody></table>';
                $row["statusPublished"]=$status;

                }

            $resprice=\Monkey::app()->repoFactory->create('ProductPublicSku')->findOneBy(['productId'=>$product->id,'productVariantId'=>$product->productVariantId]);
            $row['price']=$resprice->price;

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }

}
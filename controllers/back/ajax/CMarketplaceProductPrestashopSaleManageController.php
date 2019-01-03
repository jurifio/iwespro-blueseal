<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\repositories\CMarketplaceHasProductAssociateSaleRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\core\intl\CLang;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductName;
use bamboo\domain\entities\CDirtyProduct;
use bamboo\utils\price\SPriceToolbox;


/**
 * Class CMarketplaceProductPrestashopSaleManageController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/12/2018
 * @since 1.0
 */
class CMarketplaceProductPrestashopSaleManageController extends AAjaxController
{

    public function post()
    {
        $res = "";
        $idproduct = $this->app->router->request()->getRequestData('rows');

        $typeSale = $this->app->router->request()->getRequestData('typeSale');
        $titleSale = $this->app->router->request()->getRequestData('titleSale');
        $percentSale = $this->app->router->request()->getRequestData('percentSale');
        $i = 0;
        foreach ($idproduct as $idproducts) {
            $valueRows = $idproducts;

                $array = array($valueRows);
                $arrayproduct = implode('-', $array);

                $singleproduct = explode('-', $arrayproduct);
                $idmarketPlaceHasShop = $singleproduct[1];


                $selectMarketPlaceHasShop = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['id' => $singleproduct[1]]);
                $shopId = $selectMarketPlaceHasShop->shopId;
                $marketplaceId = $selectMarketPlaceHasShop->marketplaceId;
                $prestashopId = $selectMarketPlaceHasShop->prestashopId;
                if ($typeSale == 1) {
                    $price = $singleproduct[5];
                    $typeSaleText = " prezzo di Listino  del Sito:";
                } else {
                    $price = $singleproduct[6];
                    $typeSaleText = " prezzo da ricarico per MarketPlace Sito:";
                }
                //inserimento descrizione e nome

                $findname=\Monkey::app()->repoFactory->create('Product')->findOneBy(['id'=>$singleproduct[2], 'productVariantId' => $singleproduct[3]]);
                //concat(pb.name,' ',pn.name,' ',dp.var , dp.itemno,' ', pv.name)
                $productbrandName=$findname->productBrand->name;
                $findProductName=\Monkey::app()->repoFactory->create('ProductNameTranslation')->findOneBy(['productId'=>$singleproduct[2], 'productVariantId' => $singleproduct[3],'langId'=>1]);
                if($findProductName==null){
                    $productnameName='';
                }else {
                    $productnameName = $findProductName->name;
                }
                $dirtyProduct=\Monkey::app()->repoFactory->create('DirtyProduct')->findOneBy(['productId'=>$singleproduct[2], 'productVariantId' => $singleproduct[3]]);
                $productitemnoName=$dirtyProduct->itemno;
                $productcolorSupplierName=$dirtyProduct->var;


                $updateMarketplaceHasProductAssociate = \Monkey::app()->repoFactory->create('MarketplaceHasProductAssociate')->findOneBy(['id' => $singleproduct[0], 'productId' => $singleproduct[2], 'productVariantId' => $singleproduct[3], 'shopId' => $shopId, 'marketplaceId' => $marketplaceId, 'prestashopId' => $prestashopId]);
                $updateMarketplaceHasProductAssociate->amount = $percentSale;
                $updateMarketplaceHasProductAssociate->typeRetouchPrice = 2;
                $updateMarketplaceHasProductAssociate->priceMarketplace = $price;
                $updateMarketplaceHasProductAssociate->isOnSale = 1;
                if($typeSale == 2) {
                    $priceSale = $price - ($price / 100 * $percentSale);
                }else {
                    $findpriceSale = \Monkey::app()->repoFactory->create('ProductPublicSku')->findOneBy(['productId' => $singleproduct[2], 'productVariantId' => $singleproduct[3]]);
                    $priceSale = $findpriceSale->salePrice;
                    $fullprice = $findpriceSale->price;
                    $percentSale = ($priceSale * 100 / $fullprice);

                                    }
                $updateMarketplaceHasProductAssociate->priceSale = $priceSale;
                $updateMarketplaceHasProductAssociate->percentSale = $percentSale;
                $updateMarketplaceHasProductAssociate->typeSale = $typeSale;
                $updateMarketplaceHasProductAssociate->titleSale = $titleSale;
                $updateMarketplaceHasProductAssociate->isOnSale = 1;
                if  ($titleSale==1) {
                    $titleTextSale = $productbrandName . " " . $productnameName . " " . $productitemnoName . " " . $productcolorSupplierName . " Scontato del " . $percentSale . " %  da € " . number_format($price,2,",",".") . " a € " . number_format($priceSale,2,",",".");
                }else{
                    $titleTextSale='';
                }
                $updateMarketplaceHasProductAssociate->titleTextSale=$titleTextSale;
                $updateMarketplaceHasProductAssociate->statusPublished=2;
                $updateMarketplaceHasProductAssociate->update();
                $res .= "<p><br>PrestashopId: " . $singleproduct[0] . " codice:" . $singleproduct[2] . "-" . $singleproduct[3] ."-". $typeSaleText."-" . number_format($price,2,',',',') . "Sconto applicato:" . $percentSale . "%" . " Prezzo Finale:" . number_format($priceSale,2,",","."). "Titolo Prodotto:".$titleTextSale;


                $i++;


        }
        return $res;
    }
}
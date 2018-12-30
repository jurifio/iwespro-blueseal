<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\repositories\CMarketplaceHasProductAssociateSaleRepo;

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
                $updateMarketplaceHasProductAssociate = \Monkey::app()->repoFactory->create('MarketplaceHasProductAssociate')->findOneBy(['id' => $singleproduct[0], 'productId' => $singleproduct[2], 'productVariantId' => $singleproduct[3], 'shopId' => $shopId, 'marketplaceId' => $marketplaceId, 'prestashopId' => $prestashopId]);
                $updateMarketplaceHasProductAssociate->amount = $percentSale;
                $updateMarketplaceHasProductAssociate->typeRetouchPrice = 2;
                $updateMarketplaceHasProductAssociate->price = $price;
                $updateMarketplaceHasProductAssociate->isOnSale = 1;
                $priceSale = $price - ($price / 100 * $percentSale);
                $updateMarketplaceHasProductAssociate->priceSale = $priceSale;
                $updateMarketplaceHasProductAssociate->percentSale = $percentSale;
                $updateMarketplaceHasProductAssociate->typeSale = $typeSale;
                $updateMarketplaceHasProductAssociate->titleSale = $titleSale;
                $updateMarketplaceHasProductAssociate->isOnSale = 1;
                $updateMarketplaceHasProductAssociate->update();
                $res .= "<br>PrestashopId" . $singleproduct[0] . " codice:" . $singleproduct[2] . "-" . $singleproduct[3] . $typeSaleText . $price . "Sconto applicato:" . $percentSale . "%" . " Prezzo Finale:" . $priceSale;


                $i++;


        }
        return $res;
    }
}
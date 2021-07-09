<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSku;
use PDO;

/**
 * Class CEbayReviseProductJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 27/04/2020
 * @since 1.0
 */
class CEbayNewReviseProductJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->ReviseProductsInEbay();
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    private function ReviseProductsInEbay()
    {
        $xml = '';
        if (ENV === 'prod') {
            $db_host = '5.189.159.187';
            $db_name = 'iwesPrestaDB';
            $db_user = 'iwesprestashop';
            $db_pass = 'X+]l&LEa]zSI';
        } else {
            $db_host = 'localhost';
            $db_name = 'iwesPrestaDB';
            $db_user = 'root';
            $db_pass = 'geh44fed';
        }
        $res = "";
        try {
            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
            $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $res = " connessione ok <br>";
        } catch (PDOException $e) {
            $res = $e->getMessage();
        }
        $checkProductShop = [];
        $shopActive = \Monkey::app()->repoFactory->create('Shop')->findBy(['hasEcommerce' => '1']);
        foreach ($shopActive as $shopActives) {
            $checkProductShop[] = [$shopActives->id];
        }
        $phpRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
        $addressBookRepo = \Monkey::app()->repoFactory->create('AddressBook');
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $productSizeGroupRepo = \Monkey::app()->repoFactory->create('ProductSizeGroup');
        $productBrandRepo = \Monkey::app()->repoFactory->create('ProductBrand');
        $productSizeGroupHasProductSizeRepo = \Monkey::app()->repoFactory->create('ProductSizeGroupHasProductSize');
        $productSizeRepo = \Monkey::app()->repoFactory->create('ProductSize');
        $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');
        $productEanRepo = \Monkey::app()->repoFactory->create('ProductEan');
        $productInMarketplaceRepo = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');
        $marketplaceAccounts = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findBy(['marketplaceId' => 3,'isActive' => 1]);
        foreach ($marketplaceAccounts as $marketplaceAccount) {
            $goods = $productInMarketplaceRepo->findBy(['isPublished' => 1,'marketplaceHasShopId' => $marketplaceAccount->config['marketplaceHasShopId']]);

            $shop = $shopRepo->findOneBy(['id' => $marketplaceAccount->config['shopId']]);
            $addressBook = $addressBookRepo->findOneBy(['id' => $shop->billingAddressBookId]);
            foreach ($goods as $good) {
                if(is_null($good->refMarketplaceId)){
                    continue;
                }
                /**  @var CProduct $product * */
                $product = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $good->productId,'productVariantId' => $good->productVariantId]);
                if ($product->qty == 0) {
                    continue;
                }
                if ($product->lastUpdate == $good->lastUpdate) {
                    continue;
                }
                $productCategory=\Monkey::app()->repoFactory->create('ProductHasProductCategory')->findOneBy(['productId'=>$good->productId,'productVariantId' => $good->productVariantId]);
                $productCategoryId=$productCategory->productCategoryId;
                $category = \Monkey::app()->repoFactory->create('ProductCategoryHasMarketplaceAccountCategory')->findOneBy(['marketplaceId' => 3,'marketplaceAccountId' => 3,'productCategoryId' => $productCategoryId]);
                if (!$category) {
                    continue;
                }
                $productBrand = $productBrandRepo->findOneBy(['id' => $product->productBrandId]);
                $slugBrand = $productBrand->slug;
                $brandName = $productBrand->name;
                $productEanParent = $productEanRepo->findOneBy(['productId' => $good->productId,'productVariantId' => $good->productVariantId,'productSizeId' => 0]);
                if ($productEanParent) {
                    $productEan = $productEanParent->ean;
                } else {
                    $productEanSelect =$productEanRepo->findOneBy(['used'=>0]);
                    $productEan=$productEanSelect->ean;
                    $productEanSelect->productId=$good->productId;
                    $productEanSelect->productVariantId=$good->productVariantId;
                    $productEanSelect->productSizeId=0;
                    $productEanSelect->usedForParent=1;
                    $productEanSelect->brandAssociate=$product->productBrandId;
                    $productEanSelect->shopId=$marketplaceAccount->config['shopId'];
                    $productEanSelect->update();
                }
                $brands = explode(',',$marketplaceAccount->config['brands']);
                if (in_array($product->productBrandId,$brands)) {
                    continue;
                }

                $brandParallel = explode(',',$marketplaceAccount->config['brandParallel']);
                if (in_array($product->productBrandId,$brandParallel)) {
                    continue;
                }
                $brandSaleExclusion = explode(',',$marketplaceAccount->config['brandSaleExclusion']);
                $productSizeGroup = $productSizeGroupRepo->findOneBy(['id' => $product->productSizeGroupId]);
                $productSizeGroupHasProductSize = $productSizeGroupHasProductSizeRepo->findBy(['ProductSizeGroupId' => $productSizeGroup->id]);
                $phphmhsRepo = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');
                //etichetta taglie
                $isOnSale = $product->isOnSale;

                $xml = '';
                $xml .= '<?xml version="1.0" encoding="utf-8"?>';
                $xml .= '<ReviseFixedPriceItem xmlns="urn:ebay:apis:eBLBaseComponents">';
                $xml .= '<ErrorLanguage>it_IT</ErrorLanguage>';
                $xml .= '<WarningLevel>High</WarningLevel>';
                //intestazione prodotto
                $xml .= '<Item>';
                $xml .= '<ItemID>' . $good->refMarketplaceId . '</ItemID>';
                $xml .= '<Country>IT</Country>';
                $xml .= '<Currency>EUR</Currency>';
                $xml .= '<PostalCode>62012</PostalCode>';
                $xml .= '<Location>Civitanova Marche</Location>';
                $xml .= '<BestOfferDetails>';
                $xml .= '<BestOfferEnabled>False</BestOfferEnabled>';
                $xml .= '</BestOfferDetails>';
                $xml .= '<PrimaryCategory>';

                $xml .= '<CategoryID>' . $category->marketplaceAccountCategoryId . '</CategoryID>';
                $xml .= '</PrimaryCategory>';
                $xml .= '<HitCounter>RetroStyle</HitCounter>';
                $xml .= '<Variations>';
                //variante colore
                $productVariant = \Monkey::app()->repoFactory->create('ProductVariant')->findOneBy(['id' => $good->productVariantId]);
                //varianti taglie n
                /** @var CProductSku $productSku */
                $productSku = $productSkuRepo->findBy(['productId' => $good->productId,'productVariantId' => $good->productVariantId]);
                foreach ($productSku as $sku) {
                   // if ($sku->stockQty > 0) {
                        $xml .= '<Variation>';
                        // $xml .= '<SKU>prestashop-' . $reservedId['prestaId'] . '-' . $rowsGetReferenceIdProductAttribute[0]['id_product_attribute'] . '</SKU>';
                        $xml .= '<SKU>' . $sku->productId . '-' . $sku->productVariantId . '-' . $sku->productSizeId . '</SKU>';
                        if ($good->isOnSale == 0) {
                            $xml .= '<StartPrice>' . number_format($good->price,2,'.','') . '</StartPrice>';
                        } else {
                            $xml .= '<StartPrice>' . number_format($good->salePrice,2,'.','') . '</StartPrice>';
                        }

                        $xml .= '<Quantity>' . $sku->stockQty . '</Quantity>';
                        $xml .= '<VariationProductListingDetails>';
                        $findSkuEan=$productEanRepo->findOneBy(['productId' => $sku->productId,'productVariantId' => $sku->productVariantId,'productSizeId' => $sku->productSizeId]);
                        if ($findSkuEan) {
                            $productSkuEan = $findSkuEan->ean;
                        } else {
                            $productSkuEanSelect =$productEanRepo->findOneBy(['used'=>0]);
                            $productSkuEan=$productSkuEanSelect->ean;
                            $productSkuEanSelect->productId=$sku->productId;
                            $productSkuEanSelect->productVariantId=$sku->productVariantId;
                            $productSkuEanSelect->productSizeId=$sku->productSizeId;
                            $productSkuEanSelect->usedForParent=0;
                            $productSkuEanSelect->used=1;
                            $productSkuEanSelect->brandAssociate=$product->productBrandId;
                            $productSkuEanSelect->shopId=$marketplaceAccount->config['shopId'];
                            $productSkuEanSelect->update();
                        }

                        $xml .= '<EAN>' . $productSkuEan . '</EAN>';
                        $xml .= '<UPC>Non applicabile</UPC>';
                        $xml .= '<ProductReferenceID><![CDATA[' . $sku->productId . '-' . $sku->productVariantId . '-' . $sku->productSizeId . ']]></ProductReferenceID>';
                        $xml .= '</VariationProductListingDetails>';
                        $xml .= '<VariationSpecifics>';
                        $xml .= '<NameValueList>';
                        $xml .= '<Name>Taglia</Name>';
                        $productSizeColl = $productSizeRepo->findOneBy(['id' => $sku->productSizeId]);
                        $xml .= '<Value>' . $productSizeColl->name . '</Value>';
                        $xml .= '</NameValueList>';
                        $xml .= '<NameValueList>';
                        $xml .= '<Name>Color</Name>';
                        $xml .= '<Value>' . $product->productColorGroup->name . '</Value>';
                        $xml .= '</NameValueList>';
                        $xml .= '</VariationSpecifics>';
                        $xml .= '</Variation>';
                   // }
                }
                $xml .= '</Variations>';
                $xml .= '<PictureDetails>';
                $productHasProductPhoto = \Monkey::app()->repoFactory->create('ProductHasProductPhoto')->findBy(['productId' => $sku->productId,'productVariantId' => $sku->productVariantId]);
                foreach ($productHasProductPhoto as $phs) {
                    $productPhoto = \Monkey::app()->repoFactory->create('ProductPhoto')->findOneBy(['id' => $phs->productPhotoId]);
                    if ($productPhoto->size == '1124') {
                        $xml .= '<PictureURL>https://cdn.iwes.it/' . $slugBrand . '/' . $productPhoto->name . '</PictureURL>';
                    }

                }
                $xml .= '</PictureDetails>';
                $xml .= '<ItemSpecifics>';
                $xml .= '<NameValueList>';
                $xml .= '<Name><![CDATA[MPN]]></Name>';
                $xml .= '<Value><![CDATA[' . $sku->productId . '-' . $sku->productVariantId . '-' . $sku->productSizeId . ']]></Value>';
                $xml .= '</NameValueList>';
                $xml .= '<NameValueList>';
                $xml .= '<Name><![CDATA[Marca]]></Name>';
                $xml .= '<Value><![CDATA[' . $brandName . ']]></Value>';
                $xml .= '</NameValueList>';
                $xml .= '<NameValueList>
        <Name><![CDATA[Materiale Tomaia]]></Name>
        <Value><![CDATA[non applicabile]]></Value>
      </NameValueList><NameValueList>
        <Name><![CDATA[Larghezza della scarpa]]></Name>
        <Value><![CDATA[non applicabile]]></Value>
      </NameValueList>
      <NameValueList>
        <Name><![CDATA[Tipo]]></Name>
        <Value><![CDATA[non applicabile]]></Value>
      </NameValueList>
       <NameValueList>
        <Name><![CDATA[Modello]]></Name>
        <Value><![CDATA[non applicabile]]></Value>
      </NameValueList>
        <NameValueList>
        <Name><![CDATA[Reparto]]></Name>
        <Value><![CDATA[non applicabile]]></Value>
      </NameValueList></NameValueList>
        <NameValueList>
        <Name><![CDATA[Stile]]></Name>
        <Value><![CDATA[non applicabile]]></Value>
      </NameValueList> 
      <NameValueList>
        <Name><![CDATA[Materiale Tomaia]]></Name>
        <Value><![CDATA[non applicabile]]></Value>
      </NameValueList>
       <NameValueList>
        <Name><![CDATA[Colore]]></Name>
        <Value><![CDATA['.$product->productColorGroup->name.']]></Value>
      </NameValueList>
       <NameValueList>
        <Name><![CDATA[Materiale]]></Name>
        <Value><![CDATA[non applicabile]]></Value>
      </NameValueList>';
                $xml .= '<NameValueList>';
                $xml .= '<Name><![CDATA[Vintage]]></Name>';
                $xml .= '<Value><![CDATA[non applicabile]]></Value>';
                $xml .= '</NameValueList>';
                $xml .= '</ItemSpecifics>';
                $xml .= '<ConditionID>1000</ConditionID>';
                if ($good->titleModified == "1" && $good->isOnSale == "1") {
                    $percSc = number_format(100 * ($good->price - $good->salePrice) / $good->price,0);
                    $name = $product->productBrand->name
                        . ' Sconto del ' . $percSc . '% da ' . number_format($good->price,'2','.','') . ' € a ' . number_format($good->salePrice,'2','.','')
                        . ' € ' .
                        $product->itemno
                        . ' ' .
                        $product->productColorGroup->productColorGroupTranslation->findOneByKey('langId',1)->name;
                    $xml .= '<Title><![CDATA[' . $name . ']]></Title>';
                } else {
                    $productHasProductCategory=\Monkey::app()->repoFactory->create('ProductHasProductCategory')->findOneBy(['productId'=>$product->id,'productVariantId'=>$product->productVariantId]);

                    $name = \Monkey::app()->repoFactory->create('ProductCategoryTranslation')->findOneBy(['langId'=>1,'productCategoryId'=>$productHasProductCategory->productCategoryId,'shopId'=>44])->name
                        . ' ' .
                        $product->productBrand->name
                        . ' ' .
                        $product->itemno
                        . ' ' .
                        $product->productColorGroup->productColorGroupTranslation->findOneByKey('langId',1)->name;


                    $xml .= '<Title><![CDATA[' . $name . ']]></Title>';
                }
                $xml .= '<Description>';
                $xml .= '<![CDATA[<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="title" content="" />
        <meta name="keywords" content="{{keywords}}" />
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
<style>

* {
    padding: 0;
    margin: 0;
    outline: none;
    border: none;
    list-style: none;
    box-sizing: border-box;
    font-family: \'Montserrat\', sans-serif;
}
p {
	font-size: 14px;
	font-weight: 300;
	line-height: 26px;
	color: #4b4b4b;
}
img {
	max-width: 100%;
	max-height: -webkit-fill-available;
}
.wrapper {
    margin: 0 auto;
    width: 90%;
    max-width: 1200px;
}
.clear {
    clear: both;
}
main,
header,
section,
.store_faetures ul,
.footer_features ul,
footer {
	width: 100%;
	float: left;
}
header {
	padding: 20px 0;
	text-align: center;
}
header a {
	display: inline-block;
}
.store_features {
	background: #323b47;
}
.store_features li {
	width: 25%;
	display: table;
	float: left;
}
.store_features li div {
	padding-right: 5%;
	width: 30%;
	height: 100px;
	display: table-cell;
	vertical-align: middle;
	text-align: right;
}
.store_features li h3 {
	display: table-cell;
	vertical-align: middle;
	font-size: 15px;
	font-weight: 400;
	color: #FFF;
}
.store_features li h3 span {
	font-size: 13px;
	font-weight: 300;
}
.gif_img {
	display: none;
}
.title {
	padding: 20px;
	border: 1px solid #e9e9e9;
	border-top: none;
	text-align: center;
}
.title h2 {
	font-size: 24px;
	font-weight: 400;
	color: #323b47;
}
.image_gallery {
	margin-bottom: 20px;
	padding: 20px;
    text-align: center;
}
/*GALLERY CSS*/
.container {
    width: 100%;
    position: relative;
    margin:0 auto;
}
.thumbnails {
	text-align: center;
    list-style: none;
    font-size: 0;
}
.thumbnails li {
    margin: 15px 8px 0 8px;
    width: 90px;
	height: 90px;
	background: #fff;
    display: inline-block;
    text-align: center;
    vertical-align: middle;
}
.thumbnails input[name="select"] {
    display: none;
}
.thumbnails .item-hugger {
	padding: 5px;
	width: 100%;
	height: 100%;
    position: relative;
	display: flex;
    flex-direction: column;
    justify-content: center;
	align-items: center;
	border: 1px solid #e3e3e3;
    transition: all 150ms ease-in-out;
}
.thumbnails label {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    cursor: pointer;
}
.thumbnails .gallery_content {
	max-width: 540px;
    width: 100%;
    height: 500px;
	background: #fff;
    transition: all 150ms linear;
    display: flex;
    flex-direction: column;
    justify-content: center;
	align-items: center;
    overflow: hidden;
	opacity: 0;
	position: absolute;
    top: 0;
    left: 50%;
	transform: translateX(-50%);
}
.thumbnails input[name="select"]:checked + .item-hugger {
    border-color: #a2a2a2;
}
.thumbnails input[name="select"]:checked ~ .gallery_content {
    opacity: 1;
}
.white-box {
    height: 500px;
    overflow: hidden;
}
.temp_content {
	margin-bottom: 40px;
}
.temp_content h2 {
	padding: 12px 20px;
	background: #323b47;
	font-size: 24px;
	font-weight: 400;
	color: #FFF;
}
.description {
	padding: 20px;
	border: 1px solid #e9e9e9;
	border-top: none;
}
.description ul {
	margin-bottom: 20px;
}
.description ul li,
.tab-content ul li {
	padding: 5px 10px 5px 20px;
	background: url(https://wearephoenixteam.com/ebay/free-templates/images/temp6/bullet.png) left 10px no-repeat;
	font-weight: 300;
	font-size: 14px;
	color: #4b4b4b;
}
.vertabs {
	margin-bottom: 40px;
}
/*CSS VRETICAL TABS*/
.css_tab {
    width: 100%;
    float: left;
    position: relative;
}
.css-tab {
    display: none;
}
.css_tab li {
    display: inline-block;
    float: left;
    width: 100%;
}
.css_tab li label {
    padding: 15px;
    width: 250px;
    background: #323b47;
    font-family: \'Montserrat\', sans-serif;
    text-align: left;
    font-size: 16px;
    font-weight: 400;
    color: #FFF;
    position: absolute;
    left: 1px;
    top: 1px;
    float: left;
    cursor: pointer;
}
.css_tab li:nth-child(2) label {
    top: 52px;
}
.css_tab li:nth-child(3) label {
    top: 103px;
}
.css_tab li:nth-child(4) label {
    top: 154px;
}
.css_tab li:nth-child(5) label {
    top: 205px;
}
.css_tab li:nth-child(6) label {
    top: 256px;
}
.css_tab li:nth-child(7) label {
    top: 307px;
}
.css-tab:checked + .tab-label {
    background: #FFF;
	color: #323b47;
}
.tab-label:hover {
    background: #F2F2F2;
	color: #323b47;
}
#tab1:checked ~ #tab-content1,
#tab2:checked ~ #tab-content2,
#tab3:checked ~ #tab-content3,
#tab4:checked ~ #tab-content4,
#tab5:checked ~ #tab-content5,
#tab6:checked ~ #tab-content6,
#tab7:checked ~ #tab-content7 {
    display: block;
}
.tab-content {
    padding: 30px 20px 30px 270px;
    width: 100%;
    border: solid 1px #e9e9e9;
    display: none;
    text-align: left;
    float: left;
    min-height: 410px;
}
.tab-content h2{
	margin-bottom: 20px;
	text-align: center;
    font-size: 24px;
	letter-spacing: 1px;
    font-weight: 400;
    color: #323b47;
}
.footer_features {
	margin-bottom: 20px;
}
.footer_features ul li {
	margin: 0 2.66% 20px 0;
	width: 23%;
	float: left;
}
.footer_features ul li:nth-child(4) {
	margin-right: 0;
}
.footer_features ul li div {
	width: 100%;
	height: 120px;
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	background: #323b47;
	text-align: center;
	vertical-align: middle;
}
.footer_features ul li h2 {
	padding: 10px;
	text-align: center;
	font-size: 24px;
	font-weight: 400;
	letter-spacing: 1px;
	line-height: 24px;
	color: #323b47;
	border: 1px solid #323b47;
}
.footer_features ul li h2 span {
	font-size: 13px;
}
footer {
	padding: 30px 20px;
	text-align: center;
	border-top: 1px solid #e9e9e9;
}
@media only screen and (max-width: 840px) {
	.store_features {
		text-align: center;
	}
	.store_features ul {
		display: none;
	}
	.gif_img {
		display: inline-block;
	}
	/*tab-css*/
    .css_tab li label {
        padding: 15px;
        width: 100% !important;
        position: static;
        background: #323b47 url(https://wearephoenixteam.com/ebay/free-templates/images/temp6/plus.png) right center no-repeat;
        text-align: left;
        border: none;
    }
    .css_tab li{
        margin-bottom: 1px;
    }
    .tab-content {
        padding: 15px 20px;
    }
    .css_tab li label:hover{
        background: #d8d8d8 url(https://wearephoenixteam.com/ebay/free-templates/images/temp6/plus.png) right center no-repeat;
        border: none;
    }
    .css-tab:checked + .tab-label{
        border-bottom: 1px solid #ddd;
        background: #d8d8d8 url(https://wearephoenixteam.com/ebay/free-templates/images/temp6/images/minus.png) right center no-repeat;
    }
	.footer_features ul li {
		margin-right: 4%;
		width: 48%;
	}
	.footer_features ul li:nth-child(2n) {
		margin-right: 0;
	}
}
@media only screen and (max-width: 640px) {
	.footer_features ul li {
		margin-right: 0 !important;
		width: 100%;
	}
	.thumbnails .gallery_content,
	.white-box {
		height: 400px;
	}
}
</style>
	</head>
	<body>
<p></p>
<p></p>
<p></p>
<p></p>
<p><main>
<div class="wrapper"><header><a><img src="https://www.cartechinishop.com/assets/logowide.png" alt="" /></a></header>
<section class="store_features">
<ul>
<li>
<div><img src="https://wearephoenixteam.com/ebay/free-templates/images/temp6/shipping.png" alt="" /></div>
<h3>SPEDIZIONE VELOCE<br /><span>On All Items</span></h3>
</li>
<li>
<div><img src="https://wearephoenixteam.com/ebay/free-templates/images/temp6/moneyback.png" alt="" /></div>
<h3>30 DAYS MONEYBACK<br /><span>Restituzioni senza problemi</span></h3>
</li>
<li>
<div><img src="https://wearephoenixteam.com/ebay/free-templates/images/temp6/support.png" alt="" /></div>
<h3>SUPPORTO CLIENTI<br /><span>Servizio Eccellente</span></h3>
</li>
<li>
<div><img src="https://wearephoenixteam.com/ebay/free-templates/images/temp6/satisfaction.png" alt="" /></div>
<h3>100% SODDISFAZIONE<br /><span>Soddisfazione Garantita</span></h3>
</li>
</ul>
<img src="https://wearephoenixteam.com/ebay/free-templates/images/temp6/gif-img.gif" class="gif_img" alt="" /></section>
<section class="title">';
                $xml .= '<h2>' . $name . '</h2>';
                $xml .= '</section>
<section class="reference">';
                $xml .= '<h2>' . $good->productId . '-' . $good->productVariantId . '</h2>';
                $xml .= '</section>
<section class="ean13">';
                $xml .= '<h2>' . $productEan . '</h2>';
                $xml .= '</section>
<section class="temp_content">
<h2>Descrizione Prodotto</h2>
<div class="description">
<div vocab="https://schema.org/" typeof="Product">
<p>descrizione</p>
</div>';
                $xml .= '<p>' . $name . '<br/></p>';
                $xml .= '</div>
</section>
<section class="vertabs">
<ul class="css_tab">
<li><input id="tab1" class="css-tab" name="tab" checked="checked" type="radio" /> <label for="tab1" class="tab-label">SPEDIZIONI</label>
<div id="tab-content1" class="tab-content">
<h2>MODALITA\'</h2>
<p>ACQUISTA ORA E RICEVI IN 24/48 ORE !!! (sabato e domenica esclusi)</p>
<h2><a name="disponibilita-dei-prodotti"></a>Disponibilit&agrave; dei prodotti</h2>';
                $xml .= '<p>Ricevuto l\'ordine, ' . $addressBook->subject . ' procede, prima della spedizione, al controllo qualit&agrave; e alla conferma mediante e-mail dell\'ordine in lavorazione. Qualora gli articoli ordinati non siano pi&ugrave; disponibili o non abbiano superato il controllo qualit&agrave; ne daremo immediata comunicazione al cliente proponendo possibili alternative.</p>';
                $xml .= '<h2><a name="evasione-degli-ordini"></a>Evasione degli ordini</h2>';
                $xml .= '<p>Gli ordini saranno evasi entro 48 ore dall&rsquo;esito positivo del pagamento. E\' facolt&agrave; ' . $addressBook->subject . ' rifiutare ordini a chiunque per qualsiasi motivo.</p>';
                $xml .= '<h2><a name="modalita-di-spedizione"></a>Modalit&agrave; di spedizione</h2>';
                $xml .= '<p>Tutte le consegne effettuate da ' . $addressBook->subject . '  sono coperte da assicurazione contro il furto e danni accidentali. Ritirata la consegna la copertura assicurativa si estingue.</p>';
                $xml .= '<h2><a name="consegna"></a>Consegna</h2>';
                $xml .= '<p>Gli ordini verranno spediti da ' . $addressBook->subject . ', da Luned&igrave; al Venerd&igrave; dalle 9:00 CET alle 17:30 CET.</p>';
                $xml .= '<p>Gli ordini effettuati durante il fine settimana saranno processati il luned&igrave; successivo.</p>
<p>Gli ordini saranno processati entro le 48 ore dalla ricezione del pagamento per ordini ricevuti entro le ore 11:30 CET.</p>
<p>Informiamo che non effettuiamo spedizioni verso caselle postali.</p>
<h3>Spedizioni</h3>
<table class="table table-hover table-responsive">
<thead>
<tr><th>Paese</th><th>Tempi di consegna</th><th>Costi</th></tr>
</thead>
<tbody>
<tr>
<td>Italia</td>
<td>1-2 giorni lavorativi</td>
<td>5,00 EUR</td>
</tr>
<tr>
<td>Europa</td>
<td>3-5 giorni lavorativi</td>
<td>10,00 EUR</td>
</tr>
<tr>
<td>Altri</td>
<td>5-7 giorni lavorativi</td>
<td>10,00 EUR</td>
</tr>
</tbody>
<tfoot>
<tr>
<td colspan="3"></td>
</tr>
</tfoot>
</table>
<h3>Resi</h3>
<table class="table table-hover table-responsive">
<thead>
<tr><th>Paese</th><th>Costo</th><th>Rimborso</th></tr>
</thead>
<tbody>
<tr>
<td>Italia</td>
<td>customer charged</td>
<td>24h</td>
</tr>
<tr>
<td>Europa</td>
<td>customer charged</td>
<td>24h</td>
</tr>
<tr>
<td>Altri</td>
<td>customer charged</td>
<td>24h</td>
</tr>
</tbody>
<tfoot>
<tr>
<td colspan="3"></td>
</tr>
</tfoot>
</table>
</div>
</li>
<li><input id="tab2" class="css-tab" name="tab" type="radio" /> <label for="tab2" class="tab-label">FEEDBACK</label>
<div id="tab-content2" class="tab-content">
<h2>FEEDBACK</h2>
<p>Lasciaci sempre un Feedback Positivo! Siamo professionisti e siamo sempre disponibili per risolvere e chiarire qualsiasi incomprensione. Lasciare un Feedback negativo o neutro &egrave; inutile e non risolve un eventuale problema!</p>
</div>
</li>
<li><input id="tab3" class="css-tab" name="tab" type="radio" /> <label for="tab3" class="tab-label">PAGAMENTO</label>
<div id="tab-content3" class="tab-content">
<h2><a name="modalita-di-pagamento"></a>Carta di credito</h2>
<p>Accettiamo le seguenti carte di credito o di debito:</p>
<div class="row">
<div class="col-xs-2"><img src="https://www.iwes.it/visa.png" width="100" /></div>
<div class="col-xs-2"><img src="https://www.iwes.it/visae.png" width="100" /></div>
<div class="col-xs-2"><img src="https://www.iwes.it/mastercard.png" width="100" /></div>
<div class="col-xs-2"><img src="https://www.iwes.it/maestro.png" width="100" /></div>
<div class="col-xs-2"><img src="https://www.iwes.it/diners.png" width="100" /></div>
<div class="col-xs-2"><img src="https://www.iwes.it/amex.png" width="100" /></div>
</div>
<p>Tutte le transazioni ed in generale ogni pagina del sito, comprese quelle dedicate alla raccolta dei dati personali per la registrazione e l\'invio degli ordini, sono processate tramite server sicuro con crittografia a 128 bit, garantendo ai clienti Cartechinishop.com la massima protezione dei dati.</p>
<h3>Pagamento con Verified by Visa o Mastercard SecureCode</h3>
<p>Il sistema controlla se la carta utilizza i programmi di sicurezza 3-D-secure oppure si tratta di protocolli di sicurezza elaborati per questo tipo di carte per verificare che la transazione sia effettuata dall&rsquo;effettivo titolare della carta di credito. Se un titolare di carta di credito &egrave; iscritto al programma di sicurezza 3-D-secure, verr&agrave; richiesto di inserire il &ldquo;secure code&rdquo; (un codice scelto da Voi). Solo inserendo il codice esatto, la transazione sar&agrave; eseguita.</p>
<p><i>Se la carta di credito non &egrave; registrata per nessuno di questi procedimenti di sicurezza, il pagamento sar&agrave; effettuato senza alcuna richiesta.</i></p>
<p>Per ulteriori informazioni sui procedimenti di sicurezza per carte di credito vai su: <a href="https://www.visaitalia.com/carte-per-te/acquisti-online/verified-by-visa/" target="_blank">Verified by Visa</a> oppure <a href="http://www.mastercard.com/it/privati/servizi_securecode.html" target="_blank">Mastercard SecureCode</a></p>
<h2><a name="paypal"></a>PayPal</h2>
<p>In caso di pagamento tramite PayPal verrai automaticamente trasferito alla pagina di pagamento PayPal. Se si &egrave; gi&agrave; clienti PayPal, sar&agrave; sufficiente accedere con i propri dati e confermare il pagamento. Se non si possiede un conto PayPal, &egrave; possibile aprirne uno e confermare il pagamento.</p>
<h2><a name="bonifico-bancario"></a>Bonifico bancario</h2>
<p>Durante il processo di acquisto &egrave; possibile scegliere &ldquo;Bonifico Bancario&rdquo; come modalit&agrave; di pagamento. Il Cliente riceve automaticamente una email contenente i dati bancari di Cartechinishop.com. I prodotti ordinati verranno riservati in attesa dell&rsquo;arrivo del bonifico bancario sul conto. Il cliente dovr&agrave; inviare via email copia del pagamento entro 48 ore oltrepassate le quali l&rsquo;ordine verr&agrave; automaticamente cancellato. Scegliendo la &ldquo;modalit&agrave; di pagamento&rdquo; Bonifico Bancario dovr&agrave; trasferire il totale dell&rsquo;ordine al seguente conto bancario, indicando il numero dell&rsquo;ordine:</p>';
                $xml .= '<p>' . $addressBook->subject . '<br /> IBAN:' . $addressBook->iban . '</p>';
                $xml .= '<p>L\'ordine sar&agrave; spedito subito dopo la ricezione dell&rsquo;accredito sul nostro conto bancario.</p>
</div>
</li>
<li><input id="tab4" class="css-tab" name="tab" type="radio" /> <label for="tab4" class="tab-label">RESTITUZIONE</label>
<div id="tab-content4" class="tab-content">
<h2>RETURNS POLICY</h2>
<p>Si accettata la restituzione dell\'oggetto entro 14 gg lavorativi dal ricevimento del prodotto previo accordo e una valida motivazione. Le spese di restituzione merce sono a carico dell\'acquirente. Al ricevimento dell\'oggetto verificheremo l\'integrit&agrave; del prodotto,che dovr&agrave; essere perfettamente integro,mai usato e completo di tutti gli accessori,dopodich&eacute; rimborseremo il solo costo dell\'oggetto. In caso di cambio merce i costi di tutte le spedizioni sono a carico dell\'acquirente</p>
</div>
</li>
<li><input id="tab5" class="css-tab" name="tab" type="radio" /> <label for="tab5" class="tab-label">SOSTITUZIONE</label>
<div id="tab-content5" class="tab-content">
<h2>TERMINI PER LA SOSTITUZIONE</h2>
<p>Copyright &copy; 2015-2018 Iwes</p>
</div>
</li>
<li><input id="tab6" class="css-tab" name="tab" type="radio" /> <label for="tab6" class="tab-label">CANCELLAZIONE</label>
<div id="tab-content6" class="tab-content">
<h2>CANCELLAZIONE</h2>
</div>
</li>
<li><input id="tab7" class="css-tab" name="tab" type="radio" /> <label for="tab7" class="tab-label">CONTATTI</label>
<div id="tab-content7" class="tab-content">
<h2>CONTATTACI</h2>';
                $xml .= '<p>tel. +39 02-379 20 266<br /> &nbsp;mob. +39 327 55 90 989<br /> &nbsp;email.&nbsp; support@iwes.it&nbsp;</p>
</div>
</li>
</ul>
</section>
<section class="footer_features">
<ul>
<li>
<div><img src="https://wearephoenixteam.com/ebay/free-templates/images/temp6/free-ship.png" alt="s" /></div>
<h2>Spedizione Gratuita<br /><br /><span>ordini superiori ai 300 Euro</span></h2>
</li>
<li>
<div><img src="https://wearephoenixteam.com/ebay/free-templates/images/temp6/money-back.png" alt="s" /></div>
<h2>Garanzia Rimborsi<br /><br /><span>1 giorno lavorativo</span></h2>
</li>
<li>
<div><img src="https://wearephoenixteam.com/ebay/free-templates/images/temp6/best-quality.png" alt="s" /></div>
<h2>I Migliori Brand <br /><br /><span>Soddisfazione dei Clienti</span></h2>
</li>
<li>
<div><img src="https://wearephoenixteam.com/ebay/free-templates/images/temp6/paypal.png" alt="s" /></div>
<h2>Accettiamo<br /><br /><span>PayPal </span></h2>
</li>
</ul>
</section>
<footer>
<table valign="center" align="center" width="100%">
<tbody>
<tr>
<td align="left" style="background: #323b47; font-color: #ffffff;">';
                $xml .= '<p>Copyright &copy; ' . $addressBook->subject . ' | All Rights Reserved</p>';
                $xml .= '</td><td align="right" style="background: #323b47;"><img width="150" height="35" src="' . $shop->urlSite . '/assets/img/' . $shop->logoSite . '" /></td>';
                $xml .= '</tr>
</tbody>
</table>
</footer>
<div class="clear"></div>
</div>
</main></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
</body>
</html><div style="font-size: small; visibility:hidden;">
<center>
<span style="font-size: small; color: #28BFB3;">IWES</span>
<em> e-commerce solutions.</em>
</center>
</div>
    </div>
						</td>
	</tr>
</table><div style="font-size: small; visibility:hidden;">
<center>
<span style="font-size: small; color: #28BFB3;">Iwes</span>
<em> e-commerce solutions.</em>
</center>
</div>]]>
    </Description>';

                $xml .= '<Storefront><StoreCategoryID>4895391011</StoreCategoryID></Storefront><PostalCode>62012</PostalCode>';
                $xml .= '<Location>' . $addressBook->city . '</Location>';
                $xml .= '<DispatchTimeMax>2</DispatchTimeMax>';
                $xml .= '<PaymentMethods>PayPal</PaymentMethods>';
                $xml .= '<PayPalEmailAddress>transazioni@cartechinishop.com</PayPalEmailAddress>';
                $xml .= '<SellerProfiles>';
                $xml .= '<SellerPaymentProfile>';
                $xml .= '<PaymentProfileID>142598637016</PaymentProfileID>';
                $xml .= ' <PaymentProfileName>PayPal:Bonifico bancario accettato:Copy (3</PaymentProfileName>';
                $xml .= '</SellerPaymentProfile>';
                $xml .= ' <SellerReturnProfile> 
         <ReturnProfileID>147965984016</ReturnProfileID>
         <ReturnProfileName>Restituzione accettata,Acquirente,30 giorni</ReturnProfileName>
        </SellerReturnProfile> 
        <SellerShippingProfile> 
         <ShippingProfileID>140157110016</ShippingProfileID>
         <ShippingProfileName>Tariffa fissa:Altro corriere Gratis/UE 10/EXUE 40</ShippingProfileName>
        </SellerShippingProfile> 
        </SellerProfiles> ';
                $xml .= '<Site>Italy</Site>';
                $xml .= '</Item>';


                $xml .= '<RequesterCredentials>
    <eBayAuthToken>v^1.1#i^1#r^1#p^3#I^3#f^0#t^Ul4xMF8xOjk3MDVGODY4MkI3QUI4QkZGNzlGRTAwMjQwMjk4NkI4XzBfMSNFXjI2MA==</eBayAuthToken>
  </RequesterCredentials>
  <WarningLevel>High</WarningLevel>
</ReviseFixedPriceItem>';
                $xml = preg_replace(
                    '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'
                    . '|[\x00-\x7F][\x80-\xBF]+'
                    . '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'
                    . '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'
                    . '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
                    '?',
                    $xml
                );

                $xml = preg_replace('/\xE0[\x80-\x9F][\x80-\xBF]' . '|\xED[\xA0-\xBF][\x80-\xBF]/S','?',$xml);
                $devID = '9c29584f-1f9e-4c60-94dc-84f786d8670e';
                $appID = 'VendiloS-c310-4f4c-88a9-27362c05ea78';
                $certID = '3050bb00-db24-4842-999c-b943deb09d1a';
                $siteID = 0;

                $apiUrl = 'https://api.ebay.com/ws/api.dll';
                $apiCall = 'ReviseFixedPriceItem';
                $compatibilityLevel = 741;

                $runame = 'Vendilo_SpA-VendiloS-c310-4-prlqnbrjv';
                $loginURL = 'https://signin.ebay.it/ws/eBayISAPI.dll';

                $headers = array(
                    // Regulates versioning of the XML interface for the API
                    'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $compatibilityLevel,
                    // Set the keys
                    'X-EBAY-API-DEV-NAME: ' . $devID,
                    'X-EBAY-API-APP-NAME: ' . $appID,
                    'X-EBAY-API-CERT-NAME: ' . $certID,
                    // The name of the call we are requesting
                    'X-EBAY-API-CALL-NAME: ' . $apiCall,
                    // SiteID must also be set in the Request's XML
                    // SiteID = 0 (US) - UK = 3, Canada = 2, Australia = 15, ....
                    // SiteID Indicates the eBay site to associate the call with
                    'X-EBAY-API-SITEID: ' . $siteID
                );
                try {
                    $connection = curl_init();
                    curl_setopt($connection,CURLOPT_URL,$apiUrl);

                    curl_setopt($connection,CURLINFO_HEADER_OUT,true);
// Stop CURL from verifying the peer's certificate
                    curl_setopt($connection,CURLOPT_SSL_VERIFYPEER,0);
                    curl_setopt($connection,CURLOPT_SSL_VERIFYHOST,0);

// Set the headers (Different headers depending on the api call !)

                    curl_setopt($connection,CURLOPT_HTTPHEADER,$headers);

                    curl_setopt($connection,CURLOPT_POST,1);

// Set the XML body of the request
                    curl_setopt($connection,CURLOPT_POSTFIELDS,$xml);

// Set it to return the transfer as a string from curl_exec
                    curl_setopt($connection,CURLOPT_RETURNTRANSFER,1);

// Send the Request
                    $response = curl_exec($connection);


                    $this->report('CEbayReviseProductJob','Report Response ' . $good->refMarketplaceId,$response);
                    sleep(1);

                    $responseNewProduct = new \SimpleXMLElement($response);

                    $id_product_ref = $responseNewProduct->ItemID;
                    if($responseNewProduct->Ack=='Success') {


                        $good->lastUpdate = $product->lastUpdate;
                        $good->result = 1;
                        $good->lastTypeOperation = 1;
                        $good->update();
                        $this->report('CEbayReviseProductJob','Report  Revise ' . $good->refMarketplaceId,$xml);
                        sleep(1);
                    }elseif($responseNewProduct->Ack=='Warning') {
                        $good->lastUpdate = $product->lastUpdate;
                        $good->result = 1;
                        $good->lastTypeOperation = 1;
                        $good->update();
                        $this->report('CEbayReviseProductJob','Report  Revise ' . $good->refMarketplaceId,$xml);
                        sleep(1);
                    } else{
                        $good->result=0;
                        $good->update();
                        $this->report('CEbayReviseProductJob','Error api call  Revise ' . $good->refMarketplaceId,$xml);
                        sleep(1);
                    }

                } catch (\Throwable $e) {
                    $this->report('CEbayReviseProductJob','Error',$e->getLine() . '-' . $e->getMessage());

                }
            }


        }

    }
}
<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\repositories\CEmailRepo;
use \Throwable;
use PDO;

/**
 * Class CEbayNewAddProductJob
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
class CEbayNewAddProductJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->AddProductsInEbay();
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    private function AddProductsInEbay()
    {
        $xml = '';

        $shopActive = \Monkey::app()->repoFactory->create('Shop')->findBy(['hasEcommerce' => '1']);

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
            $goods = $productInMarketplaceRepo->findBy(['isPublished' => 2,'marketplaceHasShopId' => $marketplaceAccount->config['marketplaceHasShopId']]);

            $shop = $shopRepo->findOneBy(['id' => $marketplaceAccount->config['shopId']]);
            $addressBook = $addressBookRepo->findOneBy(['id' => $shop->billingAddressBookId]);
            foreach ($goods as $good) {
                if($good->refMarketplaceId=='') {
                    /**  @var CProduct $product * */
                    $product = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $good->productId,'productVariantId' => $good->productVariantId]);
                    if ($product->qty == 0) {
                        continue;
                    }
                    $productCategory = \Monkey::app()->repoFactory->create('ProductHasProductCategory')->findOneBy(['productId' => $good->productId,'productVariantId' => $good->productVariantId]);
                    $productCategoryId = $productCategory->productCategoryId;
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
                        $productEanSelect = $productEanRepo->findOneBy(['used' => 0]);
                        $productEan = $productEanSelect->ean;
                        $productEanSelect->productId = $good->productId;
                        $productEanSelect->productVariantId = $good->productVariantId;
                        $productEanSelect->productSizeId = 0;
                        $productEanSelect->usedForParent = 1;
                        $productEanSelect->brandAssociate = $product->productBrandId;
                        $productEanSelect->shopId = $marketplaceAccount->config['shopId'];
                        $productEanSelect->update();
                    }

                    $productSizeGroup = $productSizeGroupRepo->findOneBy(['id' => $product->productSizeGroupId]);
                    $productSizeGroupHasProductSize = $productSizeGroupHasProductSizeRepo->findBy(['ProductSizeGroupId' => $productSizeGroup->id]);
                    $phphmhsRepo = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');
                    //etichetta taglie
                    $isOnSale = $good->isOnSale;

                    $xml = '';
                    $xml .= '<?xml version="1.0" encoding="utf-8"?>';
                    $xml .= '<AddFixedPriceItem xmlns="urn:ebay:apis:eBLBaseComponents">';
                    $xml .= '<ErrorLanguage>it_IT</ErrorLanguage>';
                    $xml .= '<WarningLevel>High</WarningLevel>';
                    //intestazione prodotto
                    $xml .= '<Item>';
                    $xml .= '<AutoPay>false</AutoPay>';
                    $xml .= '<Country>IT</Country>';
                    $xml .= '<Currency>EUR</Currency>';
                    $xml .= '<ListingDuration>GTC</ListingDuration>';
                    $xml .= '<ListingType>FixedPriceItem</ListingType>';
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
                    $xml .= '<VariationSpecificsSet>';
                    $xml .= '<NameValueList>';
                    $xml .= '<Name>Taglia</Name>';
                    foreach ($productSku as $sku) {
                        if ($sku->stockQty > 0) {
                            $productSizeColl = $productSizeRepo->findOneBy(['id' => $sku->productSizeId]);
                            $xml .= '<Value>' . $productSizeColl->name . '</Value>';
                        }
                    }
                    $xml .= '</NameValueList>';
                    $xml .= '<NameValueList>';
                    $xml .= '<Name>Color</Name>';
                    $xml .= '<Value>' . $product->productColorGroup->name . '</Value>';
                    $xml .= '</NameValueList>';
                    $xml .= '</VariationSpecificsSet>';
                    foreach ($productSku as $sku) {
                        if ($sku->stockQty > 0) {
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
                            $findSkuEan = $productEanRepo->findOneBy(['productId' => $sku->productId,'productVariantId' => $sku->productVariantId,'productSizeId' => $sku->productSizeId]);
                            if ($findSkuEan) {
                                $productSkuEan = $findSkuEan->ean;
                            } else {
                                $productSkuEanSelect = $productEanRepo->findOneBy(['used' => 0]);
                                $productSkuEan = $productSkuEanSelect->ean;
                                $productSkuEanSelect->productId = $sku->productId;
                                $productSkuEanSelect->productVariantId = $sku->productVariantId;
                                $productSkuEanSelect->productSizeId = $sku->productSizeId;
                                $productSkuEanSelect->usedForParent = 0;
                                $productSkuEanSelect->used = 1;
                                $productSkuEanSelect->brandAssociate = $product->productBrandId;
                                $productSkuEanSelect->shopId = $marketplaceAccount->config['shopId'];
                                $productSkuEanSelect->update();
                            }

                            $xml .= '<EAN>n/a</EAN>';
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

                        }
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
      </NameValueList>
        <NameValueList>
        <Name><![CDATA[Stile]]></Name>
        <Value><![CDATA[non applicabile]]></Value>
      </NameValueList>';
                    $xml .= '<NameValueList>';
                    $xml .= '<Name><![CDATA[Vintage]]></Name>';
                    $xml .= '<Value><![CDATA[non applicabile]]></Value>';
                    $xml .= '</NameValueList>';
                    $xml .= '</ItemSpecifics>';

                    $xml .= '<ConditionID>1000</ConditionID>';
                    /*  if ($good->titleModified == "1" && $good->isOnSale == "1") {
                          $percSc = number_format(100 * ($good->price - $good->salePrice) / $good->price,0);
                          $name = $product->productBrand->name
                              . ' Sconto del ' . $percSc . '% da ' . number_format($good->price,'2','.','') . ' € a ' . number_format($good->salePrice,'2','.','')
                              . ' € ' .
                              $product->itemno
                              . ' ' .
                              $product->productColorGroup->productColorGroupTranslation->findOneByKey('langId',1)->name;
                          $xml .= '<Title><![CDATA[' . $name . ']]></Title>';
                      } else {*/

                    $name = \Monkey::app()->repoFactory->create('ProductCategoryTranslation')->findOneBy(['langId' => 1,'productCategoryId' => $productCategoryId,'shopId' => 44])->name
                        . ' ' .
                        $product->productBrand->name
                        . ' ' .
                        $product->itemno
                        . ' ' .
                        $product->productColorGroup->productColorGroupTranslation->findOneByKey('langId',1)->name;


                    $xml .= '<Title><![CDATA[' . $name . ']]></Title>';
                    // }
                    $xml .= '<Description>';
                    $xml .= '<![CDATA[' . $name . ']]>
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
    <eBayAuthToken>v^1.1#i^1#p^3#I^3#f^0#r^1#t^Ul4xMF81Ojk2MjY2OENBNzQxNUNDOTYwQjE0NDQ3Q0IwQUJDNEREXzFfMSNFXjI2MA==</eBayAuthToken>
  </RequesterCredentials>
  <WarningLevel>High</WarningLevel>
</AddFixedPriceItem>';
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
                    $siteID = 101;

                    $apiUrl = 'https://api.ebay.com/ws/api.dll';
                    $apiCall = 'AddFixedPriceItem';
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
                    $refMarketplaceId = '';
                    $resultCall = 0;
                    try {
                        $connection = curl_init();
                        curl_setopt($connection,CURLOPT_URL,$apiUrl);

                        curl_setopt($connection,CURLINFO_HEADER_OUT,true);
// Stop CURL from verifying the peer's certificate


// Set the headers (Different headers depending on the api call !)

                        curl_setopt($connection,CURLOPT_HTTPHEADER,$headers);

                        curl_setopt($connection,CURLOPT_POST,1);

// Set the XML body of the request
                        curl_setopt($connection,CURLOPT_POSTFIELDS,$xml);

// Set it to return the transfer as a string from curl_exec
                        curl_setopt($connection,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($connection,CURLOPT_SSL_VERIFYPEER,0);
                        curl_setopt($connection,CURLOPT_SSL_VERIFYHOST,0);
// Send the Request
                        $response = curl_exec($connection);
                        //$xmlresponse = new \SimpleXMLElement($response);


                        $this->report('CEbayAddProductJob','Report ',$response);
                        sleep(1);

                        $responseNewProduct = new \SimpleXMLElement($response);
                        if ($responseNewProduct->Ack == 'Success') {
                            $this->report('CEbayNewAddProductJob','Report ',$xml);
                            $refMarketplaceId = $responseNewProduct->ItemID;
                            $today = new \DateTime();
                            $now = $today->format('Y-m-d H:i:s');
                            $resultCall = 1;

                            \Monkey::app()->dbAdapter->update('PrestashopHasProductHasMarketplaceHasShop',['refMarketplaceId' => (string) $responseNewProduct->ItemID,'result' => $resultCall],
                                ['productId' => $good->productId,'productVariantId' => $good->productVariantId,'marketplaceHasShopId' => $marketplaceAccount->config['marketplaceHasShopId']]);
                            $this->report('CEbayNewAddProductJob','Report  Add ' . $refMarketplaceId,$xml);
                            sleep(1);

                        } elseif ($responseNewProduct->Ack == 'Warning') {
                            $this->report('CEbayNewAddProductJob','Report ',$xml);
                            $refMarketplaceId = $responseNewProduct->ItemID;
                            $today = new \DateTime();
                            $now = $today->format('Y-m-d H:i:s');


                            $resultCall = 1;
                            \Monkey::app()->dbAdapter->update('PrestashopHasProductHasMarketplaceHasShop',['refMarketplaceId' =>  (string) $responseNewProduct->ItemID,'result' => $resultCall],
                                ['productId' => $good->productId,'productVariantId' => $good->productVariantId,'marketplaceHasShopId' => $marketplaceAccount->config['marketplaceHasShopId']]);
                            /** @var CEmailRepo $emailRepo */
                            $emailRepo = \Monkey::app()->repoFactory->create('Email');
                            $emailRepo->newMail('it@iwes.it', ['gianluca@iwes.it'],[],[],'CEbayNewAddProductJob Add', 'prodotto:'.$good->productId . '-' . $good->productVariantId.(string) $responseNewProduct->ItemID, null, null, null, 'mailGun', false,null);
                            $emailRepo = \Monkey::app()->repoFactory->create('Email');
                            $emailRepo->newMail('it@iwes.it', ['juri@iwes.it'],[],[],'CEbayNewAddProductJob Add ', 'prodotto:'.$good->productId . '-' . $good->productVariantId.(string) $responseNewProduct->ItemID, null, null, null, 'mailGun', false,null);
                            $this->report('CEbayNewAddProductJob','Report  Add ' . $refMarketplaceId,$xml);
                            sleep(1);

                        } else {

                            $refMarketplaceId = '';
                            $resultCall = 0;
                            \Monkey::app()->dbAdapter->update('PrestashopHasProductHasMarketplaceHasShop',['refMarketplaceId' => $refMarketplaceId,'result' => $resultCall],
                                ['productId' => $good->productId,'productVariantId' => $good->productVariantId,'marketplaceHasShopId' => $marketplaceAccount->config['marketplaceHasShopId']]);

                            $this->report('CEbayNewAddProductJob','Error api Call ' . $good->productId . '-' . $good->productVariantId,$xml);
                            /** @var CEmailRepo $emailRepo */
                           /* $emailRepo = \Monkey::app()->repoFactory->create('Email');
                            $emailRepo->newMail('it@iwes.it', ['gianluca@iwes.it'],[],[],'CEbayNewAddProductJob Error', 'prodotto:'.$good->productId . '-' . $good->productVariantId.$xml, null, null, null, 'mailGun', false,null);
                            $emailRepo = \Monkey::app()->repoFactory->create('Email');
                            $emailRepo->newMail('it@iwes.it', ['juri@iwes.it'],[],[],'CEbayNewAddProductJob Error ', 'prodotto:'.$good->productId . '-' . $good->productVariantId.$xml, null, null, null, 'mailGun', false,null);*/



                        }
                    } catch (\Throwable $e) {
                        /** @var CEmailRepo $emailRepo */
                        /*$emailRepo = \Monkey::app()->repoFactory->create('Email');
                        $emailRepo->newMail('it@iwes.it', ['gianluca@iwes.it'],[],[],'CEbayNewAddProductJob Error', $e->getLine() . '-' . $e->getMessage(), null, null, null, 'mailGun', false,null);
                        $emailRepo = \Monkey::app()->repoFactory->create('Email');
                        $emailRepo->newMail('it@iwes.it', ['juri@iwes.it'],[],[],'CEbayNewAddProductJob Error ', $e->getLine() . '-' . $e->getMessage(), null, null, null, 'mailGun', false,null);*/
                        $this->report('CEbayNewAddProductJob','Error',$e->getLine() . '-' . $e->getMessage());

                    }
                }



            }


        }

    }
}
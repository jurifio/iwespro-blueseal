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
 * Class CEbayCloseProductJob
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
class CEbayCloseProductJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->report('CEbayCloseProductJob','start','');
        try {
            $productRepo = \Monkey::app()->repoFactory->create('Product');
            $phphmhsRepo = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');
            $marketplaceAccounts = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findBy(['marketplaceId' => 3,'isActive' => 1]);
            foreach ($marketplaceAccounts as $marketplaceAccount) {
                $phphmhs = $phphmhsRepo->findBy(['isPublished'=>1]);
                foreach ($phphmhs as $productInMarketplace) {
                    $product = $productRepo->findOneBy(['id' => $productInMarketplace->productId,'productVariantId' => $productInMarketplace->productVariantId,'marketplaceHasShopId' => $marketplaceAccount->config['marketplaceHasShopId']]);
                    if ($product->qty == 0) {
                        $productInMarketplace->isPublished = 0;
                        $productInMarketplace->update();
                        $request = '<?xml version="1.0" encoding="utf-8"?>';
                        $request .='<EndItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
  $request .='<RequesterCredentials>';
    $request .='<eBayAuthToken>v^1.1#i^1#r^1#p^3#I^3#f^0#t^Ul4xMF8xOjk3MDVGODY4MkI3QUI4QkZGNzlGRTAwMjQwMjk4NkI4XzBfMSNFXjI2MA==</eBayAuthToken>';
  $request .='</RequesterCredentials>';
                        $request .='<ItemID>' . $productInMarketplace->refMarketplaceId . '</ItemID>';
                        $request .='<EndingReason>NotAvailable</EndingReason>';
                        $request .='<ErrorLanguage>it_IT</ErrorLanguage>';

                        $request .='</EndItemRequest>';
                        $request = preg_replace(
                            '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'
                            . '|[\x00-\x7F][\x80-\xBF]+'
                            . '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'
                            . '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'
                            . '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
                            '?',
                            $request
                        );

                        $request = preg_replace('/\xE0[\x80-\x9F][\x80-\xBF]' . '|\xED[\xA0-\xBF][\x80-\xBF]/S','?',$request);
                        $devID = '9c29584f-1f9e-4c60-94dc-84f786d8670e';
                        $appID = 'VendiloS-c310-4f4c-88a9-27362c05ea78';
                        $certID = '3050bb00-db24-4842-999c-b943deb09d1a';
                        $siteID = 101;

                        $apiUrl = 'https://api.ebay.com/ws/api.dll';
                        $apiCall = 'EndItemRequest';
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
                            curl_setopt($connection,CURLOPT_POSTFIELDS,$request);

// Set it to return the transfer as a string from curl_exec
                            curl_setopt($connection,CURLOPT_RETURNTRANSFER,1);

// Send the Request
                            $response = curl_exec($connection);
                            $this->report('CEbayCloseProductJob','Report ',$response);

                        }catch(\Throwable $e){
                            $this->report('CEbayCloseProductJob','error call','Depublish '.$productInMarketplace->productId.'-'.$productInMarketplace->productVariantId.'-'.$productInMarketplace->refMarketplaceId);
                        }


                        $this->report('CEbayCloseProductJob','Success','Depublish '.$productInMarketplace->productId.'-'.$productInMarketplace->productVariantId.'-'.$productInMarketplace->refMarketplaceId);
                    }
                }
            }

        }catch(\Throwable $e) {
            $this->report('CEbayCloseProductJob','ERROR',$e->getMessage().'-'.$e->getLine());

        }
        $this->report('CEbayCloseProductJob','End','');

    }


}
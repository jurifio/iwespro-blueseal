<?php
namespace bamboo\controllers\back\ajax;
use bamboo\amazon\business\builders\AAmazonFeedBuilder;
use bamboo\amazon\business\builders\CAmazonImageFeedBuilder;
use bamboo\amazon\business\builders\CAmazonInventoryFeedBuilder;
use bamboo\amazon\business\builders\CAmazonPricingFeedBuilder;
use bamboo\amazon\business\builders\CAmazonProductFeedBuilder;
use bamboo\amazon\business\builders\CAmazonRelationshipFeedBuilder;
use bamboo\domain\entities\CMarketplaceAccountHasProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\repositories\CMarketplaceAccountHasProductRepo;
use  \Swagger\Client\Configuration;
use  \Swagger\Client\Api\OrdersV0Api;
use  \GuzzleHttp\Client;


/**
 * Class CAmazonNewAddProductAjaxController
 * @package bamboo\controllers\back\ajax
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
class CAmazonNewAddProductAjaxController extends AAjaxController
{


    public function post()
    {
        \Monkey::app()->vendorLibraries->load('amazonsapi');
        $urlInsert = "https://api.amazon.com/auth/o2/token";
        $data = array("grant_type" => "refresh_token","refresh_token" => "Atzr|IwEBIFEh8rsv4QOPx-i4zw1WuEHIzTf7Mdrz9eE3uRsrqJjyTdJ1OtmhMQSSrkkws2hpTNow3yiDBZvpe8ZXwq3pheu7U0qyS7UYErcAeAxDnUq3BkATKAy5ziD5rubs4yFfD17yOx7FVyP4AgpNp50miQe-26xRNGyaHrKhCafeEjSFhWe5Msto9DW5fNQOdyAXOQOB2kYjATC6y1hn_OhVPBEjFZOG6GRphAwOK8j-jUiHZGNAfBGBRURoByW5LRhny1gxPRUmFDVjKIS20UJSA2CZEEap-cV6a8PujG7yDmBe4HxB9r_-XFcwjpi415IqN3w","client_id"=>"amzn1.application-oa2-client.1cf3ee13dbbe435caadced510a94f1f1","client_secret" => "2574f54cc10b20c7a814a2c81df7fcbd117c28ae02e3c95e42d43e04a36e4d8e");

        $postdata = json_encode($data);

        $ch = curl_init($urlInsert);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($ch);
        curl_close($ch);
        $res=json_decode($result);
        $nextToken= $res->access_token;


        $options = [
            'refresh_token' => $res->refresh_token,
            'client_id' => 'amzn1.application-oa2-client.1cf3ee13dbbe435caadced510a94f1f1',
            'client_secret' => '2574f54cc10b20c7a814a2c81df7fcbd117c28ae02e3c95e42d43e04a36e4d8e',
            'region' =>  'eu-west-1',
            'access_key' => 'AKIAWP2CM7DXOW6TRZXL',
            'secret_key' => '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',
            'endpoint' =>  'https://sellingpartnerapi-eu.amazon.com',
            'role_arn' => 'arn:aws:iam::446278990062:user/PartnerRoleAPI',
            'marketplaceId' =>'APJ6JRA9NG5V4'
        ];
      //  \Monkey::app()->vendorLibraries->load('amazonMWS');
        $sql = "SELECT 	marketplaceAccountId as id,
						marketplaceId
				FROM 	MarketplaceAccountHasProduct mahp, 
						Marketplace m 
				WHERE 	m.id = mahp.marketplaceId 
					AND m.name = 'Amazon' and mahp.isToWork = 1 AND mahp.marketplaceAccountId=42
					GROUP BY marketplaceId,
					marketplaceAccountId";

        $marketplaceAccounts = \Monkey::app()->repoFactory->create('MarketplaceAccount')->em()->findBySql($sql,[]);

        foreach ($marketplaceAccounts as $marketplaceAccount) {
            $messageId=1;
            try {
                $sql = "SELECT 	productId, 
						productVariantId, 
						marketplaceId,
						marketplaceAccountId 
				FROM 	MarketplaceAccountHasProduct mahp, 
						Marketplace m 
				WHERE 	m.id = mahp.marketplaceId 
					AND m.name = 'Amazon' and mahp.isToWork = 1 and mahp.marketplaceAccountId = 42";
                $res = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct')->em()->findBySql($sql,[]);

                foreach ($res as $re) {
                    $this->prepareSkus($re);
                }
                $productFeed="productFeed";
                $product = new CAmazonProductFeedBuilder($this->app);
                $this->prepareAndSend($marketplaceAccount,$product,$res,$productFeed,$messageId);
                $inventoryFeed="inventoryFeed";
                $inventary = new CAmazonInventoryFeedBuilder($this->app);
                $this->prepareAndSend($marketplaceAccount,$inventary,$res,$inventoryFeed,$messageId);
                $pricingFeed = "pricingFeed";
                $pricing = new CAmazonPricingFeedBuilder($this->app);
                $this->prepareAndSend($marketplaceAccount,$pricing,$res,$pricingFeed,$messageId);
                $imageFeed="imageFeed";
                $image = new CAmazonImageFeedBuilder($this->app);
                $this->prepareAndSend($marketplaceAccount,$image,$res,$imageFeed,$messageId);
                $relationshipFeed="relationshipFeed";
                $relationship = new CAmazonRelationshipFeedBuilder($this->app);
                $this->prepareAndSend($marketplaceAccount,$relationship,$res,$relationshipFeed,$messageId);
                $messageId++;
            } catch
            (\Throwable $e) {
                \Monkey::app()->applicationLog('CAmazonAddProductJob','ERROR',$e->getLine(),$e->getMessage(),$e->getFile());
            }

            return $messageId;
        }
    }
    protected function prepareAndSend($marketplaceAccount, AAmazonFeedBuilder $builder,$products,$typeFeed,$messageId) {
        \Monkey::app()->vendorLibraries->load('amazonsapi');
//require_once('/media/sf_sites/vendor/vendor/autoload.php');
        $urlInsert = "https://api.amazon.com/auth/o2/token";
        $data = array("grant_type" => "refresh_token","refresh_token" => "Atzr|IwEBIFEh8rsv4QOPx-i4zw1WuEHIzTf7Mdrz9eE3uRsrqJjyTdJ1OtmhMQSSrkkws2hpTNow3yiDBZvpe8ZXwq3pheu7U0qyS7UYErcAeAxDnUq3BkATKAy5ziD5rubs4yFfD17yOx7FVyP4AgpNp50miQe-26xRNGyaHrKhCafeEjSFhWe5Msto9DW5fNQOdyAXOQOB2kYjATC6y1hn_OhVPBEjFZOG6GRphAwOK8j-jUiHZGNAfBGBRURoByW5LRhny1gxPRUmFDVjKIS20UJSA2CZEEap-cV6a8PujG7yDmBe4HxB9r_-XFcwjpi415IqN3w","client_id"=>"amzn1.application-oa2-client.1cf3ee13dbbe435caadced510a94f1f1","client_secret" => "2574f54cc10b20c7a814a2c81df7fcbd117c28ae02e3c95e42d43e04a36e4d8e");

        $postdata = json_encode($data);

        $ch = curl_init($urlInsert);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($ch);
        curl_close($ch);
        $res=json_decode($result);
        $nextToken= $res->access_token;


        $options = [
            'refresh_token' => 'Atzr|IwEBIHwArIJrxq-WUKNFNZIaBQJbR4DFDyo_EgkFTee9ShtJDRRxDyBwcmbeWhKiX7M_VV1KBDGPb0Edwq1UxvuXGa1dFAv9XvtUsUizlWxjwjheP4Uo1jC44YC6sFbm0ZVonn_dcGIh5MgM4q0sE7pDxwZ6G_uW99XFh5m8cLrHvHgWitzCiZxXl277cdU9inDQgtQUZxv7zLbL6ARI4idctdc3mtO6mNLMuCkiebzT6GobKJmFXxsxmV2wo5hTYctCfMg6RkTzxtxJ2tvfLaP5BOiIh-WGwcrx7ZsdMYuTK4c0nC5Yl9xTnj4zVJ7S_G1dhsQ',
            'client_id' => 'amzn1.application-oa2-client.1cf3ee13dbbe435caadced510a94f1f1',
            'client_secret' => '2574f54cc10b20c7a814a2c81df7fcbd117c28ae02e3c95e42d43e04a36e4d8e',
            'region' =>  'eu-west-1',
            'access_key' => 'AKIAWP2CM7DXOW6TRZXL',
            'secret_key' => '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',
            'endpoint' =>  'https://sellingpartnerapi-eu.amazon.com',
            'role_arn' => 'arn:aws:iam::446278990062:user/PartnerRoleAPI',
            'marketplaceId' =>'APJ6JRA9NG5V4'
        ];


        $config = \Swagger\Client\Configuration::getDefaultConfiguration();
        $config->setAccessToken($nextToken); //access token of Selling Partner
        $config->setApiKey("accessKey", $options['access_key']); // Access Key of IAM
        $config->setApiKey("secretKey",  $options['secret_key']); // Secret Key of IAM
        $config->setApiKey("region", $options['region']); //region of MarketPlace country
        $apiInstance = new \Swagger\Client\Api\FeedsApi(
// If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
// This is optional, `GuzzleHttp\Client` will be used as default.
            new \GuzzleHttp\Client(),
            $config
        );
        $content_type=['content_type'=>'text/xml; charset=UTF-8'];
        $body = new \Swagger\Client\Models\CreateFeedSpecification($content_type);
        try {
            $result = $apiInstance->createFeed($body);
            return print_r($result);
        } catch (Exception $e) {
            echo 'Exception when calling FeedsApi->createFeed: ', $e->getMessage(), PHP_EOL;
        }


        $apiInstance = new \Swagger\Client\Api\FeedsApi(
// If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
// This is optional, `GuzzleHttp\Client` will be used as default.
            new \GuzzleHttp\Client(),
            $config
        );
        $body = new \Swagger\Client\Models\CreateFeedDocumentSpecification(['content_type'=>'text/xml; charset=UTF-8']);
        try {
            $result = $apiInstance->createFeed($body);
            return print_r($result);
        } catch (Exception $e) {
            echo 'Exception when calling FeedsApi->createFeed: ', $e->getMessage(), PHP_EOL;
        }
        $content = $builder->prepare($products,false)->getRawBody();

        $x = new \XMLWriter();
        $x->openMemory();
        $x->setIndent(false);
        $x->startDocument();
        $x->startElement('AmazonEnvelope');
        $x->writeAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
        $x->writeAttribute("xsi:noNamespaceSchemaLocation","amzn-envelope.xsd");
        $x->startElement('Header');
        $x->writeElement('DocumentVersion','1.01');
        $x->writeElement('MerchantIdentifier',$marketplaceAccount->config['merchantIdentifier']);
        $x->endElement();
        $x->writeRaw($content);
        $x->endElement();
        $x->endDocument();
        $content = $x->outputMemory();
        echo $content;
        $feedHandle = @fopen('php://temp', 'rw+');
        fwrite($feedHandle, $content);
        $dateNow=(new \DateTime())->format('Y-m-d_His');
        $myfile = fopen("/media/sf_sites/iwespro/temp/".$typeFeed."_".$messageId."_".$dateNow.".xml", "w");
        fwrite($myfile, $content);
        fclose($myfile);
        rewind($feedHandle);
        //CREATEFEEDOCUMENT//

        //$marketplaceIdList=explode(',',$marketplaceAccount->config['marketplaceIdList']);
        $parameters = array (
            'marketplace_ids' =>  ["APJ6JRA9NG5V4","A13V1IB3VIYZZH","A1RKKUPIHCS9HS","A1805IZSGTT6HS","A1PA6795UKMFR9","A1F83G8C2ARO7P"],
            'feed_type' => $builder->getFeedTypeName(),
            'input_feed_document_id' => base64_encode(md5(stream_get_contents($feedHandle), true)),
            'feed_options'=>null
        );


        fclose($feedHandle);
        return true;
    }

    public function prepareSkus(CMarketplaceAccountHasProduct $marketplaceAccountHasProduct)
    {
        $sizesDone = [];
        foreach ($marketplaceAccountHasProduct->product->productSku as $sku) {
            if(empty($sku->ean)) {
                \Monkey::app()->repoFactory->create('ProductSku')->assignNewEan($sku);
            }
            $marketSku = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProductSku')->getEmptyEntity();
            $marketSku->productSizeId = $sku->productSizeId;
            $marketSku->productId = $sku->productId;
            $marketSku->productVariantId = $sku->productVariantId;
            $marketSku->marketplaceId = $marketplaceAccountHasProduct->marketplaceId;
            $marketSku->marketplaceAccountId = $marketplaceAccountHasProduct->marketplaceAccountId;
            $existingMarket = $marketSku->em()->findOneBy($marketSku->getIds());
            if (is_null($existingMarket)) {
                $sizesDone[$sku->productSizeId] = $sku->stockQty;
                $marketSku->insert();
            } else {
                //$sizesDone[$sku->productSizeId] += $sku->stockQty;
                //$existingMarket->update();
            }
        }
    }
}
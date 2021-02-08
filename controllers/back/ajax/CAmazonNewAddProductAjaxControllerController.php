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
use function \Swagger\Client\Configuration;
use function \Swagger\Client\Api\OrdersV0Api;
use  \GuzzleHttp\Client;


/**
 * Class CAmazonNewAddProductAjaxControllerController
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
class CAmazonNewAddProductAjaxControllerController extends AAjaxController
{


    public function post()
    {
        \Monkey::app()->vendorLibraries->load('amazonapi');
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
            'refresh_token' => 'Atzr|IwEBIMvivrdQS16D-QuUqy_Sa2ToU2YNHadOUnYN1aa7hIaaFtHEW5ON595uHzHMBPP7UUI8AP6RqCLA-CVdEZrB3iv4yXKkdI7FwzdPfXU5QBPLfglkB3AcvX82GJwTibSttfwPlvROYqxt6bKaiEPnqScjtub1YM42YKKvkIZa00QnEtEcy50eyJqJdWipaFF_eK1FyzuJAy7HlYuDDz0aWyiHZn_gTm4sZC5HEcw7R2HSmX1Sjsg2VFu_-fQu7YYn2ctC8vfQorU1vdihSYnLqAGTg4_bPecGEH7-HzQg2qawHWwl2VTZBjsMjR8Q4trVo8E',
            'client_id' => 'amzn1.application-oa2-client.1cf3ee13dbbe435caadced510a94f1f1',
            'client_secret' => '2574f54cc10b20c7a814a2c81df7fcbd117c28ae02e3c95e42d43e04a36e4d8e',
            'region' =>  'eu-west-1',
            'access_key' => 'AKIAWP2CM7DXFLQPFIUR',
            'secret_key' => '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',
            'endpoint' =>  'https://sandbox.sellingpartnerapi-eu.amazon.com',
            'role_arn' => 'arn:aws:iam::446278990062:role/PartnerRoleAPI',
        ];

        $config = \Swagger\Client\Configuration::getDefaultConfiguration();
        $config->setAccessToken($nextToken); //access token of Selling Partner
        $config->setApiKey("accessKey", $options['access_key']); // Access Key of IAM
        $config->setApiKey("secretKey",  $options['secret_key']); // Secret Key of IAM
        $config->setApiKey("region", $options['region']); //region of MarketPlace country

        $apiInstance = new \Swagger\Client\Api\OrdersApi(
        // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
        // This is optional, `GuzzleHttp\Client` will be used as default.
            new \GuzzleHttp\Client(),
            $config
        );
        $order_id = "404-4277642-2902732"; // string | An Amazon-defined order identifier, in 3-7-7 format.

        try {
            $result = $apiInstance->getOrder($order_id);
            print_r($result);
        } catch (Exception $e) {
            echo 'Exception when calling OrdersV0Api->getOrder: ', $e->getMessage(), PHP_EOL;
        }
    }
}
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

/**
 * Class CAmazonAddProductAjaxControllerController
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
class CAmazonAddProductAjaxController extends AAjaxController
{


    public function post()
    {
//seleziono gli account che hanno amazon
        \Monkey::app()->vendorLibraries->load('amazonMWS');
        $sql = "SELECT 	marketplaceAccountId as id,
						marketplaceId
				FROM 	MarketplaceAccountHasProduct mahp, 
						Marketplace m 
				WHERE 	m.id = mahp.marketplaceId 
					AND m.name = 'Amazon' and mahp.isToWork = 1 AND mahp.marketplaceAccountId=42
					GROUP BY marketplaceId,
					marketplaceAccountId";

        $marketplaceAccounts = \Monkey::app()->repoFactory->create('MarketplaceAccount')->em()->findBySql($sql,[]);
        //ciclo gli account
        foreach ($marketplaceAccounts as $marketplaceAccount) {
            $messageId=1;
            //inizio routine
            try {
                //seleziono i prodotti che sono eletti nel marketplace
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
        \Monkey::app()->vendorLibraries->load('amazonMWS');
        $service = new \MarketplaceWebService_Client(
            $marketplaceAccount->config['awsAccessKeyId'],
            $marketplaceAccount->config['awsSecretAccessKey'],
            ["ServiceURL"=>$marketplaceAccount->config['serviceUrl'],],
            "BlueSeal",
            "1.01");

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
        //$marketplaceIdList=explode(',',$marketplaceAccount->config['marketplaceIdList']);
        $parameters = array (
            'Merchant' => $marketplaceAccount->config['merchantIdentifier'],
            'MarketplaceIdList' => ["Id" => 'APJ6JRA9NG5V4'],
            'FeedType' => $builder->getFeedTypeName(),
            'FeedContent' => $feedHandle,
            'PurgeAndReplace' => false,
            'ContentMd5' => base64_encode(md5(stream_get_contents($feedHandle), true))
        );
        if(isset($marketplaceAccount->config['MWSAuthToken']) && !empty($marketplaceAccount->config['MWSAuthToken'])){
            $parameters['MWSAuthToken'] = $marketplaceAccount->config['MWSAuthToken']; // Optional]
        }
        rewind($feedHandle);
        $request = new \MarketplaceWebService_Model_SubmitFeedRequest($parameters);

        try {
            $response = $service->submitFeed($request);

            echo ("Service Response\n");
            echo ("=============================================================================\n");

            echo("        SubmitFeedResponse\n");
            if ($response->isSetSubmitFeedResult()) {
                echo("            SubmitFeedResult\n");
                $submitFeedResult = $response->getSubmitFeedResult();
                if ($submitFeedResult->isSetFeedSubmissionInfo()) {
                    echo("                FeedSubmissionInfo\n");
                    $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
                    if ($feedSubmissionInfo->isSetFeedSubmissionId())
                    {
                        echo("                    FeedSubmissionId\n");
                        echo("                        " . $feedSubmissionInfo->getFeedSubmissionId() . "\n");
                    }
                    if ($feedSubmissionInfo->isSetFeedType())
                    {
                        echo("                    FeedType\n");
                        echo("                        " . $feedSubmissionInfo->getFeedType() . "\n");
                    }
                    if ($feedSubmissionInfo->isSetSubmittedDate())
                    {
                        echo("                    SubmittedDate\n");
                        echo("                        " . $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                    }
                    if ($feedSubmissionInfo->isSetFeedProcessingStatus())
                    {
                        echo("                    FeedProcessingStatus\n");
                        echo("                        " . $feedSubmissionInfo->getFeedProcessingStatus() . "\n");
                    }
                    if ($feedSubmissionInfo->isSetStartedProcessingDate())
                    {
                        echo("                    StartedProcessingDate\n");
                        echo("                        " . $feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT) . "\n");
                    }
                    if ($feedSubmissionInfo->isSetCompletedProcessingDate())
                    {
                        echo("                    CompletedProcessingDate\n");
                        echo("                        " . $feedSubmissionInfo->getCompletedProcessingDate()->format(DATE_FORMAT) . "\n");
                    }
                }
            }
            if ($response->isSetResponseMetadata()) {
                echo("            ResponseMetadata\n");
                $responseMetadata = $response->getResponseMetadata();
                if ($responseMetadata->isSetRequestId())
                {
                    echo("                RequestId\n");
                    echo("                    " . $responseMetadata->getRequestId() . "\n");
                }
            }

            echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
        } catch (\MarketplaceWebService_Exception $ex) {
            echo("Caught Exception: " . $ex->getMessage() . "\n");
            echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            echo("Error Code: " . $ex->getErrorCode() . "\n");
            echo("Error Type: " . $ex->getErrorType() . "\n");
            echo("Request ID: " . $ex->getRequestId() . "\n");
            echo("XML: " . $ex->getXML() . "\n");
            echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
            fclose($feedHandle);
            return false;
        }
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
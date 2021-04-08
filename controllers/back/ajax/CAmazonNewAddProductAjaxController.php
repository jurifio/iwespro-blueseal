<?php
namespace bamboo\controllers\back\ajax;
use bamboo\amazon\business\builders\AAmazonFeedBuilder;
use bamboo\amazon\business\builders\CAmazonImageFeedBuilder;
use bamboo\amazon\business\builders\CAmazonInventoryFeedBuilder;
use bamboo\amazon\business\builders\CAmazonPricingFeedBuilder;
use bamboo\amazon\business\builders\CAmazonProductFeedBuilder;
use bamboo\amazon\business\builders\CAmazonRelationshipFeedBuilder;
use bamboo\domain\entities\CMarketplaceAccount;
use bamboo\domain\entities\CMarketplaceAccountHasProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\repositories\CMarketplaceAccountHasProductRepo;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;

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
class CAmazonNewAddProductAjaxController extends AAjaxController
{


    public function post()
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
        $marketplaceAccounts = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findBy(['marketplaceId' => 4, 'id'=>42,'isActive' => 1]);
        foreach ($marketplaceAccounts as $marketplaceAccount) {
            $messageId=1;
            try{
            $goods = $productInMarketplaceRepo->findBy(['isPublished' => 2,'marketplaceHasShopId' => $marketplaceAccount->config['marketplaceHasShopId']]);

            $shop = $shopRepo->findOneBy(['id' => $marketplaceAccount->config['shopId']]);
            $addressBook = $addressBookRepo->findOneBy(['id' => $shop->billingAddressBookId]);
            foreach ($goods as $good) {
                $this->prepareSkus($good,$marketplaceAccount);
            }
            $productFeed="productFeed";
            $product = new CAmazonProductFeedBuilder($this->app);
            $this->prepareAndSend($marketplaceAccount,$product,$goods,$productFeed,$messageId);
            $inventoryFeed="inventoryFeed";
            $inventary = new CAmazonInventoryFeedBuilder($this->app);
            $this->prepareAndSend($marketplaceAccount,$inventary,$goods,$inventoryFeed,$messageId);
            $pricingFeed = "pricingFeed";
            $pricing = new CAmazonPricingFeedBuilder($this->app);
            $this->prepareAndSend($marketplaceAccount,$pricing,$goods,$pricingFeed,$messageId);
            $imageFeed="imageFeed";
            $image = new CAmazonImageFeedBuilder($this->app);
            $this->prepareAndSend($marketplaceAccount,$image,$goods,$imageFeed,$messageId);
            $relationshipFeed="relationshipFeed";
            $relationship = new CAmazonRelationshipFeedBuilder($this->app);
            $this->prepareAndSend($marketplaceAccount,$relationship,$goods,$relationshipFeed,$messageId);
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

        $content = $builder->prepare($marketplaceAccount,$products,false)->getRawBody();

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
    /*    $request = new \MarketplaceWebService_Model_SubmitFeedRequest($parameters);

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
    */
        fclose($feedHandle);
        return true;
    }
    public function prepareSkus(CPrestashopHasProductHasMarketplaceHasShop $prestashopHasProductHasMarketplaceHasShop, CMarketplaceAccount $marketplaceAccount)
    {
        $sizesDone = [];
        foreach ($prestashopHasProductHasMarketplaceHasShop->product->productSku as $sku) {
            if(empty($sku->ean)) {
                \Monkey::app()->repoFactory->create('ProductSku')->assignNewEan($sku);
            }
            $marketSku = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProductSku')->getEmptyEntity();
            $marketSku->productSizeId = $sku->productSizeId;
            $marketSku->productId = $sku->productId;
            $marketSku->productVariantId = $sku->productVariantId;
            $marketSku->marketplaceId = $marketplaceAccount->marketplaceId;
            $marketSku->marketplaceAccountId = $marketplaceAccount->id;
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

<?php


namespace bamboo\blueseal\marketplace\prestashop;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductCategory;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\repositories\CPrestashopHasProductRepo;

/**
 * Class CPrestashopProduct
 * @package bamboo\blueseal\marketplace\prestashop
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 11/02/2019
 * @since 1.0
 */
class CPrestashopProduct extends APrestashopMarketplace
{

    const PRODUCT_RESOURCE = 'products';
    const STOCK_AVAILABLES_RESOURCE = 'stock_availables';

    /**
     * @param $products
     * @return bool
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     */
    public function addNewProducts($products)
    {

        //if argument is object create objectCollection and then iterate it
        if ($products instanceof CProduct) {
            $singleProduct = $products;

            unset($products);
            $products = new CObjectCollection();
            $products->add($singleProduct);
        }

        $prestashopShop = new CPrestashopShop();
        $shopIds = $prestashopShop->getAllPrestashopShops();

        /** @var CProduct $product */
        foreach ($products as $product) {
            try {

                $productPrice = $product->getDisplayPrice();
                if(!$productPrice) continue;

                //check if data are consistent between Prestashop database and Pickyshop database
                if (is_null($product->prestashopHasProduct)) {
                    $prodExist = $this->ws->get(array('resource' => 'products', 'filter' => array('reference' => $product->id . '-' . $product->productVariantId)));

                    if (!empty($prodExist->children()->children())) {
                        \Monkey::app()->applicationLog('PrestashopProduct', 'Error', 'Dangerous error while try to insert product', $product->id . '-' . $product->productVariantId . ' on Prestashop database but not in Pickyshop database');
                        throw new BambooException($product->id . '-' . $product->productVariantId . ' on Prestashop database but not in Pickyshop database');
                    }
                } else {
                    $prodExist = $this->ws->get(array('resource' => 'products', 'id' => $product->prestashopHasProduct->prestaId));

                    if (empty($prodExist->children()->children())) {
                        \Monkey::app()->applicationLog('PrestashopProduct', 'Error', 'Dangerous error while try to insert product', $product->id . '-' . $product->productVariantId . ' on Pickyshop database but not in Prestashop database');
                        throw new BambooException($product->id . '-' . $product->productVariantId . ' on Pickyshop database but not in Prestashop database');
                    }

                    return false;
                }



                //INSERT PRODUCT
                /** @var \SimpleXMLElement $blankProductXml */
                $blankProductXml = $this->getBlankSchema($this::PRODUCT_RESOURCE);
                $resourcesBlankProduct = $blankProductXml->children()->children();
                $resourcesBlankProduct->id_manufacturer = $product->productBrandHasPrestashopManufacturer->prestashopManufacturerId;
                $resourcesBlankProduct->reference = $product->id . '-' . $product->productVariantId;
                $resourcesBlankProduct->price = $productPrice;
                $resourcesBlankProduct->active = 1;
                $resourcesBlankProduct->available_for_order = 1;
                $resourcesBlankProduct->show_price = 1;
                $node = dom_import_simplexml($resourcesBlankProduct->meta_description->language[0][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata meta description"));
                $resourcesBlankProduct->meta_description->language[0][0] = "meta description";
                $resourcesBlankProduct->meta_description->language[0][0]['id'] = 1;
                $resourcesBlankProduct->meta_description->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
                $node = dom_import_simplexml($resourcesBlankProduct->meta_keywords->language[0][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata meta keywords"));
                $resourcesBlankProduct->meta_keywords->language[0][0] = "meta keywords1, keywords2, keywords3";
                $resourcesBlankProduct->meta_keywords->language[0][0]['id'] = 1;
                $resourcesBlankProduct->meta_keywords->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
                $node = dom_import_simplexml($resourcesBlankProduct->meta_title->language[0][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata meta title"));
                $resourcesBlankProduct->meta_title->language[0][0] = "meta title";
                $resourcesBlankProduct->meta_title->language[0][0]['id'] = 1;
                $resourcesBlankProduct->meta_title->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
                $node = dom_import_simplexml($resourcesBlankProduct->link_rewrite->language[0][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata link_rewrite"));
                $resourcesBlankProduct->link_rewrite->language[0][0] = "link-rewrite";
                $resourcesBlankProduct->link_rewrite->language[0][0]['id'] = 1;
                $resourcesBlankProduct->link_rewrite->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
                $node = dom_import_simplexml($resourcesBlankProduct->name->language[0][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata name"));
                $resourcesBlankProduct->name->language[0][0] = "New product name";
                $resourcesBlankProduct->name->language[0][0]['id'] = 1;
                $resourcesBlankProduct->name->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
                $node = dom_import_simplexml($resourcesBlankProduct->description->language[0][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata description"));
                $resourcesBlankProduct->description->language[0][0] = "description";
                $resourcesBlankProduct->description->language[0][0]['id'] = 1;
                $resourcesBlankProduct->description->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
                $node = dom_import_simplexml($resourcesBlankProduct->description_short->language[0][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata description_short"));
                $resourcesBlankProduct->description_short->language[0][0] = "description_short";
                $resourcesBlankProduct->description_short->language[0][0]['id'] = 1;
                $resourcesBlankProduct->description_short->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
                $node = dom_import_simplexml($resourcesBlankProduct->available_now->language[0][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata In stock"));
                $resourcesBlankProduct->available_now->language[0][0] = "In stock";
                $resourcesBlankProduct->available_now->language[0][0]['id'] = 1;
                $resourcesBlankProduct->available_now->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
                $node = dom_import_simplexml($resourcesBlankProduct->available_later->language[0][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata available_later"));
                $resourcesBlankProduct->available_later->language[0][0] = "available_later";
                $resourcesBlankProduct->available_later->language[0][0]['id'] = 1;
                $resourcesBlankProduct->available_later->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
                $resourcesBlankProduct->state = 1;

                //add categories
                $productCategories = $product->productCategory;
                /** @var CProductCategory $productCategory */
                foreach ($productCategories as $productCategory){
                    $prestashopCategoryObj = $productCategory->productCategoryHasPrestashopCategory;
                    if(is_null($prestashopCategoryObj)) continue;
                    //$resourcesBlankProduct->associations->categories->addChild('category')->addChild('id', $prestashopCategoryObj->prestashopCategoryId);
                    $resourcesBlankProduct->associations->categories->category->id = $prestashopCategoryObj->prestashopCategoryId;
                }



                //Here we call to add a new product
                try {
                    $opt = array('resource' => $this::PRODUCT_RESOURCE);
                    $opt['postXml'] = $blankProductXml->asXML();
                    $xml_response_product = $this->ws->add($opt);
                } catch (\PrestaShopWebserviceException $ex) {
                    echo '<b>Error : ' . $ex->getMessage() . '</b>';
                    $trace = $ex->getTrace();
                    print_r($trace);
                }

                //We take the reponse of call to do
                $resourcesProduct = $xml_response_product->children()->children();


                //add combination sizes
                /** @var CProductPublicSku $productPublicSku */
                foreach ($product->productPublicSku as $productPublicSku){

                    //ADD COMBINATION
                    $blankXmlCombination = $this->getBlankSchema('combinations');
                    $resourcesCombinationBlank = $blankXmlCombination->children()->children();
                    //add combination color
                    $resourcesCombinationBlank->id_product = $resourcesProduct->id;
                    $resourcesCombinationBlank->reference = $resourcesProduct->reference;
                    $resourcesCombinationBlank->price = $resourcesProduct->price;
                    $resourcesCombinationBlank->minimal_quantity = 1;

                    //add combination color
                    $resourcesCombinationBlank->associations->product_option_values->product_option_value->id = $product->productColorGroupHasPrestashopColorOption->prestashopColorId;

                    $prestashopSizeId = $productPublicSku->productSize->productSizeHasPrestashopSizeOption->prestashopSizeId;
                    $resourcesCombinationBlank->associations->product_option_values->addChild('product_option_value')->addChild('id', $prestashopSizeId);

                    $opt = null;
                    $opt = array('resource' => 'combinations');
                    $opt['postXml'] = $blankXmlCombination->asXML();
                    $xml_response_combination = $this->ws->add($opt);

                    $resourcesCombination = $xml_response_combination->children()->children();

                    $xml_ext_stock_available = $this->getStockAvaibles(null, ['id_product_attribute' => (int) $resourcesCombination->id]);

                    $resourcesStockAvailable = $xml_ext_stock_available->children()->children();

                    $resourcesStockAvailable->quantity = $productPublicSku->stockQty;
                    $resourcesStockAvailable->depends_on_stock = 0;
                    $resourcesStockAvailable->out_of_stock = 0;

                    try {
                        $opt = array('resource' => $this::STOCK_AVAILABLES_RESOURCE);
                        $opt['putXml'] = $resourcesStockAvailable->asXML();
                        $opt['id'] = $resourcesStockAvailable->id;
                        $xmlModifiedStockAvailable = $this->ws->edit($opt);
                        // if WebService don't throw an exception the action worked well and we don't show the following message
                        echo "Successfully updated.";
                    } catch (\PrestaShopWebserviceException $ex) {
                        // Here we are dealing with errors
                        $trace = $ex->getTrace();
                        if ($trace[0]['args'][0] == 404) echo 'Bad ID';
                        else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
                        else echo 'Other error<br />' . $ex->getMessage();
                    }
                }


                /*
                Here we add an image a created product
                 */
                //$urlRest = '/api/images/products/' . $id_created_product;
                /**.
                 * Uncomment the following line in order to update an existing image
                 */

                //$url = 'http://myprestashop.com/api/images/products/1/2?ps_method=PUT';
                // $image_path = __DIR__ . '/test/105475-4022825-001-1124.jpg';
/*
                $image_path = curl_file_create(__DIR__ . '/test/105475-4022825-001-1124.jpg');

                $request_host = $this->url;
                $headers = array("Host: " . $request_host);
                $request_url = 'https://192.168.1.245';

                $ch = curl_init();
                //curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_URL, $request_url . $urlRest);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_USERPWD, $this->key . ':');
                curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@' . $image_path->name));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $result = curl_exec($ch);
                curl_close($ch);
*/


            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('PrestashopProduct', 'Error', 'Errore while insert', $e->getMessage());
                return false;
            }
        }
        return true;
    }

    /**
     * @param $quantity
     * @param $prestashopProductId
     * @param $attributeId
     * @return bool
     */
    function setProductQuantity($quantity, $prestashopProductId, $attributeId)
    {
        try {

            $stockId = $this->getStockAvaibles($prestashopProductId, $attributeId);

            /** @var \SimpleXMLElement $blankStockAvaibleXml */
            $blankStockAvaibleXml = $this->getProductBlankSchema();
            $stockAvailablesXml = $blankStockAvaibleXml->children()->children();

            $stockAvailablesXml->id = $stockId;
            $stockAvailablesXml->id_product = $prestashopProductId;
            $stockAvailablesXml->quantity = $quantity;
            $stockAvailablesXml->id_shop = 1;
            $stockAvailablesXml->out_of_stock = 0;
            $stockAvailablesXml->depends_on_stock = 0;
            $stockAvailablesXml->id_product_attribute = $attributeId;

            $opt = array('resource' => 'stock_availables');
            $opt['putXml'] = $blankStockAvaibleXml->asXML();
            $opt['id'] = $stockId;
            $xml = $this->ws->edit($opt);

        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('PrestashopProduct', 'Error', 'Errore while set quantity on product' . $prestashopProductId, $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * @param null $stockAvailableId
     * @param array $filter
     * @return \SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function getStockAvaibles($stockAvailableId = null, $filter = []): \SimpleXMLElement
    {
        $opt = array('resource' => $this::STOCK_AVAILABLES_RESOURCE);

        if(empty($filter)){
            $opt['id'] = $stockAvailableId;
        } else {
            $opt['filter'] = $filter;
        }

        $xml = $this->ws->get($opt);

        return $xml;
    }


}
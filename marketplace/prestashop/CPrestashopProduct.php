<?php


namespace bamboo\blueseal\marketplace\prestashop;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductCategory;
use bamboo\domain\entities\CProductPhoto;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProductSheetActual;
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

                //add features
                $first = false;
                $productSheets = $product->productSheetActual;
                /** @var CProductSheetActual $productSheetActual */
                foreach ($productSheets as $productSheetActual){
                    $prestashopFeatureObj = $productSheetActual->productDetailsHasPrestashopFeatures;
                    if(is_null($prestashopFeatureObj)) continue;

                    if($first){
                        $resourcesBlankProduct->associations->product_features->product_feature->id = $prestashopFeatureObj->prestashopFeatureId;
                        $resourcesBlankProduct->associations->product_features->product_feature->id_feature_value = $prestashopFeatureObj->prestashopFeatureValueId;
                        continue;
                    }

                    $resourcesBlankProduct->associations->categories->addChild('product_feature')->addChild('id', $prestashopFeatureObj->prestashopFeatureId);
                    $resourcesBlankProduct->associations->categories->addChild('product_feature')->addChild('id_feature_value', $prestashopFeatureObj->prestashopFeatureValueId);

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

                    $xml_ext_stock_available_id = $this->getStockAvaibles(null, ['id_product_attribute' => (int) $resourcesCombination->id]);
                    $xml_ext_stock_available_resource = $xml_ext_stock_available_id->children()->children();
                    $ext_stock_available = (int) $xml_ext_stock_available_resource->stock_available[0]['id'];

                    $resourcesStockAvailableXml = $this->getStockAvaibles($ext_stock_available);
                    $resourcesStockAvailable = $resourcesStockAvailableXml->children()->children();

                    $resourcesStockAvailable->quantity = $productPublicSku->stockQty;
                    $resourcesStockAvailable->depends_on_stock = 0;
                    $resourcesStockAvailable->out_of_stock = 0;

                    try {
                        $opt = array('resource' => $this::STOCK_AVAILABLES_RESOURCE);
                        $opt['putXml'] = $resourcesStockAvailableXml->asXML();
                        $opt['id'] = (int) $resourcesStockAvailable->id;
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

                $result = $this->uploadImage($resourcesProduct->id, $product);


            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('PrestashopProduct', 'Error', 'Errore while insert', $e->getMessage());
                return false;
            }
        }
        return true;
    }

    public function uploadImage($prestashopProductId, CProduct $product): bool
    {

        //creo la cartella
        $destDir = \Monkey::app()->rootPath() . "/temp/tempPrestashopImgs/";
        if (!is_dir(rtrim($destDir, "/"))) mkdir($destDir, 0777, true);

        $cdnUrl = \Monkey::app()->cfg()->fetch("general","product-photo-host");

        $productPhotos = $product->productPhoto;
        $productPhotos->reorder('order');
        /** @var CProductPhoto $productPhoto */
        foreach ($productPhotos as $productPhoto){

            if($productPhoto->size != 843) continue;

            $url = $cdnUrl . $product->productBrand->slug . '/' . $productPhoto->name;

            //download image from aws
            $imgBody = file_get_contents(htmlspecialchars_decode($url));

            file_put_contents($destDir . $productPhoto->name, $imgBody);

            //Here we add an image a created product
            $urlRest = '/api/images/products/' . $prestashopProductId;

            //Uncomment the following line in order to update an existing image
            //$url = 'http://myprestashop.com/api/images/products/1/2?ps_method=PUT';

            // $image_path = __DIR__ . '/test/105475-4022825-001-1124.jpg';
            $image_path = curl_file_create($destDir . $productPhoto->name, 'image/jpg');

            $request_host = $this->url;
            //$headers = array("Host: " . $request_host);
            $request_url = 'https://192.168.1.245';

            $data = array('image' => $image_path);

            $ch = curl_init();
            $headers = array("Content-Type:multipart/form-data", "Host: " . $request_host);
            //curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($ch, CURLOPT_URL, $request_url . $urlRest);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_USERPWD, $this->key . ':');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($ch);
            curl_close($ch);
        }

        try {
            $files = glob($destDir . '*');
            foreach ($files as $file) {
                if (is_file($file))
                    unlink($file);
            }
            rmdir($destDir);
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CPrestashopProduct', 'error', 'Error while deleting photo', $e->getMessage());
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
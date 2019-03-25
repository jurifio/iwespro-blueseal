<?php


namespace bamboo\blueseal\marketplace\prestashop;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductCategory;
use bamboo\domain\entities\CProductPhoto;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProductSheetActual;

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
    const COMBINATION_RESOURCE = 'combinations';

    const RIGHT_IMAGE_SIZE = 843;

    /**
     * @param $products
     * @param CMarketplaceHasShop $marketplaceHasShop
     * @return bool
     */
    public function addNewProducts($products, CMarketplaceHasShop $marketplaceHasShop)
    {

        //if argument is object create objectCollection and then iterate it
        if ($products instanceof CProduct) {
            $singleProduct = $products;

            unset($products);
            $products = new CObjectCollection();
            $products->add($singleProduct);
        }


        //craete new tmp folder
        $destDir = \Monkey::app()->rootPath() . "/temp/tempPrestashopImgs/";
        if (!is_dir(rtrim($destDir, "/"))) mkdir($destDir, 0777, true);

        /** @var CProduct $product */
        foreach ($products as $product) {
            try {

                //check if price is setted
                $productPrice = $product->getDisplayPrice();
                if (!$productPrice) continue;

                $operation = $this->checkIfProductExist($product, $marketplaceHasShop->prestashopId);

                if($operation == 'insert'){
                    if (!$this->insertNewProduct($product, $productPrice, $marketplaceHasShop, $destDir)) continue;
                } else if ($operation == 'exist'){
                    continue;
                } else if ($operation instanceof \SimpleXMLElement){
                    $resourcesProduct = $operation->children()->children();
                    if (!$this->addCombination($product, $resourcesProduct, $marketplaceHasShop->prestashopId)) {
                        $this->deleteProduct((int)$resourcesProduct->id);
                        return false;
                    }
                }


            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('PrestashopProduct', 'Error', 'Errore while insert', $e->getMessage());
                return false;
            }
        }

        //delete all product photo in tmp folder
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

    private function insertNewProduct($product, $productPrice, $marketplaceHasShop, $destDir)
    {
        //INSERT PRODUCT
        $xmlResponseProduct = $this->insertProduct($product, $productPrice, $marketplaceHasShop->prestashopId);

        //if error while insert product go to next product
        if (!$xmlResponseProduct) return false;

        $resourcesProduct = $xmlResponseProduct->children()->children();
        //add combination sizes --- if false delete product
        if (!$this->addCombination($product, $resourcesProduct, $marketplaceHasShop->prestashopId)) {
            $this->deleteProduct((int)$resourcesProduct->id);
            return false;
        }

        //upload product photo
        $this->uploadImage($resourcesProduct->id, $product, $destDir, $marketplaceHasShop->prestashopId);

        //if all is ok then insert product in pickyshop db
        /** @var CPrestashopHasProduct $pHp */
        $pHp = \Monkey::app()->repoFactory->create('PrestashopHasProduct')->findOneBy([
            'productId' => $product->id,
            'productVariantId' =>  $product->productVariantId
        ]);

        $pHp->prestaId = (int)$xmlResponseProduct->children()->children()->id;
        $pHp->status = 1;
        $pHp->update();

        /** @var CPrestashopHasProductHasMarketplaceHasShop $phphmhs */
        $phphmhs = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop')->getEmptyEntity();
        $phphmhs->productId = $pHp->productId;
        $phphmhs->productVariantId = $pHp->productVariantId;
        $phphmhs->marketplaceHasShopId = $marketplaceHasShop->id;
        $phphmhs->smartInsert();

        return true;
    }

    /**
     * @param $prestashopProductId
     * @param CProduct $product
     * @param $destDir
     * @param $shopIds
     * @return bool
     */
    public function uploadImage($prestashopProductId, CProduct $product, $destDir, $shop): bool
    {

        //todo - check photo insert for multishop
        $cdnUrl = \Monkey::app()->cfg()->fetch("general", "product-photo-host");

        $productPhotos = $product->productPhoto;
        $productPhotos->reorder('order');
        /** @var CProductPhoto $productPhoto */
        foreach ($productPhotos as $productPhoto) {

            try {
                if ($productPhoto->size != $this::RIGHT_IMAGE_SIZE) continue;

                $url = $cdnUrl . $product->productBrand->slug . '/' . $productPhoto->name;

                //download image from aws
                $imgBody = file_get_contents(htmlspecialchars_decode($url));

                file_put_contents($destDir . $productPhoto->name, $imgBody);

                $urlRest = '/api/images/products/' . $prestashopProductId . '?id_shop=' . $shop;

                //Uncomment the following line in order to update an existing image
                //$url = 'http://myprestashop.com/api/images/products/1/2?ps_method=PUT';

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
            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('CPrestashopProduct', 'error', 'Error while insert photo', $e->getMessage());
                continue;
            }
        }

        return true;
    }

    /**
     * @param $resource
     * @param null $id
     * @param array $filter
     * @param null $display
     * @param null $shopGroupId
     * @param null $shopId
     * @return \SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function getDataFromResource($resource, $id = null, array $filter = [], $display = null, $shopGroupId = null, $shopId = null)
    {

        $opt['resource'] = $resource;

        if (!is_null($id)) $opt['id'] = $id;
        if (!empty($filter)) $opt['filter'] = $filter;
        if (!is_null($display)) $opt['display'] = $display;
        if (!is_null($shopGroupId)) $opt['id_group_shop'] = $shopGroupId;
        if (!is_null($shopId)) $opt['id_shop'] = $shopId;

        return $this->ws->get($opt);
    }

    /**
     * @param null $stockAvailableId
     * @param array $filter
     * @param $shopId
     * @return \SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function getStockAvaibles($stockAvailableId = null, $filter = [], $shopId): \SimpleXMLElement
    {
        $opt = array('resource' => $this::STOCK_AVAILABLES_RESOURCE);

        if (empty($filter)) {
            $opt['id'] = $stockAvailableId;
        } else {
            $opt['filter'] = $filter;
        }

        $opt['id_shop'] = $shopId;
        $xml = $this->ws->get($opt);

        return $xml;
    }

    /**
     * @param CProduct $product
     * @param $shop
     * @return string
     * @throws \PrestaShopWebserviceException
     */
    public function checkIfProductExist(CProduct $product, $shop)
    {
        $allShops = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findAll();
        /** @var CPrestashopHasProduct $pHp */
        $pHp = \Monkey::app()->repoFactory->create('PrestashopHasProduct')->findOneBy(['productId' => $product->id, 'productVariantId' => $product->productVariantId]);

        $mhs = $pHp->marketplaceHasShop;

        //if exist product for some shops
        if ($mhs->count() != 0) {
            //if exist product for specific shop
            if ($mhs->findOneByKey('prestashopId', $shop)) {
                return 'exist';
            } else {
                foreach ($allShops as $s){
                    $xml = null;
                    try {
                        $xml = $this->getDataFromResource($this::PRODUCT_RESOURCE, $pHp->prestaId, [], null, null, $s->prestashopId);
                    } catch (\Throwable $e){}

                    if($xml instanceof \SimpleXMLElement){
                        return $xml;
                    }
                }
            }
        }

        return 'insert';
    }

    /**
     * @param CProduct $product
     * @param $productPrice
     * @return bool|\SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function insertProduct(CProduct $product, $productPrice, $shop)
    {
        /** @var \SimpleXMLElement $blankProductXml */
        $blankProductXml = $this->getBlankSchema($this::PRODUCT_RESOURCE);
        $resourcesBlankProduct = $blankProductXml->children()->children();
        $resourcesBlankProduct->id_manufacturer = $product->productBrandHasPrestashopManufacturer->prestashopManufacturerId;
        $resourcesBlankProduct->reference = $product->id . '-' . $product->productVariantId;
        $resourcesBlankProduct->price = $productPrice;
        $resourcesBlankProduct->active = 1;
        $resourcesBlankProduct->available_for_order = 1;
        $resourcesBlankProduct->show_price = 1;

        $node = dom_import_simplexml($resourcesBlankProduct->name->language[0][0]);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection("cdata name"));
        $resourcesBlankProduct->name->language[0][0] = $product->getName();
        $resourcesBlankProduct->name->language[0][0]['id'] = 1;
        $resourcesBlankProduct->name->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';

        $node = dom_import_simplexml($resourcesBlankProduct->description->language[0][0]);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection("cdata description"));
        $resourcesBlankProduct->description->language[0][0] = $product->getDescription();
        $resourcesBlankProduct->description->language[0][0]['id'] = 1;
        $resourcesBlankProduct->description->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';

        $resourcesBlankProduct->state = 1;

        //add categories
        $firstCategory = true;
        $productCategories = $product->productCategory;
        /** @var CProductCategory $productCategory */
        foreach ($productCategories as $productCategory) {
            $prestashopCategoryObj = $productCategory->productCategoryHasPrestashopCategory;
            if (is_null($prestashopCategoryObj)) continue;

            if ($firstCategory) {
                $resourcesBlankProduct->associations->categories->category->id = $prestashopCategoryObj->prestashopCategoryId;
                $firstCategory = false;
                continue;
            }

            $fatherCategory = $resourcesBlankProduct->associations->categories->addChild('category');
            $fatherCategory->addChild('id', $prestashopCategoryObj->prestashopCategoryId);
        }

        //add features
        $firstSheet = true;
        $productSheets = $product->productSheetActual;
        /** @var CProductSheetActual $productSheetActual */
        foreach ($productSheets as $productSheetActual) {
            $prestashopFeatureObj = $productSheetActual->productDetailsHasPrestashopFeatures;
            if (is_null($prestashopFeatureObj)) continue;

            if ($firstSheet) {
                $resourcesBlankProduct->associations->product_features->product_feature->id = $prestashopFeatureObj->prestashopFeatureId;
                $resourcesBlankProduct->associations->product_features->product_feature->id_feature_value = $prestashopFeatureObj->prestashopFeatureValueId;
                $firstSheet = false;
                continue;
            }

            $fatherFeature = $resourcesBlankProduct->associations->product_features->addChild('product_feature');
            $fatherFeature->addChild('id', $prestashopFeatureObj->prestashopFeatureId);
            $fatherFeature->addChild('id_feature_value', $prestashopFeatureObj->prestashopFeatureValueId);
        }

        //Here we call to add a new product
        try {
            $opt = array('resource' => $this::PRODUCT_RESOURCE);
            $opt['postXml'] = $blankProductXml->asXML();
            $opt['id_shop'] = $shop;
            $xmlResponseProduct = $this->ws->add($opt);
        } catch (\PrestaShopWebserviceException $e) {
            \Monkey::app()->applicationLog('CPrestashopProduct', 'Error', 'Error while insert product' . $product->id . '-' . $product->productVariantId, $e->getMessage());
            return false;
        }

        return $xmlResponseProduct;
    }


    /**
     * @param CProduct $product
     * @param $resourcesProduct
     * @param $shops
     * @return bool
     * @throws \PrestaShopWebserviceException
     */
    public function addCombination(CProduct $product, $resourcesProduct, $shop)
    {

        /** @var CProductPublicSku $productPublicSku */
        foreach ($product->productPublicSku as $productPublicSku) {


            $resourcesCombination = null;
            $ext_stock_available = null;

            $prestashopColorId = $product->productColorGroupHasPrestashopColorOption->prestashopColorId;
            $prestashopSizeId = $productPublicSku->productSize->productSizeHasPrestashopSizeOption->prestashopSizeId;


            //ADD COMBINATION
            $blankXmlCombination = $this->getBlankSchema('combinations');
            $resourcesCombinationBlank = $blankXmlCombination->children()->children();

            //add combination
            $resourcesCombinationBlank->id_product = $resourcesProduct->id;
            $resourcesCombinationBlank->reference = $resourcesProduct->reference . '-' . $productPublicSku->productSize->id;
            $resourcesCombinationBlank->price = $resourcesProduct->price;
            $resourcesCombinationBlank->minimal_quantity = 1;
            $resourcesCombinationBlank->associations->product_option_values->product_option_value->id = $prestashopColorId;
            $resourcesCombinationBlank->associations->product_option_values->addChild('product_option_value')->addChild('id', $prestashopSizeId);

            try {
                $opt = null;
                $opt = array('resource' => $this::COMBINATION_RESOURCE);
                $opt['postXml'] = $blankXmlCombination->asXML();
                $opt['id_shop'] = $shop;
                $xml_response_combination = $this->ws->add($opt);
            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('CPrestashopProduct', 'Error', 'Error while insert combination', $e->getMessage());
                return false;
            }

            $resourcesCombination = $xml_response_combination->children()->children();

            $xml_ext_stock_available_id = $this->getStockAvaibles(null, ['id_product_attribute' => (int)$resourcesCombination->id], $shop);
            $xml_ext_stock_available_resource = $xml_ext_stock_available_id->children()->children();
            $ext_stock_available = (int)$xml_ext_stock_available_resource->stock_available[0]['id'];

            $resourcesStockAvailableXml = $this->getStockAvaibles($ext_stock_available, [], $shop);
            $resourcesStockAvailable = $resourcesStockAvailableXml->children()->children();

            $resourcesStockAvailable->quantity = $productPublicSku->stockQty;
            $resourcesStockAvailable->depends_on_stock = 0;
            $resourcesStockAvailable->out_of_stock = 0;

            try {
                $opt = array('resource' => $this::STOCK_AVAILABLES_RESOURCE);
                $opt['putXml'] = $resourcesStockAvailableXml->asXML();
                $opt['id'] = (int)$resourcesStockAvailable->id;
                $this->ws->edit($opt);
            } catch (\PrestaShopWebserviceException $e) {
                //if fail to insert quantity delete combination
                $this->deleteCombination((int)$resourcesCombination->id);
                \Monkey::app()->applicationLog('CPrestashopProduct', 'Error', 'Error while insert stock available', $e->getMessage());
                return false;
            }


        }

        return true;
    }


    /**
     * @param $prestashopProductId
     * @return bool
     */
    public function deleteProduct($prestashopProductId)
    {
        $opt['resource'] = $this::PRODUCT_RESOURCE;
        $opt['id'] = $prestashopProductId;
        try {
            $this->ws->delete($opt);
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('PrestashopProduct', 'Error', 'Error while deleting product', $e->getMessage());
        }

        return true;
    }

    /**
     * @param $combinationId
     * @return bool
     */
    public function deleteCombination($combinationId)
    {

        $opt['resource'] = $this::COMBINATION_RESOURCE;
        $opt['id'] = $combinationId;
        try {
            $this->ws->delete($opt);
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('PrestashopProduct', 'Error', 'Error while deleting combination', $e->getMessage());
        }

        return true;
    }

    /**
     * @param $productId
     * @param $sizeId
     * @param $qty
     * @param $shops
     * @return bool
     * @throws \PrestaShopWebserviceException
     */
    public function updateProductQuantity($productId, $sizeId, $qty, $shops)
    {

        foreach ($shops as $shopId) {
            $productXmlFather = $this->getDataFromResource($this::PRODUCT_RESOURCE, $productId, [], [], null, $shopId);
            $productXmlChildren = $productXmlFather->children()->children();

            foreach ($productXmlChildren->associations->combinations->combination as $association) {
                $combinationXmlFather = $this->getDataFromResource($this::COMBINATION_RESOURCE, (int)$association->id, [], [], null, $shopId);

                $combinationXmlChildren = $combinationXmlFather->children()->children();
                $productSizeOnPrestashop = explode('-', $combinationXmlChildren->reference)[2];

                if ($sizeId == $productSizeOnPrestashop) {
                    //size is the same
                    $idProductAttribute = (int)$combinationXmlChildren->id;

                    //get stock available id
                    $stockAvailableXmlIndexFather = $this->getDataFromResource($this::STOCK_AVAILABLES_RESOURCE, null, ['id_product_attribute' => $idProductAttribute], [], null, $shopId);
                    $stockAvailableXmlIndexChildren = $stockAvailableXmlIndexFather->children()->children();
                    $stockAvailableId = (int)$stockAvailableXmlIndexChildren->stock_available[0]['id'];

                    //get stock available
                    $stockAvailableXmlFather = $this->getDataFromResource($this::STOCK_AVAILABLES_RESOURCE, $stockAvailableId, [], [], null, $shopId);
                    $stockAvailableXmlChildren = $stockAvailableXmlFather->children()->children();

                    //modify stock_available quantity
                    $stockAvailableXmlChildren->quantity = $qty;

                    try {
                        $opt['resource'] = $this::STOCK_AVAILABLES_RESOURCE;
                        $opt['putXml'] = $stockAvailableXmlFather->asXML();
                        $opt['id'] = $stockAvailableId;
                        $this->ws->edit($opt);
                    } catch (\PrestaShopWebserviceException $e) {
                        \Monkey::app()->applicationLog('CPrestashopProduct', 'Error', 'Error while update product qty', $e->getMessage());
                        return false;
                    }
                }

            }
        }

        return true;
    }

}
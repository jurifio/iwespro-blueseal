<?php


namespace bamboo\blueseal\marketplace\prestashop;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductCategory;
use bamboo\domain\entities\CProductEan;
use bamboo\domain\entities\CProductPhoto;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProductSheetActual;
use bamboo\domain\repositories\CProductEanRepo;
use PDO;

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
    const SPECIFIC_PRICE_RESOURCE = 'specific_prices';
    const IMAGES_RESOURCE = 'images/products';
    const PRODUCT_FEATURES_RESOURCE = 'product_features';
    const RIGHT_IMAGE_SIZE = 843;

    /**
     * @param $products
     * @param CMarketplaceHasShop $marketplaceHasShop
     * @param $modifyType
     * @param $variantValue
     * @return bool
     */
    public function addNewProducts($products, CMarketplaceHasShop $marketplaceHasShop, $modifyType, $variantValue)
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

                /*check if price is setted
                se  utilizzo questa funzione mi prende il valore attivo ddel prezzo
                $productPrice = $product->getDisplayActivePrice();
                */
                $productPrice = $product->getDisplayFullPrice();
                // full price

                if (!$productPrice) continue;

                switch ($modifyType) {
                    case 'p+':
                        $productPrice = $productPrice + ($productPrice * $variantValue / 100);
                        break;
                    case 'p-':
                        $productPrice = $productPrice - ($productPrice * $variantValue / 100);
                        break;
                    case 'f+':
                        $productPrice = $productPrice + $variantValue;
                        break;
                    case 'f-':
                        $productPrice = $productPrice - $variantValue;
                        break;
                }


                if ($productPrice <= 0) continue;

                /** @var CPrestashopHasProduct $pHp */
                $pHp = \Monkey::app()->repoFactory->create('PrestashopHasProduct')->findOneBy(['productId' => $product->id, 'productVariantId' => $product->productVariantId]);
                $operation = $this->checkIfProductExist($product, $marketplaceHasShop->prestashopId, $pHp);

                if ($operation == 'insert') {
                    if (!$this->insertNewProduct($product, $productPrice, $marketplaceHasShop, $destDir)) continue;
                } else if ($operation == 'exist') {
                    $xml = $this->getDataFromResource($this::PRODUCT_RESOURCE, $pHp->prestaId, [], null, null, $marketplaceHasShop->prestashopId);
                    $this->updateProductPrice($xml, $productPrice, $marketplaceHasShop, $product);
                } else if ($operation instanceof \SimpleXMLElement) {
                    if (!$this->insertProductInNewShop($operation, $marketplaceHasShop, $product, $destDir, $productPrice)) continue;
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

    /**
     * @param $product
     * @param $productPrice
     * @param $marketplaceHasShop
     * @param $destDir
     * @return bool
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     * @throws \bamboo\core\exceptions\RedPandaException
     */
    private function insertNewProduct($product, $productPrice, $marketplaceHasShop, $destDir)
    {
        //INSERT PRODUCT
        $xmlResponseProduct = $this->insertProduct($product, $productPrice, $marketplaceHasShop->prestashopId);

        //if error while insert product go to next product
        if (!$xmlResponseProduct) return false;

        $resourcesProduct = $xmlResponseProduct->children()->children();
        //add combination sizes
        if (!$this->addCombination($product, $resourcesProduct, $marketplaceHasShop->prestashopId, $productPrice)) {
            return false;
        }

        //upload product photo
        $this->uploadImage($resourcesProduct->id, $product, $destDir, $marketplaceHasShop->prestashopId);

        //if all is ok then insert product in pickyshop db
        /** @var CPrestashopHasProduct $pHp */
        $pHp = \Monkey::app()->repoFactory->create('PrestashopHasProduct')->findOneBy([
            'productId' => $product->id,
            'productVariantId' => $product->productVariantId
        ]);

        $pHp->prestaId = (int)$xmlResponseProduct->children()->children()->id;
        $pHp->status = 1;
        $pHp->update();

        /** @var CPrestashopHasProductHasMarketplaceHasShop $phphmhs */
        $phphmhs = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop')->getEmptyEntity();
        $phphmhs->productId = $pHp->productId;
        $phphmhs->productVariantId = $pHp->productVariantId;
        $phphmhs->marketplaceHasShopId = $marketplaceHasShop->id;
        $phphmhs->price = $productPrice;
        $phphmhs->smartInsert();

        return true;
    }

    private function insertProductInNewShop($productXml, $marketplaceHasShop, $product, $destDir, $productPrice)
    {

        try {
            $resourcesProduct = $productXml->children()->children();
            $resourcesProduct->price = 0;
            unset($resourcesProduct->manufacturer_name);
            unset($resourcesProduct->quantity);
            unset($resourcesProduct->associations->combinations);

            $opt['resource'] = $this::PRODUCT_RESOURCE;
            $opt['putXml'] = $productXml->asXML();
            $opt['id'] = (int)$resourcesProduct->id;
            $opt['id_shop'] = $marketplaceHasShop->prestashopId;
            $this->ws->edit($opt);

            if (!$this->addCombination($product, $resourcesProduct, $marketplaceHasShop->prestashopId, $productPrice)) {
                return false;
            }

            $imagesXml = $this->ws->get(
                [
                    'resource' => 'images/products',
                    'id' => (int)$resourcesProduct->id
                ]);

            foreach ($imagesXml->image->declination as $imgIdXml) {
                $optD['resource'] = $this::IMAGES_RESOURCE . '/' . (int)$resourcesProduct->id;
                $optD['id'] = (int)$imgIdXml->attributes();
                $optD['id_group_shop'] = 1;
                $this->ws->delete($optD);
            };

            $this->uploadImage((int)$resourcesProduct->id, $product, $destDir, $marketplaceHasShop->prestashopId);

            /** @var CPrestashopHasProductHasMarketplaceHasShop $phphmhs */
            $phphmhs = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop')->getEmptyEntity();
            $phphmhs->productId = $product->id;
            $phphmhs->productVariantId = $product->productVariantId;
            $phphmhs->marketplaceHasShopId = $marketplaceHasShop->id;
            $phphmhs->price = $productPrice;
            $phphmhs->smartInsert();
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CPrestashopProduct', 'error', 'Error while insert product: ' . $product->id . '-' . $product->productVariantId . ' nello shop ' . $marketplaceHasShop->prestashopId, $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * @param $productXml
     * @param $productPrice
     * @param $marketplaceHasShop
     * @param $product
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function updateProductPrice($productXml, $productPrice, $marketplaceHasShop, $product)
    {
        $resourcesProduct = $productXml->children()->children();

        foreach ($resourcesProduct->associations->combinations->combination as $combination) {
            $combinationId = (int)$combination->id;

            $combinationXmlF = $this->getDataFromResource(self::COMBINATION_RESOURCE, $combinationId, [], null, null, $marketplaceHasShop->prestashopId);
            $combinationXmlC = $combinationXmlF->children()->children();

            $combinationXmlC->price = $productPrice;

            $opt['resource'] = self::COMBINATION_RESOURCE;
            $opt['putXml'] = $combinationXmlF->asXML();
            $opt['id'] = (int)$combinationId;
            $opt['id_shop'] = $marketplaceHasShop->prestashopId;
            $this->ws->edit($opt);
        }

        /** @var CPrestashopHasProductHasMarketplaceHasShop $phphmhs */
        $phphmhs = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop')->findOneBy([
            'productId' => $product->id,
            'productVariantId' => $product->productVariantId,
            'marketplaceHasShopId' => $marketplaceHasShop->id
        ]);

        $phphmhs->price = $productPrice;
        $phphmhs->update();
    }

    /**
     * @param $prestashopProductId
     * @param CProduct $product
     * @param $destDir
     * @param $shop
     * @return bool
     */
    public function uploadImage($prestashopProductId, CProduct $product, $destDir, $shop): bool
    {
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

                $urlRest = 'https://iwes.shop/api/images/products/' . $prestashopProductId . '?id_group_shop=1';

                //Uncomment the following line in order to update an existing image
                //$url = 'http://myprestashop.com/api/images/products/1/2?ps_method=PUT';

                $image_path = curl_file_create($destDir . $productPhoto->name, 'image/jpg');

                $request_host = $this->url;
                //$headers = array("Host: " . $request_host);
                //$request_url = 'https://192.168.1.245';

                $data = array('image' => $image_path);

                $ch = curl_init();
                //$headers = array("Content-Type:multipart/form-data", "Host: " . $request_host);
                //curl_setopt($ch, CURLOPT_HEADER, true);
                //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                curl_setopt($ch, CURLOPT_URL, $urlRest);
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
     * @param CPrestashopHasProduct $pHp
     * @return null|\SimpleXMLElement|string
     */
    public function checkIfProductExist(CProduct $product, $shop, CPrestashopHasProduct $pHp)
    {
        $allShops = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findAll();

        $mhs = $pHp->marketplaceHasShop;

        //if exist product for some shops
        if ($mhs->count() != 0) {
            //if exist product for specific shop
            if ($mhs->findOneByKey('prestashopId', $shop)) {
                return 'exist';
            } else {
                foreach ($allShops as $s) {
                    $xml = null;
                    try {
                        $xml = $this->getDataFromResource($this::PRODUCT_RESOURCE, $pHp->prestaId, [], null, null, $s->prestashopId);
                    } catch (\Throwable $e) {
                    }

                    if ($xml instanceof \SimpleXMLElement) {
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
     * @param $shop
     * @return bool|\SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function insertProduct(CProduct $product, $productPrice, $shop)
    {
        /** @var CProductEanRepo $productEanRepo */
        $productEanRepo = \Monkey::app()->repoFactory->create('ProductEan');

        /** @var CProductEan $eanFather */
        $eanFather = $productEanRepo->getParentEanForProduct($product);
        /*
         * //assign ean
        $assignedEan = $productEanRepo->productHasEan($product);

        if(is_null($assignedEan['father']) || (isset($assignedEan['children-not-assigned']) && count($assignedEan['children-not-assigned']) > 0)){
            $productEanRepo->assignEanForProduct($product);
        }
        */

        $productName = $product->productCategoryTranslation->findOneByKey('langId', 1)->name
            . ' ' .
            $product->productBrand->name
            . ' ' .
            $product->itemno
            . ' ' .
            $product->productColorGroup->productColorGroupTranslation->findOneByKey('langId', 1)->name;

        /** @var \SimpleXMLElement $blankProductXml */
        $blankProductXml = $this->getBlankSchema($this::PRODUCT_RESOURCE);
        $resourcesBlankProduct = $blankProductXml->children()->children();
        $resourcesBlankProduct->id_manufacturer = $product->productBrandHasPrestashopManufacturer->prestashopManufacturerId;
        $resourcesBlankProduct->reference = $product->id . '-' . $product->productVariantId;
        $resourcesBlankProduct->price = 0;
        $resourcesBlankProduct->active = 1;
        $resourcesBlankProduct->available_for_order = 1;
        $resourcesBlankProduct->show_price = 1;
        if (!is_null($eanFather)) $resourcesBlankProduct->ean13 = $eanFather->ean;

        //$node = dom_import_simplexml($resourcesBlankProduct->name->language[0][0]);
        //$no = $node->ownerDocument;
        //$node->appendChild($no->createCDATASection("cdata name"));
        $resourcesBlankProduct->name->language[0][0] = $productName;
        //$resourcesBlankProduct->name->language[0][0]['id'] = 1;
        //$resourcesBlankProduct->name->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';

        //$node = dom_import_simplexml($resourcesBlankProduct->description->language[0][0]);
        //$no = $node->ownerDocument;
        //$node->appendChild($no->createCDATASection("cdata description"));
        $resourcesBlankProduct->description->language[0][0] = $product->getDescription();
        //$resourcesBlankProduct->description->language[0][0]['id'] = 1;
        //$resourcesBlankProduct->description->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';

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

        $resourcesBlankProduct->id_category_default = $prestashopCategoryObj->prestashopCategoryId;
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
     * @param $shop
     * @param $productPrice
     * @return bool
     * @throws \PrestaShopWebserviceException
     */
    public function addCombination(CProduct $product, $resourcesProduct, $shop, $productPrice)
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

            $relativeSku = $productPublicSku->getActualSku();
            if (!is_null($relativeSku)) {
                $resourcesCombinationBlank->ean13 = $relativeSku->ean;
            }

            $resourcesCombinationBlank->price = $productPrice;
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
     * @param int $prestashopProductId
     * @param int $productId
     * @param int $productVariantId
     * @return bool
     * @throws \PrestaShopWebserviceException
     */
    public function deleteProduct(int $prestashopProductId, int $productId, int $productVariantId)
    {
        /** @var CPrestashopHasProduct $php */
        $php = \Monkey::app()->repoFactory->create('PrestashopHasProduct')->findOneBy(['productId' => $productId, 'productVariantId' => $productVariantId]);

        $prestashopShopsIds = $php->getShopsForProduct();

        $optD['resource'] = self::IMAGES_RESOURCE;
        $optD['id'] = $prestashopProductId;

        $images = $this->getDataFromResource(self::IMAGES_RESOURCE, $prestashopProductId);

        $opt['resource'] = $this::PRODUCT_RESOURCE;
        $opt['id'] = $prestashopProductId;

        $optD['resource'] = self::IMAGES_RESOURCE;
        foreach ($images->image->declination as $singleImage) {
            $idImage = (int)$singleImage->attributes();

            $optD['id'] = $prestashopProductId . '/' . $idImage;
            $optD['id_group_shop'] = 1;
            $this->ws->delete($optD);
        }

        foreach ($prestashopShopsIds as $prestashopShopsId) {

            //Delete product
            $opt['id_shop'] = $prestashopShopsId;
            try {
                $this->ws->delete($opt);
            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('PrestashopProduct', 'Error', 'Error while deleting product', $e->getMessage());
            }
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
     * @param null $newQty
     * @param null $differential
     * @param $shops
     * @return bool
     * @throws \PrestaShopWebserviceException
     */
    public function updateProductQuantity($productId, $sizeId, $newQty = null, $differential = null, $shops)
    {

        if (is_null($newQty) && is_null($differential)) return false;

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
                    if (!is_null($newQty)) {
                        $stockAvailableXmlChildren->quantity = $newQty;
                    } else if (!is_null($differential)) {
                        $stockAvailableXmlChildren->quantity = $stockAvailableXmlChildren->quantity + $differential;
                    }

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



    /**
     * @param $product
     * @param $productData
     * @param CMarketplaceHasShop $mhs
     * @param $action
     * @param null $price
     * @param null $salePrice
     * @return bool
     * @throws \PrestaShopWebserviceException
     */
    public function updateProductSaleDescription($productData, CMarketplaceHasShop $mhs, $action, $price = null, $salePrice = null)
    {
        /** @var CProduct $product */
        $product = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $productData['productId'], 'productVariantId' => $productData['productVariantId']]);
        $productXml = $this->getDataFromResource(self::PRODUCT_RESOURCE, $productData['prestaId'], [], null, null, $mhs->prestashopId);
        $productChildXml = $productXml->children()->children();

        switch ($action) {
            case 'add':
                $percSc = (int)(($price - $salePrice) * 100 / $price);
                $name = $product->productBrand->name
                    . ' Sconto del ' . $percSc . '% da ' . $price . '€ a ' . $salePrice
                    . '€ ' .
                    $product->itemno
                    . ' ' .
                    $product->productColorGroup->productColorGroupTranslation->findOneByKey('langId', 1)->name;
                $shopprice = $salePrice;
                break;
            case 'remove':
                $name = $product->productCategoryTranslation->findOneByKey('langId', 1)->name
                    . ' ' .
                    $product->productBrand->name
                    . ' ' .
                    $product->itemno
                    . ' ' .
                    $product->productColorGroup->productColorGroupTranslation->findOneByKey('langId', 1)->name;
                $shopprice = $price;
                break;
            default:
                return false;
        }

        $productChildXml->name->language[0][0] = $name;
        $productChildXml->price = $shopprice;
        unset($productChildXml->manufacturer_name);
        unset($productChildXml->quantity);
        unset($productChildXml->associations->combinations);

        try {
            $opt['resource'] = self::PRODUCT_RESOURCE;
            $opt['putXml'] = $productXml->asXML();
            $opt['id_shop'] = $mhs->prestashopId;
            $opt['id'] = $productData['prestaId'];
            $this->ws->edit($opt);

        } catch (\PrestaShopWebserviceException $e) {
            \Monkey::app()->applicationLog('CPrestashopProduct', 'Error', 'Error while update product name (on sale)', $e->getMessage());
            return false;
        }


        return true;
    }

    /**
     * @param $products
     * @param CMarketplaceHasShop $mhs
     * @param bool $updateDescription
     * @param $reduction
     * @param $reductionType
     * @param null $from
     * @param null $to
     * @return bool
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function insertSpecificPriceForSale($products, CMarketplaceHasShop $mhs, bool $updateDescription, $reduction, $reductionType, $from = null, $to = null)
    {

        /** @var CRepo $phphmhsR */
        $phphmhsR = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');

        foreach ($products as $product) {

            /** @var CPrestashopHasProductHasMarketplaceHasShop $phphmhs */
            $phphmhs = $phphmhsR->findOneBy(['productId' => $product['productId'], 'productVariantId' => $product['productVariantId'], 'marketplaceHasShopId' => $mhs->id]);

            if (is_null($phphmhs) || $phphmhs->isOnSale == 1) continue;

            $specificPriceBlankXml = $this->getBlankSchema(self::SPECIFIC_PRICE_RESOURCE);
            $specificPriceBlank = $specificPriceBlankXml->children()->children();


            $specificPriceBlank->id_shop_group = 0;
            $specificPriceBlank->id_shop = $mhs->prestashopId;
            $specificPriceBlank->id_cart = 0;
            $specificPriceBlank->id_product = $product['prestaId'];
            $specificPriceBlank->id_product_attribute = 0;
            $specificPriceBlank->id_currency = 1;
            $specificPriceBlank->id_country = 0;
            $specificPriceBlank->id_group = 0;
            $specificPriceBlank->id_customer = 0;
            $specificPriceBlank->id_specific_price_rule = 0;
            $specificPriceBlank->price = 0;
            $specificPriceBlank->from_quantity = 1;
            $specificPriceBlank->reduction = ($reductionType === 'amount' || $reductionType === 'nf') ? $reduction : $reduction / 100;
            $specificPriceBlank->reduction_tax = 1;
            $specificPriceBlank->reduction_type = $reductionType === 'nf' ? 'amount' : $reductionType;
            $specificPriceBlank->from = is_null($from) ? '2000-01-01 00:00:00' : $from;
            $specificPriceBlank->to = is_null($to) ? '2100-01-01 00:00:00' : $to;

            try {
                $opt['resource'] = self::SPECIFIC_PRICE_RESOURCE;
                $opt['postXml'] = $specificPriceBlankXml->asXML();
                $opt['id_shop'] = $mhs->prestashopId;
                $this->ws->add($opt);
            } catch (\PrestaShopWebserviceException $e) {
                \Monkey::app()->applicationLog('CPrestashopProduct', 'Error', 'Error while insert specific price', $e->getMessage());
                return false;
            }

            $salePrice = ($reductionType === 'amount' || $reductionType === 'nf') ? $phphmhs->price - $reduction : $phphmhs->price - ($phphmhs->price * ($reduction / 100));

            if ($updateDescription) {
                $this->updateProductSaleDescription($product, $mhs, 'add', $phphmhs->price, $salePrice);
                $phphmhs->titleModified = 1;
            }

            $phphmhs->salePrice = $salePrice;
            $phphmhs->isOnSale = 1;
            $phphmhs->update();
        }

        return true;
    }

    /**
     * @param $product
     * @param CMarketplaceHasShop $mhs
     * @return bool
     * @throws \PrestaShopWebserviceException
     */
    public function removeSpecificPriceForSale($product, CMarketplaceHasShop $mhs)
    {
        $specificPrices = $this->getDataFromResource(self::SPECIFIC_PRICE_RESOURCE, null, ['id_product' => $product['prestaId'], 'id_shop' => $mhs->prestashopId]);

        try {
            $optD['resource'] = self::SPECIFIC_PRICE_RESOURCE;
            $optD['id'] = (int)$specificPrices->specific_prices->specific_price->attributes()->id;
            $this->ws->delete($optD);
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CPrestashopProduct', 'Error', 'Error while deleting specific price', $e->getMessage());
            return false;
        }
        try {
            $this->updateProductSaleDescription($product, $mhs, 'remove');

        } catch(\Throwable $e){
            \Monkey::app()->applicationLog('CPrestashopProduct', 'Error', 'Error while Remove sale Description', $e->getMessage());
        }
        return true;
    }

    /**
     * @param $prestashopProductId
     * @param $productId
     * @param $productVariantId
     * @param CProduct $product
     * @param $destDir
     * @param $shop
     * @return bool
     */
    public function updateProductImage($prestashopProductId, $productId, $productVariantId, $shop): bool
    {
        $db_host = "5.189.159.187";
        $db_name = "iwesPrestaDB";
        $db_user = "iwesprestashop";
        $db_pass = "X+]l&LEa]zSI";
        /*define("HOST", "5.189.159.187");
        define("USERNAME", "iwesprestashop");
        define("PASSWORD", "X+]l&LEa]zSI");
        define("DATABASE", "iwesPrestaDB");*/
        $res = "";
        $destDir = \Monkey::app()->rootPath() . "/temp/tempPrestashopImgs/";
        if (!is_dir(rtrim($destDir, "/"))) mkdir($destDir, 0777, true);
        try {

            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
            $db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $res .= " connessione ok <br>";
        } catch (PDOException $e) {
            $res .= $e->getMessage();
        }

        if (ENV == 'dev') {

            $save_to = '/media/sf_sites/PickyshopNew/tmp/';

        } else {
            $save_to = '/home/iwespro/public_html/temp-prestashop/';
        }
        $cdnUrl = \Monkey::app()->cfg()->fetch("general", "product-photo-host");
        $product = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $productId, 'productVariantId' => $productVariantId]);

        $stmtGetCountImageProduct = $db_con->prepare("SELECT   count(*) as numberImage from ps_image where id_product ='" . $prestashopProductId . "'");

        $stmtGetCountImageProduct->execute();
        while ($rowGetCountImageProduct = $stmtGetCountImageProduct->fetch(PDO::FETCH_ASSOC)) {
            $countImage = $rowGetCountImageProduct['numberImage'];
        }
        if ($countImage == 0) {


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

                    $urlRest = 'https://iwes.shop/api/images/products/' . $prestashopProductId . '?id_group_shop=1';

                    //Uncomment the following line in order to update an existing image
                    //$url = 'http://myprestashop.com/api/images/products/1/2?ps_method=PUT';

                    $image_path = curl_file_create($destDir . $productPhoto->name, 'image/jpg');

                    $request_host = $this->url;
                    //$headers = array("Host: " . $request_host);
                    //$request_url = 'https://192.168.1.245';

                    $data = array('image' => $image_path);

                    $ch = curl_init();
                    //$headers = array("Content-Type:multipart/form-data", "Host: " . $request_host);
                    //curl_setopt($ch, CURLOPT_HEADER, true);
                    //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    curl_setopt($ch, CURLOPT_URL, $urlRest);
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
        }
        return true;
    }

}

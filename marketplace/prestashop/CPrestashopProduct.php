<?php


namespace bamboo\blueseal\marketplace\prestashop;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\IEntity;
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
    const COMBINATION_RESOURCE = 'combinations';

    /**
     * @param $products
     * @return bool
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


        //craete new tmp folder
        $destDir = \Monkey::app()->rootPath() . "/temp/tempPrestashopImgs/";
        if (!is_dir(rtrim($destDir, "/"))) mkdir($destDir, 0777, true);

        /** @var CProduct $product */
        foreach ($products as $product) {
            try {

                //check if price is setted
                $productPrice = $product->getDisplayPrice();
                if (!$productPrice) continue;

                $exist = true;
                if ($this->checkIfProductExist($product)) {
                    $exist = false;
                }


                //check if data are consistent between Prestashop database and Pickyshop database
                if (!$exist) {
                    //INSERT PRODUCT
                    $xmlResponseProduct = $this->insertProduct($product, $productPrice);

                    //if error while insert product go to next product
                    if (!$xmlResponseProduct) continue;

                    $resourcesProduct = $xmlResponseProduct->children()->children();
                    //add combination sizes --- if false delete product
                    if (!$this->addCombination($product, $resourcesProduct)) {
                        //todo
                        $this->deleteProduct($resourcesProduct->id);
                        continue;
                    }

                    //upload product photo
                    $this->uploadImage($resourcesProduct->id, $product, $destDir);

                    $exist = true;
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
     * @param $prestashopProductId
     * @param CProduct $product
     * @param $destDir
     * @param $shopIds
     * @return bool
     */
    public function uploadImage($prestashopProductId, CProduct $product, $destDir): bool
    {
        $cdnUrl = \Monkey::app()->cfg()->fetch("general", "product-photo-host");

        $productPhotos = $product->productPhoto;
        $productPhotos->reorder('order');
        /** @var CProductPhoto $productPhoto */
        foreach ($productPhotos as $productPhoto) {

            try {
                if ($productPhoto->size != 843) continue;

                $url = $cdnUrl . $product->productBrand->slug . '/' . $productPhoto->name;

                //download image from aws
                $imgBody = file_get_contents(htmlspecialchars_decode($url));

                file_put_contents($destDir . $productPhoto->name, $imgBody);

                $urlRest = '/api/images/products/' . $prestashopProductId . '?id_shop=' . $shopId;

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
     * @param null $stockAvailableId
     * @param array $filter
     * @param $shopId
     * @return \SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function getStockAvaibles($stockAvailableId = null, $filter = []): \SimpleXMLElement
    {
        $opt = array('resource' => $this::STOCK_AVAILABLES_RESOURCE);

        if (empty($filter)) {
            $opt['id'] = $stockAvailableId;
        } else {
            $opt['filter'] = $filter;
        }

        $opt['id_group_shop'] = $this->shopGroup;
        $xml = $this->ws->get($opt);

        return $xml;
    }

    public function checkIfProductExist(CProduct $product): bool
    {
        /** @var CPrestashopHasProduct $pHp */
        $pHp = \Monkey::app()->repoFactory->create('PrestashopHasProduct')->findOneBy(['productId' => $product->id, 'productVariantId' => $product->productVariantId]);
        if (is_null($pHp)) {
            $prodExist = $this->ws->get(array('resource' => 'products', 'filter' => array('reference' => $product->id . '-' . $product->productVariantId)));

            if (!empty($prodExist->children()->children())) {
                \Monkey::app()->applicationLog('PrestashopProduct', 'Error', 'Dangerous error while try to insert product', $product->id . '-' . $product->productVariantId . ' on Prestashop database but not in Pickyshop database');
                throw new BambooException($product->id . '-' . $product->productVariantId . ' on Prestashop database but not in Pickyshop database');
            }
        } else {
            $prodExist = $this->getResourceFromId($pHp->prestaId, $this::PRODUCT_RESOURCE);

            if (empty($prodExist->children()->children())) {
                \Monkey::app()->applicationLog('PrestashopProduct', 'Error', 'Dangerous error while try to insert product', $product->id . '-' . $product->productVariantId . ' on Pickyshop database but not in Prestashop database');
                throw new BambooException($product->id . '-' . $product->productVariantId . ' on Pickyshop database but not in Prestashop database');
            }

            return false;
        }

        return true;
    }

    /**
     * @param CProduct $product
     * @param $productPrice
     * @return bool|\SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function insertProduct(CProduct $product, $productPrice)
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
        //$firstSheet = true;
        //$productSheets = $product->productSheetActual;
        /** @var CProductSheetActual $productSheetActual */
        /*
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
        }*/

        //Here we call to add a new product
        try {
            $opt = array('resource' => $this::PRODUCT_RESOURCE);
            $opt['postXml'] = $blankProductXml->asXML();
            $opt['id_group_shop'] = $this->shopGroup;
            $xmlResponseProduct = $this->ws->add($opt);

            /** @var CPrestashopHasProduct $pHp */
            $pHp = \Monkey::app()->repoFactory->create('PrestashopHasProduct')->getEmptyEntity();
            $pHp->productId = $product->id;
            $pHp->productVariantId = $product->productVariantId;
            $pHp->prestaId = (int)$xmlResponseProduct->children()->children()->id;
            $pHp->status = 1;
            $pHp->smartInsert();

        } catch (\PrestaShopWebserviceException $e) {
            \Monkey::app()->applicationLog('CPrestashopProduct', 'Error', 'Error while insert product' . $product->id . '-' . $product->productVariantId, $e->getMessage());
            return false;
        }

        return $xmlResponseProduct;
    }


    /**
     * @param CProduct $product
     * @param $resourcesProduct
     * @return bool
     * @throws \PrestaShopWebserviceException
     */
    public function addCombination(CProduct $product, $resourcesProduct)
    {
        /** @var CProductPublicSku $productPublicSku */
        foreach ($product->productPublicSku as $productPublicSku) {


            $resourcesCombination = null;


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
            $opt = array('resource' => $this::COMBINATION_RESOURCE);
            $opt['postXml'] = $blankXmlCombination->asXML();
            $opt['id_group_shop'] = $this->shopGroup;
            $xml_response_combination = $this->ws->add($opt);

            $resourcesCombination = $xml_response_combination->children()->children();

            $xml_ext_stock_available_id = $this->getStockAvaibles(null, ['id_product_attribute' => (int)$resourcesCombination->id]);
            $xml_ext_stock_available_resource = $xml_ext_stock_available_id->children()->children();
            $ext_stock_available = (int)$xml_ext_stock_available_resource->stock_available[0]['id'];

            $resourcesStockAvailableXml = $this->getStockAvaibles($ext_stock_available, []);
            $resourcesStockAvailable = $resourcesStockAvailableXml->children()->children();

            $resourcesStockAvailable->quantity = $productPublicSku->stockQty;
            $resourcesStockAvailable->depends_on_stock = 0;
            $resourcesStockAvailable->out_of_stock = 0;

            try {
                $opt = array('resource' => $this::STOCK_AVAILABLES_RESOURCE);
                $opt['putXml'] = $resourcesStockAvailableXml->asXML();
                $opt['id'] = (int)$resourcesStockAvailable->id;
                $opt['id_group_shop'] = $this->shopGroup;
                $this->ws->edit($opt);
            } catch (\PrestaShopWebserviceException $e) {
                \Monkey::app()->applicationLog('CPrestashopProduct', 'Error', 'Error while insert combination', $e->getMessage());
                return false;
            }


        }

        return true;
    }

    /**
     * @param CProduct $product
     * @param array $fields
     * @param array $opt
     * @param $type
     * @return bool
     * @throws \PrestaShopWebserviceException
     */
    public function updatePrestashopProduct(CProduct $product, array $fields, array $opt = [])
    {

        if (isset($opt['resource']) || isset($opt['putXml']) || isset($opt['id'])) return false;

        $prestashopProductId = $product->prestashopHasProduct->prestaId;

        $xml = $this->getResourceFromId($prestashopProductId, $this::PRODUCT_RESOURCE);
        $resources = $xml->children()->children();

        if (!empty($fields)) {
            foreach ($fields as $nameField => $valueField) {
                $resources->{$nameField} = $valueField;
            }
        }

        unset($resources->manufacturer_name);
        unset($resources->quantity);

        //set static opt
        $opt['resource'] = $this::PRODUCT_RESOURCE;
        $opt['putXml'] = $xml->asXML();
        $opt['id'] = $prestashopProductId;

        //set passed opt
        $this->ws->edit($opt);

        return true;

    }

    /**
     * @param CProduct $product
     * @param array $fields
     * @param array $opt
     * @return bool
     * @throws \PrestaShopWebserviceException
     */
    public function updatePrestashopProductStockAvailable(CProduct $product, array $fields, array $opt = [])
    {

        if (isset($opt['resource']) || isset($opt['putXml']) || isset($opt['id'])) return false;

        $prestashopProductId = $product->prestashopHasProduct->prestaId;

        $xml = $this->ws->get(array('resource' => $this::COMBINATION_RESOURCE, 'filter' => array('id_product' => $prestashopProductId)));

        $combinationExist = $xml->children()->children();

        if (!empty($fields)) {
            foreach ($fields as $nameField => $valueField) {
                $combinationExist->{$nameField} = $valueField;
            }
        }

        //set static opt
        $opt['resource'] = $this::COMBINATION_RESOURCE;
        $opt['putXml'] = $xml->asXML();
        $opt['id'] = $prestashopProductId;

        //set passed opt
        $this->ws->edit($opt);

        return true;
    }


    //todo
    public function deleteProduct($prestashopProductId)
    {

    }


}
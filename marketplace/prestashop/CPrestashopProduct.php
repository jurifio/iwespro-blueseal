<?php


namespace bamboo\blueseal\marketplace\prestashop;

use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductCategory;
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


    /**
     * @return \SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function getProductBlankSchema(): \SimpleXMLElement
    {
        $xml = $this->ws->get(array('resource' => 'products/?schema=blank'));
        return $xml;
    }

    /**
     * @return \SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function getStockAvailablesBlankSchema(): \SimpleXMLElement
    {
        $xml = $this->ws->get(array('resource' => 'stock_availables/?schema=blank'));
        return $xml;
    }

    /**
     * @param CProduct $product
     * @return bool|\SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function addNewProduct(CProduct $product)
    {
        try {

           //check if data are consistent between Prestashop database and Pickyshop database
           if(is_null($product->prestashopHasProduct)){
               $prodExist = $this->ws->get(array('resource' => 'products', 'filter' => array('reference' => $product->id . '-' . $product->productVariantId)));

               if(!empty($prodExist->children()->children())){
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


            /** @var \SimpleXMLElement $blankXml */
            $blankXml = $this->getProductBlankSchema();
            $resources = $blankXml->children()->children();
            $resources->price = 22.65;
            $resources->active = 1;
            $resources->available_for_order = 1;
            $resources->show_price = 1;
            $node = dom_import_simplexml($resources->meta_description->language[0][0]);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection("cdata meta description"));
            $resources->meta_description->language[0][0] = "meta description";
            $resources->meta_description->language[0][0]['id'] = 1;
            $resources->meta_description->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
            $node = dom_import_simplexml($resources->meta_keywords->language[0][0]);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection("cdata meta keywords"));
            $resources->meta_keywords->language[0][0] = "meta keywords1, keywords2, keywords3";
            $resources->meta_keywords->language[0][0]['id'] = 1;
            $resources->meta_keywords->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
            $node = dom_import_simplexml($resources->meta_title->language[0][0]);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection("cdata meta title"));
            $resources->meta_title->language[0][0] = "meta title";
            $resources->meta_title->language[0][0]['id'] = 1;
            $resources->meta_title->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
            $node = dom_import_simplexml($resources->link_rewrite->language[0][0]);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection("cdata link_rewrite"));
            $resources->link_rewrite->language[0][0] = "link-rewrite";
            $resources->link_rewrite->language[0][0]['id'] = 1;
            $resources->link_rewrite->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
            $node = dom_import_simplexml($resources->name->language[0][0]);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection("cdata name"));
            $resources->name->language[0][0] = "New product name";
            $resources->name->language[0][0]['id'] = 1;
            $resources->name->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
            $node = dom_import_simplexml($resources->description->language[0][0]);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection("cdata description"));
            $resources->description->language[0][0] = "description";
            $resources->description->language[0][0]['id'] = 1;
            $resources->description->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
            $node = dom_import_simplexml($resources->description_short->language[0][0]);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection("cdata description_short"));
            $resources->description_short->language[0][0] = "description_short";
            $resources->description_short->language[0][0]['id'] = 1;
            $resources->description_short->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
            $node = dom_import_simplexml($resources->available_now->language[0][0]);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection("cdata In stock"));
            $resources->available_now->language[0][0] = "In stock";
            $resources->available_now->language[0][0]['id'] = 1;
            $resources->available_now->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
            $node = dom_import_simplexml($resources->available_later->language[0][0]);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection("cdata available_later"));
            $resources->available_later->language[0][0] = "available_later";
            $resources->available_later->language[0][0]['id'] = 1;
            $resources->available_later->language[0][0]['xlink:href'] = $this->url . '/api/languages/1';
            $resources->associations->categories->addChild('category')->addChild('id', 1);
            $resources->associations->categories->addChild('category')->addChild('id', 4);
            //Here we call to add a new product
            try {
                $opt = array('resource' => 'products');
                $opt['postXml'] = $blankXml->asXML();
                $xml_request = $this->ws->add($opt);
            } catch (\PrestaShopWebserviceException $ex) {
                echo '<b>Error : '.$ex->getMessage().'</b>';
                $trace = $ex->getTrace();
                print_r($trace);
            }
            //We take the reponse of call to do
            //$xml_response = $xml_request['response'];
            //$response = new \SimpleXMLElement($xml_response);
            $resources = $xml_request->children()->children();
            /*
              When new product created a new stock available id was created and we can take this id to use.
             */
            $stock_available_id = (int) $resources->associations->stock_availables->stock_available[0]->id;
            $id_created_product = (int) $resources->id;
            $id_product_attribute = (int) $resources->associations->stock_availables->stock_available->id_product_attribute;
            /*
            Here we get the sotck available with were product id
             */
            try
            {
                $xml = $this->getStockAvaibles($stock_available_id);
            }
            catch (\PrestaShopWebserviceException $e)
            {
                // Here we are dealing with errors
                $trace = $e->getTrace();
                if ($trace[0]['args'][0] == 404) echo 'Bad ID';
                else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
                else echo 'Other error<br />'.$e->getMessage();
            }
            $resources = $xml->children()->children();
            //There we put our stock
            $resources->id = $stock_available_id;
            $resources->id_product = $id_created_product;
            $resources->quantity = 999;
            $resources->id_product_attribute = $id_product_attribute;
            $resources->depends_on_stock = 0;
            $resources->out_of_stock  = 0;
            /*
            There we call to save our stock quantity.
             */
            try
            {
                $opt = array('resource' => 'stock_availables');
                $opt['putXml'] = $xml->asXML();
                $opt['id'] = $stock_available_id;
                $xml = $this->ws->edit($opt);
                // if WebService don't throw an exception the action worked well and we don't show the following message
                echo "Successfully updated.";
            }
            catch (\PrestaShopWebserviceException $ex)
            {
                // Here we are dealing with errors
                $trace = $ex->getTrace();
                if ($trace[0]['args'][0] == 404) echo 'Bad ID';
                else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
                else echo 'Other error<br />'.$ex->getMessage();
            }
            /*
            Here we add an image a created product
             */
            $urlRest = '/api/images/products/' . $id_created_product;
            /**.
             * Uncomment the following line in order to update an existing image
             */

            //$url = 'http://myprestashop.com/api/images/products/1/2?ps_method=PUT';
           // $image_path = __DIR__ . '/test/105475-4022825-001-1124.jpg';

            $image_path = curl_file_create( __DIR__ . '/test/105475-4022825-001-1124.jpg');

            $request_host   = $this->url;
            $headers = array("Host: ".$request_host);
            $request_url    = 'https://192.168.1.245';

            $ch = curl_init();
            //curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_URL, $request_url .$urlRest);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_USERPWD, $this->key.':');
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path->name));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($ch);
            curl_close($ch);

        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('PrestashopProduct', 'Error', 'Errore while insert', $e->getMessage());
            return false;
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
     * @param $stockAvailableId
     * @return \SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function getStockAvaibles($stockAvailableId) : \SimpleXMLElement
    {
        $opt = array('resource' => 'stock_availables');
        $opt['id'] = $stockAvailableId;
        $xml = $this->ws->get($opt);

        return $xml;
    }


}
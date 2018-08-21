<?php

namespace bamboo\controllers\back\ajax;


use PrestaShopWebservice;
use PrestaShopWebserviceException;
use bamboo\controllers\back\ajax\CPrestashopGetImage;

use bamboo\core\exceptions\BambooConfigException;
use bamboo\core\base\CObjectCollection;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CPrestashopAlignCategory
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 02/08/2018
 * @since 1.0
 */
class CPrestashopDumpProductImageCombination extends AAjaxController
{


    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooDBALException
     * @throws \bamboo\core\exceptions\BambooException
     */
    public function post()
    {

        // \Monkey::app()->vendorLibraries->load('prestashop');

        define('DEBUG', true);
        define('PS_SHOP_PATH', 'http://iwes.shop/');
        define('PS_WS_AUTH_KEY', 'PWTA3F211GSY6APTTCJDP2Y3UHHYFSVW');
        require_once "PSWebServiceLibrary.php";

        $key = 'PWTA3F211GSY6APTTCJDP2Y3UHHYFSVW'; //your key here
        // $url = 'http://iwes.shop/api/products?output_format=JSON'; // change the base url
        global $webService;
        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);
        /**
         * @var $db CMySQLAdapter
         */
        $sql = "
SELECT
  concat(`p`.`id`,'-',p.productVariantId)                                        AS `product_id`,
  p.id                                                                           AS  productId,
  p.productVariantId                                                             AS productVariantId,
  shp.shopId                                                                     AS id_supplier,
  p.productBrandId                                                               AS id_manufacturer,
  pb.slug                                                                 AS name_manufacturer,
  phpc.productCategoryId                                                         AS id_category_default,
  shp.price                                                                      AS price,
  '53'                                                                           AS id_tax_rules_group,
  FORMAT(shp.price/100*70 ,2)                                                    AS wholesale_price,
  shp.shopId                                                                     AS id_shop_default,
  if(p.isOnSale=1,'1','0')                                                       AS `on_sale`,
  if (p.isOnSale=1,format((shp.price - shp.salePrice),2),format(shp.price,2))    AS discount_amount,
  ''                                                                             AS discount_percent,
  '2018-01-01'                                                                   AS discount_from,
  '2018-01-01'                                                                   AS discount_to,
  concat(p.id,'-',p.productVariantId)                                            AS reference,
  concat(p.id,'-',p.productVariantId)                                            AS supplier_reference,
  concat(p.id,'-',p.productVariantId)                                            AS name,
  concat(p.id,'-',p.productVariantId)                                            AS description,

  S2.barcode                                                                     AS ean13,
  ''                                                                             AS ups,
  '.000000'                                                                      AS ecotax,
  '.000000'                                                                      AS width,
  '.000000'                                                                      AS height,
  '.000000'                                                                      AS depth,
  '.000000'                                                                      AS weight,
  `p`.`qty`                                                                      AS quantity,
  '1'                                                                            AS minimal_quantity,
  '1'                                                                            AS low_stock_level,
  'both'                                                                         AS visibility,
  '0'                                                                            AS additional_shipping_cost,
  ''                                                                             AS unity,
  '0.000000'                                                                     AS unit_price_ratio,
  concat(p.id,'-',p.productVariantId)                                            AS short_description,
  NOW()                                                                          AS date_add,
  NOW()                                                                          AS date_upd,
  '1'                                                                            AS available_for_order,
  '1'                                                                            AS indexed,
  '0'                                                                            AS customizable,
  if (pdt.langId=1,pdt.description,'')                                           AS languagedesc1,
  if (pdt.langId=2,pdt.description,'')                                           AS languagedesc2,
  if (pdt.langId=3,pdt.description,'')                                           AS languagedesc3,
  '0'                                                                            AS text_fields,
  '0'                                                                            AS uploadable_files,
  '1'                                                                            AS active,
  '404'                                                                          AS redirect_type,
  '0'                                                                            AS id_type_redirect,
  '0'                                                                            AS show_condition,
  'new'                                                                          AS`condition`,
  '1'                                                                            AS show_price,
  '3'                                                                            AS pack_stock_type,
  '1'                                                                            AS showPrice,
  concat('https://iwes.s3.amazonaws.com/',pb.slug,'/',p.id,'-',p.productVariantId,'-001-1124.jpg')   AS picture,
  concat(p.id,'-',p.productVariantId)                                            AS imageAlt,
  '1'                                                                            AS deleteImage,
  ''                                                                             AS feature,
  '1'                                                                            AS idshop,
  '0'                                                                            AS advanced_stock_managment,
  '0'                                                                            AS depend_on_stock,
  '0'                                                                            AS Warehouse

FROM `Product` `p`
  JOIN `ProductVariant` `pv` ON `p`.`productVariantId` = `pv`.`id`
  JOIN `ProductBrand` `pb` ON `p`.`productBrandId` = `pb`.`id`
  JOIN `ProductStatus` `pss` ON `pss`.`id` = `p`.`productStatusId`
  JOIN `ShopHasProduct` `shp` ON (`p`.`id`, `p`.`productVariantId`) = (`shp`.`productId`, `shp`.`productVariantId`)
  JOIN `Shop` `s` ON `s`.`id` = `shp`.`shopId`
  JOIN `ProductSeason` `ps` ON `p`.`productSeasonId` = `ps`.`id`
  JOIN  `ProductPublicSku` S3 ON  (`p`.`id`, `p`.`productVariantId`) = (`S3`.`productId`, `S3`.`productVariantId`)
  JOIN  `ProductSku` S2 ON  (`p`.`id`, `p`.`productVariantId`) = (`S2`.`productId`, `S2`.`productVariantId`)
  JOIN `ProductHasProductCategory` `phpc`  ON (`p`.`id`, `p`.`productVariantId`)=(`phpc`.`productId`, `phpc`.`productVariantId`)
  JOIN  ProductDescriptionTranslation pdt ON p.id = pdt.productId AND p.productVariantId = pdt.productVariantId
  LEFT JOIN (
      ProductHasShooting phs
      JOIN Shooting shoot ON phs.shootingId = shoot.id
      LEFT JOIN Document doc ON shoot.friendDdt = doc.id)
    ON p.productVariantId = phs.productVariantId AND p.id = phs.productId
  LEFT JOIN ProductSheetPrototype psp ON p.productSheetPrototypeId = psp.id
WHERE `pss`.`id` NOT IN (7, 8, 13) AND `p`.`qty` > 0
GROUP BY product_id
ORDER BY `p`.`id` ASC";
        $res = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        foreach ($res as $data) {

            $this->make_product($data);



        }


        $res = "eseguito";

        return $res;

    }
    function make_father_product_update($data){

        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);

        try{
            $opt                             = array();
            $opt['resource']                 = "products";
            $opt['filter']                   = array('reference' => $data['reference']);
            $xml                             = $webService->get($opt);
            $product                        = $xml->children()->children()[0];
            $id_product                        = (int) $product['id_product'][0];
            $xml = $webService->get($opt);

            $x = ($xml->products[0]->product->attributes());

            $ProductId = $x['id'];
            $xml                                                                  = $webService->get(array('url' => PS_SHOP_PATH.'api/products?schema=blank'));
            $product                                                              = $xml->children()->children();
            $product->id_product                                                  = $ProductId;
            $product->id_supplier                                                 = $data['id_supplier'];
            $product->id_manufacturer                                             = $data['id_manufacturer'];
            $product->price                                                       = $data["price"]; //Prix TTC
            $product->wholesale_price                                             = $data['wholesale_price']; //Prix d'achat
            $product->id_tax_rules_group                                          = $data['id_tax_rules_group'];
            $product->ean13                                                       = $data['ean13'];
            $product->quantity                                                    = $data["quantity"]; //Prix TTC
            $product->minimal_quantity                                            = 1;
            $product->active                                                      = '1';
            $product->on_sale                                                     = 1; //on ne veux pas de bandeau promo
            $product->show_price                                                  = 1;
            $product->available_for_order                                         = 1;
            $product->state                                                       = 1;

            $product->name->language[0][0]                                        = $data["name"];
            $product->name->language[0][0]['id']                                  = 1;

            $product->description->language[0][0]                                 = $data["description"];
            $product->description->language[0][0]['id']                           = 1;

            $product->description_short->language[0][0]                           = $data["description"];
            $product->description_short->language[0][0]['id']                     = 1;

            $product->meta_keywords->language[0][0]                               = $data["description"];
            $product->meta_keywords->language[0][0]['id']                         = 1;

            $product->meta_title->language[0][0]                                  = $data["description"];
            $product->meta_title->language[0][0]['id']                            = 1;
            $product->meta_description->language[0][0]                                  = $data["description"];
            $product->meta_description->language[0][0]['id']                            = 1;

            $product->description_short->language[0][0]                           = $data["description"];
            $product->description_short->language[0][0]['id']                     = 1;
            $product->available_now->language[0][0]                           = 'In Stock';
            $product->available_now->language[0][0]['id']                     = 1;
            $product->available_later->language[0][0]                           = 'future Arrive';
            $product->available_later->language[0][0]['id']                     = 1;

            $product->reference                                                   = $data["reference"];

            $product->associations->categories->addChild('category')->addChild('id', $data["id_category_default"]);
            $product->id_category_default                                         = $data["id_category_default"];

            //$product->associations->stock_availables->stock_available->quantity = 1222;

            $opt                                                                  = array('resource' => 'products');
            $opt['postXml']                                                       = $xml->asXML();
            sleep(1);
            $xml                                                                  = $webService->edit($opt);

            $product                                                              = $xml->product;
            $attribute =\Monkey::app()->repoFactory->create('Product')->findOneBy(['id'=>$data['product_id'],'productVariantId'=>$data['productVariantId']]);
            $productSizeGroup =$attribute->productSizeGroupId;
            $size=\Monkey::app()->repoFactory->create('ProductSizeGroupHasProductSize')->findBy(['productSizeGroupId'=>$productSizeGroup]);
            foreach ($size as $sizes) {
                $this->add_combination($data,$sizes->productSizeId, $ProductId);
            }

        } catch (PrestaShopWebserviceException $e){
            return;
        }


        return (int) $product->id;

    }
    function make_father_product($data){

        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);

        try{

            $xml                                                                  = $webService->get(array('url' => PS_SHOP_PATH.'api/products?schema=blank'));
            $product                                                              = $xml->children()->children();
            $product->id_supplier                                                 = $data['id_supplier'];
            $product->id_manufacturer                                             = $data['id_manufacturer'];
            $product->price                                                       = $data["price"]; //Prix TTC
            $product->wholesale_price                                             = $data['wholesale_price']; //Prix d'achat
            $product->id_tax_rules_group                                          = $data['id_tax_rules_group'];
            $product->ean13                                                       = $data['ean13'];
            $product->quantity                                                    = $data["quantity"]; //Prix TTC
            $product->minimal_quantity                                            = 1;
            $product->active                                                      = 1;
            $product->on_sale                                                     = 1; //on ne veux pas de bandeau promo
            $product->show_price                                                  = 1;
            $product->available_for_order                                         = 1;
            $product->state                                                       = 1;

            $product->name->language[0][0]                                        = $data["name"];
            $product->name->language[0][0]['id']                                  = 1;

            $product->description->language[0][0]                                 = $data["description"];
            $product->description->language[0][0]['id']                           = 1;

            $product->description_short->language[0][0]                           = $data["description"];
            $product->description_short->language[0][0]['id']                     = 1;

            $product->meta_keywords->language[0][0]                               = $data["description"];
            $product->meta_keywords->language[0][0]['id']                         = 1;

            $product->meta_title->language[0][0]                                  = $data["description"];
            $product->meta_title->language[0][0]['id']                            = 1;
            $product->meta_description->language[0][0]                                  = $data["description"];
            $product->meta_description->language[0][0]['id']                            = 1;

            $product->description_short->language[0][0]                           = $data["description"];
            $product->description_short->language[0][0]['id']                     = 1;
            $product->available_now->language[0][0]                           = 'In Stock';
            $product->available_now->language[0][0]['id']                     = 1;
            $product->available_later->language[0][0]                           = 'future Arrive';
            $product->available_later->language[0][0]['id']                     = 1;

            $product->reference                                                   = $data["reference"];

            $product->associations->categories->addChild('category')->addChild('id', $data["id_category_default"]);
            $product->id_category_default                                         = $data["id_category_default"];

            //$product->associations->stock_availables->stock_available->quantity = 1222;

            $opt                                                                  = array('resource' => 'products');
            $opt['postXml']                                                       = $xml->asXML();
            sleep(1);
            $xml                                                                  = $webService->add($opt);

            $product                                                              = $xml->product;

        } catch (PrestaShopWebserviceException $e){
            return;
        }

        $attribute =\Monkey::app()->repoFactory->create('Product')->findOneBy(['id'=>$data['product_id'],'productVariantId'=>$data['productVariantId']]);
        $productSizeGroup =$attribute->productSizeGroupId;
        $size=\Monkey::app()->repoFactory->create('ProductSizeGroupHasProductSize')->findBy(['productSizeGroupId'=>$productSizeGroup]);
        foreach ($size as $sizes) {

            $this->add_combination($data, $sizes->productSizeId, $product->id);

        }
        return (int) $product->id;

    }
    function add_combination($data,$sizeId,$idproduct){

        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);
        try {
            $xml = $webService->get(array('url' => PS_SHOP_PATH . 'api/combinations?schema=blank'));

            $combination = $xml->children()->children();
            $productCombination =\Monkey::app()->repoFactory->create('ProductPublicSku')->findOneBy(['productId'=>$data['productId'],'productVariantId'=>$data['productVariantId'],'productSizeId'=>$sizeId]);

            $combination->associations->product_option_values->product_option_values[0]->id =$sizeId;
            $combination->reference = $data["reference"];
            $combination->supplier_reference =$data['reference'].'-'.$sizeId;
            $combination->id_product = $idproduct;
            if(!empty($productCombination)) {
                $combination->quantity = $productCombination->stockQty; //Prix TTC

                if ($data['on_sale']) {
                    $combination->price = $productCombination->salePrice;

                } else {
                    $combination->price = $productCombination->price;
                }
            }else{
                $combination->price=$data['price'];
                $combination->quantity =$data['quantity'];
            }


            $combination->show_price = 1;
            $combination->minimal_quantity = 1;
            //$product_option_value->id                                                     = 1;


            $opt = array('resource' => 'combinations');
            $opt['postXml'] = $xml->asXML();
            sleep(1);
            $xml = $webService->add($opt);
            $combination = $xml->combination;

        } catch (PrestaShopWebserviceException $e) {
            return;
        }
        //insert stock
        return $combination;
        $this->set_product_quantity($data["quantity"], $id_product, $combination->associations->stock_availables->stock_available->id, $combination->associations->stock_availables->stock_available->product_attribute);

    }
    function edit_combination($data,$sizeId,$idproduct){

        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);
        try {
            $xml = $webService->get(array('url' => PS_SHOP_PATH . 'api/combinations?schema=blank'));

            $combination = $xml->children()->children();


            $combination->associations->product_option_values->product_option_values[0]->id =$sizeId;
            $combination->reference = $data["reference"];
            $combination->id_product = $idproduct;
            $combination->price = $data["price"]; //Prix TTC
            $combination->show_price = 1;
            $combination->quantity = $data["quantity"]; //Prix TTC
            $combination->minimal_quantity = 1;
            //$product_option_value->id                                                     = 1;


            $opt = array('resource' => 'combinations');
            $opt['postXml'] = $xml->asXML();
            sleep(1);
            $xml = $webService->add($opt);
            $combination = $xml->combination;

        } catch (PrestaShopWebserviceException $e) {
            return;
        }
        //insert stock
        return $combination;
        $this->set_product_quantity($data["quantity"], $id_product, $combination->associations->stock_availables->stock_available->id, $combination->associations->stock_availables->stock_available->product_attribute);

    }
    function make_product_options($data){

        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);

        try{
            $xml                                              = $webService->get(array('url' => PS_SHOP_PATH.'api/product_option_values?schema=blank'));

            $product_option_value                             = $xml->children()->children();

            $product_option_value->id_attribute_group         = $data["id_attribute_group"];

            $product_option_value->name->language[0][0]       = $data["name"];
            $product_option_value->name->language[0][0]['id'] = 1;


            $opt                                              = array('resource' => 'product_option_values');
            $opt['postXml']                                   = $xml->asXML();
            sleep(1);
            $xml                                              = $webService->add($opt);
            $product_option_value                             = $xml->product_option_value;
        } catch (PrestaShopWebserviceException $e){
            return 0;
        }
        //insert stock
        return (int) $product_option_value->id;
    }



    function make_product($data){


        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);

        try{
            $xml                                                                  = $webService->get(array('url' => PS_SHOP_PATH.'api/products?schema=blank'));
            $product                                                              = $xml->children()->children();

            $product->id_supplier                                                 = $data['id_supplier'];
            $product->id_manufacturer                                             = $data['id_manufacturer'];
            $product->price                                                       = $data["price"]; //Prix TTC
            $product->wholesale_price                                             = $data['wholesale_price']; //Prix d'achat
            $product->id_tax_rules_group                                          = $data['id_tax_rules_group'];
            $product->ean13                                                       = $data['ean13'];
            $product->active                                                      = '1';
            $product->on_sale                                                     = 1; //on ne veux pas de bandeau promo
            $product->show_price                                                  = 1;
            $product->available_for_order                                         = 1;
            $product->state                                                       = 1;

            $product->name->language[0][0]                                        = $data["name"];
            $product->name->language[0][0]['id']                                  = 1;

            $product->description->language[0][0]                                 = $data["description"];
            $product->description->language[0][0]['id']                           = 1;

            $product->description_short->language[0][0]                           = $data["description"];
            $product->description_short->language[0][0]['id']                     = 1;

            $product->meta_keywords->language[0][0]                               = $data["description"];
            $product->meta_keywords->language[0][0]['id']                         = 1;

            $product->meta_title->language[0][0]                                  = $data["description"];
            $product->meta_title->language[0][0]['id']                            = 1;
            $product->meta_description->language[0][0]                                  = $data["description"];
            $product->meta_description->language[0][0]['id']                            = 1;

            $product->description_short->language[0][0]                           = $data["description"];
            $product->description_short->language[0][0]['id']                     = 1;
            $product->available_now->language[0][0]                           = 'In Stock';
            $product->available_now->language[0][0]['id']                     = 1;
            $product->available_later->language[0][0]                           = 'future Arrive';
            $product->available_later->language[0][0]['id']                     = 1;

            $product->reference                                                   = $data["reference"];

            $product->associations->categories->addChild('category')->addChild('id', $data["id_category_default"]);
            $product->id_category_default                                         = $data["id_category_default"];



            $opt                                                                  = array('resource' => 'products');
            $opt['postXml']                                                       = $xml->asXML();
            sleep(1);
            $xml                                                                  = $webService->add($opt);

            $product                                                              = $xml->product;
        } catch (PrestaShopWebserviceException $e){
            return;
        }
        // initialize the class
        $image = new CPrestashopGetImage;
        $url_to_image = $data['picture'];
        if(ENV=='dev') {
            $my_save_dir = '/media/sf_sites/PickyshopNew/tmp/';

        }else{
            $my_save_dir = '/home/iwesshop/public_html/tmp/';
        }
        $filename = basename($url_to_image);
        $complete_save_loc = $my_save_dir . $filename;
        file_put_contents($complete_save_loc, file_get_contents($url_to_image));


        $image->source = $data['picture'];
        if(ENV=='dev') {
            $image->save_to = '/media/sf_sites/PickyshopNew/tmp/';

        }else{
            $image->save_to = '/home/iwesshop/public_html/tmp/';
        }


        $get = $image->download('curl'); // using GD

        if($get)
        {
            echo "The image has been saved.";
        }

        $image_name = basename($data['picture']);



// change the local path where image has been downloaded "presta-api" is my local folder from where i run API script
        if(ENV=='dev') {
            $img_path = '/data/www/iwes/production/sites/pickyshop/tmp/' . $image_name;
        }else{
            $img_path='/media/sf_sites/PickyshopNew/tmp/'. $image_name;
        }



//image will be associated with product id 4
        $url = PS_SHOP_PATH. '/api/images/products/'.$product->id;

        $ch = curl_init();


        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
//curl_setopt($ch, CURLOPT_PUT, true); To edit a picture

        curl_setopt($ch, CURLOPT_USERPWD, PS_WS_AUTH_KEY.':');
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('image'=>"@".$img_path.";type=image/jpeg"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        if(curl_exec($ch) === false)
        {
            echo "<br><br>Error : ".curl_error($ch)."<br>"; }
        else { echo '<br><br> Image added'; }
        curl_close($ch);
        $attribute =\Monkey::app()->repoFactory->create('Product')->findOneBy(['id'=>$data['product_id'],'productVariantId'=>$data['productVariantId']]);
        $productSizeGroup =$attribute->productSizeGroupId;
        $size=\Monkey::app()->repoFactory->create('ProductSizeGroupHasProductSize')->findBy(['productSizeGroupId'=>$productSizeGroup]);
        foreach ($size as $sizes) {
            $this->add_combination($data,$sizes->productSizeId, $product->id);
        }
        //insert stock
        $this->set_product_quantity($data["quantity"], $product->id, $product->associations->stock_availables->stock_available->id, $product->associations->stock_availables->stock_available->id_product_attribute);
        return $product->id;
    }
    /**
     * Actualizar stock usando WS
     */
    function set_product_quantity($quantity, $ProductId, $StokId, $AttributeId){


        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);
        try {
            $opt                             = array();
            $opt['resource']                 = "stock_availables";
            $opt['filter']                   = array('id_product' => $ProductId, "id_product_attribute" => $AttributeId);
            $xml                             = $webService->get($opt);
            $resources                       = $xml->children()->children()[0];
            $StokId                          = (int) $resources['id'][0];

            $xml                             = $webService->get(array('url' => PS_SHOP_PATH.'api/stock_availables?schema=blank'));
            $resources                       = $xml -> children() -> children();
            $resources->id                   = $StokId;
            $resources->id_product           = $ProductId;
            $resources->quantity             = $quantity;
            $resources->id_shop              = 1;
            $resources->out_of_stock         =0;
            $resources->depends_on_stock     = 0;
            $resources->id_product_attribute =$AttributeId;

            $opt                             = array('resource' => 'stock_availables');
            $opt['putXml']                   = $xml->asXML();
            $opt['id']                       = $StokId;
            $xml                             = $webService->edit($opt);
        } catch (PrestaShopWebserviceException $ex) {

        }

    }

}
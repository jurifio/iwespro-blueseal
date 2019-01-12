<?php

namespace bamboo\controllers\back\ajax;


use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;
use PrestaShopWebservice;
use PrestaShopWebserviceException;
use bamboo\controllers\back\ajax\CPrestashopGetImage;
use PDO;
use prepare;

use bamboo\core\exceptions\BambooConfigException;
use bamboo\core\base\CObjectCollection;
use bamboo\utils\time\STimeToolbox;


/**
 * Class CPrestashopInsertNewProduct
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/11/2018
 * @since 1.0
 */
class CPrestashopInsertNewProduct extends AAjaxController
{


    /**
     * @return string
     *
     * @throws \bamboo\core\exceptions\BambooDBALException
     * @throws \bamboo\core\exceptions\BambooException
     */
    public function post()
    {
        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        if (ENV == 'dev') {

            $save_to = '/media/sf_sites/PickyshopNew/tmp/';

        } else {
            $save_to = '/home/pickyshop/public_html/temp-prestashop/';
        }


        /******* apertura e creazione file csv per espostazione********/
        $db_host = "iwes.shop";
        $db_name = "iwesshop_pres848";
        $db_user = "iwesshop_pres848";
        $db_pass = "@5pM5S)Mn8";
        define("HOST", "iwes.shop");
        define("USERNAME", "iwesshop_pres848");
        define("PASSWORD", "@5pM5S)Mn8");
        define("DATABASE", "iwesshop_pres848");
        $res = "";
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
            $save_to = '/home/pickyshop/public_html/temp-prestashop/';
        }


        /**
         * @var $db CMySQLAdapter
         */
        /*********************   preparazione tabella di collegamento  ****************************************************//////
        /*** popolamento tabella */


        /****** SEZIONE PRODOTTI *//////
        /** esportazione prodotti */
        //query prodotti non esportati
        $sql = "SELECT
  php.id                                                                         AS prestaId,
  php.shopId                                                                     AS prestashopId,
  concat(`p`.`id`,'-',p.productVariantId)                                        AS product_id,
  p.id                                                                           AS  productId,
  p.productVariantId                                                             AS productVariantId,
  shp.shopId                                                                     AS id_supplier,
  p.productBrandId                                                               AS id_manufacturer,
  phpc.productCategoryId                                                         AS id_category_default,
  pb.slug                                                                        AS name_manufacturer,
  '1'                                                                            AS id_shop_default,
  '53'                                                                           AS id_tax_rules_group,
  if(p.isOnSale=1,'1','0')                                                       AS `on_sale`,
  '0'                                                                            AS online_only,
  S2.ean                                                                         AS ean13,
  ''                                                                             AS isbn,
  ''                                                                             AS upc,
  '0.000000'                                                                     AS ecotax,
  `p`.`qty`                                                                      AS quantity,
  '1'                                                                            AS minimal_quantity,
  '1'                                                                            AS low_stock_threshold,
  '0'                                                                            AS low_stock_alert,
 php.price  /122*22 AS vatfullprice,
  php.price AS full_price,
  php.priceSale AS salePrice,
  php.priceSale /122*22 AS vatsaleprice,
  php.priceMarketplace AS priceMarketplace,
  php.percentSale AS percentSale,
  php.amount AS increaseAmountSale,  
  php.isOnSale AS isOnSale,
  IF(`php`.isOnSale=1,'saldo','prezzopieno') AS tipoprezzo,
  php.price   AS price,
  php.titleSale AS titleSale,
  php.prestashopId AS shopPrestashopId,
  '0'                                                   AS wholesale_price,
  '0'                                                                            AS unity,
  '0.000000'                                                                     AS unit_price_ratio,
  concat(p.id,'-',p.productVariantId)                                            AS reference,
  concat(p.id,'-',p.productVariantId)                                            AS `name`,
  dp.itemno                                                                      AS supplier_reference,
  ''                                                                             AS location,
  '0.000000'                                                                     AS width,
  '0.000000'                                                                     AS height,
  '0.000000'                                                                     AS depth,
  '0.000000'                                                                     AS weight,
  '2'                                                                            AS out_of_stock,
  '0'                                                                            AS additional_delivery_times,
  '0'                                                                            AS quantity_discount,
  '0'                                                                            AS text_fields,
  '0'                AS discount_amount,
  ''                                                                             AS discount_percent,
  '2018-01-01'                                                                   AS discount_from,
  '2018-01-01'                                                                   AS discount_to,
  concat(pb.name,' ',pn.name,' ',dp.var , dp.itemno,' ', pv.name)                AS productName,
  pb.name                                                                        AS brand_name,
  dp.var                                                                         AS color_supplier,
  concat(p.id,'-',p.productVariantId)                                            AS description,
  'both'                                                                         AS visibility,
  '0'                                                                            AS cache_is_pack,
  '0'                                                                            AS cache_has_attachments,
  '0'                                                                            AS is_virtual,
  '0'                                                                            AS cache_default_attribute,
  '0'                                                                            AS additional_shipping_cost,
  concat(p.id,'-',p.productVariantId)                                            AS short_description,
  date_format(NOW(),'%Y-%m-%d %H:%i:%s')                                         AS date_add,
  date_format(NOW(),'%Y-%m-%d %H:%i:%s')                                         AS date_upd,
  '1'                                                                            AS available_for_order,
  '2018-01-01'                                                                   AS available_date,
  '1'                                                                            AS indexed,
  '0'                                                                            AS customizable,
  '0'                                                                            AS uploadable_files,
  '1'                                                                            AS active,
  '404'                                                                          AS redirect_type,
  '0'                                                                            AS id_type_redirected,
  '1'                                                                            AS show_condition,
  'new'                                                                          AS`condition`,
  '1'                                                                            AS show_price,
  '1'                                                                            AS showPrice,
  concat('https://iwes.s3.amazonaws.com/',pb.slug,'/',p.id,'-',p.productVariantId,'-001-1124.jpg')
    AS picture,
  concat(p.id,'-',p.productVariantId)                                            AS imageAlt,
  '1'                                                                            AS deleteImage,
  ''                                                                             AS feature,
  '1'                                                                            AS idshop,
  '0'                                                                            AS advanced_stock_management,
  '3'                                                                            AS pack_stock_type,
  '0'                                                                            AS depend_on_stock,
  '1'                                                                            AS Warehouse,
  '1'                                                                            AS state,
  php.statusPublished                                                                     AS status

FROM `Product` `p`
  JOIN `ProductVariant` `pv` ON `p`.`productVariantId` = `pv`.`id`
  JOIN `ProductBrand` `pb` ON `p`.`productBrandId` = `pb`.`id`
  JOIN `ProductStatus` `pss` ON `pss`.`id` = `p`.`productStatusId`
  JOIN `ShopHasProduct` `shp` ON (`p`.`id`, `p`.`productVariantId`) = (`shp`.`productId`, `shp`.`productVariantId`)
  JOIN `Shop` `s` ON `s`.`id` = `shp`.`shopId`
  JOIN  `ProductPublicSku` S3 ON  (`p`.`id`, `p`.`productVariantId`) = (`S3`.`productId`, `S3`.`productVariantId`)
  JOIN  `ProductSku` S2 ON  (`p`.`id`, `p`.`productVariantId`) = (`S2`.`productId`, `S2`.`productVariantId`)
  JOIN `ProductHasProductCategory` `phpc`  ON (`p`.`id`, `p`.`productVariantId`)=(`phpc`.`productId`, `phpc`.`productVariantId`)
  LEFT JOIN  ProductDescriptionTranslation pdt ON p.id = pdt.productId AND p.productVariantId = pdt.productVariantId
  JOIN  MarketplaceHasProductAssociate php ON p.id = php.productId  AND p.productVariantId =php.productVariantId
 LEFT JOIN (DirtyProduct dp
    JOIN DirtySku ds ON dp.id = ds.dirtyProductId)
    ON (shp.productId,shp.productVariantId,shp.shopId) = (dp.productId,dp.productVariantId,dp.shopId)
  LEFT  JOIN ProductColorGroup PCG ON p.productColorGroupId = PCG.id
  LEFT JOIN ProductName pn ON p.id = pn.id
  LEFT JOIN MarketplaceHasShop mpas ON php.shopId=mpas.shopId
WHERE  `p`.`qty` > 0  AND php.statusPublished = 0  AND S3.price > 0 
GROUP BY p.id,p.productVariantId
ORDER BY `p`.`id`";

        /*and p.productStatuId = 6 */
        $res_product = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        $productEanRepo = \Monkey::app()->repoFactory->create('ProductEan');
        $productNameTranslationRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');
        $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');

        foreach ($res_product as $value_product) {
            $p = $value_product['prestaId'];
            $deletepsz6_image_shop = $db_con->prepare("DELETE FROM psz6_image_shop WHERE id_product=" . $p);
            $deletepsz6_image_shop->execute();
            $deletepsz6_image = $db_con->prepare("DELETE FROM psz6_image WHERE id_product=" . $p);
            $deletepsz6_image->execute();

            $id_supplier = $value_product['id_supplier'];
            $id_manufacturer = $value_product['id_manufacturer'];
            $id_category_default = $value_product['id_category_default'];
            $id_shop_default = $value_product['id_shop_default'];
            $id_tax_rules_group = $value_product['id_tax_rules_group'];
            $on_sale = $value_product['on_sale'];
            $online_only = $value_product['online_only'];
            if ($value_product['ean13'] == '') {
                $productEanFind = $productEanRepo->findOneBy(['productId' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId'], 'productSizeId' => 0]);
                if ($productEanFind == null) {
                    $productEanInsert = $productEanRepo->findOneBy(['productId' => null, 'productVariantId' => null, 'productSizeId' => null, 'used' => 0]);
                    $productEanInsert->productId = $value_product['productId'];
                    $productEanInsert->productVariantId = $value_product['productVariantId'];
                    $productEanInsert->productSizeId = 0;
                    $productEanInsert->usedForParent = 1;
                    $productEanInsert->used = 1;
                    $productEanInsert->brandAssociate = $value_product['id_manufacturer'];
                    $productEanInsert->shopId = $value_product['id_supplier'];
                    $productEanInsert->insert();
                    $productEanRefind = $productEanRepo->findOneBy(['productId' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId'], 'productSizeId' => 0]);
                    $ean13 = $productEanRefind->ean;

                } else {
                    $ean13 = $productEanFind->ean;

                }

            } else {
                $ean13 = $value_product['ean13'];
            }


            $isbn = $value_product['isbn'];
            $upc = $value_product['upc'];
            $ecotax = $value_product['ecotax'];
            $quantity = $value_product['quantity'];
            $minimal_quantity = $value_product['minimal_quantity'];
            $low_stock_thresold = '1';
            $low_stock_alert = '0';
            $price = $value_product['price'];
            $wholesale_price = $value_product['wholesale_price'];
            $unity = $value_product['unity'];
            $unit_price_ratio = $value_product['unit_price_ratio'];
            $additional_shipping_cost = $value_product['additional_shipping_cost'];
            $reference = $value_product['reference'];
            $supplier_reference = $value_product['supplier_reference'];
            $location = $value_product['location'];
            $width = '45';
            $height = '27';
            $depth = '20';
            $weight = '1';
            $out_of_stock = $value_product['out_of_stock'];
            $additional_delivery_times = '1';
            $quantity_discount = $value_product['quantity_discount'];
            $customizable = $value_product['customizable'];
            $uploadable_files = $value_product['uploadable_files'];
            $text_fields = $value_product['text_fields'];
            $active = $value_product['active'];
            $redirect_type = $value_product['redirect_type'];
            $id_type_redirected = $value_product['id_type_redirected'];
            $available_for_order = $value_product['available_for_order'];
            $available_date = $value_product['available_date'];
            $show_condition = $value_product['show_condition'];
            $condition = $value_product['condition'];
            $show_price = $value_product['show_price'];
            $indexed = $value_product['indexed'];
            $visibility = $value_product['visibility'];
            $cache_is_pack = $value_product['cache_is_pack'];
            $cache_has_attachments = $value_product['cache_has_attachments'];
            $is_virtual = $value_product['is_virtual'];
            $cache_default_attribute = $value_product['cache_default_attribute'];
            $date_add = $value_product['date_add'];
            $date_upd = $value_product['date_upd'];
            $advanced_stock_management = $value_product['advanced_stock_management'];
            $pack_stock_type = $value_product['pack_stock_type'];
            $state = $value_product['state'];
            $status = $value_product['status'];
            try {
                $stmtFeatureProductDelete = $db_con->prepare("DELETE FROM psz6_feature_product WHERE id_product=" . $p);
                $stmtFeatureProductDelete->execute();

                $stmtInsertProduct = $db_con->prepare("INSERT INTO psz6_product (`id_product`,
                                                                          `id_supplier`,
                                                                          `id_manufacturer`,
                                                                          `id_category_default`,
                                                                          `id_shop_default`,
                                                                          `id_tax_rules_group`,
                                                                          `on_sale`,
                                                                          `online_only`,
                                                                          `ean13`,
                                                                          `isbn`,
                                                                          `upc`,
                                                                          `ecotax`,
                                                                          `quantity`,
                                                                          `minimal_quantity`,
                                                                          `low_stock_threshold`,
                                                                          `low_stock_alert`,
                                                                          `price`,
                                                                          `wholesale_price`,
                                                                          `unity`,
                                                                          `unit_price_ratio`,
                                                                          `additional_shipping_cost`,
                                                                          `reference`,
                                                                          `supplier_reference`,
                                                                          `location`,
                                                                          `width`,
                                                                          `height`,
                                                                          `depth`,
                                                                          `weight`,
                                                                          `out_of_stock`,
                                                                          `additional_delivery_times`,
                                                                          `quantity_discount`,
                                                                          `customizable`,
                                                                          `uploadable_files`,
                                                                          `text_fields`,
                                                                          `active`,
                                                                          `redirect_type`,
                                                                          `id_type_redirected`,
                                                                          `available_for_order`,
                                                                          `available_date`,
                                                                          `show_condition`,
                                                                          `condition`,
                                                                          `show_price`,
                                                                          `indexed`,
                                                                          `visibility`,
                                                                          `cache_is_pack`,
                                                                          `cache_has_attachments`,
                                                                          `is_virtual`,
                                                                          `cache_default_attribute`,
                                                                          `date_add`,
                                                                          `date_upd`,
                                                                          `advanced_stock_management`,
                                                                          `pack_stock_type`,
                                                                          `state`) 
                                                   VALUES ('" . $p . "',
                                                           '" . $id_supplier . "',
                                                           '" . $id_manufacturer . "',
                                                           '" . $id_category_default . "',
                                                           '" . $id_shop_default . "',
                                                           '" . $id_tax_rules_group . "',
                                                           '" . $on_sale . "',
                                                           '" . $online_only . "',
                                                            '" . $ean13 . "',
                                                           '" . $isbn . "',
                                                           '" . $upc . "',
                                                           '" . $ecotax . "',
                                                           '" . $quantity . "',
                                                           '" . $minimal_quantity . "',
                                                           '" . $low_stock_thresold . "',
                                                           '" . $low_stock_alert . "',
                                                           '" . $price . "',
                                                           '" . $wholesale_price . "',
                                                           '" . $unity . "',
                                                           '" . $unit_price_ratio . "',
                                                           '" . $additional_shipping_cost . "',
                                                           '" . $reference . "',
                                                           '" . $supplier_reference . "',
                                                           '" . $location . "',
                                                           '" . $width . "',
                                                           '" . $height . "',
                                                           '" . $depth . "',
                                                           '" . $weight . "',
                                                           '" . $out_of_stock . "',
                                                           '" . $additional_delivery_times . "',
                                                           '" . $quantity_discount . "',
                                                           '" . $customizable . "',
                                                           '" . $uploadable_files . "',
                                                           '" . $text_fields . "',
                                                           '" . $active . "',
                                                           '" . $redirect_type . "',
                                                           '" . $id_type_redirected . "',
                                                           '" . $available_for_order . "',
                                                           '" . $available_date . "',
                                                           '" . $show_condition . "',
                                                           '" . $condition . "',
                                                           '" . $show_price . "',
                                                           '" . $indexed . "',
                                                           '" . $visibility . "',
                                                           '" . $cache_is_pack . "',
                                                           '" . $cache_has_attachments . "',
                                                           '" . $is_virtual . "',
                                                           '" . $cache_default_attribute . "',
                                                           '" . $date_add . "',
                                                           '" . $date_upd . "',
                                                           '" . $advanced_stock_management . "',
                                                           '" . $pack_stock_type . "',
                                                           '" . $state . "')
                                                            ON DUPLICATE KEY 
                                                           UPDATE 
                                                `id_product`                ='" . $p . "',
                                                `id_supplier`               ='" . $id_supplier . "',
                                                `id_manufacturer`           ='" . $id_manufacturer . "',
                                                `id_category_default`       ='" . $id_category_default . "',
                                                `id_shop_default`           ='" . $id_shop_default . "', 
                                                `id_tax_rules_group`        ='" . $id_tax_rules_group . "',
                                                `on_sale`                   ='" . $on_sale . "',
                                                `online_only`               ='" . $online_only . "',
                                                `ean13`                     ='" . $ean13 . "',
                                                `isbn`                      ='" . $isbn . "',
                                                `upc`                       ='" . $upc . "',
                                                `ecotax`                    ='" . $ecotax . "',
                                                `quantity`                  ='" . $quantity . "',
                                                `minimal_quantity`          ='" . $minimal_quantity . "',
                                                `low_stock_threshold`       ='" . $low_stock_thresold . "',
                                                `low_stock_alert`           ='" . $low_stock_alert . "',
                                                `price`                     ='" . $price . "',
                                                `wholesale_price`           ='" . $wholesale_price . "',
                                                `unity`                     ='" . $unity . "',
                                                `unit_price_ratio`          ='" . $unit_price_ratio . "',
                                                `additional_shipping_cost`  ='" . $additional_shipping_cost . "',
                                                `reference`                 ='" . $reference . "',
                                                `supplier_reference`        ='" . $supplier_reference . "',
                                                `location`                  ='" . $location . "',
                                                `width`                     ='" . $width . "',
                                                `height`                    ='" . $height . "',
                                                `depth`                     ='" . $depth . "',
                                                `weight`                    ='" . $weight . "',
                                                `out_of_stock`              ='" . $out_of_stock . "',
                                                `additional_delivery_times`='" . $additional_delivery_times . "',
                                                `quantity_discount`         ='" . $quantity_discount . "',
                                                `customizable`              ='" . $customizable . "',
                                                `uploadable_files`          ='" . $uploadable_files . "',
                                                `text_fields`               ='" . $text_fields . "',
                                                `active`                    ='" . $active . "',
                                                `redirect_type`             ='" . $redirect_type . "',
                                                `id_type_redirected`        ='" . $id_type_redirected . "',
                                                `available_for_order`       ='" . $available_for_order . "',
                                                `available_date`            ='" . $available_date . "',
                                                `show_condition`            ='" . $show_condition . "',
                                                `condition`                 ='" . $condition . "',
                                                `show_price`                ='" . $show_price . "',
                                                `indexed`                   ='" . $indexed . "',
                                                `visibility`                ='" . $visibility . "',
                                                `cache_is_pack`             ='" . $cache_is_pack . "',
                                                `cache_has_attachments`     ='" . $cache_has_attachments . "',
                                                `is_virtual`                ='" . $is_virtual . "',
                                                `cache_default_attribute`   ='" . $cache_default_attribute . "',
                                                `date_add`                  ='" . $date_add . "',
                                                `date_upd`                  ='" . $date_upd . "',
                                                `advanced_stock_management` ='" . $advanced_stock_management . "',
                                                `pack_stock_type`           ='" . $pack_stock_type . "',
                                                `state`                     ='" . $state . "' ");

                $stmtInsertProduct->execute();


                $stmtInsertProductShop = $db_con->prepare("INSERT INTO psz6_product_shop (       
                                                                          `id_product`,
                                                                          `id_shop`,
                                                                          `id_category_default`,
                                                                          `id_tax_rules_group`,
                                                                          `on_sale`,
                                                                          `online_only`,
                                                                          `ecotax`,
                                                                          `minimal_quantity`,
                                                                          `low_stock_threshold`,
                                                                          `low_stock_alert`,
                                                                          `price`,
                                                                          `wholesale_price`,
                                                                          `unity`,
                                                                          `unit_price_ratio`,
                                                                          `additional_shipping_cost`,
                                                                          `customizable`,
                                                                          `uploadable_files`,
                                                                          `text_fields`,
                                                                          `active`,
                                                                          `redirect_type`,
                                                                          `id_type_redirected`,
                                                                          `available_for_order`,
                                                                          `available_date`,
                                                                          `show_condition`,
                                                                          `condition`,
                                                                          `show_price`,
                                                                          `indexed`,
                                                                          `visibility`,
                                                                          `cache_default_attribute`,
                                                                          `advanced_stock_management`,
                                                                          `date_add`,
                                                                          `date_upd`,
                                                                          `pack_stock_type`
                                                                          ) 
                                                   VALUES ('" . $p . "',
                                                           '" . $id_shop_default . "',
                                                           '" . $id_category_default . "',
                                                           '" . $id_tax_rules_group . "',
                                                           '" . $on_sale . "',
                                                           '" . $online_only . "',
                                                           '" . $ecotax . "',
                                                           '" . $minimal_quantity . "',
                                                           '" . $low_stock_thresold . "',
                                                           '" . $low_stock_alert . "',
                                                           '" . $price . "',
                                                           '" . $wholesale_price . "',
                                                           '" . $unity . "',
                                                           '" . $unit_price_ratio . "',
                                                           '" . $additional_shipping_cost . "',
                                                           '" . $customizable . "',
                                                           '" . $uploadable_files . "',
                                                           '" . $text_fields . "',
                                                           '" . $active . "',
                                                           '" . $redirect_type . "',
                                                           '" . $id_type_redirected . "',
                                                           '" . $available_for_order . "',
                                                           '" . $available_date . "',
                                                           '" . $show_condition . "',
                                                           '" . $condition . "',
                                                           '" . $show_price . "',
                                                           '" . $indexed . "',
                                                           '" . $visibility . "',
                                                           '" . $cache_default_attribute . "',
                                                           '" . $advanced_stock_management . "',
                                                           '" . $date_add . "',
                                                           '" . $date_upd . "',
                                                           '" . $pack_stock_type . "'
                                                          )
                                                          ON DUPLICATE KEY 
                                                           UPDATE 
                                                `id_product`                    ='" . $p . "',
                                                `id_shop`                       ='" . $id_shop_default . "',
                                                `id_category_default`           ='" . $id_category_default . "',
                                                `id_tax_rules_group`            ='" . $id_tax_rules_group . "', 
                                                `on_sale`                       ='" . $on_sale . "',
                                                `online_only`                   ='" . $online_only . "',
                                                `ecotax`                    ='" . $ecotax . "',
                                                `minimal_quantity`          ='" . $minimal_quantity . "',
                                                `low_stock_threshold`       ='" . $low_stock_thresold . "',
                                                `low_stock_alert`           ='" . $low_stock_alert . "',
                                                `price`                     ='" . $price . "',
                                                `wholesale_price`           ='" . $wholesale_price . "',
                                                `unity`                     ='" . $unity . "',
                                                `unit_price_ratio`          ='" . $unit_price_ratio . "',
                                                `additional_shipping_cost`  ='" . $additional_shipping_cost . "',
                                                `customizable`              ='" . $customizable . "',
                                                `uploadable_files`          ='" . $uploadable_files . "',
                                                `text_fields`               ='" . $text_fields . "',
                                                `active`                    ='" . $active . "',
                                                `redirect_type`             ='" . $redirect_type . "',
                                                `id_type_redirected`        ='" . $id_type_redirected . "',
                                                `available_for_order`       ='" . $available_for_order . "',
                                                `available_date`            ='" . $available_date . "',
                                                `show_condition`            ='" . $show_condition . "',
                                                `condition`                 ='" . $condition . "',
                                                `show_price`                ='" . $show_price . "',
                                                `indexed`                   ='" . $indexed . "',
                                                `visibility`                ='" . $visibility . "',
                                                `cache_default_attribute`   ='" . $cache_default_attribute . "',
                                                `advanced_stock_management` ='" . $advanced_stock_management . "',
                                                `date_add`                  ='" . $date_add . "',
                                                `date_upd`                  ='" . $date_upd . "',
                                                `pack_stock_type`           ='" . $pack_stock_type . "'
                                                          ");

                $stmtInsertProductShop->execute();


                $res_product_lang = $productNameTranslationRepo->findOneBy(['productId' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId'], 'langId' => '2']);
                if (empty($res_product_lang)) {

                    iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", htmlentities($name_product_lang = $value_product['brand_name'] . " " . $value_product['supplier_reference'] . " " . $value_product['color_supplier'], ENT_QUOTES)));
                    $in_stock = "in stock";
                    $current_supply = "Current supply. Ordering available";
                    $product_available = "Delivered in 3-4 Days";
                    $product_not_available = "Delivered in 10-15 Days";
                    $valuelang = 1;

                    $stmtLangProduct = $db_con->prepare("INSERT INTO psz6_product_lang (
                                                              `id_product`,
                                                               `id_shop`,
                                                               `id_lang`,
                                                               `description`,
                                                               `description_short`,
                                                               `link_rewrite`,
                                                               `meta_description`,
                                                               `meta_keywords`,
                                                               `meta_title`,
                                                               `name`,
                                                               `available_now`,
                                                               `available_later`,
                                                               `delivery_in_stock`,
                                                               `delivery_out_stock`) 
                                                   VALUES ('" . $p . "',
                                                           '" . $id_shop_default . "',
                                                           '" . $valuelang . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . $value_product['product_id'] . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . $in_stock . "',
                                                           '" . $current_supply . "',
                                                           '" . $product_available . "',
                                                           '" . $product_not_available . "')
                                                           ON DUPLICATE KEY 
                                                           UPDATE 
                                                               `id_product`        ='" . $p . "',
                                                               `id_shop`           ='" . $id_shop_default . "',
                                                               `id_lang`           ='" . $valuelang . "',
                                                               `description`       ='" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `description_short` ='" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `link_rewrite`      = '" . $value_product['product_id'] . "',
                                                               `meta_description`  = '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `meta_keywords`     = '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `meta_title`        = '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `name`              = '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `available_now`     = '" . $in_stock . "',
                                                               `available_later`   = '" . $current_supply . "',
                                                               `delivery_in_stock` = '" . $product_available . "',
                                                               `delivery_out_stock`= '" . $product_not_available . "'
                                                           ");

                    $stmtLangProduct->execute();

                } else {


                    iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", htmlentities($name_product_lang = $value_product['brand_name'] . " " . $res_product_lang->name . " " . $value_product['supplier_reference'] . " " . $value_product['color_supplier'], ENT_QUOTES)));

                    $in_stock = "in stock";
                    $current_supply = "Current supply. Ordering available";
                    $product_available = "Delivered in 3-4 Days";
                    $product_not_available = "Delivered in 10-15 Days";

                    $valuelang = 1;


                    $stmtLangProduct = $db_con->prepare("INSERT INTO psz6_product_lang (
                                                              `id_product`,
                                                               `id_shop`,
                                                               `id_lang`,
                                                               `description`,
                                                               `description_short`,
                                                               `link_rewrite`,
                                                               `meta_description`,
                                                               `meta_keywords`,
                                                               `meta_title`,
                                                               `name`,
                                                               `available_now`,
                                                               `available_later`,
                                                               `delivery_in_stock`,
                                                               `delivery_out_stock`) 
                                                   VALUES ('" . $p . "',
                                                           '" . $id_shop_default . "',
                                                           '" . $valuelang . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . $value_product['product_id'] . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . $in_stock . "',
                                                           '" . $current_supply . "',
                                                           '" . $product_available . "',
                                                           '" . $product_not_available . "')
                                                           ON DUPLICATE KEY 
                                                           UPDATE 
                                                               `id_product`        ='" . $p . "',
                                                               `id_shop`           ='" . $id_shop_default . "',
                                                               `id_lang`           ='" . $valuelang . "',
                                                               `description`       ='" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `description_short` ='" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `link_rewrite`      = '" . $value_product['product_id'] . "',
                                                               `meta_description`  = '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `meta_keywords`     = '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `meta_title`        = '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `name`              = '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `available_now`     = '" . $in_stock . "',
                                                               `available_later`   = '" . $current_supply . "',
                                                               `delivery_in_stock` = '" . $product_available . "',
                                                               `delivery_out_stock`= '" . $product_not_available . "'
                                                           ");

                    $stmtLangProduct->execute();
                }
                $res_product_lang = $productNameTranslationRepo->findOneBy(['productId' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId'], 'langId' => '1']);
                if (empty($res_product_lang)) {
                    iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", htmlentities($name_product_lang = $value_product['brand_name'] . " " . $value_product['supplier_reference'] . " " . $value_product['color_supplier'], ENT_QUOTES)));
                    $in_stock = "in Vendita";
                    $current_supply = 'In magazzino. ordinabile';
                    $product_available = 'Consegna in 3-4 Giorni Lavorati';
                    $product_not_available = 'Consegna  in 10-15 lavorativi';
                    $valuelang = 2;

                    $stmtLangProduct = $db_con->prepare("INSERT INTO psz6_product_lang (
                                                              `id_product`,
                                                               `id_shop`,
                                                               `id_lang`,
                                                               `description`,
                                                               `description_short`,
                                                               `link_rewrite`,
                                                               `meta_description`,
                                                               `meta_keywords`,
                                                               `meta_title`,
                                                               `name`,
                                                               `available_now`,
                                                               `available_later`,
                                                               `delivery_in_stock`,
                                                               `delivery_out_stock`) 
                                                   VALUES ('" . $p . "',
                                                           '" . $id_shop_default . "',
                                                           '" . $valuelang . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . $value_product['product_id'] . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . $in_stock . "',
                                                           '" . $current_supply . "',
                                                           '" . $product_available . "',
                                                           '" . $product_not_available . "')
                                                           ON DUPLICATE KEY 
                                                           UPDATE 
                                                               `id_product`        ='" . $p . "',
                                                               `id_shop`           ='" . $id_shop_default . "',
                                                               `id_lang`           ='" . $valuelang . "',
                                                               `description`       ='" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `description_short` ='" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `link_rewrite`      = '" . $value_product['product_id'] . "',
                                                               `meta_description`  = '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `meta_keywords`     = '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `meta_title`        = '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `name`              = '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `available_now`     = '" . $in_stock . "',
                                                               `available_later`   = '" . $current_supply . "',
                                                               `delivery_in_stock` = '" . $product_available . "',
                                                               `delivery_out_stock`= '" . $product_not_available . "'
                                                           ");

                    $stmtLangProduct->execute();


                } else {


                    iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", htmlentities($name_product_lang = $value_product['brand_name'] . " " . $res_product_lang->name . " " . $value_product['supplier_reference'] . " " . $value_product['color_supplier'], ENT_QUOTES)));


                    $in_stock = "in Vendita";
                    $current_supply = 'In magazzino. ordinabile';
                    $product_available = 'Consegna in 3-4 Giorni Lavorati';
                    $product_not_available = 'Consegna  in 10-15 lavorativi';


                    $valuelang = 2;

                    $stmtLangProduct = $db_con->prepare("INSERT INTO psz6_product_lang (
                                                              `id_product`,
                                                               `id_shop`,
                                                               `id_lang`,
                                                               `description`,
                                                               `description_short`,
                                                               `link_rewrite`,
                                                               `meta_description`,
                                                               `meta_keywords`,
                                                               `meta_title`,
                                                               `name`,
                                                               `available_now`,
                                                               `available_later`,
                                                               `delivery_in_stock`,
                                                               `delivery_out_stock`) 
                                                   VALUES ('" . $p . "',
                                                           '" . $id_shop_default . "',
                                                           '" . $valuelang . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . $value_product['product_id'] . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                           '" . $in_stock . "',
                                                           '" . $current_supply . "',
                                                           '" . $product_available . "',
                                                           '" . $product_not_available . "')
                                                            ON DUPLICATE KEY 
                                                           UPDATE 
                                                               `id_product`        ='" . $p . "',
                                                               `id_shop`           ='" . $id_shop_default . "',
                                                               `id_lang`           ='" . $valuelang . "',
                                                               `description`       ='" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `description_short` ='" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `link_rewrite`      = '" . $value_product['product_id'] . "',
                                                               `meta_description`  = '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `meta_keywords`     = '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `meta_title`        = '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `name`              = '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                               `available_now`     = '" . $in_stock . "',
                                                               `available_later`   = '" . $current_supply . "',
                                                               `delivery_in_stock` = '" . $product_available . "',
                                                               `delivery_out_stock`= '" . $product_not_available . "'
                                                           ");

                    $stmtLangProduct->execute();
                }
                /*  $res_product_lang = \Monkey::app()->repoFactory->create('ProductNameTranslation')->findBy(['productId' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId'], 'langId' => '3']);
                  if (empty($res_product_lang)) {
                      iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", htmlentities($name_product_lang = $value_product['brand_name'] . " " . $value_product['supplier_reference'] . " " . $value_product['color_supplier'], ENT_QUOTES)));
                      $in_stock = "in stock";
                      $current_supply = "Current supply. Ordering available";
                      $product_available = "Delivered in 3-4 Days";
                      $product_not_available = "Delivered in 10-15 Days";
                      $valuelang = 3;

                      $stmtLangProduct = $db_con->prepare("INSERT INTO psz6_product_lang (
                                                                    `id_product`,
                                                                     `id_shop`,
                                                                     `id_lang`,
                                                                     `description`,
                                                                     `description_short`,
                                                                     `link_rewrite`,
                                                                     `meta_description`,
                                                                     `meta_keywords`,
                                                                     `meta_title`,
                                                                     `name`,
                                                                     `available_now`,
                                                                     `available_later`,
                                                                     `delivery_in_stock`,
                                                                     `delivery_out_stock`)
                                                         VALUES ('" . $p . "',
                                                                 '" . $id_shop_default . "',
                                                                 '" . $valuelang . "',
                                                                 '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                                 '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                                 '" . $value_product['product_id'] . "',
                                                                 '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                                 '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                                 '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                                 '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                                 '" . $in_stock . "',
                                                                 '" . $current_supply . "',
                                                                 '" . $product_available . "',
                                                                 '" . $product_not_available . "')");

                      $stmtLangProduct->execute();
                  } else {


                      iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", htmlentities($name_product_lang = $value_product['brand_name'] . " " . $res_product_lang->name . " " . $value_product['supplier_reference'] . " " . $value_product['color_supplier'], ENT_QUOTES)));


                      $in_stock = "in stock";
                      $current_supply = "Current supply. Ordering available";
                      $product_available = "Delivered in 3-4 Days";
                      $product_not_available = "Delivered in 10-15 Days";


                      $valuelang = 3;

                      $stmtLangProduct = $db_con->prepare("INSERT INTO psz6_product_lang (
                                                                    `id_product`,
                                                                     `id_shop`,
                                                                     `id_lang`,
                                                                     `description`,
                                                                     `description_short`,
                                                                     `link_rewrite`,
                                                                     `meta_description`,
                                                                     `meta_keywords`,
                                                                     `meta_title`,
                                                                     `name`,
                                                                     `available_now`,
                                                                     `available_later`,
                                                                     `delivery_in_stock`,
                                                                     `delivery_out_stock`)
                                                         VALUES ('" . $p . "',
                                                                 '" . $id_shop_default . "',
                                                                 '" . $valuelang . "',
                                                                 '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                                 '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                                 '" . $value_product['product_id'] . "',
                                                                 '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                                 '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                                 '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                                 '" . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'", " ", $name_product_lang)) . "',
                                                                 '" . $in_stock . "',
                                                                 '" . $current_supply . "',
                                                                 '" . $product_available . "',
                                                                 '" . $product_not_available . "')");

                      $stmtLangProduct->execute();
                  }*/
                /** ricerca varianti attributi */
                /**** acquisizione ultimo id attributo tabella psz6_attribute_product
                 */
                $stmtLastIdProductAttribute = $db_con->prepare("SELECT max(id_product_attribute) AS maxIdProductAttribute FROM psz6_product_attribute");
                $stmtLastIdProductAttribute->execute();
                $id_product_attribute = $stmtLastIdProductAttribute->fetch();
                $w = $id_product_attribute[0];

                $res_product_attribute = $productSkuRepo->findBy(['productId' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId']]);
                $lock_default_on = 0;
                $stmtdeleteProductAttributeStockAvailable = $db_con->prepare("DELETE FROM psz6_stock_available WHERE id_product=" . $p);
                $stmtdeleteProductAttributeStockAvailable->execute();
                foreach ($res_product_attribute as $value_product_attribute) {
                    $w = $w + 1;
                    $productSizeId_attribute_combination = $value_product_attribute->productSizeId;
                    $quantity_attribute_combination = $value_product_attribute->stockQty;
                    if (($quantity_attribute_combination >= 0) && ($lock_default_on == 0)) {
                        $default_on = '1';
                        $lock_default_on = 1;
                    } else {
                        $default_on = '0';
                    }
                    $price_attribute_combination = $value_product_attribute->price - ($value_product_attribute->price * 22 / 122);
                    $salePrice_attribute_combination = $value_product_attribute->salePrice - ($value_product_attribute->salePrice * 22 / 122);
                    if ($value_product['on_sale'] == '1') {
                        $price = $salePrice_attribute_combination;
                    } else {
                        $price = $price_attribute_combination;
                    }
                    if ($quantity_attribute_combination >= 1) {
                        $available_date = date("Y-m-d");

                    } else {
                        $available_date = '2018-08-01';
                    }

                    $stmtInsertProductAttribute = $db_con->prepare("INSERT INTO psz6_product_attribute (`id_product_attribute`,
                                                                          `id_product`,
                                                                          `reference`,
                                                                          `supplier_reference`,
                                                                          `location`,
                                                                          `ean13`,
                                                                          `isbn`,
                                                                          `upc`,
                                                                          `wholesale_price`,
                                                                          `price`,
                                                                          `ecotax`,
                                                                          `quantity`,
                                                                          `weight`,
                                                                          `unit_price_impact`,
                                                                          `default_on`,             
                                                                          `minimal_quantity`,
                                                                          `low_stock_threshold`,
                                                                          `low_stock_alert`,
                                                                          `available_date`) 
                                                   VALUES ('" . $w . "',
                                                           '" . $p . "',
                                                           '" . $value_product['reference'] . "-" . $productSizeId_attribute_combination . "',
                                                           '" . $value_product['supplier_reference'] . "',
                                                           ' ',
                                                           '" . $value_product_attribute->ean . "',
                                                           '" . $value_product['isbn'] . "',
                                                           '" . $value_product['upc'] . "',
                                                           '0.000000',
                                                           '.0.000000',
                                                            '" . $value_product['ecotax'] . "',
                                                           '" . $quantity_attribute_combination . "',
                                                           '" . $value_product['weight'] . "',
                                                            '0.000000',
                                                            NULL,
                                                           '" . $value_product['minimal_quantity'] . "',
                                                           '" . $value_product['low_stock_threshold'] . "',
                                                           '" . $value_product['low_stock_alert'] . "',
                                                           '" . $available_date . "')
                                                           ON DUPLICATE KEY UPDATE
                                                `id_product_attribute`      =  '" . $w . "',    
                                                `id_product`                ='" . $p . "',
                                                `reference`                 ='" . $value_product['reference'] . "-" . $productSizeId_attribute_combination . "',
                                                `supplier_reference`        = '" . $value_product['supplier_reference'] . "',
                                                `location`                  =' ', 
                                                `ean13`                     = '" . $value_product_attribute->ean . "',
                                                `isbn`                      = '" . $value_product['isbn'] . "',
                                                `upc`                       = '" . $value_product['upc'] . "',
                                                `wholesale_price`           = '0.000000',
                                                `price`                     ='.0.000000',
                                                `ecotax`                    = '" . $value_product['ecotax'] . "',
                                                `quantity`                  ='" . $quantity_attribute_combination . "',
                                                `weight`                    ='" . $value_product['weight'] . "',
                                                `unit_price_impact`         = '0.000000',
                                                 `default_on`               = NULL,
                                                `minimal_quantity`          ='" . $value_product['minimal_quantity'] . "',
                                                `low_stock_threshold`       ='" . $value_product['low_stock_threshold'] . "',
                                                `low_stock_alert`           = '" . $value_product['low_stock_alert'] . "',
                                                `available_date`            = '" . $available_date . "'
                                                           ");

                    $stmtInsertProductAttribute->execute();


                    $stmtInsertProductAttributeCombination = $db_con->prepare("INSERT INTO psz6_product_attribute_combination (
                                                              `id_attribute`,
                                                              `id_product_attribute`) 
                                                   VALUES ('" . $productSizeId_attribute_combination . "',
                                                           '" . $w . "')
                                                            ON DUPLICATE KEY UPDATE
                                                            `id_attribute`                ='" . $productSizeId_attribute_combination . "',
                                                            `id_product_attribute`      =  '" . $w . "'");


                    $stmtInsertProductAttributeCombination->execute();


                    $stmtInsertProductAttributeShop = $db_con->prepare("INSERT INTO psz6_product_attribute_shop (
                                                                          `id_product`,
                                                                          `id_product_attribute`,
                                                                          `id_shop`,
                                                                          `wholesale_price`,
                                                                          `price`,
                                                                          `ecotax`,
                                                                          `weight`,
                                                                          `unit_price_impact`,
                                                                          `default_on`,
                                                                          `minimal_quantity`,
                                                                          `low_stock_threshold`,
                                                                          `low_stock_alert`,
                                                                          `available_date`) 
                                                   VALUES ('" . $p . "',
                                                           '" . $w . "',
                                                           '" . $value_product['prestashopId'] . "',
                                                           '0.000000',
                                                           '0.000000',
                                                           '" . $value_product['ecotax'] . "',
                                                           '1',
                                                           '0.000000',
                                                           NULL,
                                                           '" . $value_product['minimal_quantity'] . "',
                                                           '" . $value_product['low_stock_threshold'] . "',
                                                           '" . $value_product['low_stock_alert'] . "',
                                                           '" . $available_date . "')
                                                             ON DUPLICATE KEY UPDATE
                                                              `id_product`            =     '" . $p . "',
                                                              `id_product_attribute`  =     '" . $w . "',
                                                              `id_shop`               =     '" . $value_product['prestashopId'] . "',                        
                                                              `wholesale_price`       =     '0.000000',
                                                              `price`                 =       '0.000000', 
                                                              `ecotax`                =     '" . $value_product['ecotax'] . "',
                                                              `weight`                =     '1',
                                                              `unit_price_impact`     =     '0.000000',
                                                              `default_on`            =     NULL,
                                                              `minimal_quantity`      =      '" . $value_product['minimal_quantity'] . "',
                                                              `low_stock_threshold`   =     '" . $value_product['low_stock_threshold'] . "',
                                                              `low_stock_alert`       =    '" . $value_product['low_stock_alert'] . "',
                                                              `available_date`        =     '" . $available_date . "'
                                                            
                                                           ");
                    $stmtInsertProductAttributeShop->execute();


                    $stmtInsertProductAttributeStockAvailable = $db_con->prepare("INSERT INTO psz6_stock_available (
                                                                `id_product`,
                                                                `id_product_attribute`,
                                                                `id_shop`,
                                                                `id_shop_group`,
                                                                `quantity`,
                                                                `physical_quantity`,
                                                                `reserved_quantity`,
                                                                `depends_on_stock`,
                                                                `out_of_stock`)
                                                   VALUES ( 
                                                           '" . $p . "',
                                                           '" . $w . "',
                                                           '" . $value_product['prestashopId'] . "',
                                                           '0',
                                                           '" . $quantity_attribute_combination . "',
                                                           '0',
                                                           '0',
                                                           '0',
                                                           '0')");
                    $stmtInsertProductAttributeStockAvailable->execute();

                }
            } catch (PDOException $e) {
                $res .= $e->getMessage();
            }
        }


        $sql = "
            SELECT  php.id AS ProductId ,php.prestashopId,
            sum(pps.stockQty) AS quantity
            FROM ProductPublicSku pps JOIN MarketplaceHasProductAssociate php ON pps.productId=php.productId AND pps.productVariantId =php.productVariantId WHERE php.statusPublished IN (0)  GROUP BY pps.ProductId";
        $res_quantity_stock = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        foreach ($res_quantity_stock as $res_value_quantity) {
            try {
                $stmtInsertProductStockAvailable = $db_con->prepare("INSERT INTO psz6_stock_available (
                                                                `id_product`,
                                                                `id_product_attribute`,
                                                                `id_shop`,
                                                                `id_shop_group`,
                                                                `quantity`,
                                                                `physical_quantity`,
                                                                `reserved_quantity`,
                                                                `depends_on_stock`,
                                                                `out_of_stock`)
                                                   VALUES ( 
                                                           '" . $p . "',
                                                           '0',
                                                           '" . $value_product['prestashopId'] . "',
                                                           '0',
                                                           '" . $res_value_quantity['quantity'] . "',
                                                           '" . $res_value_quantity['quantity'] . "',
                                                           '0',
                                                           '0',
                                                           '0')
                                                           ON DUPLICATE KEY 
                                                           UPDATE
                                                                `id_product` =     '" . $p . "',
                                                                `id_product_attribute`='0',
                                                                `id_shop`='" . $value_product['prestashopId'] . "',
                                                                `id_shop_group`='0',
                                                                `quantity`= '" . $res_value_quantity['quantity'] . "',
                                                                `physical_quantity`='" . $res_value_quantity['quantity'] . "',
                                                                `reserved_quantity`='0',
                                                                `depends_on_stock`='0',
                                                                `out_of_stock`='0'
                                                           
                                                           ");
                $stmtInsertProductStockAvailable->execute();
            } catch (PDOException $e) {
                $res .= $e->getMessage();
            }
        }
        $sql = "SELECT php.id AS prestaId, psa.productDetailLabelId AS productDetailLabelId, psa.productDetailId AS productDetailId 
                FROM  MarketplaceHasProductAssociate php 
                JOIN ProductSheetActual psa ON php.productId=psa.productId AND php.productVariantId =psa.productVariantId WHERE php.statusPublished IN (0) AND php.productId=" . $value_product['productId'] . "
             AND php.productVariantId=" . $value_product['productVariantId'];
        $res_feature_product = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        foreach ($res_feature_product as $value_feature_product) {
            try {
                $stmtFeatureProduct = $db_con->prepare("INSERT INTO psz6_feature_product (`id_feature`,`id_product`,`id_feature_value`) 
                                                   VALUES ('" . $value_feature_product['productDetailLabelId'] . "',
                                                           '" . $value_feature_product['prestaId'] . "',
                                                           '" . $value_feature_product['productDetailId'] . "')");

                $stmtFeatureProduct->execute();
            } catch (PDOException $e) {
                $res .= $e->getMessage();
            }
        }
        /*** immagini   */

        $sql = "SELECT php.id AS productId, php.shopId AS shopId, concat(php.productId,'-',php.productVariantId) AS reference, concat('https://iwes.s3.amazonaws.com/',pb.slug,'/',pp.name) AS link , pp.name AS namefile,  concat('https://iwes.s3.amazonaws.com/',pb.slug,'/',pp.name)   AS picture, pp.order AS position, if(pp.order='1',1,0) AS cover
FROM MarketplaceHasProductAssociate php JOIN ProductHasProductPhoto phpp ON php.productId =phpp.productId AND php.productVariantId = phpp.productVariantId
  JOIN  Product p ON php.productId = p.id AND php.productVariantId = p.productVariantId
  JOIN ProductPublicSku S ON p.id = S.productId AND p.productVariantId = S.productVariantId
  JOIN ProductBrand pb ON p.productBrandId = pb.id
  JOIN ProductPhoto pp ON phpp.productPhotoId = pp.id WHERE  LOCATE('-1124.jpg',pp.name)  AND p.productStatusId=6 AND p.qty>0 AND php.statusPublished IN (0) GROUP BY picture  ORDER BY productId,position ASC";
        $image_product = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        $a = 0;

        //popolamento aggiornamento tabella PrestashopHasProductImage
        $current_productId = 0;


        foreach ($image_product as $value_image_product) {
            try {
                $link = $value_image_product['link'];
                $position = $value_image_product['position'];
                $shopId = $value_image_product['shopId'];
                $namefile = $value_image_product['namefile'];
                $cover = $value_image_product['position'];

                if ($cover != 1) {
                    $cover = 'null';
                }
                $stmtInsertImage = $db_con->prepare("INSERT INTO psz6_image (`id_product`,`position`,`cover`) 
                                                   VALUES (
                                                           '" . $value_image_product['productId'] . "',
                                                           '" . $position . "',
                                                           " . $cover . ")
                                                           ON DUPLICATE KEY
                                                           UPDATE
                                                           `id_product`= '" . $value_image_product['productId'] . "',
                                                           `position`='" . $position . "',
                                                           `cover`=" . $cover
                );
                $stmtInsertImage->execute();


                $stmtLastIdImageProduct = $db_con->prepare("SELECT max(id_image) AS maxIdImageProduct FROM psz6_image");
                $stmtLastIdImageProduct->execute();
                $id_lastImage = $stmtLastIdImageProduct->fetch();
                $q = $id_lastImage[0];
                if ($cover != 1) {
                    $cover = 'null';
                    $stmtInsertImageShop = $db_con->prepare("INSERT INTO psz6_image_shop (`id_product`,`id_image`,`id_shop`,`cover`) 
                                                   VALUES ('" . $value_image_product['productId'] . "',
                                                           '" . $q . "',
                                                           '" . $shopId . "',
                                                           " . $cover . ")
                                                           ON DUPLICATE KEY
                                                           UPDATE
                                                           `id_product`='" . $value_image_product['productId'] . "',
                                                           `id_image`= '" . $q . "',
                                                           `id_shop`='" . $shopId . "',
                                                           `cover`=".$cover
                    );
                    $stmtInsertImageShop->execute();
                } else {
                    $stmtInsertImageShop = $db_con->prepare("INSERT INTO psz6_image_shop (`id_product`,`id_image`,`id_shop`,`cover`) 
                                                   VALUES ('" . $value_image_product['productId'] . "',
                                                           '" . $q . "',
                                                           '" . $value_image_product['shopId'] . "',
                                                            '" . $cover . "')
                                                             ON DUPLICATE KEY
                                                           UPDATE
                                                           `id_product`='" . $value_image_product['productId'] . "',
                                                           `id_image`= '" . $q . "',
                                                           `id_shop`='" . $shopId . "',
                                                           `cover`=".$cover
                    );
                    $stmtInsertImageShop->execute();
                }


               $stmtInsertImageLang = $db_con->prepare("INSERT INTO psz6_image_lang (`id_image`,`id_lang`,`legend`) 
                                                   VALUES ('" . $q . "',
                                                           '1',
                                                           '" . $value_image_product['reference'] . "')
                                                           ON DUPLICATE KEY UPDATE
                                                           `id_image`='" . $q . "',
                                                           `id_lang`='1',
                                                           `legend`= '" . $value_image_product['reference'] . "'                                                                                                                      
                                                                                                                      ");
                $stmtInsertImageLang->execute();
                $stmtInsertImageLang = $db_con->prepare("INSERT INTO psz6_image_lang (`id_image`,`id_lang`,`legend`) 
                                                   VALUES ('" . $q . "',
                                                           '2',
                                                           '" . $value_image_product['reference'] . "')
                                                            ON DUPLICATE KEY UPDATE
                                                           `id_image`='" . $q . "',
                                                           `id_lang`='2',
                                                           `legend`= '" . $value_image_product['reference'] . "'                                                                                                                      
                                                                                                                      ");
                $stmtInsertImageLang->execute();
                $stmtInsertImageLang = $db_con->prepare("INSERT INTO psz6_image_lang (`id_image`,`id_lang`,`legend`) 
                                                   VALUES ('" . $q . "',
                                                           '3',
                                                           '" . $value_image_product['reference'] . "')
                                                            ON DUPLICATE KEY UPDATE
                                                           `id_image`='" . $q . "',
                                                           `id_lang`='3',
                                                           `legend`= '" . $value_image_product['reference'] . "'                                                                                                                      
                                                                                                                      ");
                $stmtInsertImageLang->execute();


                $fileUrl = $link;

//The path & filename to save to.
                $saveTo = $save_to . $namefile;

//Open file handler.
                $fp = fopen($saveTo, 'w+');

//If $fp is FALSE, something went wrong.
                if ($fp === false) {
                    throw new Exception('Could not open: ' . $saveTo);
                }

                $ch = curl_init($fileUrl);
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                curl_exec($ch);
                if (curl_errno($ch)) {
                    throw new Exception(curl_error($ch));
                }
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($statusCode == 200) {
                    echo 'Downloaded!';
                } else {
                    echo "Status Code: " . $statusCode;
                }
                $success = file_get_contents("http://iwes.shop/createdirImage.php?token=10210343943202393403&dir=" . $q);


                echo $success;  // "OK" or "FAIL"
                /*****  trasferimento ftp ******/
                $ftp_server = "ftp.iwes.shop";
                $ftp_user_name = "iwesshop";
                $ftp_user_pass = "XtUWicJUrEXv";
                $remote_file = "/public_html/img/p/" . chunk_split($q, 1, '/');;

                $ftp_url = "ftp://" . $ftp_user_name . ":" . $ftp_user_pass . "@" . $ftp_server . $remote_file . $q . ".jpg";
                $errorMsg = 'ftp fail connect';
                $fileToSend = $saveTo;
// ------- Upload file through FTP ---------------

                $ch = curl_init();
                $fp = fopen($fileToSend, "r");
                // we upload a TXT file
                curl_setopt($ch, CURLOPT_URL, $ftp_url);
                curl_setopt($ch, CURLOPT_UPLOAD, 1);
                curl_setopt($ch, CURLOPT_INFILE, $fp);
                // set size of the file, which isn't _mandatory_ but
                // helps libcurl to do extra error checking on the upload.
                curl_setopt($ch, CURLOPT_INFILESIZE, filesize($fileToSend));
                $res = curl_exec($ch);
                $errorMsg = curl_error($ch);
                $errorNumber = curl_errno($ch);
                curl_close($ch);
                $success = file_get_contents("http://iwes.shop/createThumbImage.php?token=10210343943202393403&dir=" . $q);
            } catch (PDOException $e) {
                $res .= $e->getMessage();
            }
        }

        $sql = "UPDATE MarketplaceHasProductAssociate SET statusPublished='1' WHERE statusPublished='0'";
        \Monkey::app()->dbAdapter->query($sql, []);
        $sql = "UPDATE PrestashopHasProductImage SET status='1' WHERE status='0'";
        \Monkey::app()->dbAdapter->query($sql, []);



        $res = "Inserimento Nuovo Prodotto Eseguito";
            return $res;
        }

}

          





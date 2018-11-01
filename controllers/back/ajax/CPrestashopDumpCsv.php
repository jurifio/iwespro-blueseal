<?php

namespace bamboo\controllers\back\ajax;


use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;
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
class CPrestashopDumpCsv extends AAjaxController
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


        /* $sql = "DELETE FROM PrestashopHasProduct";
          $res_delete = \Monkey::app()->dbAdapter->query($sql, []);
          $sql = "ALTER TABLE PrestashopHasProduct AUTO_INCREMENT=1";
          $res_delete = \Monkey::app()->dbAdapter->query($sql, []);*/
        /******* apertura e creazione file csv per espostazione********/


        if (ENV == 'dev') {

            $save_to = '/media/sf_sites/PickyshopNew/tmp/';

        } else {
            $save_to = '/data/www/iwes/production/sites/pickyshop/temp-prestashop/';
        }
        if (file_exists($save_to . 'psz6_category.csv')) {
            unlink($save_to . 'psz6_category.csv');
        }
        $category_csv = fopen($save_to . 'psz6_category.csv', 'w');

        if (file_exists($save_to . 'psz6_product.csv')) {
            unlink($save_to . 'psz6_product.csv');
        }
        $product_csv = fopen($save_to . 'psz6_product.csv', 'w');

        if (file_exists($save_to . 'psz6_product_shop.csv')) {
            unlink($save_to . 'psz6_product_shop.csv');
        }
        $product_shop_csv = fopen($save_to . 'psz6_product_shop.csv', 'w');
        if (file_exists($save_to . 'psz6_product_lang.csv')) {
            unlink($save_to . 'psz6_product_lang.csv');
        }

        $product_lang_csv = fopen($save_to . 'psz6_product_lang.csv', 'w');


        if (file_exists($save_to . 'psz6_product_attribute.csv')) {
            unlink($save_to . 'psz6_product_attribute.csv');
        }
        $product_attribute_csv = fopen($save_to . 'psz6_product_attribute.csv', 'w');


        if (file_exists($save_to . 'psz6_product_attribute_combination.csv')) {
            unlink($save_to . 'psz6_product_attribute_combination.csv');
        }
        $product_attribute_combination_csv = fopen($save_to . 'psz6_product_attribute_combination.csv', 'w');
        if (file_exists($save_to . 'psz6_product_attribute_shop.csv')) {
            unlink($save_to . 'psz6_product_attribute_shop.csv');
        }
        $product_attribute_shop_csv = fopen($save_to . 'psz6_product_attribute_shop.csv', 'w');

        if (file_exists($save_to . 'psz6_feature_product.csv')) {
            unlink($save_to . 'psz6_feature_product.csv');
        }
        $feature_product_csv = fopen($save_to . 'psz6_feature_product.csv', 'w');
        if (file_exists($save_to . 'psz6_image.csv')) {
            unlink($save_to . 'psz6_image.csv');
        }
        $image_csv = fopen($save_to . 'psz6_image.csv', 'w');


        if (file_exists($save_to . 'psz6_image_link.csv')) {
            unlink($save_to . 'psz6_image_link.csv');
        }
        $image_link_csv = fopen($save_to . 'psz6_image_link.csv', 'w');

        if (file_exists($save_to . 'psz6_image_lang.csv')) {
            unlink($save_to . 'psz6_image_lang.csv');
        }
        $image_lang_csv = fopen($save_to . 'psz6_image_lang.csv', 'w');

        if (file_exists($save_to . 'psz6_image_shop.csv')) {
            unlink($save_to . 'psz6_image_shop.csv');
        }
        $image_shop_csv = fopen($save_to . 'psz6_image_shop.csv', 'w');

        if (file_exists($save_to . 'psz6_stock_available.csv')) {
            unlink($save_to . 'psz6_stock_available.csv');
        }
        $stock_available_csv = fopen($save_to . 'psz6_stock_available.csv', 'w');


        if (file_exists($save_to . 'psz6_stock_mvt.csv')) {
            unlink($save_to . 'psz6_stock_mvt.csv');
        }
        $stock_mvt_csv = fopen($save_to . 'psz6_stock_mvt.csv', 'w');

        if (file_exists($save_to . 'psz6_manufacturer_shop.csv')) {
            unlink($save_to . 'psz6_manufacturer_shop.csv');
        }
        $manufacturer_shop_csv = fopen($save_to . 'psz6_manufacturer_shop.csv', 'w');
        fputcsv($manufacturer_shop_csv, array('id_manufacturer', 'id_shop'), ";");

        if (file_exists($save_to . 'psz6_category_shop.csv')) {
            unlink($save_to . 'psz6_category_shop.csv');
        }
        $category_shop_csv = fopen($save_to . 'psz6_category_shop.csv', 'w');

        if (file_exists($save_to . 'psz6_category_group.csv')) {
            unlink($save_to . 'psz6_category_group.csv');
        }
        $category_group_csv = fopen($save_to . 'psz6_category_group.csv', 'w');

        if (file_exists($save_to . 'psz6_supplier.csv')) {
            unlink($save_to . 'psz6_supplier.csv');
        }
        $supplier_csv = fopen($save_to . 'psz6_supplier.csv', 'w');

        if (file_exists($save_to . 'psz6_category_lang.csv')) {
            unlink($save_to . 'psz6_category_lang.csv');
        }
        $category_lang_csv = fopen($save_to . 'psz6_category_lang.csv', 'w');

        if (file_exists($save_to . 'psz6_manufacturer.csv')) {
            unlink($save_to . 'psz6_manufacturer.csv');
        }
        $manufacturer_csv = fopen($save_to . 'psz6_manufacturer.csv', 'w');

        if (file_exists($save_to . 'psz6_category_product.csv')) {
            unlink($save_to . 'psz6_category_product.csv');
        }
        $category_product_csv = fopen($save_to . 'psz6_category_product.csv', 'w');
        if (file_exists($save_to . 'psz6_supplier_lang.csv')) {
            unlink($save_to . 'psz6_supplier_lang.csv');
        }
        $supplier_lang_csv = fopen($save_to . 'psz6_supplier_lang.csv', 'w');

        if (file_exists($save_to . 'psz6_supplier_shop.csv')) {
            unlink($save_to . 'psz6_supplier_shop.csv');
        }
        $supplier_shop_csv = fopen($save_to . 'psz6_supplier_shop.csv', 'w');

        if (file_exists($save_to . 'psz6_manufacturer_lang.csv')) {
            unlink($save_to . 'psz6_manufacturer_lang.csv');
        }
        $manufacturer_lang_csv = fopen($save_to . 'psz6_manufacturer_lang.csv', 'w');
        if (file_exists($save_to . 'psz6_attribute_shop.csv')) {
            unlink($save_to . 'psz6_attribute_shop.csv');
        }
        $attribute_shop_csv = fopen($save_to . 'psz6_attribute_shop.csv', 'w');

        if (file_exists($save_to . 'psz6_attribute_group_lang.csv')) {
            unlink($save_to . 'psz6_attribute_group_lang.csv');
        }
        $attribute_group_lang_csv = fopen($save_to . 'psz6_attribute_group_lang.csv', 'w');

        if (file_exists($save_to . 'psz6_attribute_group_shop.csv')) {
            unlink($save_to . 'psz6_attribute_group_shop.csv');
        }
        $attribute_group_shop_csv = fopen($save_to . 'psz6_attribute_group_shop.csv', 'w');

        if (file_exists($save_to . 'psz6_attribute_group.csv')) {
            unlink($save_to . 'psz6_attribute_group.csv');
        }
        $attribute_group_csv = fopen($save_to . 'psz6_attribute_group.csv', 'w');

        if (file_exists($save_to . 'psz6_attribute.csv')) {
            unlink($save_to . 'psz6_attribute.csv');
        }
        $attribute_csv = fopen($save_to . 'psz6_attribute.csv', 'w');
        if (file_exists($save_to . 'psz6_attribute_lang.csv')) {
            unlink($save_to . 'psz6_attribute_lang.csv');
        }
        $attribute_lang_csv = fopen($save_to . 'psz6_attribute_lang.csv', 'w');
        if (file_exists($save_to . 'psz6_feature_shop.csv')) {
            unlink($save_to . 'psz6_feature_shop.csv');
        }
        $feature_shop_csv = fopen($save_to . 'psz6_feature_shop.csv', 'w');
        if (file_exists($save_to . 'psz6_feature.csv')) {
            unlink($save_to . 'psz6_feature.csv');
        }
        $feature_csv = fopen($save_to . 'psz6_feature.csv', 'w');
        if (file_exists($save_to . 'psz6_feature_lang.csv')) {
            unlink($save_to . 'psz6_feature_lang.csv');
        }
        $feature_lang_csv = fopen($save_to . 'psz6_feature_lang.csv', 'w');
        if (file_exists($save_to . 'psz6_feature_value.csv')) {
            unlink($save_to . 'psz6_feature_value.csv');
        }
        $feature_value_csv = fopen($save_to . 'psz6_feature_value.csv', 'w');

        if (file_exists($save_to . 'psz6_feature_value_lang.csv')) {
            unlink($save_to . 'psz6_feature_value_lang.csv');
        }
        $feature_value_lang_csv = fopen($save_to . 'psz6_feature_value_lang.csv', 'w');

        /**
         * @var $db CMySQLAdapter
         */
        /*********************   preparazione tabella di collegamento  ****************************************************//////
        /*** popolamento tabella */

        $sql = "SELECT
        concat(`p`.`id`,'-',p.productVariantId)                                        AS `product_id`,
        dp.shopId                                                                     as `prestashopId`,
        mphas.id                                                                      as `marketplaceHasShopId`,
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
        S2.barcode                                                                     AS ean13,
        ''                                                                             AS isbn,
        ''                                                                             AS upc,
        '0.000000'                                                                     AS ecotax,
        `p`.`qty`                                                                      AS quantity,
        '1'                                                                            AS minimal_quantity,
        '1'                                                                            AS low_stock_threshold,
        '0'                                                                            AS low_stock_alert,
        S3.price  /122*22 as vatfullprice,
  S3.price as full_price,
  S3.salePrice as salePrice,
  S3.salePrice /122*22 as vatsaleprice,
  IF(`p`.isOnSale=1,'saldo','prezzopieno') as tipoprezzo,
  IF(`p`.isOnSale=1,S3.salePrice-(S3.salePrice *22/122),S3.price-(S3.price*22/122) )     AS price,
        '0'                                                   AS wholesale_price,
        '0'                                                                             AS unity,
        '0.000000'  AS unit_price_ratio,
        concat(p.id,'-',p.productVariantId)                                            AS reference,
        concat(p.id,'-',p.productVariantId)                                            AS supplier_reference,
        ''                                                                             AS location,
        '0.000000'                                                                      AS width,
        '0.000000'                                                                      AS height,
        '0.000000'                                                                      AS depth,
        '0.000000'                                                                      AS weight,
        '2'  AS out_of_stock,
        '0'  AS additional_delivery_times,
        '0' AS quantity_discount,
        '0' AS text_fields,
        '0'                                                                           AS discount_amount,
        ''                                                                             AS discount_percent,
        '2018-01-01'                                                                   AS discount_from,
        '2018-01-01'                                                                   AS discount_to,
        concat(p.id,'-',p.productVariantId)                                            AS name,
        concat(p.id,'-',p.productVariantId)                                            AS description,
        'both'                                                                         AS visibility,
        '0' AS cache_is_pack,
        '0' AS cache_has_attachments,
        '0' AS is_virtual,
        '0' AS cache_default_attribute,
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
        concat('https://iwes.s3.amazonaws.com/',pb.slug,'/',p.id,'-',p.productVariantId,'-001-1124.jpg')   AS picture,
        concat(p.id,'-',p.productVariantId)                                            AS imageAlt,
        '1'                                                                            AS deleteImage,
        ''                                                                             AS feature,
        '1'                                                                            AS idshop,
        '0'                                                                            AS advanced_stock_management,
        '3' AS pack_stock_type,
        '0'                                                                            AS depend_on_stock,
        '1'                                                                            AS Warehouse,
       
        '1'  AS state


FROM `Product` `p`
        JOIN `ProductVariant` `pv` ON `p`.`productVariantId` = `pv`.`id`
        JOIN `ProductBrand` `pb` ON `p`.`productBrandId` = `pb`.`id`
        JOIN `ProductStatus` `pss` ON `pss`.`id` = `p`.`productStatusId`
        JOIN `ShopHasProduct` `shp` ON (`p`.`id`, `p`.`productVariantId`) = (`shp`.`productId`, `shp`.`productVariantId`)
        JOIN `Shop` `s` ON `s`.`id` = `shp`.`shopId`
        JOIN  `ProductPublicSku` S3 ON  (`p`.`id`, `p`.`productVariantId`) = (`S3`.`productId`, `S3`.`productVariantId`)
        JOIN  `ProductSku` S2 ON  (`p`.`id`, `p`.`productVariantId`) = (`S2`.`productId`, `S2`.`productVariantId`)
        JOIN `ProductHasProductCategory` `phpc`  ON (`p`.`id`, `p`.`productVariantId`)=(`phpc`.`productId`, `phpc`.`productVariantId`)
        JOIN  ProductDescriptionTranslation pdt ON p.id = pdt.productId AND p.productVariantId = pdt.productVariantId
        JOIN DirtyProduct dp ON p.id = dp.productId AND dp.productVariantId = p.productVariantId
        left  JOIN ProductColorGroup PCG ON p.productColorGroupId = PCG.id
        left JOIN ProductName pn ON p.id = pn.id
        left join MarketplaceHasShop mphas on dp.shopId =mphas.shopId
WHERE p.qty>0 AND p.productStatusId=6 and mphas.typeSync='0' and S3.price>0 
GROUP BY p.id,p.productVariantId
ORDER BY `p`.`id`";


        /**** esportazione prodotti su ProductHasPrestashop******/
        $product = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        foreach ($product as $val) {

            $producthasprestashop = \Monkey::app()->repoFactory->create('MarketplaceHasProductAssociate')->findOneBy(['productId' => $val['productId'], 'productVariantId' => $val['productVariantId']]);

            if (empty($producthasprestashop)) {
                $producthasprestashopinsert = \Monkey::app()->repoFactory->create('MarketplaceHasProductAssociate')->getEmptyEntity();
                $producthasprestashopinsert->productId = $val['productId'];
                $producthasprestashopinsert->productVariantId = $val['productVariantId'];
                $producthasprestashopinsert->shopId =$val['prestashopId'];
                $producthasprestashopinsert->marketplaceId='3';
                $producthasprestashopinsert->typeRetouchPrice='0';
                $producthasprestashopinsert->amount='0';
                $producthasprestashopinsert->prestashopId=$val['prestashopId'];
                $producthasprestashopinsert->statusPublished=0;
                $producthasprestashopinsert->marketPlaceHasShopId=$val['marketplaceHasShopId'];
                if ($val['price']==0){
                    $producthasprestashopinsert->price=$val['full_price']-$val['vatfullprice'];
                }else {
                    $producthasprestashopinsert->price = $val['price'];
                }
                $producthasprestashopinsert->smartInsert();
            }
        }


        /***********************sezione categorie***********************************************************************************/
        /*** estrazione dati  categorie e categorie shop*/

        $sql = " SELECT `id`                                            AS id_category,
         (SELECT   id FROM ProductCategory t2
                      WHERE t2.lft < t1.lft AND t2.rght > t1.rght
                      ORDER BY t2.rght-t1.rght ASC LIMIT 1)
                                                                    AS id_parent, 
               '1'                                                  AS id_shop_default,
                depth                                               AS level_depth,
                lft                                                 AS nleft,
                rght                                                AS nright, 
                '1'                                                 AS active,
                DATE_FORMAT(now(),'%Y-%m-%d %H:%m:%s')              AS date_add,
                DATE_FORMAT(now(),'%Y-%m-%d %H:%m:%s')              AS date_upd,
                '0'                                                 AS is_root_category
                FROM ProductCategory t1
                ORDER BY id_category,(rght-lft) DESC ";


        fputcsv($category_csv, array('id_category', 'id_parent', 'id_shop_default', 'level_depth', 'nleft', 'nright', 'active', 'date_add', 'date_upd', 'position', 'is_root_category'), ';');
        fputcsv($category_shop_csv, array('id_category', 'id_shop', 'position'), ';');
        $res_category = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        $i = 0;
        foreach ($res_category as $value_category) {
            $i = $i + 1;
            if ($value_category['id_category'] == "1") {
                $is_root_category = '1';
            } else {
                $is_root_category = $value_category['id_category'];
            }
            $data_category = array(
                array($value_category['id_category'],
                    $value_category['id_parent'],
                    $value_category['id_shop_default'],
                    $value_category['level_depth'],
                    $value_category['nleft'],
                    $value_category['nright'],
                    $value_category['active'],
                    $value_category['date_add'],
                    $value_category['date_upd'],
                    $i,
                    $is_root_category));


            $data_category_shop = array(
                array($value_category['id_category'],
                    1,
                    $i));
            $data_category_group = array(
                array($value_category['id_category'],
                    1),
                array($value_category['id_category'],
                    2),
                array($value_category['id_category'],
                    3));





            foreach ($data_category as $row_category) {
                fputcsv($category_csv, $row_category, ';');
            }
            foreach ($data_category_shop as $row_category_shop) {
                fputcsv($category_shop_csv, $row_category_shop, ';');
            }
            foreach ($data_category_group as $row_category_group) {
                fputcsv($category_group_csv, $row_category_group, ';');
            }


        }


        /****** estrazione dati lingua categorie **/

        $sql = "SELECT t1.id  AS id_category,
       '1' AS id_shop_default,
       pct.langId AS id_lang,
       pct.name AS name,
       pct.name AS description,
       concat(t1.id,'-',pct.slug) AS link_rewrite,
       pct.name AS meta_title,
       pct.name AS meta_keywords,
       pct.name AS meta_description

FROM ProductCategory t1
  JOIN ProductCategoryTranslation pct ON t1.id = pct.productCategoryId
ORDER BY id_category,(rght-lft) DESC";


        fputcsv($category_lang_csv, array('id_category', 'id_shop', 'id_lang', 'name', 'description', 'link_rewrite', 'meta_title', 'meta_keywords', 'meta_description'), ';');
        fputcsv($category_lang_csv, array('1', '1', '1', 'Home', 'Home', 'home', 'Home', 'Home', 'Home',), ';');
        fputcsv($category_lang_csv, array('1', '1', '2', 'Home', 'Home', 'home', 'Home', 'Home', 'Home',), ';');
        fputcsv($category_lang_csv, array('1', '1', '3', 'Home', 'Home', 'home', 'Home', 'Home', 'Home',), ';');
        $res_category_lang = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        $i = 0;
        foreach ($res_category_lang as $value_category_lang) {
            if ($value_category_lang['id_lang'] == '1') {
                $id_lang = '2';
            } elseif ($value_category_lang['id_lang'] == '2') {
                $id_lang = '1';
            } else {
                $id_lang = $value_category_lang['id_lang'];
            }
            $data_category_lang = array(
                array($value_category_lang['id_category'],
                    $value_category_lang['id_shop_default'],
                    $id_lang,
                    iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',htmlentities($value_category_lang['name'], ENT_QUOTES)),
                    iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',htmlentities($value_category_lang['description'], ENT_QUOTES)),
                    $value_category_lang['link_rewrite'],
                    iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',htmlentities($value_category_lang['meta_title'], ENT_QUOTES)),
                    iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',htmlentities($value_category_lang['meta_keywords'], ENT_QUOTES)),
                    iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',htmlentities($value_category_lang['meta_description'], ENT_QUOTES))));
            foreach ($data_category_lang as $row_category_lang) {
                fputcsv($category_lang_csv, $row_category_lang, ';');
            }


        }


        /*** esportazione prodotti appartenenti a più categorie  *****/


        $sql = "SELECT phpc.productCategoryId AS id_category,
             pap.prestaId AS id_product, '0' AS position
             FROM ProductHasProductCategory phpc JOIN PrestashopHasProduct pap ON phpc.productId =pap.productId AND phpc.productVariantId = pap.productVariantId ORDER BY id_category";
        fputcsv($category_product_csv, array('id_category', 'id_product', 'position'), ';');
        $res_category_product = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        foreach ($res_category_product as $value_category_product) {
            $data_cat_product = array(
                array($value_category_product['id_category'],
                    $value_category_product['id_product'],
                    '0'));


            foreach ($data_cat_product as $row_cat_product) {
                fputcsv($category_product_csv, $row_cat_product, ';');
            }
        }


        /************* Sezione brand e produttori*******************************************************************************************/
        /***** esportazione Brand ****/

        fputcsv($manufacturer_csv, array('id_manufacturer', 'name', 'date_add', 'date_upd', 'active'), ";");
        $res_brand = \Monkey::app()->repoFactory->create('ProductBrand')->findAll();
        foreach ($res_brand as $value_brand) {
            $date_brand = date('Y-m-d H:i:s:');
            $data_brand = array(
                array($value_brand->id,
                    $value_brand->slug,
                    $date_brand,
                    $date_brand,
                    '1'));

            foreach ($data_brand as $row_brand) {
                fputcsv($manufacturer_csv, $row_brand, ';');
            }
        }


        /***** esportazione Brand language ****/

        fputcsv($manufacturer_lang_csv, array('id_manufacturer', 'id_lang', 'description', 'short_description', 'meta_title', 'meta_keywords', 'meta_description'), ";");
        $res_brand_lang = \Monkey::app()->repoFactory->create('ProductBrand')->findAll();
        foreach ($res_brand_lang as $value_brand_lang) {
            $data_brand_lang = array(
                array($value_brand_lang->id,
                    1,
                    $value_brand_lang->name,
                    $value_brand_lang->name,
                    $value_brand_lang->name,
                    $value_brand_lang->name,
                    $value_brand_lang->name),

                array($value_brand_lang->id,
                    2,
                    $value_brand_lang->name,
                    $value_brand_lang->name,
                    $value_brand_lang->name,
                    $value_brand_lang->name,
                    $value_brand_lang->name),

                array($value_brand_lang->id,
                    3,
                    $value_brand_lang->name,
                    $value_brand_lang->name,
                    $value_brand_lang->name,
                    $value_brand_lang->name,
                    $value_brand_lang->name));

            foreach ($data_brand_lang as $row_brand_lang) {
                fputcsv($manufacturer_lang_csv, $row_brand_lang, ';');
            }
        }

        /***** esportazione Brand shop ****/

        $res_brand_shop = \Monkey::app()->repoFactory->create('ProductBrand')->findAll();
        foreach ($res_brand_shop as $value_brand_shop) {

            $data_brand_shop = array(
                array($value_brand_shop->id,
                    '1'));

            foreach ($data_brand_shop as $row_brand_shop) {
                fputcsv($manufacturer_shop_csv, $row_brand_shop, ';');
            }
        }


        /******************************************* sezione Supplier Fornitori Shop*******************************************************************************/

        /***** esportazione Supplier ****/

        fputcsv($supplier_csv, array('id_supplier', 'name', 'date_add', 'date_upd', 'active'), ";");
        $res_supplier = \Monkey::app()->repoFactory->create('Shop')->findAll();
        foreach ($res_supplier as $value_supplier) {
            $date_supplier = date('Y-m-d H:i:s:');
            $data_supplier = array(
                array($value_supplier->id,
                    $value_supplier->title,
                    $date_supplier,
                    $date_supplier,
                    $value_supplier->isActive));

            foreach ($data_supplier as $row_supplier) {
                fputcsv($supplier_csv, $row_supplier, ';');
            }
        }

        /***** esportazione Supplier language ****/

        fputcsv($supplier_lang_csv, array('id_supplier', 'id_lang', 'description', 'meta_title', 'meta_keywords', 'meta_description'), ";");
        $res_supplier_lang = \Monkey::app()->repoFactory->create('Shop')->findAll();
        foreach ($res_supplier_lang as $value_supplier_lang) {
            $data_supplier_lang = array(
                array($value_supplier_lang->id,
                    1,
                    $value_supplier_lang->title,
                    $value_supplier_lang->title,
                    $value_supplier_lang->title,
                    $value_supplier_lang->title),
                array($value_supplier_lang->id,
                    2,
                    $value_supplier_lang->title,
                    $value_supplier_lang->title,
                    $value_supplier_lang->title,
                    $value_supplier_lang->title),

                array($value_supplier_lang->id,
                    3,
                    $value_supplier_lang->title,
                    $value_supplier_lang->title,
                    $value_supplier_lang->title,
                    $value_supplier_lang->title));

            foreach ($data_supplier_lang as $row_supplier_lang) {
                fputcsv($supplier_lang_csv, $row_supplier_lang, ';');
            }
        }

        /***** esportazione Supplier shop ****/

        fputcsv($supplier_shop_csv, array('id_supplier', 'id_shop'), ";");
        $res_supplier_shop = \Monkey::app()->repoFactory->create('Shop')->findAll();
        foreach ($res_supplier_shop as $value_supplier_shop) {

            $data_supplier_shop = array(
                array($value_supplier_shop->id,
                    '1'));

            foreach ($data_supplier_shop as $row_supplier_shop) {
                fputcsv($supplier_shop_csv, $row_supplier_shop, ';');
            }
        }


        /****************** sezione attributi *********************************/

        /** caricamento gruppi attributi  */
        $sql = "SELECT psmg.id  AS id_attribute_group,
        '0' AS is_color_group,
        'select' AS group_type,
        'name' AS name
FROM ProductSizeMacroGroup psmg
  ";


        fputcsv($attribute_group_csv, array('id_attribute_group', 'is_color_group', 'group_type', 'position'), ';');
        fputcsv($attribute_group_csv, array('1', '0', 'select', '1'), ';');
        fputcsv($attribute_group_csv, array('2', '0', 'select', '2'), ';');


        /** caricamento  traduzioni gruppi attributi  */


        fputcsv($attribute_group_lang_csv, array('id_attribute_group', 'id_lang', 'name', 'public_name'), ';');
        fputcsv($attribute_group_lang_csv, array('1', '1', 'Size', 'Size'), ';');
        fputcsv($attribute_group_lang_csv, array('1', '2', 'Taglie', 'Taglie'), ';');
        fputcsv($attribute_group_lang_csv, array('1', '3', 'Größe', 'Größe'), ';');
        fputcsv($attribute_group_lang_csv, array('2', '1', 'Color', 'Color'), ';');
        fputcsv($attribute_group_lang_csv, array('2', '2', 'Colore', 'Colore'), ';');
        fputcsv($attribute_group_lang_csv, array('2', '3', 'Farben', 'Farben'), ';');


        /** caricamento  gruppi attributi shop  */


        fputcsv($attribute_group_shop_csv, array('id_attribute_group', 'id_shop'), ';');
        fputcsv($attribute_group_shop_csv, array('1', '1'), ';');
        fputcsv($attribute_group_shop_csv, array('2', '1'), ';');


        /**  esportazione attributi taglie */
        $sql = "SELECT S.id AS id_attribute ,
       '1' AS id_attribute_group,
       S.name AS name,
       '' AS color

 FROM  ProductSize S ORDER BY id_attribute";


        fputcsv($attribute_csv, array('id_attribute', 'id_attribute_group', 'color', 'position'), ';', '"');
        $res_attribute = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();


        foreach ($res_attribute as $value_attribute) {
            $z = '1';
            $data_attribute = array(
                array($value_attribute['id_attribute'],
                    $value_attribute['id_attribute_group'],
                    '',
                    $value_attribute['id_attribute']));

            foreach ($data_attribute as $row_attribute) {
                fputcsv($attribute_csv, $row_attribute, ';');

            }

        }


        /**  esportazione  attributi  negozio */
        $sql = "SELECT S.id AS id_attribute ,
         '1' AS id_attribute_group,
          S.name AS name,
          '' AS color
          FROM  ProductSize S ORDER BY id_attribute";


        fputcsv($attribute_shop_csv, array('id_attribute', 'id_shop'), ';');
        $res_attribute_shop = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();


        foreach ($res_attribute_shop as $value_attribute_shop) {
            $z = '1';
            $data_attribute_shop = array(
                array($value_attribute_shop['id_attribute'],
                    '1',));

            foreach ($data_attribute_shop as $row_attribute_shop) {
                fputcsv($attribute_shop_csv, $row_attribute_shop, ';');

            }

        }

        /**  esportazione  attributi  traduzioni */
        $sql = "SELECT S.id AS id_attribute ,
        '1' AS id_attribute_group,
        S.name AS name,
        '' AS color
        FROM  ProductSize S ORDER BY id_attribute";


        fputcsv($attribute_lang_csv, array('id_attribute', 'id_lang', 'name'), ';');
        $res_attribute_lang = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();


        foreach ($res_attribute_lang as $value_attribute_lang) {
            for ($y = 1; $y <= 3; $y++) {
                $data_attribute_lang = array(
                    array($value_attribute_lang['id_attribute'],
                        $y,
                        $value_attribute_lang['name']));

                foreach ($data_attribute_lang as $row_attribute_lang) {
                    fputcsv($attribute_lang_csv, $row_attribute_lang, ';');

                }
            }
        }

        /****** sezione feature label caratteristiche
         * /**  esportazione caratteristiche etichetta */
        $sql = "SELECT pdl.id AS id_feature ,
        pdl.order AS position
        FROM ProductDetailLabel pdl";

        fputcsv($feature_csv, array('id_feature', 'position'), ';');
        fputcsv($feature_shop_csv,array('id_feature','id_shop'));
        $res_feature = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();

        foreach ($res_feature as $value_feature) {
            $z = '1';
            $data_feature = array(
                array($value_feature['id_feature'],
                    $value_feature['position']));


            $data_feature_shop = array(
                array($value_feature['id_feature'],
                    1));
            foreach ($data_feature as $row_feature) {
                fputcsv($feature_csv, $row_feature, ';');

            }

            foreach ($data_feature_shop as $row_feature_shop) {
                fputcsv($feature_shop_csv, $row_feature_shop, ';');

            }

        }

        /**  esportazione caratteristiche etichetta lingua */
        $sql = "SELECT pdlt.productDetailLabelId AS id_feature ,
        pdlt.langId AS id_lang,
        pdlt.name AS name
        FROM ProductDetailLabelTranslation pdlt";


        fputcsv($feature_lang_csv, array('id_feature', 'id_lang', 'name'), ';');
        $res_feature_lang = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();


        foreach ($res_feature_lang as $value_feature_lang) {
            $z = '1';
            if ($value_feature_lang['id_lang'] == "2") {
                $LAN = '1';
            } elseif ($value_feature_lang['id_lang'] == "1") {
                $LAN = '2';
            } else {
                $LAN = '3';
            }
            $data_feature_lang = array(
                array($value_feature_lang['id_feature'],
                    $LAN,
                    iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',$value_feature_lang['name'])));
            foreach ($data_feature_lang as $row_feature_lang) {
                fputcsv($feature_lang_csv, $row_feature_lang, ';');

            }

        }


        /**  esportazione caratteristiche valore*/
        $sql = "SELECT psa.productDetailId  AS id_feature_value ,
        psa.productDetailLabelId AS id_feature,
        '0' AS custom
        FROM ProductSheetActual psa";

        fputcsv($feature_value_csv, array('id_feature_value', 'id_feature', 'custom'), ';');
        $res_feature_value = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();

        foreach ($res_feature_value as $value_feature_value) {

            $data_feature_value = array(
                array($value_feature_value['id_feature_value'],
                    $value_feature_value['id_feature'],
                    '0'));

            foreach ($data_feature_value as $row_feature_value) {
                fputcsv($feature_value_csv, $row_feature_value, ';');

            }

        }

        /**  esportazione caratteristiche valore lingua */
        $sql = "SELECT pdt.productDetailId AS id_feature_value ,
        pdt.langId AS id_lang,
        pdt.name AS value
        FROM ProductDetailTranslation pdt";


        fputcsv($feature_value_lang_csv, array('id_feature_value', 'id_lang', 'name'), ';');
        $res_feature_value_lang = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();

        foreach ($res_feature_value_lang as $value_feature_value_lang) {
            $z = '1';
            if ($value_feature_value_lang['id_lang'] == '1') {
                $id_lang = '2';
            } elseif ($value_feature_value_lang['id_lang'] == '2') {
                $id_lang = '1';
            } else {
                $id_lang = $value_feature_value_lang['id_lang'];
            }
            $data_feature_value_lang = array(
                array(
                    $value_feature_value_lang['id_feature_value'],
                    $id_lang,
                    $value_feature_value_lang['value']));

            foreach ($data_feature_value_lang as $row_feature_value_lang) {
                fputcsv($feature_value_lang_csv, $row_feature_value_lang, ';');

            }

        }

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
 S3.price  /122*22 as vatfullprice,
  S3.price as full_price,
  S3.salePrice as salePrice,
  S3.salePrice /122*22 as vatsaleprice,
  IF(`p`.isOnSale=1,'saldo','prezzopieno') as tipoprezzo,
  IF(`p`.isOnSale=1,S3.salePrice-(S3.salePrice *22/122),S3.price-(S3.price*22/122) )     AS price,
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
  JOIN  ProductDescriptionTranslation pdt ON p.id = pdt.productId AND p.productVariantId = pdt.productVariantId
  JOIN  MarketplaceHasProductAssociate php ON p.id = php.productId  AND p.productVariantId =php.productVariantId
  JOIN DirtyProduct dp ON p.id = dp.productId AND dp.productVariantId = p.productVariantId
  left  JOIN ProductColorGroup PCG ON p.productColorGroupId = PCG.id
  left JOIN ProductName pn ON p.id = pn.id
  left join MarketplaceHasShop mpas on php.shopId=mpas.shopId
WHERE  `p`.`qty` > 0 AND p.productStatusId='6' AND php.statusPublished in (0,2)  and S3.price > 0 
GROUP BY p.id,p.productVariantId
ORDER BY `p`.`id`";





        fputcsv($product_shop_csv, array('id_product',
            'id_shop',
            'id_category_Default',
            'id_tax_rules_group',
            'on_sale',
            'online_only',
            'ecotax',
            'minimal_quantity',
            'low_stock_threshold',
            'low_stock_alert',
            'price',
            'wholesale_price',
            'unity',
            'unit_price_ratio',
            'additional_shipping_cost',
            'customizable',
            'text_fields',
            'active',
            'redirect_type',
            'id_type_redirected',
            'available_for_order',
            'available_date',
            'show_condition',
            'condition',
            'show_price',
            'indexed',
            'visibility',
            'cache_default_attribute',
            'advanced_stock_management',
            'date_add',
            'date_upd',
            'pack_stock_type'), ';');

        fputcsv($product_csv, array('id_product',
            'id_supplier',
            'id_manufacturer',
            'id_category_default',
            'id_shop_default',
            'id_tax_rules_group',
            'on_sale',
            'online_only',
            'ean13',
            'isbn',
            'upc',
            'ecotax',
            'quantity',
            'minimal_quantity',
            'low_stock_threshold',
            'low_stock_alert',
            'price',
            'wholesale_price',
            'unity',
            'unit_price_ratio',
            'additional_shipping_cost',
            'reference',
            'supplier_reference',
            'location',
            'width',
            'height',
            'depth',
            'weight',
            'out_of_stock',
            'addtitional_delivery_times',
            'quantity_discount',
            'customizable',
            'uploadable_files',
            'text_fields',
            'active',
            'redirect_type',
            'id_type_redirected',
            'available_for_order',
            'available_date',
            'show_condition',
            'condition',
            'show_price',
            'indexed',
            'visibility',
            'cache_is_pack',
            'cache_has_attachments',
            'is_virtual',
            'cache_default_attribute',
            'date_add',
            'date_upd',
            'advanced_stock_management',
            'pack_stock_type',
            'state',
            'status'), ';');

        fputcsv($product_lang_csv, array('id_product',
            'id_shop',
            'id_lang',
            'description',
            'description_short',
            'link_rewrite',
            'meta_description',
            'meta_keywords',
            'meta_title',
            'name',
            'available_now',
            'available_later',
            'delivery_on_stock',
            'delivery_out_stock'), ';');

        fputcsv($product_attribute_csv, array('id_product_attribute',
            'id__product',
            'reference',
            'supplier_reference',
            'location',
            'ean13',
            'isbn',
            'upc',
            'wholesale_price',
            'price',
            'ecotax',
            'quantity',
            'weight',
            'unit_price_impact',
            'default_on',
            'minimal_quantity',
            'low_stock_threshold',
            'low_stock_alert',
            'available_date'
        ), ';');

        fputcsv($product_attribute_shop_csv, array(
            'id_product',
            'id_product_attribute',
            'id_shop',
            'wholesale_price',
            'price',
            'ecotax',
            'weight',
            'unit_price_impact',
            'default_on',
            'minimal_quantity',
            'low_stock_threshold',
            'low_stock_alert',
            'available_date'
        ), ';');
        fputcsv($product_attribute_combination_csv, array('id_attribute', 'id_product_attribute'), ';');


        fputcsv($feature_product_csv, array('id_feature',
            'id_product',
            'id_feature_value'), ';');

        fputcsv($image_csv, array('id_image',
            'id_product',
            'position',
            'cover'), ';');

        fputcsv($image_link_csv, array('id_image',
            'id_product',
            'position',
            'cover',
            'link'), ';');
        fputcsv($image_lang_csv, array('id_image',
            'id_lang',
            'legend'), ';');
        fputcsv($image_shop_csv, array('id_product',
            'id_image',
            'id_shop',
            'cover'), ';');
        fputcsv($stock_available_csv, array('id_stock_available', 'id_product', 'id_product_attribute', 'id_shop', 'id_shop_group', 'quantity', 'phisical_quantity', 'reserved_quantity', 'depends_on_stock', 'out_of_stock'), ';');
        fputcsv($stock_mvt_csv, array('id_stock_mvt', 'id_stock', 'id_order', 'id_supply_order', 'id_stock_mvt_reason', 'id_employee', 'employee_lastname', 'employee_firstname', 'physical_quantity', 'date_add', 'sign', 'price_te', 'last_wa', 'current_wa', 'referer'), ';');


        $w = 0;

        $z = 0;
        $k = 0;

        $n = 0;
        $mvt = 0;
        $res_product = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();

        //connessione db prestahop
        $pConnection = new CReadExtDbTable(3);

        //afferro il massimo attribute id da assegnare poi ai nuovi prodotti
        $maxAttributeId = $pConnection->readTables(
            ['psz6_product_attribute'],
            [],
            ["id_product_attribute" => "MAX"]
        );

        foreach ($res_product as $value_product) {


            $p = $value_product['prestaId'];


            $finalProductStatus = null;


            if ($value_product['status'] == 0) {

                $findExtProdAttr = $pConnection->readTables(
                    ['psz6_product_attribute'],
                    ["psz6_product_attribute" =>
                        [
                            "id_product" => $p
                        ]
                    ],
                    []
                );
                //Se è vuoto è proprio vero che non esiste e quindi sarebbe proprio il caso di inserirlo
                $exist = false;
                if (empty($findExtProdAttr)) {
                    if (is_null($maxAttributeId[0]["MAX(id_product_attribute)"])) {
                        $w = 0;
                        $maxAttributeId[0]["MAX(id_product_attribute)"] = "is the end";
                    }
                    $finalProductStatus = 0;
                } else {
                    $exist = true;
                    $finalProductStatus = 2;
                    \Monkey::app()->applicationLog('PrestaDumpCSv', 'alert', 'Product founded', "Product with prestashopId $p WAS founded even if has status = 0");
                }
            } else if ($value_product['status'] == 2) {
                $findExtProdAttr = $pConnection->readTables(
                    ['psz6_product_attribute'],
                    ["psz6_product_attribute" =>
                        [
                            "id_product" => $p
                        ]
                    ],
                    []
                );

                $exist = false;
                if (empty($findExtProdAttr)) {
                    $finalProductStatus = 0;
                    \Monkey::app()->applicationLog('PrestaDumpCSv', 'alert', 'Product not founded', "Product with prestashopId $p WAS NOT founded even if has status = 2");
                } else {
                    $exist = true;
                    $finalProductStatus = 2;
                }
            }
            $priceProduct=0;
// popolamento array tabella prodotti
            if ($value_product['price']==0){
                $priceProduct=$value_product['full_price']-$value_product['vatfullprice'];
            }else {
                $priceProduct=$value_product['price'];
            }


            $data_product = array(
                array($p,
                    $value_product['id_supplier'],
                    $value_product['id_manufacturer'],
                    $value_product['id_category_default'],
                    $value_product['prestashopId'],
                    $value_product['id_tax_rules_group'],
                    $value_product['on_sale'],
                    $value_product['online_only'],
                    $value_product['ean13'],
                    $value_product['isbn'],
                    $value_product['upc'],
                    $value_product['ecotax'],
                    $value_product['quantity'],
                    $value_product['minimal_quantity'],
                    $value_product['low_stock_threshold'],
                    $value_product['low_stock_alert'],
                    $priceProduct,
                    $value_product['wholesale_price'],
                    $value_product['unity'],
                    $value_product['unit_price_ratio'],
                    $value_product['additional_shipping_cost'],
                    $value_product['reference'],
                    $value_product['supplier_reference'],
                    $value_product['location'],
                    '45',
                    '27',
                    '20',
                    '1',
                    $value_product['out_of_stock'],
                    $value_product['additional_delivery_times'],
                    $value_product['quantity_discount'],
                    $value_product['customizable'],
                    $value_product['uploadable_files'],
                    $value_product['text_fields'],
                    $value_product['active'],
                    $value_product['redirect_type'],
                    $value_product['id_type_redirected'],
                    $value_product['available_for_order'],
                    $value_product['available_date'],
                    $value_product['show_condition'],
                    $value_product['condition'],
                    $value_product['show_price'],
                    $value_product['indexed'],
                    $value_product['visibility'],
                    $value_product['cache_is_pack'],
                    $value_product['cache_has_attachments'],
                    $value_product['is_virtual'],
                    $value_product['cache_default_attribute'],
                    $value_product['date_add'],
                    $value_product['date_upd'],
                    $value_product['advanced_stock_management'],
                    $value_product['pack_stock_type'],
                    $value_product['state'],
                    $finalProductStatus));


            //popolamento array prodotti shop


            $data_product_shop = array(
                array($p,
                    $value_product['prestashopId'],
                    $value_product['id_category_default'],
                    $value_product['id_tax_rules_group'],
                    $value_product['on_sale'],
                    $value_product['online_only'],
                    $value_product['ecotax'],
                    $value_product['minimal_quantity'],
                    $value_product['low_stock_threshold'],
                    $value_product['low_stock_alert'],
                    $priceProduct,
                    $value_product['wholesale_price'],
                    $value_product['unity'],
                    $value_product['unit_price_ratio'],
                    $value_product['additional_shipping_cost'],
                    $value_product['customizable'],
                    $value_product['uploadable_files'],
                    $value_product['text_fields'],
                    $value_product['active'],
                    $value_product['redirect_type'],
                    $value_product['id_type_redirected'],
                    $value_product['available_for_order'],
                    $value_product['available_date'],
                    $value_product['show_condition'],
                    $value_product['condition'],
                    $value_product['show_price'],
                    $value_product['indexed'],
                    $value_product['visibility'],
                    $value_product['cache_default_attribute'],
                    $value_product['advanced_stock_management'],
                    $value_product['date_add'],
                    $value_product['date_upd'],
                    $value_product['pack_stock_type'],
                    $value_product['state']));

            //popolamento array lingua prodotti

            $res_product_lang = \Monkey::app()->repoFactory->create('ProductNameTranslation')->findBy(['productId' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId'],'langId'=>'2']);
            if ($res_product_lang->isEmpty()) {

                iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",htmlentities($name_product_lang = $value_product['brand_name'] . " " . $value_product['supplier_reference'] . " " . $value_product['color_supplier'], ENT_QUOTES )));
                $in_stock = "in stock";
                $current_supply = "Current supply. Ordering available";
                $product_available = "Delivered in 3-4 Days";
                $product_not_available = "Delivered in 10-15 Days";
                $valuelang = 1;

                $data_product_lang = array(
                    array($p,
                        $value_product['prestashopId'],
                        $valuelang,
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                        $value_product['product_id'],
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                        $in_stock,
                        $current_supply,
                        $product_available,
                        $product_not_available
                    ));
                foreach ($data_product_lang as $row_product_lang) {
                    fputcsv($product_lang_csv, $row_product_lang, ';');

                }
            }else {
                foreach ($res_product_lang as $value_product_lang) {

                    iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",htmlentities($name_product_lang = $value_product['brand_name'] . " " . $value_product_lang->name . " " . $value_product['supplier_reference'] . " " . $value_product['color_supplier'], ENT_QUOTES )));

                    $in_stock = "in stock";
                    $current_supply = "Current supply. Ordering available";
                    $product_available = "Delivered in 3-4 Days";
                    $product_not_available = "Delivered in 10-15 Days";

                    $valuelang = 1;


                    $data_product_lang = array(
                        array($p,
                            $value_product['prestashopId'],
                            $valuelang,
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                            $value_product['product_id'],
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                            $in_stock,
                            $current_supply,
                            $product_available,
                            $product_not_available
                        ));
                    foreach ($data_product_lang as $row_product_lang) {
                        fputcsv($product_lang_csv, $row_product_lang, ';');

                    }
                }
            }
            $res_product_lang = \Monkey::app()->repoFactory->create('ProductNameTranslation')->findBy(['productId' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId'],'langId'=>'1']);
            if ($res_product_lang->isEmpty()) {
                iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",htmlentities($name_product_lang = $value_product['brand_name'] .  " " . $value_product['supplier_reference'] . " " . $value_product['color_supplier'], ENT_QUOTES )));
                $in_stock = "in Vendita";
                $current_supply = 'In magazzino. ordinabile';
                $product_available = 'Consegna in 3-4 Giorni Lavorati';
                $product_not_available = 'Consegna  in 10-15 lavorativi';
                $valuelang = 2;

                $data_product_lang = array(
                    array($p,
                        $value_product['prestashopId'],
                        $valuelang,
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'"," ",$name_product_lang)),
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'"," ",$name_product_lang)),
                        $value_product['product_id'],
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'"," ",$name_product_lang)),
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'"," ",$name_product_lang)),
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'"," ",$name_product_lang)),
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'"," ",$name_product_lang)),
                        $in_stock,
                        $current_supply,
                        $product_available,
                        $product_not_available
                    ));
                foreach ($data_product_lang as $row_product_lang) {
                    fputcsv($product_lang_csv, $row_product_lang, ';');

                }
            }else {
                foreach ($res_product_lang as $value_product_lang) {

                    iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",htmlentities($name_product_lang = $value_product['brand_name'] . " " . $value_product_lang->name . " " . $value_product['supplier_reference'] . " " . $value_product['color_supplier'], ENT_QUOTES )));


                    $in_stock = "in Vendita";
                    $current_supply = 'In magazzino. ordinabile';
                    $product_available = 'Consegna in 3-4 Giorni Lavorati';
                    $product_not_available = 'Consegna  in 10-15 lavorativi';



                    $valuelang = 2;

                    $data_product_lang = array(
                        array($p,
                            $value_product['prestashopId'],
                            $valuelang,
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'"," ",$name_product_lang)),
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'"," ",$name_product_lang)),
                            $value_product['product_id'],
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'"," ",$name_product_lang)),
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'"," ",$name_product_lang)),
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'"," ",$name_product_lang)),
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', str_replace("'"," ",$name_product_lang)),
                            $in_stock,
                            $current_supply,
                            $product_available,
                            $product_not_available
                        ));
                    foreach ($data_product_lang as $row_product_lang) {
                        fputcsv($product_lang_csv, $row_product_lang, ';');

                    }
                }
            }
            $res_product_lang = \Monkey::app()->repoFactory->create('ProductNameTranslation')->findBy(['productId' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId'],'langId'=>'3']);
            if ($res_product_lang->isEmpty()) {
                iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",htmlentities($name_product_lang = $value_product['brand_name'] .  " " . $value_product['supplier_reference'] . " " . $value_product['color_supplier'], ENT_QUOTES )));
                $in_stock = "in stock";
                $current_supply = "Current supply. Ordering available";
                $product_available = "Delivered in 3-4 Days";
                $product_not_available = "Delivered in 10-15 Days";
                $valuelang = 3;

                $data_product_lang = array(
                    array($p,
                        $value_product['prestashopId'],
                        $valuelang,
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                        $value_product['product_id'],
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                        iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                        $in_stock,
                        $current_supply,
                        $product_available,
                        $product_not_available
                    ));
                foreach ($data_product_lang as $row_product_lang) {
                    fputcsv($product_lang_csv, $row_product_lang, ';');

                }
            }else {
                foreach ($res_product_lang as $value_product_lang) {

                    iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",htmlentities($name_product_lang = $value_product['brand_name'] . " " . $value_product_lang->name . " " . $value_product['supplier_reference'] . " " . $value_product['color_supplier'], ENT_QUOTES )));


                    $in_stock = "in stock";
                    $current_supply = "Current supply. Ordering available";
                    $product_available = "Delivered in 3-4 Days";
                    $product_not_available = "Delivered in 10-15 Days";



                    $valuelang = 3;

                    $data_product_lang = array(
                        array($p,
                            $value_product['prestashopId'],
                            $valuelang,
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                            $value_product['product_id'],
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                            iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',str_replace("'"," ",$name_product_lang)),
                            $in_stock,
                            $current_supply,
                            $product_available,
                            $product_not_available
                        ));
                    foreach ($data_product_lang as $row_product_lang) {
                        fputcsv($product_lang_csv, $row_product_lang, ';');

                    }
                }
            }

// popolamento prodotti attributi


//se lo stato è 0 lo cerco, mi assicuro che non lo trovo e lo inserico - se lo stato è 2 lo cerco lo ag



            $res_product_attribute = \Monkey::app()->repoFactory->create('ProductSku')->findBy(['productId' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId']]);

            $lock_default_on = 0;


            foreach ($res_product_attribute as $value_product_attribute) {
                if (!$exist) {
                    $w = $w + 1;

                } else {

                    /**
                     * select *
                     * from psz6_product_attribute pa
                     * join psz6_product_attribute_combination pac ON pa.id_product_attribute = pac.id_product_attribute
                     * join psz6_attribute a ON a.id_attribute = pac.id_attribute
                     * join psz6_attribute_lang al ON al.id_attribute = pac.id_attribute
                     * where pa.id_product = 8827 && al.id_lang = 3
                     */
                    $rightSku = $pConnection->readTables(
                        ['psz6_product_attribute',
                            'psz6_product_attribute_combination' => [
                                'Self' => ['id_product_attribute'],
                                'psz6_product_attribute' => ['id_product_attribute']
                            ],
                            'psz6_attribute' => [
                                'Self' => ['id_attribute'],
                                'psz6_product_attribute_combination' => ['id_attribute']
                            ],
                            'psz6_attribute_lang' => [
                                'Self' => ['id_attribute'],
                                'psz6_product_attribute_combination' => ['id_attribute']
                            ]
                        ],
                        [
                            "psz6_product_attribute" =>
                                [
                                    "id_product" => $p
                                ],
                            'psz6_attribute_lang' => [
                                'id_lang' => 2,
                                'name' => $value_product_attribute->productSize->name
                            ]
                        ],
                        []
                    );

                    $w = $rightSku[0]["id_product_attribute"];
                }

                $n = $n + 1;


                $mvt = $mvt + 1;
                $productSizeId_attribute_combination = $value_product_attribute->productSizeId;
                $quantity_attribute_combination = $value_product_attribute->stockQty;
                if (($quantity_attribute_combination >= 0) && ($lock_default_on == 0)) {
                    $default_on = '1';
                    $lock_default_on = 1;
                } else {
                    $default_on = '0';
                }
                $price_attribute_combination = $value_product_attribute->price-($value_product_attribute->price*22/122);
                $salePrice_attribute_combination = $value_product_attribute->salePrice-($value_product_attribute->salePrice*22/122);
                if ($value_product['on_sale'] == '1') {
                    $price = $salePrice_attribute_combination;
                } else {
                    $price = $price_attribute_combination;
                }
                if ($quantity_attribute_combination >=1) {
                    $available_date = date("Y-m-d");

                } else {
                    $available_date = '2018-08-01';
                }


                //popolamento array prodotti attributi
                $data_product_attribute = array(
                    array($w,
                        $p,
                        $value_product['reference'].'-'.$productSizeId_attribute_combination ,
                        $value_product['supplier_reference'],
                        '',
                        $value_product_attribute->ean,
                        $value_product['isbn'],
                        $value_product['upc'],
                        '0.000000',
                       '.0.000000',
                        $value_product['ecotax'],
                        $quantity_attribute_combination,
                        $value_product['weight'],
                        '0.000000',
                        $default_on,
                        $value_product['minimal_quantity'],
                        $value_product['low_stock_threshold'],
                        $value_product['low_stock_alert'],
                        $available_date));
// popoplamento array movimenti magazzino
                $data_stock_mvt = array(
                    array($mvt,
                        $n,
                        '',
                        '',
                        '11',
                        '1',
                        'Cartechini',
                        'Gianluca',
                        $quantity_attribute_combination,
                        date("Y-m-d H:i:s"),
                        '1',
                        '0.000000',
                        '0.000000',
                        '0.000000',
                        ''));

// popolamento array stock prodotti per combinazioni
                $data_stock_available = array(
                    array($n,
                        $p,
                        $w,
                        $value_product['prestashopId'],
                        '0',
                        $quantity_attribute_combination,
                        '0',
                        '0',
                        '0',
                        '0'));


                foreach ($data_product_attribute as $row_product_attribute) {
                    fputcsv($product_attribute_csv, $row_product_attribute, ';');
                }
                foreach ($data_stock_mvt as $row_stock_mvt) {
                    fputcsv($stock_mvt_csv, $row_stock_mvt, ';');
                }
                foreach ($data_stock_available as $row_stock_available) {

                    fputcsv($stock_available_csv, $row_stock_available, ';');


                }

// popolamento array prodotti attributi shop
                $data_product_attribute_shop = array(
                    array($p,
                        $w,
                        $value_product['prestashopId'],
                        '0.000000',
                        '0.000000',
                        $value_product['ecotax'],
                        '1',
                        '0.000000',
                        $default_on,
                        $value_product['minimal_quantity'],
                        $value_product['low_stock_threshold'],
                        $value_product['low_stock_alert'],
                        $available_date));
                foreach ($data_product_attribute_shop as $row_product_attribute_shop) {
                    fputcsv($product_attribute_shop_csv, $row_product_attribute_shop, ';');
                }


                // popolamento array chiavi attributo prodotto attributo
                $data_product_attribute_combination = array(
                    array($productSizeId_attribute_combination,
                        $w
                    )
                );
                foreach ($data_product_attribute_combination as $row_product_attribute_combination) {
                    fputcsv($product_attribute_combination_csv, $row_product_attribute_combination, ';');

                }

            }


            foreach ($data_product as $row_product) {
                fputcsv($product_csv, $row_product, ';');

            }
            foreach ($data_product_shop as $row_product_shop) {
                fputcsv($product_shop_csv, $row_product_shop, ';');

            }



        }


        /** sezione immagini */

        $sql = "SELECT php.id AS productId, php.shopId as shopId, concat(php.productId,'-',php.productVariantId) AS reference,   concat('https://iwes.s3.amazonaws.com/',pb.slug,'/',pp.name)   AS picture, pp.order AS position, if(pp.order='1',1,0) AS cover
FROM MarketplaceHasProductAssociate php JOIN ProductHasProductPhoto phpp ON php.productId =phpp.productId AND php.productVariantId = phpp.productVariantId
  JOIN  Product p ON php.productId = p.id AND php.productVariantId = p.productVariantId
  JOIN ProductPublicSku S ON p.id = S.productId AND p.productVariantId = S.productVariantId
  JOIN ProductBrand pb ON p.productBrandId = pb.id
  JOIN ProductPhoto pp ON phpp.productPhotoId = pp.id WHERE  LOCATE('-1124.jpg',pp.name)  AND p.productStatusId=6 AND p.qty>0 AND php.statusPublished in (0,2) GROUP BY picture  ORDER BY productId,position ASC";
        $image_product = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        $a = 0;

        //popolamento aggiornamento tabella PrestashopHasProductImage
        $current_productId=0;
        foreach ($image_product as $value_image_product) {

            $prestashopHasProductImageInsert = \Monkey::app()->repoFactory->create('PrestashopHasProductImage')->getEmptyEntity();
            $prestashopHasProductImageInsert->prestaId = $value_image_product['productId'];
            $prestashopHasProductImageInsert->position = $value_image_product['position'];
            $prestashopHasProductImageInsert->picture = $value_image_product['picture'];
            $prestashopHasProductImageInsert->cover = $value_image_product['cover'];
            $prestashopHasProductImageInsert->status = '0';
            $prestashopHasProductImageInsert->smartInsert();

            // popolamento array immagini con id sequenziale

            $data_image = array(
                array($prestashopHasProductImageInsert->idImage,
                    $value_image_product['productId'],
                    $value_image_product['position'],
                    $value_image_product['position']));

            //popolamento array immagini shop

            $data_image_shop = array(
                array($value_image_product['productId'],
                    $prestashopHasProductImageInsert->idImage,
                    $value_image_product['shopId'],
                    $value_image_product['position']));
            $data_image_lang = array(
                array($prestashopHasProductImageInsert->idImage,
                    '1',
                    $value_image_product['reference']),
                array($prestashopHasProductImageInsert->idImage,
                    '2',
                    $value_image_product['reference']),
                array($prestashopHasProductImageInsert->idImage,
                    '3',
                    $value_image_product['reference'])
            );

            //popolameto array image con link a amazon
            $data_image_link = array(
                array($prestashopHasProductImageInsert->idImage,
                    $value_image_product['productId'],
                    $value_image_product['position'],
                    $value_image_product['position'],
                    $value_image_product['picture']));


            foreach ($data_image as $row_image_product) {
                fputcsv($image_csv, $row_image_product, ';');
            }
            //caricamento immagini

            foreach ($data_image_shop as $row_image_shop) {
                fputcsv($image_shop_csv, $row_image_shop, ';');
            }
            foreach ($data_image_lang as $row_image_lang) {
                fputcsv($image_lang_csv, $row_image_lang, ';');

            }
            foreach ($data_image_link as $row_image_product_link) {
                fputcsv($image_link_csv, $row_image_product_link, ';');
            }
            $current_productId=$value_image_product['productId'];
        }

// popolamento stock magazzino quantità totali per prodotto
        $sql = "
            SELECT  php.id AS ProductId ,php.prestashopId,
            sum(pps.stockQty) AS quantity
            FROM ProductPublicSku pps JOIN MarketplaceHasProductAssociate php ON pps.productId=php.productId AND pps.productVariantId =php.productVariantId GROUP BY pps.ProductId";
        $res_quantity_stock = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        foreach ($res_quantity_stock as $value_quantity_stock) {
            $n = $n + 1;
            $data_quantity_stock_available = array(
                array($n,
                    $value_quantity_stock['ProductId'],
                    '0',
                    $value_quantity_stock['prestashopId'],
                    '0',
                    $value_quantity_stock['quantity'],
                    '0',
                    '0',
                    '0',
                    '0'));
            foreach ($data_quantity_stock_available as $row_quantity_stock_available) {
                fputcsv($stock_available_csv, $row_quantity_stock_available, ';');
            }
        }
        /****** sezione caratteristiche prodotto *****/
        $sql = "SELECT php.id AS prestaId, psa.productDetailLabelId AS productDetailLabelId, psa.productDetailId AS productDetailId 
                FROM  MarketplaceHasProductAssociate php 
                JOIN ProductSheetActual psa ON php.productId=psa.productId AND php.productVariantId =psa.productVariantId";


        $res_feature_product = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        foreach ($res_feature_product as $value_feature_product) {
            $z = $z + 1;
            $data_feature_product = array(
                array($value_feature_product['productDetailLabelId'],
                    $value_feature_product['prestaId'],
                    $value_feature_product['productDetailId']));

            foreach ($data_feature_product as $row_feature_product) {
                fputcsv($feature_product_csv, $row_feature_product, ';');
            }
        }

        fclose($category_csv);
        fclose($category_lang_csv);
        fclose($category_shop_csv);
        fclose($category_group_csv);
        fclose($manufacturer_csv);
        fclose($manufacturer_lang_csv);
        fclose($manufacturer_shop_csv);
        fclose($supplier_csv);
        fclose($supplier_lang_csv);
        fclose($supplier_shop_csv);
        fclose($attribute_group_csv);
        fclose($attribute_group_lang_csv);
        fclose($attribute_group_shop_csv);
        fclose($attribute_csv);
        fclose($attribute_shop_csv);
        fclose($attribute_lang_csv);
        fclose($feature_csv);
        fclose($feature_shop_csv);
        fclose($feature_lang_csv);
        fclose($feature_value_csv);
        fclose($feature_value_lang_csv);
        fclose($image_lang_csv);
        fclose($image_shop_csv);
        fclose($image_csv);
        fclose($image_link_csv);
        fclose($product_csv);
        fclose($product_shop_csv);
        fclose($product_lang_csv);
        fclose($product_attribute_csv);
        fclose($product_attribute_combination_csv);
        fclose($product_attribute_shop_csv);
        fclose($feature_product_csv);
        fclose($category_product_csv);
        fclose($stock_available_csv);
        fclose($stock_mvt_csv);


        /********************* operazioni di crezione archivio per trasferimento*******/

        $exportToPrestashopcsv = "export_" . date("Y-m-d") . ".tar";
        if (file_exists($save_to . $exportToPrestashopcsv)) {
            unlink($save_to . $exportToPrestashopcsv);
        }

        $zipName = $save_to . $exportToPrestashopcsv;
        $pharfiletounlink = $save_to . $exportToPrestashopcsv . ".gz";
        if (file_exists($pharfiletounlink)) {
            unlink($pharfiletounlink);
        }
        $pharfile = $exportToPrestashopcsv . ".gz";


        $phar = new \PharData($zipName);
        $phar->addFile($save_to . 'psz6_stock_mvt.csv', 'psz6_stock_mvt.csv');
        $phar->addFile($save_to . 'psz6_attribute.csv', 'psz6_attribute.csv'); //V
        $phar->addFile($save_to . 'psz6_manufacturer.csv', 'psz6_manufacturer.csv');
        $phar->addFile($save_to . 'psz6_manufacturer_lang.csv', 'psz6_manufacturer_lang.csv');
        $phar->addFile($save_to . 'psz6_manufacturer_shop.csv', 'psz6_manufacturer_shop.csv');
        $phar->addFile($save_to . 'psz6_supplier.csv', 'psz6_supplier.csv');
        $phar->addFile($save_to . 'psz6_supplier_lang.csv', 'psz6_supplier_lang.csv');
        $phar->addFile($save_to . 'psz6_supplier_shop.csv', 'psz6_supplier_shop.csv');
        $phar->addFile($save_to . 'psz6_category_shop.csv', 'psz6_category_shop.csv');
        $phar->addFile($save_to . 'psz6_category_group.csv', 'psz6_category_group.csv');
        $phar->addFile($save_to . 'psz6_stock_available.csv', 'psz6_stock_available.csv');
        $phar->addFile($save_to . 'psz6_attribute_shop.csv', 'psz6_attribute_shop.csv'); //V
        $phar->addFile($save_to . 'psz6_attribute_group.csv', 'psz6_attribute_group.csv'); //V
        $phar->addFile($save_to . 'psz6_attribute_group_lang.csv', 'psz6_attribute_group_lang.csv'); //V
        $phar->addFile($save_to . 'psz6_attribute_group_shop.csv', 'psz6_attribute_group_shop.csv'); //V
        $phar->addFile($save_to . 'psz6_attribute_lang.csv', 'psz6_attribute_lang.csv'); //V
        $phar->addFile($save_to . 'psz6_category.csv', 'psz6_category.csv');
        $phar->addFile($save_to . 'psz6_category_product.csv', 'psz6_category_product.csv');
        $phar->addFile($save_to . 'psz6_category_lang.csv', 'psz6_category_lang.csv');
        $phar->addFile($save_to . 'psz6_feature_shop.csv', 'psz6_feature_shop.csv');
        $phar->addFile($save_to . 'psz6_feature.csv', 'psz6_feature.csv');
        $phar->addFile($save_to . 'psz6_feature_lang.csv', 'psz6_feature_lang.csv');
        $phar->addFile($save_to . 'psz6_feature_product.csv', 'psz6_feature_product.csv');
        $phar->addFile($save_to . 'psz6_feature_value.csv', 'psz6_feature_value.csv');
        $phar->addFile($save_to . 'psz6_feature_value_lang.csv', 'psz6_feature_value_lang.csv');
        $phar->addFile($save_to . 'psz6_image.csv', 'psz6_image.csv');
        $phar->addFile($save_to . 'psz6_image_lang.csv', 'psz6_image_lang.csv');
        $phar->addFile($save_to . 'psz6_image_link.csv', 'psz6_image_link.csv');
        $phar->addFile($save_to . 'psz6_image_shop.csv', 'psz6_image_shop.csv');
        $phar->addFile($save_to . 'psz6_product.csv', 'psz6_product.csv');
        $phar->addFile($save_to . 'psz6_product_shop.csv', 'psz6_product_shop.csv');
        $phar->addFile($save_to . 'psz6_product_attribute.csv', 'psz6_product_attribute.csv');
        $phar->addFile($save_to . 'psz6_product_attribute_combination.csv', 'psz6_product_attribute_combination.csv');
        $phar->addFile($save_to . 'psz6_product_attribute_shop.csv', 'psz6_product_attribute_shop.csv');
        $phar->addFile($save_to . 'psz6_product_lang.csv', 'psz6_product_lang.csv');

        if ($phar->count() > 0) {
            /** @var \PharData $compressed */
            $compressed = $phar->compress(\Phar::GZ);
            if (file_exists($compressed->getPath())) {
                unlink($save_to . 'psz6_attribute.csv');
                unlink($save_to . 'psz6_stock_available.csv');
                unlink($save_to . 'psz6_attribute_shop.csv');
                unlink($save_to . 'psz6_attribute_group.csv');
                unlink($save_to . 'psz6_category_shop.csv');
                unlink($save_to . 'psz6_attribute_group_lang.csv');
                unlink($save_to . 'psz6_attribute_group_shop.csv');
                unlink($save_to . 'psz6_category_product.csv');
                unlink($save_to . 'psz6_category_group.csv');
                unlink($save_to . 'psz6_attribute_lang.csv');
                unlink($save_to . 'psz6_category.csv');
                unlink($save_to . 'psz6_category_lang.csv');
                unlink($save_to . 'psz6_feature.csv');
                unlink($save_to . 'psz6_feature_shop.csv');
                unlink($save_to . 'psz6_feature_lang.csv');
                unlink($save_to . 'psz6_feature_product.csv');
                unlink($save_to . 'psz6_feature_value.csv');
                unlink($save_to . 'psz6_feature_value_lang.csv');
                unlink($save_to . 'psz6_image.csv');
                unlink($save_to . 'psz6_image_lang.csv');
                unlink($save_to . 'psz6_image_link.csv');
                unlink($save_to . 'psz6_image_shop.csv');
                unlink($save_to . 'psz6_product.csv');
                unlink($save_to . 'psz6_product_attribute.csv');
                unlink($save_to . 'psz6_product_attribute_combination.csv');
                unlink($save_to . 'psz6_product_attribute_shop.csv');
                unlink($save_to . 'psz6_product_lang.csv');
                unlink($save_to . 'psz6_product_shop.csv');
                unlink($save_to . 'psz6_manufacturer.csv');
                unlink($save_to . 'psz6_manufacturer_lang.csv');
                unlink($save_to . 'psz6_manufacturer_shop.csv');
                unlink($save_to . 'psz6_supplier.csv');
                unlink($save_to . 'psz6_supplier_lang.csv');
                unlink($save_to . 'psz6_supplier_shop.csv');
                unlink($save_to . 'psz6_stock_mvt.csv');


            }

        }


        /*****  trasferimento ftp ******/
        $ftp_server = "ftp.iwes.shop";
        $ftp_user_name = "iwesshop";
        $ftp_user_pass = "XtUWicJUrEXv";
        $remote_file = "/public_html/tmp/";

        $ftp_url = "ftp://" . $ftp_user_name . ":" . $ftp_user_pass . "@" . $ftp_server . $remote_file . $pharfile;
        $errorMsg = 'ftp fail connect';
        $fileToSend = $save_to . $pharfile;
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

        /****sezione per lancio allineamento script su server prestashop*/

        $url = 'https://iwes.shop/alignpresta.php';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);


        curl_close($ch);

        $url = 'https://iwes.shop/alignImage.php';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);


        curl_close($ch);





        /**** aggiornamento stato tabella PrestashopHasProduct e  PrestashopHasProductImage  **/
        $sql = "UPDATE MarketplaceHasProductAssociate SET statusPublished='1' WHERE statusPublished='0'";
        \Monkey::app()->dbAdapter->query($sql, []);
        $sql = "UPDATE PrestashopHasProductImage SET status='1' WHERE status='0'";
        \Monkey::app()->dbAdapter->query($sql, []);
        $sql = "UPDATE MarketplaceHasProductAssociate SET statusPublished='1' WHERE statusPublished='2'";
        \Monkey::app()->dbAdapter->query($sql, []);
        $sql = "UPDATE PrestashopHasProductImage SET status='1' WHERE status='2'";
        \Monkey::app()->dbAdapter->query($sql, []);



        $res = 'esportazione eseguita';
        return $res;
    }


}
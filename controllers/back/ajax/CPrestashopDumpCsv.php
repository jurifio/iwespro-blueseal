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
class CPrestashopDumpCsv extends AAjaxController
{


    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooDBALException
     * @throws \bamboo\core\exceptions\BambooException
     */
    public function post()
    {
        ini_set('memory_limit', '2048M');
        /**
         * @var $db CMySQLAdapter
         */

        /*** popolamento tabella */
$product=\Monkey::app()->repoFactory->create('Product')->findby(['productStatusId'=>'6']);
 foreach ($product as $val){
     $producthasprestashop=\Monkey::app()->repoFactory->create('PrestashopHasProduct')->findOneBy(['productId'=>$val->id,'productVariantId'=>$val->productVariantId]);
     if(!empty($producthasprestashop)) {
         $producthasprestashop->productId = $val->id;
         $producthasprestashop->productVariantId = $val->productVariantId;
         $producthasprestashop->status='2';
         $producthasprestashop->update();
     }else{
         $producthasprestashopinsert=\Monkey::app()->repoFactory->create('PrestashopHasProduct')->getEmptyEntity();
         $producthasprestashopinsert->productId = $val->id;
         $producthasprestashopinsert->productVariantId = $val->productVariantId;
         $producthasprestashopinsert->status='0';
         $producthasprestashopinsert->smartInsert();
     }
 }

        /*** estrazione dati  categorie*/

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
                ORDER BY (rght-lft) DESC ";
        if (ENV == 'dev') {

            $save_to = '/media/sf_sites/PickyshopNew/tmp/';

        } else {
            $save_to = '/data/www/iwes/production/sites/pickyshop/temp/';
        }
        if (file_exists($save_to . 'psz6_category.csv')) {
            unlink($save_to . 'psz6_category.csv');
        }
        $category_csv = fopen($save_to . 'psz6_category.csv', 'w');

        fputcsv($category_csv, array('id_category', 'id_parent', 'id_shop_default', 'level_depth', 'nleft', 'nright', 'active', 'date_add', 'date_upd', 'position', 'is_root_category'), ';');
        $res_category = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        $i = 0;
        foreach ($res_category as $value_category) {
            $i = $i + 1;
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
                    $value_category['is_root_category']));
            foreach ($data_category as $row_category) {
                fputcsv($category_csv, $row_category, ';');
            }


        }
        fclose($category_csv);

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
ORDER BY (rght-lft) DESC";
        if (file_exists($save_to . 'psz6_category_lang.csv')) {
            unlink($save_to . 'psz6_category_lang.csv');
        }
        $category_lang_csv = fopen($save_to . 'psz6_category_lang.csv', 'w');

        fputcsv($category_lang_csv, array('id_category', 'id_shop', 'id_lang', 'name', 'description', 'link_rewrite', 'meta_title', 'meta_keywords', 'meta_description'), ';');
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
                    $value_category_lang['name'],
                    $value_category_lang['description'],
                    $value_category_lang['link_rewrite'],
                    $value_category_lang['meta_title'],
                    $value_category_lang['meta_keywords'],
                    $value_category_lang['meta_description']));
            foreach ($data_category_lang as $row_category_lang) {
                fputcsv($category_lang_csv, $row_category_lang, ';');
            }


        }
        fclose($category_lang_csv);

        /** caricamento gruppi attributi  */
        $sql = "SELECT psmg.id  AS id_attribute_group,
        '0' AS is_color_group,
        'select' AS group_type,
        'name' AS name
FROM ProductSizeMacroGroup psmg
  ";
        if (file_exists($save_to . 'psz6_attribute_group.csv')) {
            unlink($save_to . 'psz6_attribute_group.csv');
        }
        $attribute_group_csv = fopen($save_to . 'psz6_attribute_group.csv', 'w');

        fputcsv($attribute_group_csv, array('id_attribute_group', 'is_color_group', 'group_type', 'position'), ';');
        $res_attribute_group = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        $m = 0;
        foreach ($res_attribute_group as $value_attribute_group) {
            $m = $m + 1;
            $data_attribute_group = array(
                array($value_attribute_group['id_attribute_group'],
                    $value_attribute_group['is_color_group'],
                    $value_attribute_group['group_type'],
                    $m));
            foreach ($data_attribute_group as $row_attribute_group) {
                fputcsv($attribute_group_csv, $row_attribute_group, ';');
            }


        }
        fclose($attribute_group_csv);

        /** caricamento  traduzioni gruppi attributi  */
        $sql = "SELECT psmg.id  AS id_attribute_group,
        psmg.name AS name
        FROM ProductSizeMacroGroup psmg
  ";
        if (file_exists($save_to . 'psz6_attribute_group_lang.csv')) {
            unlink($save_to . 'psz6_attribute_group_lang.csv');
        }
        $attribute_group_lang_csv = fopen($save_to . 'psz6_attribute_group_lang.csv', 'w');

        fputcsv($attribute_group_lang_csv, array('id_attribute_group', 'id_lang', 'name', 'public_name'), ';');
        $res_attribute_group_lang = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();


        foreach ($res_attribute_group_lang as $value_attribute_group_lang) {

            for ($y = 1; $y <= 3; $y++) {
                $data_attribute_group_lang = array(
                    array($value_attribute_group_lang['id_attribute_group'],
                        $y,
                        $value_attribute_group_lang['name'],
                        $value_attribute_group_lang['name']));

                foreach ($data_attribute_group_lang as $row_attribute_group_lang) {
                    fputcsv($attribute_group_lang_csv, $row_attribute_group_lang, ';');

                }
            }
        }
        fclose($attribute_group_lang_csv);
        /** caricamento  gruppi attributi shop  */
        $sql = "SELECT psmg.id  AS id_attribute_group
        
        FROM ProductSizeMacroGroup psmg
  ";
        if (file_exists($save_to . 'psz6_attribute_group_shop.csv')) {
            unlink($save_to . 'psz6_attribute_group_shop.csv');
        }
        $attribute_group_shop_csv = fopen($save_to . 'psz6_attribute_group_shop.csv', 'w');

        fputcsv($attribute_group_shop_csv, array('id_attribute_group', 'id_shop'), ';');
        $res_attribute_group_shop = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();


        foreach ($res_attribute_group_shop as $value_attribute_group_shop) {
            $z = '1';
            $data_attribute_group_shop = array(
                array($value_attribute_group_shop['id_attribute_group'],
                    $z));

            foreach ($data_attribute_group_shop as $row_attribute_group_shop) {
                fputcsv($attribute_group_shop_csv, $row_attribute_group_shop, ';');

            }

        }
        fclose($attribute_group_shop_csv);

        /**  esportazione attributi */
        $sql = "SELECT psghps.productSizeId AS id_attribute ,
   psmg.id AS id_attribute_group,
  S.name AS name,
  '' AS color,
  psghps.position AS position FROM ProductSizeGroupHasProductSize psghps
JOIN ProductSizeMacroGroup psmg ON psmg.id =psghps.productSizeGroupId
JOIN ProductSize S ON psghps.productSizeId = S.id ORDER  BY id_attribute_group,position  ASC";

        if (file_exists($save_to . 'psz6_attribute.csv')) {
            unlink($save_to . 'psz6_attribute.csv');
        }
        $attribute_csv = fopen($save_to . 'psz6_attribute.csv', 'w');

        fputcsv($attribute_csv, array('id_attribute', 'id_attribute_group', 'color', 'position'), ';');
        $res_attribute = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();


        foreach ($res_attribute as $value_attribute) {
            $z = '1';
            $data_attribute = array(
                array($value_attribute['id_attribute'],
                    $value_attribute['id_attribute_group'],
                    '',
                    $value_attribute['position']));

            foreach ($data_attribute as $row_attribute) {
                fputcsv($attribute_csv, $row_attribute, ';');

            }

        }
        fclose($attribute_csv);

        /**  esportazione  attributi  negozio */
        $sql = "SELECT psghps.productSizeId AS id_attribute ,
   psmg.id AS id_attribute_group,
  S.name AS name,
  '' AS color,
  psghps.position AS position FROM ProductSizeGroupHasProductSize psghps
JOIN ProductSizeMacroGroup psmg ON psmg.id =psghps.productSizeGroupId
JOIN ProductSize S ON psghps.productSizeId = S.id ORDER  BY id_attribute_group,position  ASC";

        if (file_exists($save_to . 'psz6_attribute_shop.csv')) {
            unlink($save_to . 'psz6_attribute_shop.csv');
        }
        $attribute_shop_csv = fopen($save_to . 'psz6_attribute_shop.csv', 'w');

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
        fclose($attribute_shop_csv);
        /**  esportazione  attributi  traduzioni */
        $sql = "SELECT psghps.productSizeId AS id_attribute ,
   psmg.id AS id_attribute_group,
  S.name AS name,
  '' AS color,
  psghps.position AS position FROM ProductSizeGroupHasProductSize psghps
JOIN ProductSizeMacroGroup psmg ON psmg.id =psghps.productSizeGroupId
JOIN ProductSize S ON psghps.productSizeId = S.id ORDER  BY id_attribute_group,position  ASC";

        if (file_exists($save_to . 'psz6_attribute_lang.csv')) {
            unlink($save_to . 'psz6_attribute_lang.csv');
        }
        $attribute_lang_csv = fopen($save_to . 'psz6_attribute_lang.csv', 'w');

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
        fclose($attribute_lang_csv);

        /**  esportazione caratteristiche etichetta */
        $sql = "SELECT pdl.id AS id_feature ,
   pdl.order AS position
FROM ProductDetailLabel pdl";

        if (file_exists($save_to . 'psz6_feature.csv')) {
            unlink($save_to . 'psz6_feature.csv');
        }
        $feature_csv = fopen($save_to . 'psz6_feature.csv', 'w');

        fputcsv($feature_csv, array('id_feature', 'position'), ';');
        $res_feature = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();


        foreach ($res_feature as $value_feature) {
            $z = '1';
            $data_feature = array(
                array($value_feature['id_feature'],
                    $value_feature['position']));

            foreach ($data_feature as $row_feature) {
                fputcsv($feature_csv, $row_feature);

            }

        }
        fclose($feature_csv);
        /**  esportazione caratteristiche etichetta lingua */
        $sql = "SELECT pdlt.productDetailLabelId AS id_feature ,
    pdlt.langId AS id_lang,
    pdlt.name AS name
   
FROM ProductDetailLabelTranslation pdlt";

        if (file_exists($save_to . 'psz6_feature_lang.csv')) {
            unlink($save_to . 'psz6_feature_lang.csv');
        }
        $feature_lang_csv = fopen($save_to . 'psz6_feature_lang.csv', 'w');

        fputcsv($feature_lang_csv, array('id_feature', 'id_lang', 'name'), ';');
        $res_feature_lang = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();


        foreach ($res_feature_lang as $value_feature_lang) {
            $z = '1';
            if ($value_feature_lang['id_lang'] == '1') {
                $id_lang = '2';
            } elseif ($value_feature_lang['id_lang'] == '2') {
                $id_lang = '1';
            } else {
                $id_lang = $value_feature_lang['id_lang'];
            }
            $data_feature_lang = array(
                array($value_feature_lang['id_feature'],
                    $id_lang,
                    $value_feature_lang['name']));

            foreach ($data_feature_lang as $row_feature_lang) {
                fputcsv($feature_lang_csv, $row_feature_lang, ';');

            }

        }
        fclose($feature_lang_csv);


        /**  esportazione caratteristiche valore*/
        $sql = "SELECT psa.productDetailId  AS id_feature_value ,
   psa.productDetailLabelId AS id_feature,
   '0' AS custom
FROM ProductSheetActual psa";

        if (file_exists($save_to . 'psz6_feature_value.csv')) {
            unlink($save_to . 'psz6_feature_value.csv');
        }
        $feature_value_csv = fopen($save_to . 'psz6_feature_value.csv', 'w');

        fputcsv($feature_value_csv, array('id_feature_value', 'id_feature', 'custom'), ';');
        $res_feature_value = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();


        foreach ($res_feature_value as $value_feature_value) {
            $z = '1';
            $data_feature_value = array(
                array($value_feature_value['id_feature_value'],
                    $value_feature_value['id_feature'],
                    '0'));

            foreach ($data_feature_value as $row_feature_value) {
                fputcsv($feature_value_csv, $row_feature_value);

            }

        }
        fclose($feature_value_csv);
        /**  esportazione caratteristiche valore lingua */
        $sql = "SELECT pdt.productDetailId AS id_feature_value ,
    pdt.langId AS id_lang,
    pdt.name AS value
   
FROM ProductDetailTranslation pdt";

        if (file_exists($save_to . 'psz6_feature_value_lang.csv')) {
            unlink($save_to . 'psz6_feature_value_lang.csv');
        }
        $feature_value_lang_csv = fopen($save_to . 'psz6_feature_value_lang.csv', 'w');

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
        fclose($feature_value_lang_csv);

        /** esportazione prodotti */
        $sql = "

SELECT
  php.prestaId                                                                   AS prestaId,
  concat(`p`.`id`,'-',p.productVariantId)                                        AS `product_id`,
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
  shp.price                                                                      AS price,
  FORMAT(shp.price/100*70 ,2)                                                    AS wholesale_price,
  ''                                                                             AS unity,
  '0,000000'  AS unit_price_ratio,
  '0' AS additional_shipping_cost,
  concat(p.id,'-',p.productVariantId)                                            AS reference,
  concat(p.id,'-',p.productVariantId)                                            AS supplier_reference,
  ''                                                                             AS location,
  '0.000000'                                                                      AS width,
  '0.000000'                                                                      AS height,
  '0.000000'                                                                      AS depth,
  '0.000000'                                                                      AS weight,
  '2'  AS out_of_stock,
  ''  AS additional_delivery_times,
  '0' AS quantity_discount,
  '0' AS customizable,
  '0' AS uploadable_files,
  '0' AS text_fields,
  '1' AS active,

  if (p.isOnSale=1,format((shp.price - shp.salePrice),2),format(shp.price,2))    AS discount_amount,
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
  '0'                                                                            AS text_fields,
  '0'                                                                            AS uploadable_files,
  '1'                                                                            AS active,
  '404'                                                                          AS redirect_type,
  '0'                                                                            AS id_type_redirected,
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
  '0'                                                                            AS advanced_stock_management,
  '3' AS pack_stock_type,
  '0'                                                                            AS depend_on_stock,
  '0'                                                                            AS Warehouse,
  '1' AS state,
  php.status as status

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
  JOIN  PrestashopHasProduct php ON p.id = php.productId  and p.productVariantId =php.productVariantId
WHERE  `p`.`qty` > 0 AND p.productStatusId='6'
GROUP BY product_id
ORDER BY `p`.`id` ASC
";

        if (file_exists($save_to . 'psz6_product.csv')) {
            unlink($save_to . 'psz6_product.csv');
        }
        $product_csv = fopen($save_to . 'psz6_product.csv', 'w');
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


        $res_product = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        $w = 0;
        $p = 0;
        $z = 0;
        $k = 0;
        $u = 0;
        $a = 0;
        $b = 0;

        foreach ($res_product as $value_product) {
           // $p = $p + 1;
$p=$value_product['prestaId'];
            $data_product = array(
                array($p,
                    $value_product['id_supplier'],
                    $value_product['id_manufacturer'],
                    $value_product['id_category_default'],
                    $value_product['id_shop_default'],
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
                    $value_product['price'],
                    $value_product['wholesale_price'],
                    $value_product['unity'],
                    $value_product['unit_price_ratio'],
                    $value_product['additional_shipping_cost'],
                    $value_product['reference'],
                    $value_product['supplier_reference'],
                    $value_product['location'],
                    $value_product['width'],
                    $value_product['height'],
                    $value_product['depth'],
                    $value_product['weight'],
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
                    $value_product['status']));

            $data_product_lang = array(
                array($p,
                    '1',
                    '1',
                    $value_product['product_id'],
                    $value_product['product_id'],
                    $value_product['product_id'],
                    $value_product['product_id'],
                    $value_product['product_id'],
                    $value_product['product_id'],
                    $value_product['product_id'],
                    'in stock',
                    'Current supply. Ordering available',
                    'Delivered in 3-4 Days',
                    'Delivered in 10-15 Days'
                ),
                array($p,
                    '1',
                    '1',
                    $value_product['product_id'],
                    $value_product['product_id'],
                    $value_product['product_id'],
                    $value_product['product_id'],
                    $value_product['product_id'],
                    $value_product['product_id'],
                    $value_product['product_id'],
                    'in Vendita',
                    'In magazzino. ordinabile',
                    'Consegna in 3-4 Giorni Lavorati',
                    'Consegna  in 10-15 lavorativi'
                ),
                array($p,
                    '1',
                    '1',
                    $value_product['product_id'],
                    $value_product['product_id'],
                    $value_product['product_id'],
                    $value_product['product_id'],
                    $value_product['product_id'],
                    $value_product['product_id'],
                    $value_product['product_id'],
                    'in Stock',
                    'Current supply. Ordering availlable',
                    'Delivered in 3-4 Days',
                    'Delivered in 10-15 Days'
                ));


            $data_image_lang = array(
                array($p,
                    '1',
                    $value_product['product_id'] . "-" . $value_product['productVariantId']),
                array($p,
                    '2',
                    $value_product['product_id'] . "-" . $value_product['productVariantId']),
                array($p,
                    '3',
                    $value_product['product_id'] . "-" . $value_product['productVariantId'])
            );


            $res_product_attribute = \Monkey::app()->repoFactory->create('ProductPublicSku')->findBy(['productId' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId']]);

            $lock_default_on = 0;
            foreach ($res_product_attribute as $value_product_attribute) {
                $w = $w + 1;
                $productSizeId_attribute_combination = $value_product_attribute->productSizeId;
                $quantity_attribute_combination = $value_product_attribute->stockQty;
                if (($quantity_attribute_combination >= 0) && ($lock_default_on == 0)) {
                    $default_on = '1';
                    $lock_default_on = 1;
                } else {
                    $default_on = '';
                }
                $price_attribute_combination = $value_product_attribute->price;
                $salePrice_attribute_combination = $value_product_attribute->salePrice;
                if ($value_product['on_sale'] == '1') {
                    $price = $salePrice_attribute_combination;
                } else {
                    $price = $price_attribute_combination;
                }
                if ($quantity_attribute_combination >= 1) {
                    $available_date = date("Y-m-d");

                } else {
                    $available_date = '';
                }
                $data_product_attribute = array(
                    array($w,
                        $p,
                        $value_product['reference'] . '-' . $productSizeId_attribute_combination,
                        $value_product['supplier_reference'],
                        '',
                        $value_product['ean13'],
                        $value_product['isbn'],
                        $value_product['upc'],
                        $value_product['wholesale_price'],
                        $price,
                        $value_product['ecotax'],
                        $quantity_attribute_combination,
                        $value_product['weight'],
                        '',
                        $default_on,
                        $value_product['minimal_quantity'],
                        $value_product['low_stock_threshold'],
                        $value_product['low_stock_alert'],
                        $available_date));
                foreach ($data_product_attribute as $row_product_attribute) {
                    fputcsv($product_attribute_csv, $row_product_attribute, ';');
                }

                $data_product_attribute_shop = array(
                    array($w,
                        $p,
                        '1',
                        $value_product['wholesale_price'],
                        $price,
                        $value_product['ecotax'],
                        $value_product['weight'],
                        '',
                        $default_on,
                        $value_product['minimal_quantity'],
                        $value_product['low_stock_threshold'],
                        $value_product['low_stock_alert'],
                        $available_date));
                foreach ($data_product_attribute_shop as $row_product_attribute_shop) {
                    fputcsv($product_attribute_shop_csv, $row_product_attribute_shop, ';');
                }
                $data_product_attribute_combination = array(
                    array($w,
                        $productSizeId_attribute_combination)
                );
                foreach ($data_product_attribute_combination as $row_product_attribute_combination) {
                    fputcsv($product_attribute_combination_csv, $row_product_attribute_combination, ';');

                }
            }


            foreach ($data_product as $row_product) {
                fputcsv($product_csv, $row_product, ';');

            }

            foreach ($data_product_lang as $row_product_lang) {
                fputcsv($product_lang_csv, $row_product_lang, ';');

            }
            foreach ($data_image_lang as $row_image_lang) {
                fputcsv($image_lang_csv, $row_image_lang, ';');

            }
            $feature_product = \Monkey::app()->repoFactory->create('ProductSheetActual')->findBy(['productId' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId']]);
            foreach ($feature_product as $value_feature_product) {
                $z = $z + 1;
                $data_feature_product = array(
                    array($value_feature_product->productDetailLabelId,
                        $p,
                        $value_feature_product->productDetailId));
            }
            foreach ($data_feature_product as $row_feature_product) {
                fputcsv($feature_product_csv, $row_feature_product, ';');
            }
            $sql = "SELECT p.id AS productId, p.productVariantId AS productVariantId, phpp.productPhotoId, pp.name AS image, pb.slug  AS slug, pp.order AS position FROM ProductHasProductPhoto phpp JOIN ProductPhoto pp ON phpp.productPhotoId = pp.id
JOIN Product p ON phpp.productId = p.id AND phpp.productVariantId = p.productVariantId
JOIN ProductBrand pb ON p.productBrandId = pb.id  WHERE p.id='" . $value_product['productId'] . "' AND p.productVariantId='" . $value_product['productVariantId'] . "' AND pp.name LIKE '%-001-1124%'";
            $image_product = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
            $a = 0;
            $data_image = [];
            foreach ($image_product as $value_image_product) {
                $k = $k + 1;
                $a = $a + 1;
                $data_image = array(
                    array($p,
                        $p,
                        $value_image_product['position'],
                        '1'));
                $data_image_shop = array(
                    array($p,
                        $p,
                        '1',
                        '1'));
            }

            foreach ($data_image as $row_image_product) {
                fputcsv($image_csv, $row_image_product, ';');
            }

            foreach ($data_image_shop as $row_image_shop) {
                fputcsv($image_shop_csv, $row_image_shop, ';');
            }
            $sql = "SELECT p.id AS productId, p.productVariantId AS productVariantId, phpp.productPhotoId, pp.name AS image, pb.slug  AS slug, pp.order AS position FROM ProductHasProductPhoto phpp JOIN ProductPhoto pp ON phpp.productPhotoId = pp.id
JOIN Product p ON phpp.productId = p.id AND phpp.productVariantId = p.productVariantId
JOIN ProductBrand pb ON p.productBrandId = pb.id WHERE p.id='" . $value_product['productId'] . "' AND p.productVariantId='" . $value_product['productVariantId'] . "' AND pp.name LIKE '%-001-1124%'";
            $image_product_link = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();

            $data_image_link = [];
            $b = 0;
            foreach ($image_product_link as $value_image_product_link) {
                $u = $u + 1;
                $b = $b + 1;
                $data_image_link = array(
                    array($p,
                        $p,
                        $value_image_product_link['position'],
                        '1',
                        'https://iwes.s3.amazonaws.com/' . $value_image_product_link['slug'] . '/' . $value_image_product_link['image']));
            }

            foreach ($data_image_link as $row_image_product_link) {
                fputcsv($image_link_csv, $row_image_product_link, ';');
            }


        }
        fclose($image_lang_csv);
        fclose($image_shop_csv);
        fclose($image_csv);
        fclose($image_link_csv);
        fclose($product_csv);
        fclose($product_lang_csv);
        fclose($product_attribute_csv);
        fclose($product_attribute_combination_csv);
        fclose($product_attribute_shop_csv);
        fclose($feature_product_csv);
        $exportToPrestashopcsv = "export_" . date("Y-m-d") . ".tar";
        if (file_exists($save_to.$exportToPrestashopcsv)) {
            unlink($save_to.$exportToPrestashopcsv);
        }

        $zipName = $save_to.$exportToPrestashopcsv;
        $pharfiletounlink= $save_to.$exportToPrestashopcsv.".gz";
        if (file_exists($pharfiletounlink)) {
            unlink($pharfiletounlink);
        }
        $pharfile=$exportToPrestashopcsv.".gz";


        $phar = new \PharData($zipName);
        $phar->addFile($save_to . 'psz6_attribute.csv', 'psz6_attribute.csv');
        $phar->addFile($save_to . 'psz6_attribute_shop.csv', 'psz6_attribute_shop.csv');
        $phar->addFile($save_to .'psz6_attribute_group.csv',  'psz6_attribute_group.csv');
        $phar->addFile($save_to .'psz6_attribute_group_lang.csv',  'psz6_attribute_group_lang.csv');
        $phar->addFile($save_to .'psz6_attribute_group_shop.csv',  'psz6_attribute_group_shop.csv');
        $phar->addFile($save_to .'psz6_attribute_lang.csv',  'psz6_attribute_lang.csv');
        $phar->addFile($save_to .'psz6_category.csv',  'psz6_category.csv');
        $phar->addFile($save_to .'psz6_category_lang.csv',  'psz6_category_lang.csv');
        $phar->addFile($save_to .'psz6_feature.csv', 'psz6_feature.csv');
        $phar->addFile($save_to .'psz6_feature_lang.csv',  'psz6_feature_lang.csv');
        $phar->addFile($save_to .'psz6_feature_product.csv',  'psz6_feature_product.csv');
        $phar->addFile($save_to .'psz6_feature_value.csv',  'psz6_feature_value.csv');
        $phar->addFile($save_to .'psz6_feature_value_lang.csv',  'psz6_feature_value_lang.csv');
        $phar->addFile($save_to .'psz6_image.csv',  'psz6_image.csv');
        $phar->addFile($save_to . 'psz6_image_lang.csv', 'psz6_image_lang.csv');
        $phar->addFile($save_to .'psz6_image_link.csv', 'psz6_image_link.csv');
        $phar->addFile($save_to .'psz6_image_shop.csv',  'psz6_image_shop.csv');
        $phar->addFile($save_to .'psz6_product.csv',  'psz6_product.csv');
        $phar->addFile($save_to .'psz6_product_attribute.csv',  'psz6_product_attribute.csv');
        $phar->addFile($save_to .'psz6_product_attribute_combination.csv',  'psz6_product_attribute_combination.csv');
        $phar->addFile($save_to .'psz6_product_attribute_shop.csv',  'psz6_product_attribute_shop.csv');
        $phar->addFile($save_to .'psz6_product_lang.csv',  'psz6_product_lang.csv');

        if ($phar->count() > 0) {
            /** @var \PharData $compressed */
            $compressed = $phar->compress(\Phar::GZ);
            if (file_exists($compressed->getPath())) {
                unlink($save_to.'psz6_attribute.csv');
                unlink($save_to.'psz6_attribute_shop.csv');
                unlink($save_to.'psz6_attribute_group.csv');
                unlink($save_to.'psz6_attribute_group_lang.csv');
                unlink($save_to.'psz6_attribute_group_shop.csv');
                unlink($save_to.'psz6_attribute_lang.csv');
                unlink($save_to.'psz6_category.csv');
                unlink($save_to.'psz6_category_lang.csv');
                unlink($save_to.'psz6_feature.csv');
                unlink($save_to.'psz6_feature_lang.csv');
                unlink($save_to.'psz6_feature_product.csv');
                unlink($save_to.'psz6_feature_value.csv');
                unlink($save_to.'psz6_feature_value_lang.csv');
                unlink($save_to.'psz6_image.csv');
                unlink($save_to.'psz6_image_lang.csv');
                unlink($save_to.'psz6_image_link.csv');
                unlink($save_to.'psz6_image_shop.csv');
                unlink($save_to.'psz6_product.csv');
                unlink($save_to.'psz6_product_attribute.csv');
                unlink($save_to.'psz6_product_attribute_combination.csv');
                unlink($save_to.'psz6_product_attribute_shop.csv');
                unlink($save_to.'psz6_product_lang.csv');


            }

        }

        $ftp_server="ftp.iwes.shop";
        $ftp_user_name="iwesshop";
        $ftp_user_pass="XtUWicJUrEXv";
        $remote_file = "/public_html/tmp/";

        $ftp_url = "ftp://".$ftp_user_name.":".$ftp_user_pass."@".$ftp_server.$remote_file.$pharfile;
        $errorMsg = '';
        $fileToSend= $save_to.$pharfile;
// ------- Upload file through FTP ---------------

            $ch = curl_init();
            $fp = fopen ($fileToSend, "r");
            // we upload a TXT file
            curl_setopt($ch, CURLOPT_URL, $ftp_url);
            curl_setopt($ch, CURLOPT_UPLOAD, 1);
            curl_setopt($ch, CURLOPT_INFILE, $fp);
            // set size of the file, which isn't _mandatory_ but
            // helps libcurl to do extra error checking on the upload.
            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($fileToSend));
            $res = curl_exec ($ch);
            $errorMsg = curl_error($ch);
            $errorNumber = curl_errno($ch);
            curl_close ($ch);
/*if (file_exists($save_to.$exportToPrestashopcsv)) {

    unlink($save_to.$exportToPrestashopcsv);
}*/

        /* exec( 'cd '.$save_to );
         exec('zip -r '.$filename.' psz6_attribute.csv psz6_attribute_group.csv psz6_attribute_group_lang.csv psz6_attribute_group_shop.csv
         psz6_attribute_lang.csv psz6_category.csv psz6_category_lang.csv psz6_feature.csv psz6_feature_lang.csv psz6_feature_product.csv psz6_feature_value.csv
         psz6_feature_value_lang.csv psz6_image.csv psz6_image_lang.csv psz6_image_link.csv psz6_image_shop.csv psz6_product.csv psz6_product_attribute.csv psz6_product_attribute_combination.csv
          psz6_product_attribute_shop.csv psz6_product_lang.csv' );*/



        $res = 'esportazione eseguita';
        return $res;
    }


}
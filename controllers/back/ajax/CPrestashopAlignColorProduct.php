<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
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
 * Class CPrestashopAlignColorProduct
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 26/11/2018
 * @since 1.0
 */


class CPrestashopAlignColorProduct extends AAjaxController
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
        $sql = "UPDATE MarketplaceHasProductAssociate SET statusPublished='2' WHERE statusPublished='1'";
        \Monkey::app()->dbAdapter->query($sql, []);

        /* $sql = "DELETE FROM PrestashopHasProduct";
          $res_delete = \Monkey::app()->dbAdapter->query($sql, []);
          $sql = "ALTER TABLE PrestashopHasProduct AUTO_INCREMENT=1";
          $res_delete = \Monkey::app()->dbAdapter->query($sql, []);*/
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
        $stmtdeleteFeatureColorProduct = $db_con->prepare("delete from   psz6_feature_product  where id_feature='4657'");
        $stmtdeleteFeatureColorProduct->execute();

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
        /** selezione  prodotti  */
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
  S3.price  /122*22 AS vatfullprice,
  S3.price AS full_price,
  S3.salePrice AS salePrice,
  S3.salePrice /122*22 AS vatsaleprice,
  IF(`p`.isOnSale=1,'saldo','prezzopieno') AS tipoprezzo,
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
  php.statusPublished                                                                     AS status,
  Translation.name as color,
  p.productColorGroupId as colorId

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
  LEFT  JOIN ProductColorGroup PCG ON p.productColorGroupId = PCG.id
  join ProductColorGroupTranslation Translation ON PCG.id = Translation.productColorGroupId
  LEFT JOIN ProductName pn ON p.id = pn.id
  LEFT JOIN MarketplaceHasShop mpas ON php.shopId=mpas.shopId
WHERE   php.statusPublished IN (2)  AND S3.price > 0 and Translation.langId=1
GROUP BY p.id,p.productVariantId
ORDER BY `p`.`id`";


        $res_product = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();

$res="";
        foreach ($res_product as $value_product) {


            $p = $value_product['prestaId'];
            $productId=$value_product['productId'];
            $productVariantId=$value_product['productVariantId'];
            $quantity_product = $value_product['quantity'];
            $reference =$value_product['productId']."-".$value_product['productVariantId'];
           $color=$value_product['color'];

                $stmtUpdateProduct = $db_con->prepare("INSERT INTO  psz6_feature_product (`id_feature`,`id_product`,`id_feature_value`) VALUES ('4657'," . $p .
                    ",(select pfv.id_feature_value from psz6_feature_value pfv  join psz6_feature_value_lang pfvn on pfv.id_feature_value=pfvn.id_feature_value where
                 pfvn.id_lang=2 and pfv.id_feature=4657 and pfvn.value ='".$color."'))");

               $stmtUpdateProduct->execute();

            }

        $sql = "UPDATE MarketplaceHasProductAssociate SET statusPublished='1' WHERE statusPublished='2'";
        \Monkey::app()->dbAdapter->query($sql, []);
                    $res.="Inserimento Colori Prodotto Variante Eseguito";
        return $res;
    }
}

          





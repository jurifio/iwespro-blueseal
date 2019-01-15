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
class CPrestashopAlignColorAttributeProduct extends AAjaxController
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
        /* @var $productId */
        /* @var $productVariantId */
        $productId = '';
        $productVariantId = '';

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

        if (ENV == 'dev') {

            $save_to = '/media/sf_sites/PickyshopNew/tmp/';

        } else {
            $save_to = '/home/pickyshop/public_html/temp-prestashop/';
        }


        $stmtGetProduct = $db_con->prepare("SELECT id_product, reference FROM psz6_product");

        $stmtGetProduct->execute();
        while ($rowGetProduct = $stmtGetProduct->fetch(PDO::FETCH_ASSOC)) {
            $prestashopProductId = $rowGetProduct['id_product'];
            $reference = $rowGetProduct['reference'];
            $array = array($reference);
            $arrayproduct = implode('-', $array);

            $singleproduct = explode('-', $arrayproduct);

            $productId = $singleproduct[0];
            $productVariantId = $singleproduct[1];


            /**
             * @var $db CMySQLAdapter
             */
            /*********************   preparazione tabella di collegamento  ****************************************************//////
            /*** popolamento tabella */


            /****** SEZIONE PRODOTTI */
            /** esportazione prodotti */

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
  p.productColorGroupId                                                          AS colorGroupId,
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
WHERE   p.id=" . $productId . " AND p.productVariantId=" . $productVariantId . " AND  php.statusPublished IN (1)  AND S3.price > 0 
GROUP BY p.id,p.productVariantId
ORDER BY `p`.`id`";


            $res_product = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();


            $productRepo = \Monkey::app()->repoFactory->create('Product');
            $productNameTranslationRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');
            $dirtyProductRepo = \Monkey::app()->repoFactory->create('DirtyProduct');
            $productColorGroupRepo = \Monkey::app()->repoFactory->create('ProductColorGroup');
            $productColorGroupTranslationRepo = \Monkey::app()->repoFactory->create('ProductColorGroupTranslation');


            foreach ($res_product as $value_product) {
// recupero id colore dalle caratteristiche
            switch($value_product['colorGroupId']){
                case 1:
                    $colorgroup=77226;
                    break;
                case 2:
                    $colorgroup=77227;
                    break;
                case 3:
                    $colorgroup=77228;
                    break;
                case 4:
                    $colorgroup=77229;
                    break;
                case 5:
                    $colorgroup=77230;
                    break;
                case 6:
                    $colorgroup=77231;
                    break;
                case 7:
                    $colorgroup=77232;
                    break;
                case 9:
                    $colorgroup=77233;
                    break;
                case 10:
                    $colorgroup=77234;
                    break;
                case 14:
                    $colorgroup=77235;
                    break;
                case 16:
                    $colorgroup=77236;
                    break;
                case 18:
                    $colorgroup=77237;
                    break;
                case 19:
                    $colorgroup=77238;
                    break;
                case 24:
                    $colorgroup=77239;
                    break;
                case 26:
                    $colorgroup=77240;
                    break;
                case 27:
                    $colorgroup=77241;
                    break;
                case 29:
                    $colorgroup=77242;
                    break;
                case 30:
                    $colorgroup=77243;
                    break;
                case 31:
                    $colorgroup=77244;
                    break;
                case 37:
                    $colorgroup=77245;
                    break;
                case 38:
                    $colorgroup=77246;
                    break;
                case 39:
                    $colorgroup=77247;
                    break;
                case 42:
                    $colorgroup=77248;
                    break;
                case 46:
                    $colorgroup=77249;
                    break;
                case 49:
                    $colorgroup=77250;
                    break;
                case 52:
                    $colorgroup=77251;
                    break;
                case 53:
                    $colorgroup=77252;
                    break;
                case 59:
                    $colorgroup=77253;
                    break;
                case 61:
                    $colorgroup=77254;
                    break;

            }
                $featureId=4685;
                $id_product=$value_product['prestaId'];
                $colorgroupName=$productColorGroupTranslationRepo->findOneBy(['productColorGroupId'=>$value_product['colorGroupId']]);
                $colorName=$colorgroupName->name;


                //associazione Gruppo colore con feature prodotto
                try {
                    $stmtFeatureProduct = $db_con->prepare("INSERT INTO psz6_feature_product (`id_feature`,`id_product`,`id_feature_value`) 
                                                   VALUES ('" . $featureId . "',
                                                           '" . $id_product . "',
                                                           '" . $colorgroup . "')
                                                           ON DUPLICATE KEY
                                                           UPDATE
                                                           `id_feature`='".$featureId."',
                                                           `id_product`='".$id_product."',
                                                           `id_feature_value`='".$colorgroup."'");

                    $stmtFeatureProduct->execute();
                    $res.="Inserimento Gruppo Colore " .$colorName." per il prodotto ". $value_product['product_id']. " <br>";
                } catch (PDOException $e) {
                    $res .= $e->getMessage();
                }



                $featureColorId=4686;

                // recupero colore produttore da  Tabella DirtyProduct
                $color=$value_product['color_supplier'];
                try {

                    //creazione feature su psz6_feature_value
                    $stmtFeaturevalueinsert=$db_con->prepare("INSERT INTO psz6_feature_value (`id_feature`,`custom`) 
                    VALUES (".$featureColorId.", 0)");
                    $stmtFeaturevalueinsert->execute();
                    //recupero ultimo id inserito
                    $stmtFeatureLastValueInsert=$db_con->prepare("select max(id_feature_value) as idfeaturevalue from psz6_feature_value");
                    $stmtFeatureLastValueInsert->execute();

                    $stmtfeatureValueLastnumberId=$stmtFeatureLastValueInsert->fetch();
                    $stmtfeatureValueLastId=$stmtfeatureValueLastnumberId[0];
                    //inserimento valore su tabella lingua psz6_feature_value_lang per tutte e tre le lingue
                    $stmtFeatureValueLang=$db_con->prepare("INSERT INTO psz6_feature_value_lang (`id_feature_value`,`id_lang`,`value`) 
                                                    VALUES ('" . $stmtfeatureValueLastId . "',
                                                           ' 1 ',
                                                           '" . $color . "')
                                                           ON DUPLICATE KEY
                                                           UPDATE
                                                           `id_feature_value`='".$featureColorId."',
                                                           `id_lang`='1',
                                                           `value`='".$color."'");
                    $stmtFeatureValueLang->execute();
                    $res.="Inserimento lingua inglese colore fornitore". $color. "per il prodotto ". $value_product['product_id']. " <br>";

                    $stmtFeatureValueLang=$db_con->prepare("INSERT INTO psz6_feature_value_lang (`id_feature_value`,`id_lang`,`value`) 
                                                    VALUES ('" . $stmtfeatureValueLastId . "',
                                                           ' 2 ',
                                                           '" . $color . "')
                                                           ON DUPLICATE KEY
                                                           UPDATE
                                                           `id_feature_value`='".$stmtfeatureValueLastId."',
                                                           `id_lang`='2',
                                                           `value`='".$color."'");
                    $stmtFeatureValueLang->execute();
                    $res.="Inserimento lingua italiana colore fornitore". $color. "per il prodotto ". $value_product['product_id']. " <br>";

                    $stmtFeatureValueLang=$db_con->prepare("INSERT INTO psz6_feature_value_lang (`id_feature_value`,`id_lang`,`value`) 
                                                    VALUES ('" . $stmtfeatureValueLastId . "',
                                                           ' 3 ',
                                                           '" . $color . "')
                                                           ON DUPLICATE KEY
                                                           UPDATE
                                                           `id_feature_value`='".$stmtfeatureValueLastId."',
                                                           `id_lang`='3',
                                                           `value`='".$color."'");
                    $stmtFeatureValueLang->execute();
                    $res.="Inserimento lingua tedesca colore fornitore". $color. "per il prodotto ". $value_product['product_id']. " <br>";
                    //associazione feature colore produttore a prodotto prestashop
                    $stmtFeatureProduct = $db_con->prepare("INSERT INTO psz6_feature_product (`id_feature`,`id_product`,`id_feature_value`) 
                                                   VALUES ('" . $stmtfeatureValueLastId . "',
                                                           '" . $id_product . "',
                                                           '" . $color . "')
                                                           ON DUPLICATE KEY
                                                           UPDATE
                                                           `id_feature`='".$stmtfeatureValueLastId."',
                                                           `id_product`='".$id_product."',
                                                           `id_feature_value`='".$color."'");

                    $stmtFeatureProduct->execute();
                    $res.="associazione colore fornitore ".$color. " prodotto prestashop ".$id_product;
                } catch (PDOException $e) {

                    $res .= $e->getMessage();
                }



            }

        }
        return $res;

    }


}


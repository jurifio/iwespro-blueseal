<?php

namespace bamboo\blueseal\jobs;

use bamboo\domain\entities\CCartAbandonedEmailSend;
use bamboo\domain\repositories\CCartAbandonedEmailSendRepo;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CCart;
use bamboo\domain\entities\CCartAbandonedEmailParam;
use bamboo\domain\entities\CCouponType;
use bamboo\domain\entities\CCoupon;
use bamboo\domain\entities\CCartLine;
use bamboo\core\base\CSerialNumber;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;
use pdo;
use prepare;

use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProduct;
use bamboo\core\events\AEventListener;


/**
 * Class CPrestashopAlignQuantityJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 09/11/2018
 * @since 1.0
 */
class CPrestashopAlignQuantityJob extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {

        set_time_limit(0);
        ini_set('memory_limit', '2048M');
        /* @var $productId*/
        /* @var $productVariantId */
        $productId='';
        $productVariantId='';

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
  LEFT  JOIN ProductColorGroup PCG ON p.productColorGroupId = PCG.id
  LEFT JOIN ProductName pn ON p.id = pn.id
  LEFT JOIN MarketplaceHasShop mpas ON php.shopId=mpas.shopId
WHERE   p.id=" . $productId . " AND p.productVariantId=" . $productVariantId . " AND  php.statusPublished IN (2)  AND S3.price > 0 
GROUP BY p.id,p.productVariantId
ORDER BY `p`.`id`";


            $res_product = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();


            foreach ($res_product as $value_product) {
                $eanproductparent = \Monkey::app()->repoFactory->create('ProductEan')->findOneBy(['productId' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId']]);

                if (!is_null($eanproductparent)) {
                    $ean13product = $eanproductparent->ean;
                } else {
                    $ean13product = '';
                }

                $p = $value_product['prestaId'];
                $productId = $value_product['productId'];
                $productVariantId = $value_product['productVariantId'];
                $quantity_product = $value_product['quantity'];
                if ($value_product['isOnSale'] == 0) {
                    $price = $value_product['priceMarketplace'] - ($value_product['priceMarketplace'] * 22 / 122);
                } else {

                    $price = $value_product['salePrice'] - ($value_product['salePrice'] * 22 / 122);

                }


                round($price, 1, PHP_ROUND_HALF_DOWN);
                $stmtUpdateProduct = $db_con->prepare("UPDATE psz6_product SET quantity=" . $quantity_product . ",  price='" . $price . "',  ean13='" . $ean13product . "'
             WHERE id_product=" . $p);
                $stmtUpdateProduct->execute();
                if ($value_product['titleSale'] == 1) {
                    $findname = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId']]);
                    //concat(pb.name,' ',pn.name,' ',dp.var , dp.itemno,' ', pv.name)
                    $productbrandName = $findname->productBrand->name;
                    $findProductName = \Monkey::app()->repoFactory->create('ProductNameTranslation')->findOneBy(['productId' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId'], 'langId' => 1]);
                    if ($findProductName == null) {
                        $productnameName = '';
                    } else {
                        $productnameName = $findProductName->name;
                    }
                    $dirtyProduct = \Monkey::app()->repoFactory->create('DirtyProduct')->findOneBy(['productId' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId']]);
                    $productitemnoName = $dirtyProduct->itemno;
                    $productcolorSupplierName = $dirtyProduct->var;
                    $titleTextSaleLang2 = str_replace("'", "\'", $productbrandName) . " " . $productnameName . " " . $productitemnoName . " " . $productcolorSupplierName . " Sconto del " . number_format($value_product['percentSale'],0,',','.') . " %  da Euro " . number_format($value_product['priceMarketplace'], 2, ',', '.') . " a Euro " . number_format($value_product['salePrice'], 2, ',', '.');
                    $titleTextSaleLang1 = str_replace("'", "\'", $productbrandName) . " " . $productnameName . " " . $productitemnoName . " " . $productcolorSupplierName . " Sale " . number_format($value_product['percentSale'],0,',','.') . " % OFF  From Euro " . number_format($value_product['priceMarketplace'], 2, ',', '.') . " To Euro " . number_format($value_product['salePrice'], 2, ',', '.');
                    $titleTextSaleLang3 = str_replace("'", "\'", $productbrandName) . " " . $productnameName . " " . $productitemnoName . " " . $productcolorSupplierName . " Sale " . number_format($value_product['percentSale'],0,',','.') . " % OFF  From Euro " . number_format($value_product['priceMarketplace'], 2, ',', '.') . " To Euro " . number_format($value_product['salePrice'], 2, ',', '.');
                    $descriptionTextSaleLang2 = str_replace("'", "\'", $productbrandName) . " " . $productnameName . " " . $productitemnoName . " " . $productcolorSupplierName . " Scontato del " . number_format($value_product['percentSale'],0,',','.') . " %  da Euro " . number_format($value_product['priceMarketplace'], 2, ',', '.') . " a Euro " . number_format($value_product['salePrice'], 2, ',', '.');
                    $descriptionTextSaleLang1 = str_replace("'", "\'", $productbrandName) . " " . $productnameName . " " . $productitemnoName . " " . $productcolorSupplierName . " Special Discount  " . number_format($value_product['percentSale'],0,',','.') . " % OFF  From Euro " . number_format($value_product['priceMarketplace'], 2, ',', '.') . " To Euro " . number_format($value_product['salePrice'], 2, ',', '.');
                    $descriptionTextSaleLang3 = str_replace("'", "\'", $productbrandName) . " " . $productnameName . " " . $productitemnoName . " " . $productcolorSupplierName . " Special Discount  " . number_format($value_product['percentSale'],0,',','.') . " % OFF  From Euro " . number_format($value_product['priceMarketplace'], 2, ',', '.') . " To Euro " . number_format($value_product['salePrice'], 2, ',', '.');
                    $descriptionshortTextSaleLang2 =  " Sconto del " . $value_product['percentSale'] . " %  da Euro " . number_format($value_product['priceMarketplace'], 2, ',', '.') . " a Euro " . number_format($value_product['salePrice'], 2, ',', '.');
                    $descriptionshortTextSaleLang1 =  " Sale  " . $value_product['percentSale'] . " % OFF  From Euro " . number_format($value_product['priceMarketplace'], 2, ',', '.') . " To Euro " . number_format($value_product['salePrice'], 2, ',', '.');
                    $descriptionshortTextSaleLang3 =  " Sale  " . $value_product['percentSale'] . " % OFF  From Euro " . number_format($value_product['priceMarketplace'], 2, ',', '.') . " To Euro " . number_format($value_product['salePrice'], 2, ',', '.');

                    $stmtUpdateProductLang = $db_con->prepare("UPDATE psz6_product_lang SET  description_short=concat('".$descriptionshortTextSaleLang2."',description_short),`description`=concat('" . $descriptionTextSaleLang2 . "',description),`name`='" . $titleTextSaleLang2 . "', meta_title='" . $titleTextSaleLang2 . "' WHERE id_product=" . $p . " AND id_lang=2 AND id_shop=" . $value_product['shopPrestashopId'] . "  ");
                    $stmtUpdateProductLang->execute();
                    $stmtUpdateProductLang = $db_con->prepare("UPDATE psz6_product_lang SET description_short=concat('".$descriptionshortTextSaleLang1."',description_short), `description`=concat('" . $descriptionTextSaleLang1 . "',description),`name`='" . $titleTextSaleLang1 . "', meta_title='" . $titleTextSaleLang1 . "' WHERE id_product=" . $p . " AND id_lang=1 AND id_shop=" . $value_product['shopPrestashopId'] . "  ");
                    $stmtUpdateProductLang->execute();
                    $stmtUpdateProductLang = $db_con->prepare("UPDATE psz6_product_lang SET description_short=concat('".$descriptionshortTextSaleLang3."',description_short), `description`=concat('" . $descriptionTextSaleLang3 . "',description),`name`='" . $titleTextSaleLang3 . "', meta_title='" . $titleTextSaleLang3 . "' WHERE id_product=" . $p . " AND id_lang=3 AND id_shop=" . $value_product['shopPrestashopId'] . "  ");
                    $stmtUpdateProductLang->execute();


                } else {
                    $findname = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId']]);
                    //concat(pb.name,' ',pn.name,' ',dp.var , dp.itemno,' ', pv.name)
                    $productbrandName = $findname->productBrand->name;
                    $findProductName = \Monkey::app()->repoFactory->create('ProductNameTranslation')->findOneBy(['productId' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId'], 'langId' => 1]);
                    if ($findProductName == null) {
                        $productnameName = '';
                    } else {
                        $productnameName = $findProductName->name;
                    }
                    $dirtyProduct = \Monkey::app()->repoFactory->create('DirtyProduct')->findOneBy(['productId' => $value_product['productId'], 'productVariantId' => $value_product['productVariantId']]);
                    $productitemnoName = $dirtyProduct->itemno;
                    $productcolorSupplierName = $dirtyProduct->var;
                    $titleTextSaleLang2 = str_replace("'", "\'", $productbrandName) . " " . $productnameName . " " . $productitemnoName . " " . $productcolorSupplierName;
                    $titleTextSaleLang1 = str_replace("'", "\'", $productbrandName) . " " . $productnameName . " " . $productitemnoName . " " . $productcolorSupplierName;
                    $titleTextSaleLang3 = str_replace("'", "\'", $productbrandName) . " " . $productnameName . " " . $productitemnoName . " " . $productcolorSupplierName;
                    $descriptionTextSaleLang2 = str_replace("'", "\'", $productbrandName) . " " . $productnameName . " " . $productitemnoName . " " . $productcolorSupplierName;
                    $descriptionTextSaleLang1 = str_replace("'", "\'", $productbrandName) . " " . $productnameName . " " . $productitemnoName . " " . $productcolorSupplierName;
                    $descriptionTextSaleLang3 = str_replace("'", "\'", $productbrandName) . " " . $productnameName . " " . $productitemnoName . " " . $productcolorSupplierName;
                    $stmtUpdateProductLang = $db_con->prepare("UPDATE psz6_product_lang SET `description`='" . $descriptionTextSaleLang2 . "',`name`='" . $titleTextSaleLang2 . "', meta_title='" . $titleTextSaleLang2 . "' WHERE id_product=" . $p . " AND id_lang=2 AND id_shop=" . $value_product['shopPrestashopId'] . "  ");
                    $stmtUpdateProductLang->execute();
                    $stmtUpdateProductLang = $db_con->prepare("UPDATE psz6_product_lang SET `description`='" . $descriptionTextSaleLang1 . "',`name`='" . $titleTextSaleLang1 . "', meta_title='" . $titleTextSaleLang1 . "' WHERE id_product=" . $p . " AND id_lang=1 AND id_shop=" . $value_product['shopPrestashopId'] . "  ");
                    $stmtUpdateProductLang->execute();
                    $stmtUpdateProductLang = $db_con->prepare("UPDATE psz6_product_lang SET `description`='" . $descriptionTextSaleLang3 . "',`name`='" . $titleTextSaleLang3 . "', meta_title='" . $titleTextSaleLang3 . "' WHERE id_product=" . $p . " AND id_lang=3 AND id_shop=" . $value_product['shopPrestashopId'] . "  ");
                    $stmtUpdateProductLang->execute();
                }

                $stmtCheckStockAvailable = $db_con->prepare("SELECT  count(id_stock_available) AS checkStockExist FROM    psz6_stock_available WHERE id_product_attribute=0 and id_product=" . $p);
                $stmtCheckStockAvailable->execute();
                $rows = $stmtCheckStockAvailable->fetchAll(PDO::FETCH_ASSOC);
                if ($rows[0]['checkStockExist'] == 0) {
                    $stmtInsertStockAvailable = $db_con->prepare("INSERT INTO psz6_stock_available (id_product,
                                                                                                        id_product_attribute,
                                                                                                        id_shop,
                                                                                                        id_shop_group,
                                                                                                        quantity,
                                                                                                        physical_quantity,
                                                                                                        reserved_quantity,
                                                                                                        depends_on_stock,
                                                                                                        out_of_stock)
                                                                                                         VALUES (" . $p . ",
                                                                                                                 '0',   
                                                                                                                 " . $value_product['shopPrestashopId'] . ",
                                                                                                                 '0',
                                                                                                                 " . $quantity_product . ",
                                                                                                                 " . $quantity_product . ",
                                                                                                                 '0',
                                                                                                                 '0',
                                                                                                                 '0')");
                    $stmtInsertStockAvailable->execute();
                } else {
                    $stmtUpdateStockAvailable = $db_con->prepare("UPDATE psz6_stock_available SET quantity=" . $quantity_product . " 
             WHERE id_product_attribute=0 AND id_product=" . $p);
                    $stmtUpdateStockAvailable->execute();
                }
                $res_product_attribute = \Monkey::app()->repoFactory->create('ProductSku')->findBy(['productId' => $productId, 'productVariantId' => $productVariantId]);
                foreach ($res_product_attribute as $value_attribute) {
                    $stockQty = $value_attribute->stockQty;
                    $ean = $value_attribute->ean;
                    $reference = $value_attribute->productId . "-" . $value_attribute->productVariantId . "-" . $value_attribute->productSizeId;
                    if ($ean == null) {
                        $res_product_ean = \Monkey::app()->repoFactory->create('ProductEan')->findOneBy(['ProductId' => $value_attribute->productId, 'productVariantId' => $value_attribute->productVariantId, 'productSizeId' => $value_attribute->productSizeId]);
                        if ($res_product_ean != null) {
                            $ean = $res_product_ean->ean;
                        } else {
                            $ean = '';
                        }
                    }


                    $stmtUpdateProductEan = $db_con->prepare("UPDATE psz6_product_attribute  SET quantity=" . $stockQty . " , ean13='" . $ean . "' WHERE reference ='" . $reference . "'");
                    $stmtUpdateProductEan->execute();
                    $stmtGetProductAttribute = $db_con->prepare("SELECT id_product_attribute, id_product FROM psz6_product_attribute
 WHERE reference='" . $reference . "'");
                    $stmtGetProductAttribute->execute();
                    while ($rowGetProductAttribute = $stmtGetProductAttribute->fetch(PDO::FETCH_ASSOC)) {
                        $product_stockAttribute = $rowGetProductAttribute['id_product_attribute'];
                        $product_stock = $rowGetProductAttribute['id_product'];

                        $stmtCheckStockAvailable = $db_con->prepare("SELECT  count(id_stock_available) AS checkStockExist FROM    psz6_stock_available WHERE id_product=" . $product_stock . " AND id_product_attribute=" . $product_stockAttribute);
                        $stmtCheckStockAvailable->execute();
                        $rows = $stmtCheckStockAvailable->fetchAll(PDO::FETCH_ASSOC);
                        if ($rows[0]['checkStockExist'] == 0) {
                            $stmtInsertStockAvailable = $db_con->prepare("INSERT INTO psz6_stock_available (id_product,
                                                                                                        id_product_attribute,
                                                                                                        id_shop,
                                                                                                        id_shop_group,
                                                                                                        quantity,
                                                                                                        physical_quantity,
                                                                                                        reserved_quantity,
                                                                                                        depends_on_stock,
                                                                                                        out_of_stock)
                                                                                                         VALUES (" . $product_stock . ",
                                                                                                                 " . $product_stockAttribute . ",
                                                                                                                 " . $value_product['shopPrestashopId'] . ",
                                                                                                                 '0',
                                                                                                                 " . $stockQty . ",
                                                                                                                 '0',
                                                                                                                 '0',
                                                                                                                 '0',
                                                                                                                 '0')");
                            $stmtInsertStockAvailable->execute();
                        } else {
                            $stmtUpdateAttributeStockAvailable = $db_con->prepare("UPDATE psz6_stock_available SET quantity=" . $stockQty . "
                 WHERE id_product=" . $product_stock . " AND id_product_attribute=" . $product_stockAttribute);

                            $stmtUpdateAttributeStockAvailable->execute();
                        }
                    }

                }
            }
            $sql = "UPDATE MarketplaceHasProductAssociate SET statusPublished='1' WHERE statusPublished='2' and id=".$prestashopProductId;
            \Monkey::app()->dbAdapter->query($sql, []);
        }
        $res="Allineamento quantità Stock  eseguita  finita alle ore ". date('Y-m-d H:i:s');
        $this->report('Align  to Prestashop Quantity ',$res,$res);
        return $res;
    }


}
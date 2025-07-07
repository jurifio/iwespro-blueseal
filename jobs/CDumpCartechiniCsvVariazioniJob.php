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

use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProduct;
use bamboo\core\events\AEventListener;
use PDO;
use DateTime;
use Throwable;



/**
 * Class CDumpCartechiniCsvVariazioniJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/07/2018
 * @since 1.0
 */
class CDumpCartechiniCsvVariazioniJob extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {

if (ENV=='dev'){
    $host = 'localhost';
    $db   = 'pickyshop_dev';
    $user = 'root';
    $pass = 'geh44fed';
    $charset = 'utf8_general_ci';
}else{
    $host = 'localhost';
    $db   = 'pickyshopfront';
    $user = 'pickyshop4';
    $pass = 'rrtYvg6W!';
    $charset = 'utf8mb4';
}


// --- CONFIGURAZIONE FILE ---
        $csvFileName = 'variazioni.csv';
if (ENV=='dev') {
    $tempPath = '/media/sf_sites/iwespro/temp-prestashop/' . $csvFileName;
}else{
    $tempPath = '/home/iwespro/public_html/temp-prestashop/' . $csvFileName;
}
// --- CONFIGURAZIONE SFTP ---
        $ftpHost = '5.189.152.89';
        $ftpUser = 'export@cartechinishop.com';
        $ftpPass = 'Scoponi2024!';
        $ftpRemotePath = '/' . $csvFileName;

// --- CONNESSIONE DATABASE ---
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);

            // --- QUERY ---
            $stmt = $pdo->query("SELECT p.id as productId,
		 concat(`p`.`id`,'-',p.productVariantId)  AS `Reference`,
		  CONCAT(`S2`.`productId`,'-',S2.productVariantId,'-',S2.productSizeId)  AS `Reference_combination`,
		  'Colore|Taglia' as `Attribute Names`,
		  concat(`pv`.`name`,'|',`psz`.`name`) as `Attribute Values`,
		 
		  S2.ean AS EAN,
			(SELECT if(`pp21`.`local`=null, GROUP_CONCAT(concat('https://iwes.s3.eu-west-1.amazonaws.com/',pb.slug,'/',`pp21`.`name`) SEPARATOR '|'),GROUP_CONCAT(concat('https://iwes.pro/product/',`pp21`.`name`) SEPARATOR '|')) FROM ProductPhoto pp21  JOIN ProductHasProductPhoto phpp21 ON phpp21.productPhotoId=pp21.id
			WHERE phpp21.productId=p.id AND phpp21.productVariantId=p.productVariantId AND pp21.size='1124' limit 1) as Images,
			
	(SELECT if(`pp22`.`local`=null,concat('https://iwes.s3.eu-west-1.amazonaws.com/',pb.slug,'/',`pp22`.`name`),concat('https://iwes.pro/product/',`pp22`.`name`)) FROM ProductPhoto pp22  JOIN ProductHasProductPhoto phpp22 ON phpp22.productPhotoId=pp22.id
			WHERE phpp22.productId=p.id AND phpp22.productVariantId=p.productVariantId AND pp22.size='1124' AND `pp22`.`order`=1 limit 1) AS `Image 1`,
				
			(SELECT if(`pp23`.`local`=null,concat('https://iwes.s3.eu-west-1.amazonaws.com/',pb.slug,'/',`pp23`.`name`),concat('https://iwes.pro/product/',`pp23`.`name`))  FROM ProductPhoto pp23  JOIN ProductHasProductPhoto phpp23 ON phpp23.productPhotoId=pp23.id
			WHERE phpp23.productId=p.id AND phpp23.productVariantId=p.productVariantId AND pp23.size='1124' AND `pp23`.`order`=2 limit 1) AS `Image 2`,
			
(SELECT if(`pp24`.`local`=null,concat('https://iwes.s3.eu-west-1.amazonaws.com/',pb.slug,'/',`pp24`.`name`),concat('https://iwes.pro/product/',`pp24`.`name`))  FROM ProductPhoto `pp24`  JOIN ProductHasProductPhoto phpp24 ON phpp24.productPhotoId=pp24.id
			WHERE phpp24.productId=p.id AND phpp24.productVariantId=p.productVariantId AND `pp24`.`size`='1124' AND `pp24`.`order`=3 limit 1) AS `Image 3`,
			
(SELECT if(`pp25`.`local`=null,concat('https://iwes.s3.eu-west-1.amazonaws.com/',pb.slug,'/',`pp25`.`name`),concat('https://iwes.pro/product/',`pp25`.`name`))  FROM ProductPhoto pp25  JOIN ProductHasProductPhoto phpp25 ON phpp25.productPhotoId=pp25.id
			WHERE phpp25.productId=p.id AND phpp25.productVariantId=p.productVariantId AND `pp25`.`size`='1124' AND `pp25`.`order`=4 limit 1) AS `Image 4`,
''  AS `Image 5`,	
'' AS `Image 6`,
'' AS `Image 7`,
'' AS `Image 8`,
 S2.price as `Retail Price Tax Exc`,
 S2.price as `Retail Price Tax Inc`, 
 S2.salePrice as `Discounted Price Tax Exc`,
 S2.salePrice as `Discounted Price Tax Inc`,
 '' AS `Discounted Price Tax Exc If Discount Exists`,
		'' AS 	`Discounted Price Tax Inc If Discount Exists`,
		'' AS 	`Discount Percent`,
		'' AS 	`Discount Amount`,
		'' AS 	`Discount Base Price`,
		'' AS 	`Discount Starting Unit`,
		'' AS 	`Discount from`,
		'' AS 	`Discount to`,
 0 AS `Cost price`,	
S2.price as `Impact on Price`,	
	0 as `EcoTax`,
S2.stockQty as Quantity,
'in Stock' as `Stock Availability`,
1 as `Minimal Quantity`,
 '' as `Stock Location`,
 0 as `Low Stock Level`,
 '' as `Email Alert on Low Stock`,
 '' AS 	`Availability date`,
0 as `Impact on Weight`,
if(S2.stockQty > 1,'1','0') AS `Default`,
		 '15.000000'                                                                      AS `Width`,
        '25.000000'                                                                      AS `Height`,
        '10.000000'                                                                      AS `Depth`,
        '1.000000'                                                                      AS `Weight`,
CONCAT('https://www.cartechinishop.com/it','/',pb.slug,'/cpf/',p.itemno,'/p/',p.id,'/v/',p.productVariantId,'#/colore-',`pv`.`name`,'/taglia-',`psz`.`name`) AS `Combination URL IT`,
CONCAT('https://www.cartechinishop.com/de','/',pb.slug,'/cpf/',p.itemno,'/p/',p.id,'/v/',p.productVariantId,'#/colore-',`pv`.`name`,'/taglia-',`psz`.`name`) AS `Combination URL DE`,	
CONCAT('https://www.cartechinishop.com/gb','/',pb.slug,'/cpf/',p.itemno,'/p/',p.id,'/v/',p.productVariantId,'#/colore-',`pv`.`name`,'/taglia-',`psz`.`name`) AS `Combination URL GB`,	
CONCAT('https://www.cartechinishop.com/fr','/',pb.slug,'/cpf/',p.itemno,'/p/',p.id,'/v/',p.productVariantId,'#/colore-',`pv`.`name`,'/taglia-',`psz`.`name`) AS `Combination URL FR`,	
1 AS 		`Shop ID`,
	'Cartechini Shop' AS 		`Shop Name`, concat(p.itemno, '  ', pv.name,' ',`pb`.`name` )     AS `Product Name IT`,
		   concat(p.itemno, '  ', pv.name,' ',`pb`.`name` )     AS `Product Name DE`,
		    concat(p.itemno, '  ', pv.name,' ',`pb`.`name` )     AS `Product Name GB`,
			 concat(p.itemno, '  ', pv.name,' ',`pb`.`name` )     AS `Product Name FR`,
			 1 AS active,
			  concat(p.itemno, '  ', pv.name,' ',`pb`.`name`,' ',p.externalId) AS `Short Description IT`,
concat(p.itemno, '  ', pv.name,' ',`pb`.`name`,' ',p.externalId) AS `Short Description DE`,
concat(p.itemno, '  ', pv.name,' ',`pb`.`name`,' ',p.externalId) AS `Short Description GB`,	
concat(p.itemno, '  ', pv.name,' ',`pb`.`name`,' ',p.externalId) AS `Short Description FR`,	
 concat(p.itemno, '  ', pv.name,' ',`pb`.`name`,' ',p.externalId) AS `Long Description IT`,
concat(p.itemno, '  ', pv.name,' ',`pb`.`name`,' ',p.externalId) AS `Long Description DE`,
concat(p.itemno, '  ', pv.name,' ',`pb`.`name`,' ',p.externalId) AS `Long Description GB`,	
concat(p.itemno, '  ', pv.name,' ',`pb`.`name`,' ',p.externalId) AS `Long Description FR`,	
CONCAT('/',pb.slug,'/cpf/',p.itemno,'/p/',p.id,'/v/',p.productVariantId) AS `Friendly URL IT`,
CONCAT('/',pb.slug,'/cpf/',p.itemno,'/p/',p.id,'/v/',p.productVariantId) AS `Friendly URL DE`,	
CONCAT('/',pb.slug,'/cpf/',p.itemno,'/p/',p.id,'/v/',p.productVariantId) AS `Friendly URL GB`,	
CONCAT('/',pb.slug,'/cpf/',p.itemno,'/p/',p.id,'/v/',p.productVariantId) AS `Friendly URL FR`,	
 0 as `Product Price Tax Exc`,
 0 as `Product Price Tax inc`,
 0 as `Product Discounted Price Tax Exc`,
 0 as `Product Discounted Price Tax Inc`,
 '' `Product Discounted Price Tax Exc If Discount Exists`,
 '' as `Product Discounted Price Tax Inc If Discount Exists`,
 '' as `Product Discount Percent`,
  '' as `Product Discount Amount`,
  '' as `Product Discount Base Price`,
   '' as `Product Discount Starting Unit`,
   '' as `Product Discount from`,
   '' as `Product Discount to`,
    '' as `tax Rule`,
	 `pb`.`name`    AS `Brand`,
  (SELECT `node`.`slug` FROM `ProductCategory` `node`  WHERE 
		  `node`.`id`=`phpc`.`productCategoryId`  ) AS 
 `Default Category IT`,
	 (SELECT `node1`.`slug` FROM `ProductCategory` `node1`  WHERE 
		  `node1`.`id`=`phpc`.`productCategoryId`  ) AS 		`Default Category DE`,
	 (SELECT `node2`.`slug` FROM `ProductCategory` `node2`  WHERE 
		  `node2`.`id`=`phpc`.`productCategoryId`  ) AS 		`Default Category GB`,
 (SELECT `node3`.`slug` FROM `ProductCategory` `node3`  WHERE 
		  `node3`.`id`=`phpc`.`productCategoryId`  ) AS 		`Default Category FR`,
	(SELECT CONCAT(GROUP_CONCAT(`parent10`.`slug` SEPARATOR ' | '),' | ',`node10`.`slug`) FROM `ProductCategory` AS `node10`,
        `ProductCategory` AS `parent10` WHERE 
		  `node10`.`lft` BETWEEN `parent10`.`lft` AND parent10.rght  AND node10.id!=parent10.id AND `node10`.`id`=`phpc`.`productCategoryId`  ) AS		`Categories IT`,
		  
	(SELECT CONCAT(GROUP_CONCAT(`parent20`.`slug` SEPARATOR ' | '),' | ',`node20`.`slug`) FROM `ProductCategory` AS `node20`,
        `ProductCategory` AS `parent20` WHERE 
		  `node20`.`lft` BETWEEN `parent20`.`lft` AND parent20.rght  AND node20.id!=parent20.id AND `node20`.`id`=`phpc`.`productCategoryId`  ) AS		`Categories DE`,
		  
	(SELECT CONCAT(GROUP_CONCAT(`parent30`.`slug` SEPARATOR ' | '),' | ',`node30`.`slug`) FROM `ProductCategory` AS `node30`,
        `ProductCategory` AS `parent30` WHERE 
		  `node30`.`lft` BETWEEN `parent30`.`lft` AND parent30.rght  AND node30.id!=parent30.id AND `node30`.`id`=`phpc`.`productCategoryId`  ) AS		`Categories GB`,
		  
(SELECT CONCAT(GROUP_CONCAT(`parent40`.`slug` SEPARATOR ' | '),' | ',`node40`.`slug`) FROM `ProductCategory` AS `node40`,
        `ProductCategory` AS `parent40` WHERE 
		  `node40`.`lft` BETWEEN `parent40`.`lft` AND parent40.rght  AND node40.id!=parent40.id AND `node40`.`id`=`phpc`.`productCategoryId`  ) AS 		`Categories FR`,
	 (SELECT if(`pp2`.`local` = null, concat('https://iwes.s3.eu-west-1.amazonaws.com/',pb.slug,'/',`pp2`.`name`),concat('https://iwes.pro/product/',`pp2`.`name`))  FROM ProductPhoto pp2  JOIN ProductHasProductPhoto phpp2 ON phpp2.productPhotoId=pp2.id
			WHERE phpp2.productId=p.id AND phpp2.productVariantId=p.productVariantId AND pp2.size='1124' AND   `pp2`.`order`=1 limit 1 ) AS `Cover Image_URL`,
		
			'' as Accessories,
	'' AS `Meta Title IT`,
		'' AS 	`Meta Title DE`,
		'' AS	`Meta Title GB`,
		'' AS	`Meta Title FR`,
		'' AS	`Meta Description IT`,
		'' AS	`Meta Description DE`,
		'' AS	`Meta Description GB`,
		'' AS	`Meta Description FR`,
		'' AS	`Meta Keywords IT`,
		'' AS `Meta Keywords DE`,
		'' AS 	`Meta Keywords GB`,
		'' AS	`Meta Keywords FR`,	  
 0 as `Unit Price`,
 '' as Unity,
  '' as Suppliers,
  '' as `Supplier References`,
 '' as `Supplier Prices`,		
(SELECT if(`pp26`.`local`=null, concat('https://iwes.s3.eu-west-1.amazonaws.com/',pb.slug,'/',`pp26`.`name`),concat('https://iwes.pro/product/',`pp26`.`name`))  FROM ProductPhoto pp26  JOIN ProductHasProductPhoto phpp26 ON phpp26.productPhotoId=pp26.id
			WHERE phpp26.productId=p.id AND phpp26.productVariantId=p.productVariantId AND pp26.size='1124' AND `pp26`.`order`=1 LIMIT 1) AS	`Image Captions`,
			'Catalogo' as `Catalogo`,
			'' as `Related products (Accessories)`,
	'' as Carriers,
	(SELECT group_concat(DISTINCT t1.name SEPARATOR '|')
                   FROM ProductHasTag pht1
                     JOIN TagTranslation t1 ON pht1.tagId = t1.tagId
                   WHERE langId = 1 AND pht1.productId = p.id AND pht1.productVariantId = p.productVariantId)   AS `Tags IT`,
				   (SELECT group_concat(DISTINCT t2.name SEPARATOR '|')
                   FROM ProductHasTag pht2
                     JOIN TagTranslation t2 ON pht2.tagId = t2.tagId
                   WHERE langId = 3 AND pht2.productId = p.id AND pht2.productVariantId = p.productVariantId)   AS `Tags DE`,
				   (SELECT group_concat(DISTINCT t3.name SEPARATOR '|')
                   FROM ProductHasTag pht3
                     JOIN TagTranslation t3 ON pht3.tagId = t3.tagId
                   WHERE langId = 2 AND pht3.productId = p.id AND pht3.productVariantId = p.productVariantId)   AS `Tags GB`,
				   (SELECT group_concat(DISTINCT t4.name SEPARATOR '|')
                   FROM ProductHasTag pht4
                     JOIN TagTranslation t4 ON pht4.tagId = t4.tagId
                   WHERE langId = 2 AND pht4.productId = p.id AND pht4.productVariantId = p.productVariantId)   AS `Tags FR`,
				   '' AS Attachments,
				 '' AS   	`Attachment Names IT`,
			'' AS  `Attachment Names DE`,
			 '' AS `Attachment Names GB`,
			'' AS  `Attachment Names FR`,
			'' AS `Attachment Descriptions IT`,
			'' AS `Attachment Descriptions DE`,
			'' AS `Attachment Descriptions GB`,
			'' AS  `Attachment Descriptions FR`,
			'' AS 	`Pack Items`,	
			CONCAT('https://www.cartechinishop.com/it','/',pb.slug,'/cpf/',p.itemno,'/p/',p.id,'/v/',p.productVariantId) AS `Product URL IT`,
CONCAT('https://www.cartechinishop.com/de','/',pb.slug,'/cpf/',p.itemno,'/p/',p.id,'/v/',p.productVariantId) AS `Product URL DE`,	
CONCAT('https://www.cartechinishop.com/gb','/',pb.slug,'/cpf/',p.itemno,'/p/',p.id,'/v/',p.productVariantId) AS `Product URL GB`,	
CONCAT('https://www.cartechinishop.com/fr','/',pb.slug,'/cpf/',p.itemno,'/p/',p.id,'/v/',p.productVariantId) AS `Product URL FR`,	
		'new' as `Condition`,	
		'' as `Product EAN`,
		'' as `Product UPC`,
		'' as `Product ISBN`,
		'' as `Product MPN`,     
	CONCAT(st.sigla, ' ',st.address,',',st.`number`,' ',`st`.`city`,' ',`st`.`phone`) AS 		`Custom Column 1`,
	'' AS 		`Custom Column 2`,
	'' AS 		`Custom Column 3`

FROM `Product`   `p`
        JOIN `ProductVariant` `pv` ON `p`.`productVariantId` = `pv`.`id`
        JOIN `ProductBrand` `pb` ON `p`.`productBrandId` = `pb`.`id`
        JOIN `ProductStatus` `pss` ON `pss`.`id` = `p`.`productStatusId`
        JOIN `ShopHasProduct` `shp` ON (`p`.`id`, `p`.`productVariantId`) = (`shp`.`productId`, `shp`.`productVariantId` )
        JOIN `Shop` `s` ON `s`.`id` = `shp`.`shopId`
   
        JOIN  `ProductSku` S2 ON  (`p`.`id`, `p`.`productVariantId`) = (`S2`.`productId`, `S2`.`productVariantId`) 
        JOIN `ProductHasProductCategory` `phpc`  ON (`p`.`id`, `p`.`productVariantId`)= (`phpc`.`productId`, `phpc`.`productVariantId`)
        JOIN  `ProductDescriptionTranslation` `pdt` ON `p`.`id` = `pdt`.`productId` AND `p`.`productVariantId` = `pdt`.`productVariantId`
        JOIN `DirtyProduct` `dp` ON `p`.`id` = `dp`.`productId` AND `dp`.`productVariantId` = `p`.`productVariantId`
        JOIN `DirtySku` `ds` ON `dp`.id=ds.dirtyProductId 
        JOIN Storehouse st ON ds.shopId=st.shopId AND ds.storeHouseId=st.id 
       
        left  JOIN ProductColorGroup PCG ON p.productColorGroupId = PCG.id
        JOIN ProductSizeGroup pghps ON p.productSizeGroupId=pghps.id
        JOIN ProductSizeMacroGroup pmg ON pghps.productSizeMacroGroupId=pmg.id
		join ProductSize psz on S2.productSizeId = psz.id
     
WHERE ds.qty > 0  AND p.productSeasonId>40 AND pdt.langId=1 AND s.id=1 AND
  (if((p.id, p.productVariantId) IN (SELECT
                                                              ProductHasProductPhoto.productId,
                                                              ProductHasProductPhoto.productVariantId
                                                            FROM ProductHasProductPhoto), 1, 2))= 1
GROUP BY dp.productId,dp.productVariantId,ds.productSizeId,ds.storeHouseId");

            // --- CREAZIONE FILE CSV ---
            $fp = fopen($tempPath, 'w');

            // Header
            $headers = array_keys($stmt->fetch(PDO::FETCH_ASSOC));
            fputcsv($fp, $headers);

            // Ritorna all'inizio per leggere tutti i dati
            $stmt->execute(); // re-run la query

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($fp, $row);
            }

            fclose($fp);

            \Monkey::app()->applicationLog( "CDumpCartechiniCsvVariazioniJob","success","CSV generato con successo: ",$csvFileName,"");

            // --- INVIO FILE VIA SFTP ---
            $ftpConn = ftp_connect($ftpHost);
            if (!$ftpConn) {
                \Monkey::app()->applicationLog("CDumpCartechiniCsvVariazioniJob",'error'," Connessione SSH fallita",'line 306','');
            }

            if (!ftp_login($ftpConn, $ftpUser, $ftpPass)) {
                \Monkey::app()->applicationLog("CDumpCartechiniCsvVariazioniJob",'error'," Autenticazione SSH fallita","line 312",'');
            }
            ftp_pasv($ftpConn, true);



            if (!ftp_put($ftpConn, $ftpRemotePath, $tempPath, FTP_BINARY)) {
                \Monkey::app()->applicationLog("CDumpCartechiniCsvVariazioniJob",'error'," Impossibile aprire il file remoto per scrittura","line 319",'');
            }


            ftp_close($ftpConn);


            \Monkey::app()->applicationLog("CDumpCartechiniCsvVariazioniJob","success"," File caricato correttamente su ",$ftpHost." nella cartella ".$ftpRemotePath,"");

        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog("CDumpCartechiniCsvVariazioniJob","error", "Errore importazione",$e->getLine().'-'.$e->getMessage(),"");
        }

           }


}
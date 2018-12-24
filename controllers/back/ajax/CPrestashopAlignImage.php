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
 * Class CPrestashopAlignImage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/12/2018
 * @since 1.0
 */
class CPrestashopAlignImage extends AAjaxController
{


    /**
     * @return string
     *
     * @throws \bamboo\core\exceptions\BambooDBALException
     * @throws \bamboo\core\exceptions\BambooException
     */
    public function post()
    {
        /*
        1 mi connetto al database
        2  leggo gli id dei  prodotti esistenti tramite il reference
        3  tiro fuori il recordset dei prodotti
        4  per ogni prodotto ciclo la verifica del'esistenza delle  immagini nelle tabelle di prestashop
           5 se esistono salto il ciclo
           5 se non esistono eseguo la query sulle immagini di pickyshop
           6 Ciclo
             Per ogni immmagine del recordset inserisco una nuova riga sulle tabelle psz6_image, psz6_image_shop,psz6_image_lang del  database di prestashop
           7  tengo l'id dell'immagine inserita attraverso la lettura dell'ultimo id.
con questo faccio il chunk dell'id per ottenere la stringa del percorso di destinazione
eseguo  il download dell'immagine con curl da amazon  e la trasferisco sul server via ftp sulle cartelle delle immagini con la destinazione ottenuta e rinominando il file con l'id dell'immagine
        */


        set_time_limit(0);
        ini_set('memory_limit', '2048M');


        /******* Collegamento al Database ********/
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

        /*leggo il reference dei prodotti*/
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
            $stmtGetImage = $db_con->prepare("SELECT count(id_product) AS existImage FROM psz6_image WHERE id_product=" . $prestashopProductId);
            $stmtGetImage->execute();
            $rows = $stmtGetImage->fetchAll(PDO::FETCH_ASSOC);
            if($rows[0]['existImage']==0) {

                $sql = "SELECT php.id AS productId, php.shopId AS shopId,  phpp.productPhotoId AS photoId, concat('https://iwes.s3.amazonaws.com/',pb.slug,'/',pp.name) AS link , pp.name AS namefile, concat(phpp.productId,'-',phpp.productVariantId) AS product, pp.`order` as position  FROM ProductHasProductPhoto phpp
  JOIN ProductPhoto pp ON phpp.productPhotoId = pp.id
  JOIN MarketplaceHasProductAssociate php ON  phpp.productId =php.productId AND phpp.productVariantId=php.productVariantId
  JOIN  Product p ON phpp.productId = p.id AND phpp.productVariantId = p.productVariantId
  JOIN ProductPublicSku S ON p.id = S.productId AND p.productVariantId = S.productVariantId
  JOIN ProductBrand pb ON p.productBrandId = pb.id
WHERE concat('https://iwes.s3.amazonaws.com/',pb.slug,'/',pp.name)  LIKE '%-1124.JPG%' and php.productId='".$productId."' and php.productVariantId = '".$productVariantId."' order by
  product,position ASC";

                $image_product = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
                $countFirstImage = 0;
                $cover = null;
                foreach ($image_product as $image_products) {
                    $link = $image_products['link'];
                    $position = $image_products['position'];
                    $shopId = $image_products['shopId'];
                    $namefile = $image_products['namefile'];
                    $countFirstImage = $countFirstImage + 1;
                    if ($countFirstImage == 1) {
                        $cover = 1;
                    } else {
                        $cover = "null";
                    }
                    $stmtInsertImage = $db_con->prepare("INSERT INTO psz6_image (id_product, position, cover) VALUES (" .$prestashopProductId . ", " .  $position . "," . $cover . ")");
                    $stmtInsertImage->execute();
                    $stmtLastIdImageProduct = $db_con->prepare("SELECT max(id_image) AS maxIdImageProduct FROM psz6_image");
                    $stmtLastIdImageProduct->execute();
                    $id_lastImage = $stmtLastIdImageProduct->fetch();
                    $q = $id_lastImage[0];
                    $stmtInsertImageShop = $db_con->prepare("INSERT INTO psz6_image_shop (id_product,id_image, id_shop, cover) VALUES (" . $prestashopProductId . "," . $q . "," . $shopId . "," . $cover . ")");
                    $stmtInsertImageShop->execute();
                    $stmtInsertImageLang = $db_con->prepare("INSERT INTO psz6_image_lang (`id_image`,`id_lang`,`legend`) 
                                                   VALUES ('" . $q . "',
                                                           '1',
                                                           '" . $reference . "')");
                    $stmtInsertImageLang->execute();
                    $stmtInsertImageLang = $db_con->prepare("INSERT INTO psz6_image_lang (`id_image`,`id_lang`,`legend`) 
                                                   VALUES ('" . $q . "',
                                                           '2',
                                                           '" . $reference . "')");
                    $stmtInsertImageLang->execute();
                    $stmtInsertImageLang = $db_con->prepare("INSERT INTO psz6_image_lang (`id_image`,`id_lang`,`legend`) 
                                                   VALUES ('" . $q . "',
                                                           '3',
                                                           '" . $reference . "')");
                    $stmtInsertImageLang->execute();
                    //The resource that we want to download.
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
                    $success = file_get_contents("http://iwes.shop/createdirImage.php?token=10210343943202393403&dir=".$q);


                    echo $success;  // "OK" or "FAIL"
                    /*****  trasferimento ftp ******/
                    $ftp_server = "ftp.iwes.shop";
                    $ftp_user_name = "iwesshop";
                    $ftp_user_pass = "XtUWicJUrEXv";
                    $remote_file = "/public_html/img/p/".chunk_split($q, 1, '/');;

                    $ftp_url = "ftp://" . $ftp_user_name . ":" . $ftp_user_pass . "@" . $ftp_server . $remote_file . $q.".jpg";
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
                    $success = file_get_contents("http://iwes.shop/createThumbImage.php?token=10210343943202393403&dir=".$q);

                }

            } else {

                continue;

            }


        }


        $sql = "UPDATE MarketplaceHasProductAssociate SET statusPublished='1' WHERE statusPublished='2'";
        \Monkey::app()->dbAdapter->query($sql, []);
        $res .= "Allineamento Immagini Eseguito";
        return $res;
    }
}

          





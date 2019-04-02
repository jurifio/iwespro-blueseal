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
use PDO;
use prepare;

use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProduct;
use bamboo\core\events\AEventListener;


/**
 * Class CPrestashopProductImageGeneratorJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/12/2018
 * @since 1.0
 */
class CPrestashopProductImageGeneratorJob extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {

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
        $stmtdeletepsz6_image=$db_con->prepare("truncate  psz6_image");
        $stmtdeletepsz6_image->execute();
        $stmtdeletepsz6_image=$db_con->prepare("ALTER TABLE psz6_image AUTO_INCREMENT = 1;");
        $stmtdeletepsz6_image->execute();
        $stmtdeletepsz6_image_lang=$db_con->prepare("truncate  psz6_image_lang");
        $stmtdeletepsz6_image_lang->execute();
        $stmtdeletepsz6_image_shop=$db_con->prepare("truncate  psz6_image_shop");
        $stmtdeletepsz6_image_shop->execute();

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
WHERE concat('https://iwes.s3.amazonaws.com/',pb.slug,'/',pp.name)  LIKE '%-1124.JPG%' and php.productId='".$productId."' and php.productVariantId = '".$productVariantId."' group by link order by
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
                    $remote_file = "/public_html/img/p/".chunk_split($q, 1, '/');

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



            }


        }





        $res = "Allineamento immagini prodotti  finito alle ore " . date('Y-m-d H:i:s');
        $this->report('Align image Product Pickyshop  to Prestashop ', $res, $res);


        return $res;
    }


}
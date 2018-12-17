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

        if (ENV == 'dev') {

            $save_to = '/media/sf_sites/PickyshopNew/tmp/';

        } else {
            $save_to = '/home/pickyshop/public_html/temp-prestashop/';
        }
        if (file_exists($save_to . 'psz6_image_multiple_link.csv')) {
            unlink($save_to . 'psz6_image_multiple_link.csv');
        }
        $image_multiple_link_csv = fopen($save_to . 'psz6_image_multiple_link.csv', 'w');
        fputcsv($image_multiple_link_csv, array('id_image',
            'id_product',
            'position',
            'cover',
            'link'), ';');

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


        /*** immagini   */
        $stmtLastIdImageProduct = $db_con->prepare("SELECT max(id_image) AS maxIdImageProduct FROM psz6_image");
        $stmtLastIdImageProduct->execute();
        $id_lastImage = $stmtLastIdImageProduct->fetch();
        $q = $id_lastImage[0];



        $sql = "SELECT php.id AS productId, php.shopId AS shopId, concat(php.productId,'-',php.productVariantId) AS reference,   concat('https://iwes.s3.amazonaws.com/',pb.slug,'/',pp.name)   AS picture, pp.order AS position, if(pp.order='1',1,0) AS cover
FROM MarketplaceHasProductAssociate php JOIN ProductHasProductPhoto phpp ON php.productId =phpp.productId AND php.productVariantId = phpp.productVariantId
  JOIN  Product p ON php.productId = p.id AND php.productVariantId = p.productVariantId
  JOIN ProductPublicSku S ON p.id = S.productId AND p.productVariantId = S.productVariantId
  JOIN ProductBrand pb ON p.productBrandId = pb.id
  JOIN ProductPhoto pp ON phpp.productPhotoId = pp.id WHERE  LOCATE('-1124.jpg',pp.name)  and  php.id >999 AND p.productStatusId=6 AND p.qty>0  GROUP BY picture  ORDER BY productId,position ASC";

        $image_product = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();


        $a = 0;

        //popolamento aggiornamento tabella PrestashopHasProductImage
        $current_productId = null;

        foreach ($image_product as $value_image_product) {
           /* $stmtProductExist = $db_con->prepare('SELECT id_product FROM psz6_product WHERE id_product=' . $value_image_product['productId']);
            $stmtProductExist->execute();
            $user = $stmtProductExist->fetchAll();

            if ($user !== false) {*/

    $q=$q+1;



              /*  $prestashopHasProductImageInsert = \Monkey::app()->repoFactory->create('PrestashopHasProductImage')->getEmptyEntity();
                $prestashopHasProductImageInsert->idImage = $q;
                $prestashopHasProductImageInsert->prestaId = $value_image_product['productId'];
                $prestashopHasProductImageInsert->position = $value_image_product['position'];
                $prestashopHasProductImageInsert->picture = $value_image_product['picture'];
                $prestashopHasProductImageInsert->cover = $value_image_product['cover'];
                $prestashopHasProductImageInsert->status = '0';
                $prestashopHasProductImageInsert->smartInsert();*/

              $w=$value_image_product['position'];
              $cover=$value_image_product['position'];

                if ($cover != 1) {
                    $cover = null;
                    $stmtInsertImage = $db_con->prepare("INSERT INTO psz6_image (`id_image`,`id_product`,`position`,`cover`) 
                                                   VALUES ('" . $q . "',
                                                           '" . $value_image_product['productId'] . "',
                                                           '" . $w . "', NULL)");
                    $stmtInsertImage->execute();
                } else {
                    $stmtInsertImage = $db_con->prepare("INSERT INTO psz6_image (`id_image`,`id_product`,`position`,`cover`) 
                                                   VALUES ('" . $q . "',
                                                           '" . $value_image_product['productId'] . "',
                                                           '" . $w . "',
                                                           '" . '1' . "')");
                    $stmtInsertImage->execute();
                    /*$stmtInsertImage = $db_con->prepare("INSERT INTO psz6_image (`id_image`,`id_product`,`position`,`cover`)
                                                       VALUES ('" . $q . "',
                                                               '" . $value_image_product['productId'] . "',
                                                               '" . $value_image_product['position'] . "', null)");
                    $stmtInsertImage->execute();*/

                }
                if ($cover != 1) {
                    $cover = null;
                    $stmtInsertImageShop = $db_con->prepare("INSERT INTO psz6_image_shop (`id_product`,`id_image`,`id_shop`,`cover`) 
                                                   VALUES ('" . $value_image_product['productId'] . "',
                                                           '" . $q . "',
                                                           '" . $value_image_product['shopId'] . "',NULL)");
                    $stmtInsertImageShop->execute();
                } else {
                    $stmtInsertImageShop = $db_con->prepare("INSERT INTO psz6_image_shop (`id_product`,`id_image`,`id_shop`,`cover`) 
                                                   VALUES ('" . $value_image_product['productId'] . "',
                                                           '" . $q . "',
                                                           '" . $value_image_product['shopId'] . "',
                                                            '" . $cover . "')");
                    $stmtInsertImageShop->execute();
                }


                $stmtInsertImageLang = $db_con->prepare("INSERT INTO psz6_image_lang (`id_image`,`id_lang`,`legend`) 
                                                   VALUES ('" . $q . "',
                                                           '1',
                                                           '" . $value_image_product['reference'] . "')");
                $stmtInsertImageLang->execute();
                $stmtInsertImageLang = $db_con->prepare("INSERT INTO psz6_image_lang (`id_image`,`id_lang`,`legend`) 
                                                   VALUES ('" . $q . "',
                                                           '2',
                                                           '" . $value_image_product['reference'] . "')");
                $stmtInsertImageLang->execute();
                $stmtInsertImageLang = $db_con->prepare("INSERT INTO psz6_image_lang (`id_image`,`id_lang`,`legend`) 
                                                   VALUES ('" . $q . "',
                                                           '3',
                                                           '" . $value_image_product['reference'] . "')");
                $stmtInsertImageLang->execute();
                $data_image_multiple_link = array(
                    array($q,
                        $value_image_product['productId'],
                        $w,
                        $cover,
                        $value_image_product['picture']));

                foreach ($data_image_multiple_link as $row_image_product_link) {
                    fputcsv($image_multiple_link_csv, $row_image_product_link, ';');
                }


        }


        fclose($image_multiple_link_csv);
        /*****  trasferimento ftp ******/
        $ftp_server = "ftp.iwes.shop";
        $ftp_user_name = "iwesshop";
        $ftp_user_pass = "XtUWicJUrEXv";
        $remote_file = "/public_html/tmp/";

        $ftp_url = "ftp://" . $ftp_user_name . ":" . $ftp_user_pass . "@" . $ftp_server . $remote_file.$image_multiple_link_csv ;
        $errorMsg = 'ftp fail connect';
        $fileToSend = $save_to . "psz6_image_multiple_link.csv";
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

        $url = 'https://iwes.shop/alignMultipleImage.php';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);


        curl_close($ch);




        $res = "Allineamento immagini prodotti eseguita file psz6_image_multiple_link.csv  finita alle ore " . date('Y-m-d H:i:s');
        $this->report('Align image Product Pickyshop  to Prestashop ', $res, $res);


        return $res;
    }


}
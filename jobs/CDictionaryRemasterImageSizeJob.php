<?php

namespace bamboo\blueseal\jobs;

use bamboo\core\base\CFTPClient;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooFTPClientException;
use bamboo\domain\entities\CCartAbandonedEmailSend;
use bamboo\domain\entities\CShop;
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

use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProduct;
use bamboo\core\events\AEventListener;


/**
 * Class CDictionaryRemasterImageSizeJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/02/2019
 * @since 1.0
 */
class CDictionaryRemasterImageSizeJob extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {
        $today = new \DateTime();
        $resultdate = $today->format('Y-m-d');
        /**@var CRepo $repoDictionaryImageSizeRepo
         **/
        $repoDictionaryImageSizeRepo = \Monkey::app()->repoFactory->create('DictionaryImageSize');

//definisco l'ambiente di sviluppo
        if (ENV == 'dev') {
            $pathlocal = '/media/sf_sites/PickyshopNew/temp-remaster/';
            $save_to = '/media/sf_sites/PickyshopNew/temp-remaster/';
            $save_to_dir = '/media/sf_sites/PickyshopNew/temp-remaster';
            $path = 'shootImport/incoming2';
            $remotepathTodo = 'shootImport/newage2/todo2/';
            $remotepathOriginal = '/shootImport/newage2/original2/';
            $remotepathToRename = '/shootImport/newage2/torename2/';

        } else {
            $pathlocal = '/home/pickyshop/public_html/temp-remaster/';
            $save_to = '/home/pickyshop/public_html/temp-remaster/';
            $save_to_dir = '/home/pickyshop/public_html/temp-remaster';
            $path = 'shootImport/incoming';
            $remotepathTodo = 'shootImport/newage2/todo/';
            $remotepathOriginal = '/shootImport/newage2/original/';
            $remotepathToRename = '/shootImport/newage2/torename/';
        }
        $ftp_server = 'fiber.office.iwes.it';
        $ftp_server_port = "21";
        $ftp_user_name = 'jobimages';
        $ftp_user_pass = "cartne01";

// setto la connessione al ftp
        $conn_id = ftp_connect($ftp_server, $ftp_server_port);
// Eseguo il login con  username e password
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

// check connessione e risultato del login
        if ((!$conn_id) || (!$login_result)) {
            echo "Fail</br>";
        } else {
            echo "Success</br>";
            // enabling passive mode
            ftp_pasv($conn_id, true);
            // prendo il contenuto di tutta la directory sul server
            $contents = ftp_nlist($conn_id, $path);
            // output $contents
            foreach ($contents as $item) {
                echo "directory";
                $item = trim($item, '/');
                $item = '/' . $item;
                if ($item === '/') {

                    return true;

                }
                $result = in_array($item, ftp_nlist($conn_id, dirname($item)));
                if ($result == true) {

                    $localDirectory = $save_to_dir . $item;
                    if (!file_exists($localDirectory)) {
                        mkdir($localDirectory);
                    }
                    if (!file_exists($localDirectory . '/' . $resultdate)) {
                        mkdir($localDirectory . '/' . $resultdate);
                    }
                    $remotetoLocalDirectory = $localDirectory . '/' . $resultdate;
                    copy($pathlocal . 'destination1125X1500.jpg', $remotetoLocalDirectory . '/destination1125X1500.jpg');
                    copy($pathlocal . 'destination1200X1500.jpg', $remotetoLocalDirectory . '/destination1200X1500.jpg');
                    copy($pathlocal . 'destination1200X1500.jpg', $remotetoLocalDirectory . '/destination1200X1600.jpg');
                    copy($pathlocal . 'destination2000X2500.jpg', $remotetoLocalDirectory . '/destination2000X2500.jpg');

                    $folder = $item . '/' . $resultdate;
                    $directorypathArr = explode(DIRECTORY_SEPARATOR, $item);
                    $directoryName = end($directorypathArr);
                }
                $result2 = in_array($remotepathOriginal . $directoryName . '_' . $resultdate, ftp_nlist($conn_id, dirname($remotepathOriginal . $directoryName . '_' . $resultdate)));
                if ($result2 == false) {
                    ftp_mkdir($conn_id, $remotepathOriginal . $directoryName . '_' . $resultdate);
                }
                $result3 = in_array($remotepathTodo . $directoryName . '_' . $resultdate, ftp_nlist($conn_id, dirname($remotepathTodo . $directoryName . '_' . $resultdate)));
                if ($result3 == false) {
                    ftp_mkdir($conn_id, $remotepathTodo . $directoryName . '_' . $resultdate);
                }
                $result4 = in_array($remotepathToRename . $directoryName . '_' . $resultdate, ftp_nlist($conn_id, dirname($remotepathToRename . $directoryName . '_' . $resultdate)));
                if ($result4 == false) {
                    ftp_mkdir($conn_id, $remotepathToRename . $directoryName . '_' . $resultdate);
                }


                echo $item . "directory<p>";

                $listimage = ftp_nlist($conn_id, $item);
                foreach ($listimage as $image) {
                    $image = trim($image, '/');
                    $image = '/' . $image;
                    if ($image === '/') {

                        return true;

                    }
                    $result = in_array($image, ftp_nlist($conn_id, dirname($image)));
                    if ($result == true) {
                        // ftp_get($conn_id,$image,$localDirectory.$image,FTP_BINARY);
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, "ftp://" . $ftp_server . $image); #input
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($curl, CURLOPT_USERPWD, $ftp_user_name . ":" . $ftp_user_pass);
                        $imagetopath = $image;
                        $pathArr = explode(DIRECTORY_SEPARATOR, $imagetopath);
                        $filenametoextrat = end($pathArr);
                        $file = fopen($remotetoLocalDirectory . '/' . $filenametoextrat, 'w');
                        curl_setopt($curl, CURLOPT_FILE, $file); #output
                        curl_exec($curl);
                        curl_close($curl);
                        fclose($file);
                        ftp_delete($conn_id, $image);
                    }
                    echo $save_to_dir . $image . "<br>";


                    //operazioni sulle immagini

                    // close the FTP connection

//directory di lavoro
                    // $pathlocal = '/media/sf_sites/PickyshopNew/temp-remaster/';
//file dal lavorare
                    //$imagetoWork = '3025-4478963__saucony-2044-434-001.jpg';
                    $imagetoWork = $remotetoLocalDirectory . '/' . $filenametoextrat;
                    $pathArr1 = explode(DIRECTORY_SEPARATOR, $imagetoWork);
                    $filename = end($pathArr1);

//nome file elaborato

                    //  $destination = $pathlocal . 'destination.jpg';

                    $source = $remotetoLocalDirectory . '/' . $filenametoextrat;

                    /** @var   \bamboo\core\db\pandaorm\repositories\CRepo $repoShop */
                    $repoShop = \Monkey::app()->repoFactory->create('Shop')->findAll();
                    foreach ($repoShop as $stringitems) {
                        $stringitem = $stringitems->name;
                        if ($stringitem == 'pickyshop') {
                            continue;
                        } else {
                            if (strpos($source, $stringitem) !== false) {


                                /** @var integer $shopId */
                                $shopId = $stringitems->id;
                            } else {
                                continue;
                            }
                        }


                    }

                    $repoDictionaryImageSizeRepo = \Monkey::app()->repoFactory->create('DictionaryImageSize')->findOneBy(['shopId' => $shopId]);
                    \Monkey::app()->applicationLog('CdictionaryRemasterImageSizeJob', 'Report', 'ShopId in Remaster Image', "ShopId defined" . $shopId);
                    $emptyZero = $repoDictionaryImageSizeRepo->emptyZero;
                    $renameImage = $repoDictionaryImageSizeRepo->renameAction;
                    if ($renameImage == 1) {
                        if ($emptyZero != 1) {
                            $sectional = substr($imagetoWork, -7, 3) . '.jpg';
                            $imagetoWorkName = substr($filename, 0, -4) . '_' . $sectional;
                        } else {
                            $sectional = '00' . substr($imagetoWork, -5, 1) . '.jpg';
                            $imagetoWorkName = substr($filename, 0, -4) . '_' . $sectional;
                        }
                    } else {

                        $imagetoWorkName =$filename;
                    }

                    $PuntoCopiaX = 0;
                    $PuntoCopiaY = 0;
                    $LarghezzaCopia = $repoDictionaryImageSizeRepo->widthImage;
                    $AltezzaCopia = $repoDictionaryImageSizeRepo->heightImage;
                    $divisoreX = $repoDictionaryImageSizeRepo->divisionByX;
                    $divisoreY = $repoDictionaryImageSizeRepo->divisionByY;
                    $percentualeVariazioneLarghezza = $repoDictionaryImageSizeRepo->widthPercentageVariation;
                    $percentualeVariazioneAltezza = $repoDictionaryImageSizeRepo->heightPercentageVariation;
                    $destination = $remotetoLocalDirectory . '/' . $repoDictionaryImageSizeRepo->destinationfile;
                    $useDivision = $repoDictionaryImageSizeRepo->useDivision;
                    $NomeFile = $source;// carica il file
//$percentualeVariazione = 1.8; //definisce la percentuale di variazione,se superiore 1 ingrandisce
// legge dimensioni dell'immagine
                    $infoImage = getimagesize($NomeFile);
//echo "larghezza = ".$infoImage[0]; // larghezza
//echo "altezza = ".$infoImage[1]; // altezza
// calcola ingrandimento o riduzione
                    $larghezzaNEW = $LarghezzaCopia * $percentualeVariazioneLarghezza;
                    $altezzaNEW = $AltezzaCopia * $percentualeVariazioneAltezza;
//  misure per il centraggio dell'immagine
                    if ($useDivision == 1) {
                        $PuntoDestinazioneX = ($infoImage[0] - $larghezzaNEW) / $divisoreX;
                        $PuntoDestinazioneY = ($infoImage[1] - $altezzaNEW) / $divisoreY;
                    } else {
                        $PuntoDestinazioneX = $repoDictionaryImageSizeRepo->destinationXPoint;
                        $PuntoDestinazioneY = $repoDictionaryImageSizeRepo->destinationYPoint;
                    }


// costruisce immagine per adesso vuota
                    $Immagine_destinazione = imagecreatetruecolor($larghezzaNEW, $altezzaNEW);
                    $Immagine_destinazione = imagecolorallocate($Immagine_destinazione, 255, 255, 255);
// immagine originaria 'smiley.jpg' come destinazione
                    $Immagine_destinazione = imagecreatefromjpeg($destination);
                    $Immagine_Origine = imagecreatefromjpeg($NomeFile);
// a questo punto $Immagine_destinazione e $Immagine_destinazione sono identiche
// esegue la funzione
                    imagecopyresampled($Immagine_destinazione, $Immagine_Origine,
                        $PuntoDestinazioneX, $PuntoDestinazioneY,
                        $PuntoCopiaX, $PuntoCopiaY,
                        $larghezzaNEW, $altezzaNEW,
                        $LarghezzaCopia, $AltezzaCopia
                    );

                    if ($renameImage == 1) {
                        imagejpeg($Immagine_destinazione, $save_to_dir . $item . '/' . $resultdate . '/' . $imagetoWorkName); // salva file
                        ftp_put($conn_id, $remotepathOriginal . $directoryName . '_' . $resultdate . '/' . $filenametoextrat, $source, FTP_BINARY);
                        $filenameremaster = $save_to_dir . $item . '/' . $resultdate . '/' . $imagetoWorkName;
                        $source = imagecreatefromjpeg($filenameremaster);
                        list($width, $height) = getimagesize($filenameremaster);
                        $newwidth = 1125;
                        $newheight = 1500;
                        $destination1 = imagecreatetruecolor($newwidth, $newheight);
                        imagecopyresampled($destination1, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);


                        imagejpeg($destination1, $filenameremaster);
                        ftp_put($conn_id, $remotepathTodo . $directoryName . '_' . $resultdate . '/' . $imagetoWorkName, $filenameremaster, FTP_BINARY);
                        //  ftp_put($conn_id, $remote_file, $file, FTP_ASCII);
                    }else{
                        imagejpeg($Immagine_destinazione, $save_to_dir . $item . '/' . $resultdate . '/' . $imagetoWorkName); // salva file
                        ftp_put($conn_id, $remotepathToRename . $directoryName . '_' . $resultdate . '/' . $filenametoextrat, $source, FTP_BINARY);
                        $filenameremaster = $save_to_dir . $item . '/' . $resultdate . '/' . $imagetoWorkName;
                        $source = imagecreatefromjpeg($filenameremaster);
                        list($width, $height) = getimagesize($filenameremaster);
                        $newwidth = 1125;
                        $newheight = 1500;
                        $destination1 = imagecreatetruecolor($newwidth, $newheight);
                        imagecopyresampled($destination1, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);


                        imagejpeg($destination1, $filenameremaster);
                        ftp_put($conn_id, $remotepathToRename . $directoryName . '_' . $resultdate . '/' . $imagetoWorkName, $filenameremaster, FTP_BINARY);

                    }

                    unlink($remotetoLocalDirectory . '/' . $filenametoextrat);
                    unlink($filenameremaster);

                }
                unlink($remotetoLocalDirectory . '/destination1125X1500.jpg');
                unlink($remotetoLocalDirectory . '/destination1200X1500.jpg');
                unlink($remotetoLocalDirectory . '/destination1200X1600.jpg');
                unlink($remotetoLocalDirectory . '/destination2000X2500.jpg');
                rmdir($remotetoLocalDirectory);
                rmdir($localDirectory);

            }
            //var_dump($contents);
        }
        ftp_close($conn_id);

    }


}
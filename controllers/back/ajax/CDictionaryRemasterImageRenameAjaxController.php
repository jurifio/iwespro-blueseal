<?php


namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;

use bamboo\domain\entities\CBillingJournal;

/**
 * Class CDictionaryRemasterImageSizeAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 09/02/2019
 * @since 1.0
 */

class CDictionaryRemasterImageRenameAjaxController extends AAjaxController
{

    public function POST()
    {
        $today = new \DateTime();
        $resultdate = $today->format('Y-m-d');
        /**@var CRepo $repoDictionaryImageSizeRepo
         **/
        $repoDictionaryImageSizeRepo = \Monkey::app()->repoFactory->create('DictionaryImageSize');

//definisco l'ambiente di sviluppo
        if (ENV == 'dev') {
            $pathlocal = '/media/sf_sites/iwespro/temp-remaster/';
            $save_to = '/media/sf_sites/iwespro/temp-remaster/';
            $save_to_dir = '/media/sf_sites/iwespro/temp-remaster';
            $path = 'shootImport/incoming2/barbagalloimport';
            $remotepathTodo = 'shootImport/incoming2/barbagalloexpoprt';
            $remotepathOriginal = '/shootImport/newage2/original_dev/';
            $remotepathToRename = '/shootImport/newage2/torename_dev/';

        } else {
            $pathlocal = '/home/iwespro/public_html/temp-remaster/';
            $save_to = '/home/iwespro/public_html/temp-remaster/';
            $save_to_dir = '/home/iwespro/public_html/temp-remaster';
            $path = 'shootImport/incoming2/barbagalloimport';
            $remotepathTodo = 'shootImport/incoming2/barbagalloexport/';
            $remotepathOriginal = '/shootImport/newage2/original/';
            $remotepathToRename = '/shootImport/newage2/torename/';
        }
        $ftp_server = '192.168.1.155';
        $ftp_server_port = "21";
        $ftp_user_name = 'shooting';
        $ftp_user_pass = "PBYI34nbf";

// setto la connessione al ftp
        $conn_id = ftp_connect($ftp_server,$ftp_server_port);
// Eseguo il login con  username e password
        if($conn_id !== false) $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
        ftp_pasv($conn_id, true);
// check connessione e risultato del login
        if ((!$conn_id) || (!$login_result)) {
            echo "Fail</br>";
        } else {
            echo "Success</br>";
            // enabling passive mode
            ftp_pasv($conn_id,true);
            // prendo il contenuto di tutta la directory sul server
            $contents = ftp_nlist($conn_id,$path);
            // output $contents
            foreach ($contents as $item) {
                echo "directory";
                $item = trim($item,'/');
                $item = '/' . $item;
                if ($item === '/') {

                    return true;

                }
                $result = in_array($item,ftp_nlist($conn_id,dirname($item)));
                if ($result == true) {

                    $localDirectory = $save_to_dir .'/'. substr(strrchr($item, '/'), 1);
                    if (!file_exists($localDirectory)) {
                        mkdir($localDirectory);
                    }
                    if (!file_exists($localDirectory)) {
                        mkdir($localDirectory);
                    }
                    $remotetoLocalDirectory = $localDirectory;


                    $folder = $item;
                    $directorypathArr = explode(DIRECTORY_SEPARATOR,$item);
                    $directoryName = end($directorypathArr);
                }
                $result2 = in_array($remotepathOriginal . $directoryName,ftp_nlist($conn_id,dirname($remotepathOriginal . $directoryName)));
                if ($result2 == false) {
                    ftp_mkdir($conn_id,$remotepathOriginal . $directoryName);
                }
                $result3 = in_array($remotepathTodo . $directoryName,ftp_nlist($conn_id,dirname($remotepathTodo . $directoryName)));
                if ($result3 == false) {
                    ftp_mkdir($conn_id,$remotepathTodo . $directoryName);
                }
                $result4 = in_array($remotepathToRename . $directoryName,ftp_nlist($conn_id,dirname($remotepathToRename . $directoryName)));
                if ($result4 == false) {
                    ftp_mkdir($conn_id,$remotepathToRename . $directoryName);
                }


                echo $item . "directory<p>";

                $listimage = ftp_nlist($conn_id,$item);
                foreach ($listimage as $image) {
                    $image = trim($image,'/');
                    $image = '/' . $image;
                    if ($image === '/') {

                        return true;

                    }
                    $result = in_array($image,ftp_nlist($conn_id,dirname($image)));
                    if ($result == true) {
                        // ftp_get($conn_id,$image,$localDirectory.$image,FTP_BINARY);
                        $curl = curl_init();
                        curl_setopt($curl,CURLOPT_URL,"ftp://" . $ftp_server . $image); #input
                        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($curl,CURLOPT_USERPWD,$ftp_user_name . ":" . $ftp_user_pass);
                        $imagetopath = $image;
                        $pathArr = explode(DIRECTORY_SEPARATOR,$imagetopath);
                        $filenametoextrat = end($pathArr);
                        $file = fopen($remotetoLocalDirectory . '/' . $filenametoextrat,'w');
                        curl_setopt($curl,CURLOPT_FILE,$file); #output
                        curl_exec($curl);
                        curl_close($curl);
                        fclose($file);
                        ftp_delete($conn_id,$image);
                    }
                    echo $save_to_dir . $image . "<br>";


                    //operazioni sulle immagini

                    // close the FTP connection

//directory di lavoro
                    // $pathlocal = '/media/sf_sites/PickyshopNew/temp-remaster/';
//file dal lavorare
                    //$imagetoWork = '3025-4478963__saucony-2044-434-001.jpg';
                    $imagetoWork = $remotetoLocalDirectory . '/' . $filenametoextrat;
                    $pathArr1 = explode(DIRECTORY_SEPARATOR,$imagetoWork);
                    $filename = end($pathArr1);

//nome file elaborato

                    //  $destination = $pathlocal . 'destination.jpg';

                    $source = $remotetoLocalDirectory . '/' . $filenametoextrat;


                    $firstPosition = strpos($filenametoextrat,'__');

                    $lastPosition = strpos($filenametoextrat,substr(strrchr($filenametoextrat,'__'),0));

                    $firsPart = substr($filenametoextrat,0,$firstPosition);

                    $secondPart = substr($filenametoextrat,$firstPosition,$lastPosition - $firstPosition + 13);

                    $prefinalName = $firsPart . '__Barbagalloshop' . $secondPart . '_00' . substr(strrchr($item, '/'), 1) . '.jpg';


                    ftp_put($conn_id,$remotepathOriginal . $directoryName . '/' . $prefinalName,$source,FTP_BINARY);

                    ftp_put($conn_id,$remotepathToRename . $directoryName . '/' . $prefinalName,$source,FTP_BINARY);


                }
                //var_dump($contents);
            }
            ftp_close($conn_id);
            $res = 'eseguito';
            return $res;
        }
    }
}
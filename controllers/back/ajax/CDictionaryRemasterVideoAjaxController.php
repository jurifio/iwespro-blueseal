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

class CDictionaryRemasterVideoAjaxController extends AAjaxController
{

    public function POST()
    {
        set_time_limit(0);
        ini_set('memory_limit', '2048M');
        $today = new \DateTime();
        $resultdate = $today->format('Y-m-d');
        /**@var CRepo $repoDictionaryImageSizeRepo
         **/
        $repoDictionaryImageSizeRepo = \Monkey::app()->repoFactory->create('DictionaryImageSize');

//definisco l'ambiente di sviluppo
        if (ENV == 'dev') {
            $ftp_server = '192.168.1.155';
            $pathlocal = '/media/sf_sites/iwespro/temp-remaster/';
            $save_to = '/media/sf_sites/iwespro/temp-remaster/';
            $save_to_dir = '/media/sf_sites/iwespro/temp-remaster';
            $path = 'shootImport/workvideo';
            $remotepathTodo = 'shootImport/newage2/topublish_dev/';
            $remotepathOriginal = '/shootImport/newage2/original_dev/';
            $remotepathToRename = '/shootImport/newage2/torename_dev/';

        } else {
            $ftp_server = 'fiber.office.iwes.it';
            $pathlocal = '/home/iwespro/public_html/temp-remaster/';
            $save_to = '/home/iwespro/public_html/temp-remaster/';
            $save_to_dir = '/home/iwespro/public_html/temp-remaster';
            $path = 'shootImport/workvideo';
            $remotepathTodo = 'shootImport/newage2/topublish/';
            $remotepathOriginal = '/shootImport/newage2/original/';
            $remotepathToRename = '/shootImport/newage2/torename/';
        }
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
            if (ENV == 'dev') {
                ftp_pasv($conn_id,true);
            }else{
                ftp_pasv($conn_id,false);
            }
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
                        //   ftp_get($conn_id,$image,$localDirectory.$image,FTP_BINARY);
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, "ftp://" . $ftp_server . $image); #input
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($curl, CURLOPT_USERPWD, $ftp_user_name . ":" . $ftp_user_pass);
                        $imagetopath = $image;
                        $pathArr = explode(DIRECTORY_SEPARATOR, $imagetopath);
                        $filenametoextrat=end($pathArr);
                        $filenametoextrat = str_replace('#','_',$filenametoextrat);
                        $filenametoextrat = str_replace(' ','_', $filenametoextrat);
                        $countPoint=substr_count($filenametoextrat, '.');
                        if($countPoint==2){
                            $filenametoextrat=preg_replace('(.)', '_', $filenametoextrat, 1);

                        }
                           ftp_get($conn_id,$remotetoLocalDirectory . '/' . $filenametoextrat,$image,FTP_BINARY);

                        /*$file = fopen($remotetoLocalDirectory . '/' . $filenametoextrat, 'w');
                        curl_setopt($curl, CURLOPT_FILE, $file); #output
                        curl_exec($curl);
                        curl_close($curl);
                        fclose($file);*/
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





                    $imagetoWorkName =$filename;









                        ftp_put($conn_id, $remotepathOriginal . $directoryName . '_' . $resultdate . '/' . $filenametoextrat, $source, FTP_BINARY);

/*$save_to_dir = '/home/iwespro/public_html/temp-remaster';
                    $ftp_server = 'fiber.office.iwes.it';
                    $pathlocal = '/home/iwespro/public_html/temp-remaster/';
                    $save_to = '/home/iwespro/public_html/temp-remaster/';
                    $save_to_dir = '/home/iwespro/public_html/temp-remaster';
                    $path = 'shootImport/workvideo';
                    $remotepathTodo = 'shootImport/newage2/topublish/';
                    $remotepathOriginal = '/shootImport/newage2/original/';
                    $remotepathToRename = '/shootImport/newage2/torename/';*/



                        $filenameremaster = $save_to_dir . $item . '/' . $resultdate . '/' . 'remaster_'.$imagetoWorkName;



                    $cmd = "ffmpeg -i ".$source." -vcodec copy -an ".$filenameremaster;
//$cmd = "ffmpeg -y -i /media/sf_sites/iwespro/temp/video.mp4 -i /media/sf_sites/iwespro/temp/audio.mp3 -shortest -vcodec libx264 -acodec libfaac -b:v 1000k -refs 6 -coder 1 -sc_threshold 40 -flags +loop -me_range 16 -subq 7 -i_qfactor 0.71 -qcomp 0.6 -qdiff 4 -trellis 1 -b:a 128k -pass 1 -passlogfile /media/sf_sites/iwespro/temp-remaster/shootImport/resize/carte1610ok/2020-10-23/video3.mp4";
                    exec($cmd,$output);
                    sleep(4);
                        ftp_put($conn_id, $remotepathTodo . $directoryName . '_' . $resultdate . '/' . $imagetoWorkName, $filenameremaster, FTP_BINARY);
                        unlink($filenameremaster);
                        unlink($save_to_dir . $item . '/' . $resultdate . '/' . $imagetoWorkName);

                        // unlink($filenameremaster);




                }

                rmdir($remotetoLocalDirectory);
                rmdir($localDirectory);

            }
            //var_dump($contents);
        }
        ftp_close($conn_id);
        $res='eseguito';
        return $res;
    }
}
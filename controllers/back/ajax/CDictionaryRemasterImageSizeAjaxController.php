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

class CDictionaryRemasterImageSizeAjaxController extends AAjaxController
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
            $ftp_server = '192.168.1.155';
            $pathlocal = '/media/sf_sites/iwespro/temp-remaster/';
            $save_to = '/media/sf_sites/iwespro/temp-remaster/';
            $save_to_dir = '/media/sf_sites/iwespro/temp-remaster';
            $path = 'shootImport/resize';
            $remotepathTodo = 'shootImport/newage2/topublish_dev/';
            $remotepathOriginal = '/shootImport/newage2/original_dev/';
            $remotepathToRename = '/shootImport/newage2/torename_dev/';

        } else {
            $ftp_server = 'fiber.office.iwes.it';
            $pathlocal = '/home/iwespro/public_html/temp-remaster/';
            $save_to = '/home/iwespro/public_html/temp-remaster/';
            $save_to_dir = '/home/iwespro/public_html/temp-remaster';
            $path = 'shootImport/resize';
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
                        mkdir($localDirectory,0777, true);
                    }
                    if (!file_exists($localDirectory . '/' . $resultdate)) {
                        mkdir($localDirectory . '/' . $resultdate,0777,true);
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
                        $filenametoextrat = str_replace('.','dariconvertire', $filenametoextrat);
                        $filenametoextrat = str_replace('dariconvertirejpg','.jpg', $filenametoextrat);
                        $filenametoextrat = str_replace('dariconvertire','', $filenametoextrat);
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


                    $renameImage=1;


                    $imagetoWorkName =$filename;








                    if ($renameImage == 1) {
                        ftp_put($conn_id, $remotepathOriginal . $directoryName . '_' . $resultdate . '/' . $filenametoextrat, $source, FTP_BINARY);
                    }else{
                        ftp_put($conn_id, $remotepathToRename . $directoryName . '_' . $resultdate . '/' . $filenametoextrat, $source, FTP_BINARY);
                    }
                    //nuova procudura

                    $img = imagecreatefromjpeg($source);

//find the size of the borders
                    $b_top = 0;
                    $b_btm = 0;
                    $b_lft = 0;
                    $b_rt = 0;

//top
                    for(; $b_top < imagesy($img); ++$b_top) {
                        for($x = 0; $x < imagesx($img); ++$x) {
                            if(imagecolorat($img, $x, $b_top) != 0xFFFFFF) {
                                break 2; //out of the 'top' loop
                            }
                        }
                    }

//bottom
                    for(; $b_btm < imagesy($img); ++$b_btm) {
                        for($x = 0; $x < imagesx($img); ++$x) {
                            if(imagecolorat($img, $x, imagesy($img) - $b_btm-1) != 0xFFFFFF) {
                                break 2; //out of the 'bottom' loop
                            }
                        }
                    }

//left
                    for(; $b_lft < imagesx($img); ++$b_lft) {
                        for($y = 0; $y < imagesy($img); ++$y) {
                            if(imagecolorat($img, $b_lft, $y) != 0xFFFFFF) {
                                break 2; //out of the 'left' loop
                            }
                        }
                    }

//right
                    for(; $b_rt < imagesx($img); ++$b_rt) {
                        for($y = 0; $y < imagesy($img); ++$y) {
                            if(imagecolorat($img, imagesx($img) - $b_rt-1, $y) != 0xFFFFFF) {
                                break 2; //out of the 'right' loop
                            }
                        }
                    }

//copy the contents, excluding the border
                    $newimg = imagecreatetruecolor(
                        imagesx($img)-($b_lft+$b_rt), imagesy($img)-($b_top+$b_btm));

//copy the contents, excluding the border
                    $targetImage = imagecreatetruecolor( imagesx($img)-($b_lft+$b_rt), imagesy($img)-($b_top+$b_btm));
                    $width=imagesx($img)-($b_lft+$b_rt);
                    $height=imagesy($img)-($b_top+$b_btm);
                    imagecopy($targetImage, $img, 0, 0, $b_lft, $b_top, imagesx($targetImage), imagesy($targetImage));
                    if($height>($width+($height/100*21.5))){
                        $type='v';
                    }else{
                        $type='o';
                    }




                    if($type=='v'){
                        $targetImage = imagecreatetruecolor( imagesx($img)-($b_lft+$b_rt), imagesy($img)-($b_top+$b_btm));
                        $width=imagesx($img)-($b_lft+$b_rt);
                        $height=imagesy($img)-($b_top+$b_btm);

                        imagecopy($targetImage, $img, 0, 0, $b_lft, $b_top, imagesx($targetImage), imagesy($targetImage));
                        /*header("Content-Type: image/jpeg");
                        imagejpeg($targetImage);*/
                        $ratioHeight=$height/1300;
                        $newHeight=$height/$ratioHeight;
                        $newWidth=$width/$ratioHeight;
                        $newImage=imagescale($targetImage,$newWidth,$newHeight);


                        $destination1 = imagecreatetruecolor(1125, 1500);
                        /*
                        $dst_x=(1125-imagesx($targetImage))/2;
                        $dst_y=1500-imagesy($targetImage)-173;*/
                        $dst_x=(1125-$newWidth)/2;
                        $dst_y=(1500-$newHeight)-130;

                        $color = imagecolorallocate($targetImage, 255, 255, 255);
// fill entire image
                        imagefill($destination1, 0, 0, $color);
                        imagecopy($destination1, $newImage, $dst_x, $dst_y, 0, 0,$newWidth, $newHeight);



                    }else {

                        $targetImage = imagecreatetruecolor(imagesx($img) - ($b_lft + $b_rt),imagesy($img) - ($b_top + $b_btm));
                        $width = imagesx($img) - ($b_lft + $b_rt);
                        $height = imagesy($img) - ($b_top + $b_btm);

                        imagecopy($targetImage,$img,0,0,$b_lft,$b_top,imagesx($targetImage),imagesy($targetImage));
                        /*header("Content-Type: image/jpeg");
                        imagejpeg($targetImage);*/
                        $ratioHeight = $width / 1025;
                        $newHeight = $height / $ratioHeight;
                        $newWidth = $width / $ratioHeight;
                        $newImage = imagescale($targetImage,$newWidth,$newHeight);


                        $destination1 = imagecreatetruecolor(1125,1500);
                        /*
                        $dst_x=(1125-imagesx($targetImage))/2;
                        $dst_y=1500-imagesy($targetImage)-173;*/
                        $dst_x = (1125 - $newWidth)/2;
                        $dst_y = (1500 - $newHeight) - 130;

                        $color = imagecolorallocate($targetImage,255,255,255);
// fill entire image
                        imagefill($destination1,0,0,$color);
                        imagecopy($destination1,$newImage,$dst_x,$dst_y,0,0,$newWidth,$newHeight);

                    }


                    if ($renameImage == 1) {


                        $filenameremaster = $save_to_dir . $item . '/' . $resultdate . '/' . $imagetoWorkName;



                        imagejpeg($destination1, $filenameremaster);
                        ftp_put($conn_id, $remotepathTodo . $directoryName . '_' . $resultdate . '/' . $imagetoWorkName, $filenameremaster, FTP_BINARY);
                        //  ftp_put($conn_id, $remote_file, $file, FTP_ASCII);
                        unlink($remotetoLocalDirectory . '/' . $filenametoextrat);
                       // unlink($filenameremaster);
                    }else{


                        $filenameremaster = $save_to_dir . $item . '/' . $resultdate . '/' . $imagetoWorkName;



                        imagejpeg($destination1, $filenameremaster);
                        ftp_put($conn_id, $remotepathToRename . $directoryName . '_' . $resultdate . '/' . $imagetoWorkName, $filenameremaster, FTP_BINARY);
                        unlink($remotetoLocalDirectory . '/' . $filenametoextrat);
                        // unlink($filenameremaster);
                    }



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
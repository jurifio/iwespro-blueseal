<?php
/**
 *
 */

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\ARepo;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CWishList;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CSiteApi;
use bamboo\domain\entities\CSite;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CCartLine;
use bamboo\core\base\CSerialNumber;
use bamboo\domain\entities\CCartAbandonedEmailSend;
use bamboo\domain\repositories\CCartAbandonedEmailSendRepo;
use bamboo\utils\time\STimeToolbox;
use bamboo\utils\price\SPriceToolbox;
use bamboo\core\events\AEventListener;

class CFtpShootingFileListAjaxController extends AAjaxController
{

    /**
     *
     */
    public function get()
    {
        if (ENV == 'dev') {
            $ftp_server = 'ftp.iwes.pro';
            $pathlocal = '/media/sf_sites/iwespro/temp-remaster/';
            $save_to = '/home/iwespro/public_html/imgTransfer';
            $save_to_dir = '/media/sf_sites/iwespro/temp-remaster';
            $path = '/public_html/imgTransfer/';
            $remotepathTodo = 'shootImport/newage2/topublish_dev/';
            $remotepathOriginal = '/shootImport/newage2/original_dev/';
            $remotepathToRename = '/shootImport/newage2/torename_dev/';

        } else {
            $ftp_server = 'ftp.iwes.pro';
            $pathlocal = '/home/iwespro/public_html/temp-remaster/';
            $save_to = '/home/iwespro/public_html/temp-remaster/';
            $save_to_dir = '/home/iwespro/public_html/temp-remaster';
            $path = '/public_html/imgTransfer/';
            $remotepathTodo = 'shootImport/newage2/topublish/';
            $remotepathOriginal = '/shootImport/newage2/original/';
            $remotepathToRename = '/shootImport/newage2/torename/';
        }
        $ftp_server_port = "21";
        $ftp_user_name = 'iwespro';
        $ftp_user_pass = "Cartne01!";
        $shops = \Monkey::app()->repoFactory->create('Shop')->findAll();

// setto la connessione al ftp
        $conn_id = ftp_connect($ftp_server,$ftp_server_port);
// Eseguo il login con  username e password
        $login_result = ftp_login($conn_id,$ftp_user_name,$ftp_user_pass);

// check connessione e risultato del login
        if ((!$conn_id) || (!$login_result)) {
            echo "Fail</br>";
        } else {
            echo "Success</br>";
            // enabling passive mode
            ftp_pasv($conn_id,false);
            // prendo il contenuto di tutta la directory sul server
            $contents = ftp_nlist($conn_id,$path);
            // output $contents
            $response = [];
            $response ['data'] = [];
            $i = 1;
            $shopName = '';
            foreach ($contents as $item) {
                $item = trim($item,'/');
                $item = '/' . $item;
                if ($item === '/') {

                    return true;

                }
                $result = in_array($item, ftp_nlist($conn_id, dirname($item)));
                if ($result == true) {
                    $pathArr = explode(DIRECTORY_SEPARATOR,$item);
                    $filenametoextrat = end($pathArr);
                    foreach ($shops as $shop) {
                        $shopToFind = 'shop_' . $shop->id . '_';
                        if (strpos($filenametoextrat,$shopToFind) !== false) {
                            $shopName = $shop->name;
                            break;
                        }
                    }
                }


                $row = [];
                $row["DT_RowId"] = 'row__' . $i;
                $row["id"] = $i;
                $row['file'] = '<i class="fa fa-file-archive-o" aria-hidden="true"></i> '. $filenametoextrat;
                $row['shopName']=$shopName;
                $response['data'][] = $row;
            }
        }

        return json_encode($response);
    }


}
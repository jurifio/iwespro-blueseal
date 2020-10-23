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
            $ftp_server = 'dev.iwes.pro';
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
        $ftp_user_name = 'app@iwes.pro';
        $ftp_user_pass = "Cartne01!";

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
            ftp_pasv($conn_id, false);
            // prendo il contenuto di tutta la directory sul server
            $contents = ftp_nlist($conn_id, $path);
            // output $contents
            foreach ($contents as $item) {
                echo "directory";
                $item = trim($item,'/');
                $item = '/' . $item;
                if ($item === '/') {

                    return true;

                }

            }

        $sql = "SELECT id as id, shopId as shopId   FROM SiteApi ";

        $datatable = new CDataTables($sql, ['id'], $_GET, false);

        $datatable->doAllTheThings('true');

        foreach ($datatable->getResponseSetData() as $key => $row) {

            $datatable->setResponseDataSetRow($key, $row);

        }

        return $datatable->responseOut();
    }


}
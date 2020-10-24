<?php
/**
 *
 */

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\ARepo;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CShootingFtpTemp;
use bamboo\domain\entities\CShop;

class CFtpShootingFileListAjaxController extends AAjaxController
{

    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $shootingFtpTempRepo=\Monkey::app()->repoFactory->create('ShootingFtpTemp');
        $shootingFtpTemp=$shootingFtpTempRepo->findAll();
        foreach ($shootingFtpTemp as $archive){
            $archive->delete();
        }

        if (ENV == 'dev') {
            $ftp_server = '192.168.1.155';

            $path = '/shootImport/resize';

            $ftp_server_port = "21";
            $ftp_user_name = 'jobimages';
            $ftp_user_pass = "cartne01";

        } else {
            $ftp_server = 'ftp.iwes.pro';

            $path = '/';

            $ftp_server_port = "21";
            $ftp_user_name = 'app@iwes.pro';
            $ftp_user_pass = "Cartne01!";
        }

        $shops = \Monkey::app()->repoFactory->create('Shop')->findAll();


        $conn_id = ftp_connect($ftp_server,$ftp_server_port);

        $login_result = ftp_login($conn_id,$ftp_user_name,$ftp_user_pass);



            // enabling passive mode
            if (ENV == 'dev') {
                ftp_pasv($conn_id,true);
            }else{
                ftp_pasv($conn_id,false);
            }
            // prendo il contenuto di tutta la directory sul server
            $buff = ftp_rawlist($conn_id,$path);

// close the connection
            ftp_close($conn_id);
            // output $contents

            $i = 1;
            $shopName = '';
            $shopId='';
            foreach ($buff as $val) {
                $pathArr = explode(' ',$val);
                $filenametoextrat = end($pathArr);
                  $find=0;
                    foreach ($shops as $shop) {
                        $shopToFind = 'shop_' . $shop->id . '_';
                        if (strpos($filenametoextrat,$shopToFind) !== false) {
                            $shopName = $shop->name;
                            $shopId=$shop->id;
                            $find='1';
                            break;
                        }
                    }
        $insertArchiveFileIntoTemp=$shootingFtpTempRepo->getEmptyEntity();
                    $insertArchiveFileIntoTemp->fileName=$filenametoextrat;
                    if($find==1) {
                        $insertArchiveFileIntoTemp->shopId = $shopId;
                        $insertArchiveFileIntoTemp->shopName = $shopName;
                    }else{
                        $insertArchiveFileIntoTemp->shopId = 57;
                        $insertArchiveFileIntoTemp->shopName = 'iwes';
                    }
                    $insertArchiveFileIntoTemp->insert();

                $i++;
            }

        $sql = "SELECT n.id as id, n.fileName as fileName, n.shopId as shopId, `s`.`name` as shopName from ShootingFtpTemp n join Shop s on n.shopId = s.id
        ";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);


        $datatable->doAllTheThings(false);

        $shootingFtpTempRepo = \Monkey::app()->repoFactory->create('ShootingFtpTemp');



        foreach ($datatable->getResponseSetData() as $key=>$row) {


            $shopFtpTemp = $shootingFtpTempRepo->findOneBy(['id' => $row["id"] ]);
            $row['row_id']=$shopFtpTemp->id;
            $row['row_fileName']=$shopFtpTemp->fileName;
            $row['id'] = $shopFtpTemp->id;
            $row['file']='<i class="fa fa-file-archive-o" aria-hidden="true"></i>'.$shopFtpTemp->fileName;
            $row['shopId']=$shopFtpTemp->shopId;
            $row['shopName']=$shopFtpTemp->shopName;


            $datatable->setResponseDataSetRow($key,$row);


        }

        return $datatable->responseOut();
    }


}
<?php

namespace bamboo\controllers\back\ajax;


use bamboo\domain\entities\CShop;
use bamboo\core\exceptions\BambooException;
use bamboo\core\utils\sftp\sftpClient;


/**
 * Class CCreateCPanelAccountAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 25/01/2022
 * @since 1.0
 */
class CCreateSetupFileShopAjaxController extends AAjaxController
{
    public function get()
    {

    }

    public
    function put()
    {

    }
    public
    function post()
    {
        $data = $this->app->router->request()->getRequestData();
        $newDomain=$data['ftpHost'];
        $newUser=$data['ftpUser'];
        $newPassword=$data['ftpPassword'];
        $emailUser=$data['emailUser'];
        if(ENV =='dev') {

            $shop=\Monkey::app()->repoFactory->create('Shop')->findOneBy(['id'=>$idShop]);
            $root='/media/sf_sites/'.$shop->remotePath;
            $prefix='dev';
        }else{

            $shop=\Monkey::app()->repoFactory->create('Shop')->findOneBy(['id'=>$idShop]);
            $root='/home/'.$shop->remotePath.'/public_html';
            $prefix='prod';
        }
        $localFile='setup.php';
        if(ENV=='dev') {
            $myfile = fopen("/media/sf_sites/iwespro/temp/" . $localFile,"w");
            $nameLocalFile="/media/sf_sites/iwespro/temp/" . $localFile;
        }else{
            $myfile = fopen("/home/iwespro/public_html/temp/" . $localFile,"w");
            $nameLocalFile="/home/iwespro/public_html/temp/" . $localFile;
        }
        $json="<?php 
        $data = system('unzip -d ".$root." /home/shared/setup/setupMonkey.zip);   ?>';";
        fwrite($myfile, $json);

        fclose($myfile);


        try {
            $sftp = new sftpClient($shop->ftpHost, 22);
            $sftp->login($shop->ftpUser, $shop->ftpPassword);
            $sftp->uploadFile($nameLocalFile, $root);
            unlink($nameLocalFile);
            return 'File Setup Creato per lanciare l\'installazione  vai su '.$shop->urlSite.'/setup.php';
        }
        catch (\Exception $e) {
            \Monkey::app()->applicationLog('CCreateSetupFileShopAjaxController','Error','sftp Tranfer'. $nameLocalFile,$e->getMessage(),$e->getLine());
            return 'C\'e stato qualche problema consultare l\'amministratore';
        }
    }


}
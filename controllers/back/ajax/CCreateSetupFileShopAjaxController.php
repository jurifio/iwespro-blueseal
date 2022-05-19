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
        $localFile='setup.json';
        if(ENV=='dev') {
            $myfile = fopen("/media/sf_sites/iwespro/temp/" . $localFile,"w");
            $nameLocalFile="/media/sf_sites/iwespro/temp/" . $localFile;
        }else{
            $myfile = fopen("/home/iwespro/public_html/temp/" . $localFile,"w");
            $nameLocalFile="/home/iwespro/public_html/temp/" . $localFile;
        }
        $json=json_encode($shop);
       /* $json="<?php
        $data = system('unzip -d ".$root." /home/shared/setup/setupMonkey.zip);   ?>';";*/
        fwrite($myfile, $json);

        fclose($myfile);
        $indexFile='index.php';
        if(ENV=='dev') {
            $myIndexfile = fopen("/media/sf_sites/iwespro/temp/" . $indexFile,"w");
            $nameIndexFile="/media/sf_sites/iwespro/temp/" . $indexFile;
        }else{
            $myIndexfile = fopen("/home/iwespro/public_html/temp/" . $indexFile,"w");
            $nameIndexFile="/home/iwespro/public_html/temp/" . $indexFile;
        }
        $codesetup="<?php
copy('/home/shared/setup/preinstall.zip','/home/".$shop->remotePath."/public_html/preinstall.zip');
\$zip = new ZipArchive;
if (\$zip->open('preinstall.zip') === TRUE) {
    \$zip->extractTo('/home/".$shop->remotePath."/public_html/');
    \$zip->close();
    echo ' decompressione pacchetto ok';
} else {
    echo 'failed';
}
?>
<a href='/setup/dist/index.php'>installa</a>";
        fwrite($myIndexfile, $codesetup);

        fclose($myIndexfile);


        try {
            $sftp = new sftpClient($shop->ftpHost, 22);
            $sftp->login($shop->ftpUser, $shop->ftpPassword);
            $sftp->uploadFile($nameLocalFile, $root);
            unlink($nameLocalFile);
            $sftp->uploadFile($nameIndexFile, $root);
            unlink($nameIndexFile);
            return 'File Setup Creato per lanciare l\'installazione  vai su '.$shop->urlSite;
        }
        catch (\Exception $e) {
            \Monkey::app()->applicationLog('CCreateSetupFileShopAjaxController','Error','sftp Tranfer'. $nameLocalFile,$e->getMessage(),$e->getLine());
            return 'C\'e stato qualche problema consultare l\'amministratore';
        }
    }


}
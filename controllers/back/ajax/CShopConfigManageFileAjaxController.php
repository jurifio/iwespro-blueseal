<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use DateTime;
use PDO;
use PDOException;
use bamboo\domain\entities\CShopConfigDev;
use bamboo\domain\entities\CShopConfigProd;
use bamboo\core\utils\sftp\sftpClient;

/**
 * Class CSelectOrderLineAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/02/2020
 * @since 1.0
 */
class CShopConfigManageFileAjaxController extends AAjaxController
{
    public function get()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $idShop = $data['idShop'];
        $shopConfigId = $data['shopConfigId'];
        $tableColumns =$data['fileConf'];
        if(ENV=='dev') {
            $sql = 'select ' . $tableColumns . ' as jsonFile from ShopConfigDev where id='.$shopConfigId. '  and shopId='.$idShop;

            }else{
            $sql = 'select ' . $tableColumns . ' as jsonFile from ShopConfigProd where id='.$shopConfigId. '  and shopId='.$idShop;

        }
        $res=\Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
        foreach ($res as $result){
            $datas=$result['jsonFile'];
        }


                $return=$datas;
                return $return;


    }

    /**
     * @return int
     * @throws BambooException
     * @throws \Exception
     */
    public function post()
    {


        $data = $this->app->router->request()->getRequestData();

        $idShop = $data['idShop'];
        $shopConfigId = $data['shopConfigId'];
        $json=$data['json'];
        $tableColumns =$data['fileConf'];
        $root='';
        $remotePath='';
        $shop=\Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $idShop]);
        if(ENV =='dev') {
            $root='/media/sf_sites/'.$shop->remotePath;
            $shopConfig=\Monkey::app()->repoFactory->create('ShopConfigDev')->findOneBy(['id'=>$shopConfigId,'shopId'=>$idShop]);
            $prefix='dev';
            }else{
            $root='/home/'.$shop->remotePath.'/public_html';
            $prefix='prod';
            $shopConfig=\Monkey::app()->repoFactory->create('ShopConfigProd')->findOneBy(['id'=>$shopConfigId,'shopId'=>$idShop]);
        }
        switch (true){
            case $tableColumns=='backComponents':
            $shopConfig->backComponents=$json;
            $remotePath=$root.'/back/conf/'.$prefix.'.components.json';
            $localFile=$prefix.'.components.json';
            break;
            case $tableColumns=='backRoutes':
                $shopConfig->backRoutes=$json;
            $remotePath=$root.'/back/conf/'.$prefix.'.routes.json';
                $localFile=$prefix.'.routes.json';
                break;
            case $tableColumns=='backModule':
                $shopConfig->backModule=$json;
                $remotePath=$root.'/back/conf/'.$prefix.'.module.json';
                $localFile=$prefix.'.module.json';
                break;
            case $tableColumns=='clientAssets':
                $shopConfig->clientAssets=$json;
                $remotePath=$root.'/client/conf/'.$prefix.'.assets.json';
                $localFile=$prefix.'.assets.json';
                break;
            case $tableColumns=='clientComponents':
                $shopConfig->clientComponents=$json;
                $remotePath=$root.'/client/conf/'.$prefix.'.components.json';
                $localFile=$prefix.'.components.json';
                break;
            case $tableColumns=='clientModule':
                $shopConfig->clientModule=$json;
                $remotePath=$root.'/client/conf/'.$prefix.'.module.json';
                $localFile=$prefix.'.module.json';
                break;
            case $tableColumns=='clientRoutes':
            $shopConfig->clientRoutes=$json;
                $remotePath=$root.'/client/conf/'.$prefix.'.routes.json';
                $localFile=$prefix.'.routes.json';
                break;
            case $tableColumns=='coreComponents':
                $shopConfig->coreComponents=$json;
                $remotePath=$root.'/core/conf/'.$prefix.'.components.json';
                $localFile=$prefix.'.components.json';
                break;
            case $tableColumns=='coreModule':
                $shopConfig->coreModule=$json;
                $remotePath=$root.'/core/conf/'.$prefix.'.module.json';
                $localFile=$prefix.'.module.json';
                break;
            case $tableColumns=='ecommerceComponents':
                $shopConfig->ecommerceComponents=$json;
                $remotePath=$root.'/front/ecommerce/conf/'.$prefix.'.components.json';
                $localFile=$prefix.'.components.json';
                break;
            case $tableColumns=='ecommerceModule':
                $shopConfig->ecommerceModule=$json;
                $remotePath=$root.'/front/ecommerce/conf/'.$prefix.'.module.json';
                $localFile=$prefix.'.module.json';
                break;
            case $tableColumns=='ecommerceRoutes':
                $shopConfig->ecommerceRoutes=$json;
                $remotePath=$root.'/front/ecommerce/conf/'.$prefix.'.routes.json';
                $localFile=$prefix.'.routes.json';
                break;
            case $tableColumns=='siteComponents':
                $shopConfig->siteComponents=$json;
                $remotePath=$root.'/front/site/conf/'.$prefix.'.components.json';
                $localFile=$prefix.'.components.json';
                break;
            case $tableColumns=='siteModule':
                $shopConfig->siteModule=$json;
                $remotePath=$root.'/front/site/conf/'.$prefix.'.module.json';
                $localFile=$prefix.'.module.json';
                break;
            case $tableColumns=='siteRoutes':
                $shopConfig->siteRoutes=$json;
                $remotePath=$root.'/front/site/conf/'.$prefix.'.routes.json';
                $localFile=$prefix.'.routes.json';
                break;
        }
        if(ENV=='dev') {
            $myfile = fopen("/media/sf_sites/iwespro/temp/" . $localFile,"w");
            $nameLocalFile="/media/sf_sites/iwespro/temp/" . $localFile;
        }else{
            $myfile = fopen("/home/iwespro/public_html/temp/" . $localFile,"w");
            $nameLocalFile="/home/iwespro/public_html/temp/" . $localFile;
        }
        fwrite($myfile, $json);
        fclose($myfile);
       $shopConfig->update();

        try {
            $sftp = new sftpClient($shop->ftpHost, 22);
            $sftp->login($shop->ftpUser, $shop->ftpPassword);
              $sftp->uploadFile($nameLocalFile, $remotePath);
              unlink($nameLocalFile);
            return 'ok';
        }
        catch (\Exception $e) {
            \Monkey::app()->applicationLog('CShopConfigManageFileAjaxController','Error','sftp Tranfer'. $myfile,$e->getMessage(),$e->getLine());
        return 'ko';
        }



    }
    public function put()
    {



    }



}
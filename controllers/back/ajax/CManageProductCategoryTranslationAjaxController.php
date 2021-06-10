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
use bamboo\core\utils\slugify\CSlugify;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CSelectProductBrandTranslationAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 17/04/2021
 * @since 1.0
 */
class CManageProductCategoryTranslationAjaxController extends AAjaxController
{
    public function get()
    {
        $listTranslation = [];
        $productCategoryId = $this->app->router->request()->getRequestData('productCategoryId');
        $shopId = $this->app->router->request()->getRequestData('shopId');
        $langId = $this->app->router->request()->getRequestData('langId');
        $productCategoryTranslationRepo = \Monkey::app()->repoFactory->create('productCategoryTranslation');
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $langRepo = \Monkey::app()->repoFactory->create('Lang');
        $productCategoryTranslation = $productCategoryTranslationRepo->findOneBy(['productCategoryId' => $productCategoryId,'shopId' => $shopId,'langId' => $langId]);
        if (count($productCategoryTranslation) > 0) {
            $listTranslation[] = ['productCategoryId' => $productCategoryTranslation->productCategoryId,
                'name' => $productCategoryTranslation->name,
                'description' => $productCategoryTranslation->description,
                'longDescription' => $productCategoryTranslation->longDescription,
                'remoteShopId' => $productCategoryTranslation->shopId,
                'langId' => $productCategoryTranslation->langId,
                'responseOk' => '1'
            ];
        } else {
            $listTranslation[] = ['productCategoryId' => '',
                'name' => '',
                'description' => '',
                'longDescription' => '',
                'remoteShopId' => '',
                'langId' => '',
                'responseOk' => '2'
            ];
        }


        return json_encode($listTranslation);
    }

    public function post()
    {
        try {
            $productCategoryId = $this->app->router->request()->getRequestData('productCategoryId');
            $shopId = $this->app->router->request()->getRequestData('shopId');
            $langId = $this->app->router->request()->getRequestData('langId');
            $name = $this->app->router->request()->getRequestData('name');
            $description = $this->app->router->request()->getRequestData('description');
            $longDescription = $this->app->router->request()->getRequestData('longDescription');
            $this->app->router->request()->getRequestData('langId');
            $productCategoryTranslationRepo = \Monkey::app()->repoFactory->create('ProductCategoryTranslation');

                $productCategoryTranslation = $productCategoryTranslationRepo->findOneBy(['productCategoryId' => $productCategoryId,'shopId' => $shopId,'langId' => $langId]);

                $productCategoryTranslation->name = $name;
                $slugy = new CSlugify();
                $slug=$slugy->slugify(trim($name));
                $productCategoryTransalation->slug=$slug;
                $productCategoryTranslation->description = $description;
                $productCategoryTranslation->longDescription = $longDescription;
                $productCategoryTranslation->update();
            $shopRepo = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $shopId]);
            $db_host = $shopRepo->dbHost;
            $db_name = $shopRepo->dbName;
            $db_user = $shopRepo->dbUsername;
            $db_pass = $shopRepo->dbPassword;
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $res = ' connessione ok <br>';
            } catch (PDOException $e) {
                $res = $e->getMessage();
            }
            $stmtUpdateCategoryTranslation=$db_con->prepare("Update ProductCategoryTranslation set 
                      `name`='".$name."',
                      `description`='".$description."',
                      longDescription='".$longDescription."',
                      sluf='".$slug."'
                      where productCategoryId=".$productCategoryId." and langId=".$langId." and shopId=".$shopId);
            $stmtUpdateCategoryTranslation->execute();


            return 'Aggiornamento eseguito con successo';
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CManageProductCategoryTranslationAjaxController','Error','Problem update Translation',$e->getMessage(),$e->getLine());
            return 'errore aggiornamento';
        }
    }

    public function put()
    {

        try {
            $productCategoryId = $this->app->router->request()->getRequestData('productCategoryId');
            $shopId = $this->app->router->request()->getRequestData('shopId');
            $langId = $this->app->router->request()->getRequestData('langId');
            $name = $this->app->router->request()->getRequestData('name');
            $description = $this->app->router->request()->getRequestData('description');
            $longDescription = $this->app->router->request()->getRequestData('longDescription');
            $this->app->router->request()->getRequestData('langId');
            $productCategoryTranslationRepo = \Monkey::app()->repoFactory->create('ProductCategoryTranslation');
            $productCategoryTranslation = $productCategoryTranslationRepo->findOneBy(['productCategoryId' => $productCategoryId,'shopId' => $shopId,'langId' => $langId]);
            if(count($productCategoryTranslation)>0){
                return 'Attenzione esiste una traduzione della categoria per la lingua selezionata';
            }
            $productCategoryTranslationInsert = $productCategoryTranslationRepo->getEmptyEntity();
            $productCategoryTranslationInsert->productCategoryId = $productCategoryId;
            $productCategoryTranslationInsert->langId = $langId;
            $productCategoryTranslationInsert->name = $name;
            $productCategoryTranslationInsert->description = $description;
            $productCategoryTranslationInsert->longDescription = $longDescription;
            $slugy = new CSlugify();
            $slug=$slugy->slugify(trim($name));
            $productCategoryTransalationInsert->slug = $slug;
            $productCategoryTranslationInsert->shopId = $shopId;
            $productCategoryTranslationInsert->insert();
            $shopRepo = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $shopId]);
            $db_host = $shopRepo->dbHost;
            $db_name = $shopRepo->dbName;
            $db_user = $shopRepo->dbUsername;
            $db_pass = $shopRepo->dbPassword;
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $res = ' connessione ok <br>';
            } catch (PDOException $e) {
                $res = $e->getMessage();
            }
            $stmtInsertCategoryTranslation=$db_con->prepare("INSERT INTO  ProductCategoryTranslation (`productCategoryId`,`langId`,`name`,`description`,`longDescription`,`slug`,`shopId`)
             VALUES('".$productCategoryId."','".$langId."','".$name."','".$description."','".$longDescription."','".$slug."','".$shopId."')");

            $stmtInsertCategoryTranslation->execute();



            return 'Inserimento eseguito con successo';
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CManageProductCategoryTranslationAjaxController','Error','Problem insert Translation',$e->getMessage(),$e->getLine());
            return 'errore Inserimento';
        }
    }


}
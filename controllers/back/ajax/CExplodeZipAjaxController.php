<?php
/**
 * Created by PhpStorm.
 * User: jurif
 * Date: 06/04/2018
 * Time: 16:55
 */

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\email\CEmail;
use bamboo\core\facebook\CFacebookCookieSession;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanDetail;
use bamboo\domain\entities\CEditorialPlanSocial;
use \bamboo\utils\time\STimeToolbox;
use Facebook\Facebook;
use DateTime;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdCreative;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\Ad;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Values\AdSetBillingEventValues;
use FacebookAds\Object\Values\AdSetOptimizationGoalValues;
use FacebookAds\Http\RequestInterface;
use FacebookAds\Object\Values\AdCreativeCallToActionTypeValues;
use FacebookAds\Object\Fields\AdCreativeFields;
use FacebookAds\Object\Fields\AdFields;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\Fields\AdsInsightsFields;
use FFMpeg;
use ZipArchive;

class CExplodeZipAjaxController extends AAjaxController
{

    /**
     * @return bool|string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $fileName = $data['fileName'];
        $productBatchRepo = \Monkey::app()->repoFactory->create('ProductBatch');
        $pbhpiRepo = \Monkey::app()->repoFactory->create('ProductBatchHasProductionImage');
        $shopId = $data['shopId'];
        $shop=\Monkey::app()->repoFactory->create('Shop')->findOneBy(['id'=>$shopId]);
        $shopName=$shop->name;
        $id = $data['id'];
        if (ENV == 'dev') {
            $pathFileZip = '/media/sf_sites/iwespro/imgTransfer/';
            $pathFileDest = '/media/sf_sites/iwespro/client/public/media/folderImages';
            $pathBackUp = '/media/sf_sites/iwespro/client/public/productsync/'.$shopName.'/photos/';
        } else {
            $pathFileZip = '/home/iwespro/public_html/imgTransfer/';
            $pathFileDest = '/home/iwespro/public_html/client/public/media/folderImages';
            $pathBackUp = '/home/iwespro/public_html/client/public/productsync/'.$shopName.'/photos/';
        }
        try {
            $zip_obj = new ZipArchive;
            if ($zip_obj->open($pathFileZip . $fileName) === TRUE) {
                $zip_obj->extractTo($pathFileDest);
                $productBatch = $productBatchRepo->getEmptyEntity();
                $productBatch->description = "Post Produzione Archivio Immagini " . $fileName;
                $productBatch->workCategoryId = 34;
                $productBatch->estimatedWorkDays = 2;
                $productBatch->isUnassigned = 1;
                $productBatch->marketplace = 1;
                $productBatch->name = 'Post Produzione Immagini '. $fileName;
                $productBatch->insert();
                $resu = \Monkey::app()->dbAdapter->query('select max(id) as id from ProductBatch ',[])->fetchAll();
                foreach ($resu as $result) {
                    $lastId = $result['id'];
                }
                foreach (glob($pathFileDest . "/" . "*.jpg") as $file) {
                    $path_parts = pathinfo($file);
                    $nameFile = $path_parts['filename'].'.'.$path_parts['extension'];
                    $imageToProduce = $pbhpiRepo->getEmptyEntity();
                    $imageToProduce->imageName = $nameFile;
                    $imageToProduce->productBatchId = $lastId;
                    $imageToProduce->workCategoryStepsId = 123;
                    $imageToProduce->shopId = $shopId;
                    $imageToProduce->insert();
                }
                $res = "i File sono stati estratti con successo";
            } else {
                $res = "Ci sono stati errori durante l'estrazione del file";
            }
            $srcFile=$pathFileZip.$fileName;
            $destFile=$pathBackUp.$fileName;
            if (!copy($srcFile, $destFile)) {
                $res= "Impossibile fare il backup del  $file...\n";
            }
        unlink($srcFile);
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CExplodeZipAjaxController','Error',$e->getMessage(),$e->getLine());
            $res = "Ci sono stati errori durante l'estrazione del file";
        }


        return $res;
    }


}
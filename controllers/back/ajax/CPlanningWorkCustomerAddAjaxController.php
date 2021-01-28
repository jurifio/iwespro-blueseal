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


class CPlanningWorkCustomerAddAjaxController extends AAjaxController
{

    /**
     * @return bool|string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {
        try {
            $data = \Monkey::app()->router->request()->getRequestData();
            $title=$data['title'];
            if ($title == '') {
                return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> titolo non compilato</i>';
            }
            $billRegistryClientId = $data['billRegistryClientId'];
            if ($billRegistryClientId == '') {
                return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Cliente non Selezionato</i>';
            }
            $planningWorkStatusId = $data['planningWorkStatusId'];
            if ($planningWorkStatusId == '') {
                return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Stato  non Selezionato</i>';
            }
            $planningWorkTypeId = $data['planningWorkTypeId'];
            if ($planningWorkTypeId == '') {
                return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Tipo attività  non Selezionato</i>';
            }
            $startDateWork = $data['start'];
            if ($startDateWork == '') {
                return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Data inizio Lavoro non selezionata</i>';
            }
            $endDateWork = $data['end'];
            if ($endDateWork == '') {
                return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Data fine Lavoro non selezionata</i>';
            }
            $percentageStatus = $data['percentageStatus'];
            if($percentageStatus==''){
                $percentageStatus='0';
            }
            $notifyEmail = (isset($data['notifyEmail']) ? $data['notifyEmail'] : '0' );
            $newStartDate=(new \DateTime($startDateWork))->format('Y-m-d H:i:s');
            $newEndDate=(new \DateTime($endDateWork))->format('Y-m-d H:i:s');


            $planningWork = \Monkey::app()->repoFactory->create('PlanningWork')->getEmptyEntity();
            $planningWork->title = $data['title'];
            $planningWork->startDateWork = $newStartDate;
            $planningWork->endDateWork = $newEndDate;
            $planningWork->billRegistryClientId = $data['billRegistryClientId'];
            $bri=\Monkey::app()->repoFactory->create('BillRegistryClient')->findOneBy(['id'=>$data['billRegistryClientId']]);
            $brca=\Monkey::app()->repoFactory->create('BillRegistryClientAccount')->findOneBy(['billRegistryClientId'=>$data['billRegistryClientId']]);
            if($brca){
                $planningWork->shopId=$brca->shopId;
            }
            $companyName = $bri->companyName;
            $emailAdmin=$bri->emailAdmin;
            $planningWork->planningWorkTypeId = $data['planningWorkTypeId'];
            $planningWork->planningWorkStatusId = $data['planningWorkStatusId'];
            $planningWork->title = $data['title'];
            $planningWork->request = $data['request'];
            $planningWork->solution = $data['solution'];
            $planningWork->planningType=1;
            $planningWork->hour = $data['hour'];
            $planningWork->cost = $data['cost'];
            $dateNow = (new \DateTime())->format('Y-m-d H:i:s');
            $planningWork->dateCreate = $dateNow;
            $planningWork->percentageStatus = $percentageStatus;
            $planningWork->insert();
            $res = \Monkey::app()->dbAdapter->query('select max(id) as planningWorkId from PlanningWork ',[])->fetchAll();
            foreach ($res as $result) {
                $planningWorkId = $result['planningWorkId'];
            }
            $newStartMailDate = (new \DateTime($startDateWork))->format('d-m-Y');
            $newEndMailDate = (new \DateTime($endDateWork))->format('d-m-Y');
            $today = (new \DateTime())->format('d-m-Y');
            $todaySent = (new \DateTime())->format('Y-m-d H:i:s');
            $planningWorkType=\Monkey::app()->repoFactory->create('PlanningWorkType')->findOneBy(['id'=>$data['planningWorkTypeId']]);

            switch($data['planningWorkStatusId']){
                case '1':
                    $message=$planningWorkType->textRequest;
                    break;
                case '2':
                    $message=$planningWorkType->textPlanning;
                    break;
                case '3':
                    $message=$planningWorkType->textProgress;
                    break;
                case '4':
                    $message=$planningWorkType->textComplete;
                    break;
                case '5':
                    $message=$planningWorkType->textComplete;
                    break;

            }
            $subject=$planningWorkType->subject;
            $subject=str_replace('{planningWorkId}',$planningWorkId,$subject);
            $message=str_replace('{planningWorkId}',$planningWorkId,$message);
            $message=str_replace('{companyName}',$companyName,$message);
            $message=str_replace('{title}',$data['title'],$message);
            $message=str_replace('{today}',$today,$message);
            $message=str_replace('{startDateWork}',$newStartMailDate,$message);
            $message=str_replace('{endDateWork}',$newEndMailDate,$message);
            $message=str_replace('{percentageStatus}',$data['percentageStatus'],$message);
            $message=str_replace('{request}',$data['request'],$message);
            $message=str_replace('{solution}',$data['solution'],$message);
            $planningWorkEvent=\Monkey::app()->repoFactory->create('PlanningWorkEvent')->getEmptyEntity();
            $planningWorkEvent->planningWorkId = $planningWorkId;
            $planningWorkEvent->planningWorkStatusId = $data['planningWorkStatusId'];
            $planningWorkEvent->planningWorkTypeId=$data['planningWorkTypeId'];
            $planningWorkEvent->mail=$message;
            $planningWorkEvent->solution=$data['solution'];
            $planningWorkEvent->planningType=1;
            $planningWorkEvent->notifyEmail=$notifyEmail;
            $planningWorkEvent->percentageStatus = $percentageStatus;
            if ($notifyEmail == "1") {
                if ($planningWorkStatusId== '1') {
                      $planningWorkEvent->isSent=1;
                      $planningWorkEvent->dateSent=$todaySent;
                    if (ENV != 'dev') {
                        /** @var \bamboo\domain\repositories\CEmailRepo $emailRepo */
                        $emailRepo = \Monkey::app()->repoFactory->create('Email');
                        $emailRepo->newMail('services@iwes.it',[$emailAdmin],[],[],$subject,$message,null,null,null,'mailGun',false,null);
                        $emailRepo = \Monkey::app()->repoFactory->create('Email');
                        $emailRepo->newMail('services@iwes.it',['gianluca@iwes.it'],['juri@iwes.it'],[],$subject,$message,null,null,null,'mailGun',false,null);
                    }
                }
            }
            $planningWorkEvent->insert();

            return 'Richiesta Inviata';
        }catch(\Throwable $e){
            return 'Errore:'.$e;
        }


    }


}
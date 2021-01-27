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


class CPlanningWorkEditAjaxController extends AAjaxController
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

            $planningWorkId = $data['planningWorkStatusId'];
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
            $notifyEmail = (isset($data['notifyEmail'])?$data['notifyEmail']:'0');
            $newStartDate=(new \DateTime($startDateWork))->format('Y-m-d H:i:s');
            $newEndDate=(new \DateTime($endDateWork))->format('Y-m-d H:i:s');


            $planningWork = \Monkey::app()->repoFactory->create('PlanningWork')->findOneBy(['id'=>$planningWorkId]);
            $planningType=$planningWork->planningType;
            $planningWork->title = $data['title'];
            $planningWork->startDateWork = $newStartDate;
            $planningWork->endDateWork = $newEndDate;
            $planningWork->billRegistryClientId = $data['billRegistryClientId'];
            $bri=\Monkey::app()->repoFactory->create('BillRegistryClient')->findOneBy(['id'=>$data['billRegistryClientId']]);
            $companyName = $bri->companyName;
            $emailAdmin=$bri->emailAdmin;
            $planningWork->planningWorkTypeId = $data['planningWorkTypeId'];
            $planningWork->planningWorkStatusId = $data['planningWorkStatusId'];
            $planningWork->title = $data['title'];
            $planningWork->request = $data['request'];
            $planningWork->solution = $data['solution'];
            $planningWork->hour = (isset($data['hour']))?$data['hour']:'0.00';
            $planningWork->cost = (isset($data['cost']))?$data['cost']:'0.00';
            $planningWork->percentageStatus = $data['percentageStatus'];
            if($data['planningWorkStatusId']=='5' || $data['planningWorkStatusId']=='4'){
                $planningWork->close=1;
            }
            $planningWork->update();
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
            str_replace('{planningWorkId}',$planningWorkId,$subject);
            str_replace('{planningWorkId}',$planningWorkId,$message);
            str_replace('{companyName}',$companyName,$message);
            str_replace('{title}',$data['title'],$message);
            str_replace('{today}',$today,$message);
            str_replace('{startDateWork}',$newStartMailDate,$message);
            str_replace('{endDateWork}',$newEndMailDate,$message);
            str_replace('{percentageStatus}',$data['percentageStatus'],$message);
            str_replace('{request}',$data['request'],$message);
            str_replace('{solution}',$data['solution'],$message);
            $planningWorkEvent=\Monkey::app()->repoFactory->create('PlanningWorkEvent')->getEmptyEntity();
            $planningWorkEvent->planningWorkId = $planningWorkId;
            $planningWorkEvent->planningWorkStatusId = $data['planningWorkStatusId'];
            $planningWorkEvent->planningWorkTypeId=$data['planningWorkTypeId'];
            $planningWorkEvent->mail=$message;
            $planningWorkEvent->planningType=2;
            $planningWorkEvent->notifyEmail=$notifyEmail;
            $planingWorkEvent->percentageStatus = $data['percentageStatus'];
            if ($notifyEmail == "1") {
                if ($data['planningWorkStatusId'] == '1') {
                    $planningWorkEvent->isSent=1;
                    $planningWorkEvent->dateSent=$today;
                    if (ENV != 'dev') {
                        /** @var \bamboo\domain\repositories\CEmailRepo $emailRepo */
                        $emailRepo = \Monkey::app()->repoFactory->create('Email');
                        if (!is_array($to)) {
                            $to = [$emailAdmin];
                        }
                        $emailRepo->newMail('Iwes IT Department <services@iwes.it>',$to,[],[],$subject,$message,null,null,null,'mailGun',false,null);
                    }
                }
            }
            $planningWorkEvent->insert();

            return 'inserimento eseguito';
        }catch(\Throwable $e){
            return 'Errore:'.$e;
        }


    }


}
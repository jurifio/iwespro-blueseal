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
            $notifyEmail = $data['notifyEmail'];
            $newStartDate=(new \DateTime($startDateWork))->format('Y-m-d H:i:s');
            $newEndDate=(new \DateTime($endDateWork))->format('Y-m-d H:i:s');


            $planningWork = \Monkey::app()->repoFactory->create('PlanningWork')->findOneBy(['id'=>$planningWorkId]);
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
            $planningWork->hour = (is_null($data['hour']))?'0.00':$data['hour'];
            $planningWork->cost = (is_null($data['cost']))?'0.00':$data['cost'];
            $planningWork->percentageStatus = $data['percentageStatus'];
            $planningWork->update();
            if ($notifyEmail == "1") {

                if (ENV != 'dev') {
                    $subject='Modifica Attivita per il cliente '.$companyName;
                        $message='Richiesta:<br>'.$data['request'];
                    /** @var \bamboo\domain\repositories\CEmailRepo $emailRepo */
                    $emailRepo = \Monkey::app()->repoFactory->create('Email');
                    if (!is_array($to)) {
                        $to = [$emailAdmin];
                    }
                    $emailRepo->newMail('Iwes IT Department <services@iwes.it>',$to,[],[],$subject,$message,null,null,null,'mailGun',false,null);
                }
            }
            return 'inserimento eseguito';
        }catch(\Throwable $e){
            return 'Errore:'.$e;
        }


    }


}
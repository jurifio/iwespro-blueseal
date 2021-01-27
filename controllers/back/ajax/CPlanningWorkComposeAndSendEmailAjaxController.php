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

/**
 * Class CSelectAddressBookAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 31/01/2020
 * @since 1.0
 */
class CPlanningWorkComposeAndSendEmailAjaxController extends AAjaxController
{
    public function get()
    {
        $textEmail = [];
        $planningWorkId = $this->app->router->request()->getRequestData('planningWorkId');
        $planningWorkStatusId = $this->app->router->request()->getRequestData('planningWorkStatusId');
        $planningWorkTypeId = $this->app->router->request()->getRequestData('planningWorkTypeId');
        $billRegistryClientId=$this->app->router->request()->getRequestData('billRegistryClientId');
        $bri=\Monkey::app()->repoFactory->create('BillRegistryClient')->findOneBy(['id'=>$billRegistryClientId]);
        $companyName=$bri->companyName;
        $emailAdmin=$bri->emailAdmin;
        $title = $this->app->router->request()->getRequestData('title');
        $request = $this->app->router->request()->getRequestData('request');
        $startDateWork = $this->app->router->request()->getRequestData('start');
        $endDateWork = $this->app->router->request()->getRequestData('end');
        $solution = $this->app->router->request()->getRequestData('solution');
        $cost = $this->app->router->request()->getRequestData('cost');
        $hour = $this->app->router->request()->getRequestData('hour');
        $percentageStatus = $this->app->router->request()->getRequestData('percentageStatus');
        $notifyEmail = $this->app->router->request()->getRequestData('notifyEmail');
        $planningWorkType=\Monkey::app()->repoFactory->create('PlanningWorkType')->findOneBy(['id'=>$planningWorkTypeId]);
        $subject=str_replace('{planningWorkId}',$planningWorkId,$planningWorkType->subject);
        $planningWorkEvent=\Monkey::app()->repoFactory->create('PlanningWorkEvent')->findOneBy(['planningWorkId'=>$planningWorkId,'planningWorkStatusId'=>$planningWorkStatusId,'planningWorkTypeId'=>$planningWorkTypeId]);
if($planningWorkEvent) {


        $textEmail[] = ['result' => '1',
            'toMail' => $emailAdmin,
            'subject' => $subject,
            'text' => $planningWorkEvent->mail
        ];

}else{
    $textEmail[] = ['result' => '0',
        'toMail' => 'Compila',
        'subject' => 'Compila',
        'text' => 'Compila'
    ];
}

        return json_encode($textEmail);
    }
    public function post(){
        try {
            $planningWorkId = $this->app->router->request()->getRequestData('planningWorkId');
            $planningWorkStatusId = $this->app->router->request()->getRequestData('planningWorkStatusId');
            $planningWorkTypeId = $this->app->router->request()->getRequestData('planningWorkTypeId');
            $billRegistryClientId = $this->app->router->request()->getRequestData('billRegistryClientId');
            $bri = \Monkey::app()->repoFactory->create('BillRegistryClient')->findOneBy(['id' => $billRegistryClientId]);
            $companyName = $bri->companyName;
            $emailAdmin = $bri->emailAdmin;
            $title = $this->app->router->request()->getRequestData('title');
            $request = $this->app->router->request()->getRequestData('request');
            $startDateWork = $this->app->router->request()->getRequestData('start');
            $endDateWork = $this->app->router->request()->getRequestData('end');
            $solution = $this->app->router->request()->getRequestData('solution');
            $cost = $this->app->router->request()->getRequestData('cost');
            $hour = $this->app->router->request()->getRequestData('hour');
            $percentageStatus = $this->app->router->request()->getRequestData('percentageStatus');
            $notifyEmail = $this->app->router->request()->getRequestData('notifyEmail');
            $toMail=$this->app->router->request()->getRequestData('toMail');
            $mail=$this->app->router->request()->getRequestData('mail');
            $subject = $this->app->router->request()->getRequestData('subject');
            $planningWorkType = \Monkey::app()->repoFactory->create('PlanningWorkType')->findOneBy(['id' => $planningWorkTypeId]);

            $planningWorkEvent = \Monkey::app()->repoFactory->create('PlanningWorkEvent')->findOneBy(['planningWorkId' => $planningWorkId,'planningWorkStatusId' => $planningWorkStatusId,'planningWorkTypeId' => $planningWorkTypeId]);
            $planningWorkEvent->mail=$mail;
            if ($notifyEmail == "1") {
                if ($planningWorkStatusId == '1') {
                    $planningWorkEvent->isSent=1;
                    $planningWorkEvent->dateSent=$today;
                    if (ENV != 'dev') {
                        /** @var \bamboo\domain\repositories\CEmailRepo $emailRepo */
                        $emailRepo = \Monkey::app()->repoFactory->create('Email');
                        if (!is_array($to)) {
                            $to = [$toMail];
                        }
                        $emailRepo->newMail('Iwes IT Department <services@iwes.it>',$to,[],[],$subject,$mail,null,null,null,'mailGun',false,null);
                    }
                }
            }
            $planningWorkEvent->update();
            return 'invio eseguito';


        }catch(\Throwable $e){
            return $e;

        }

    }

}
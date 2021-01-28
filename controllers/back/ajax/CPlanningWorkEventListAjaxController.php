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
use bamboo\domain\repositories\CEmailRepo;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CPlanningWorkEventListAjaxController
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
class CPlanningWorkEventListAjaxController extends AAjaxController
{
    public function get()
    {
        $arrayHistory = [];
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
        $planningWorkType = \Monkey::app()->repoFactory->create('PlanningWorkType')->findOneBy(['id' => $planningWorkTypeId]);
        $subject = str_replace('{planningWorkId}',$planningWorkId,$planningWorkType->subject);
        $planningWorkEvent = \Monkey::app()->repoFactory->create('PlanningWorkEvent')->findBy(['planningWorkId' => $planningWorkId]);
        if ($planningWorkEvent) {
            foreach ($planningWorkEvent as $event) {
                $planningWorkStatus = \Monkey::app()->repoFactory->create('PlanningWorkStatus')->findOneBy(['id' => $event->planningWorkStatusId]);
                $arrayHistory[] = ['planningWorkStatusName' => $planningWorkStatus->name,
                    'percentageStatus' => $event->percentageStatus,
                    'dateCreate' => (new \DateTime($event->dateCreate))->format('d-m-Y H:i:s'),
                    'solution' => $event->solution,
                    'isSent' => ($event->isSent == 1) ? 'inviata il ' . ((new \DateTime($event->dateSent))->format('d-m-Y H:i:s')) : 'no'
                ];
            }

        }
        return json_encode($arrayHistory);
    }



}
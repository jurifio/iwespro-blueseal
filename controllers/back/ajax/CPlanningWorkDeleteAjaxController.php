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

/**
 * Class CPlanningWorkDeleteAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/02/2021
 * @since 1.0
 */
class CPlanningWorkDeleteAjaxController extends AAjaxController
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
            $planningWorkId = $data['id'];




            /** @var \bamboo\domain\repositories\CEmailRepo $emailRepo */

            $to = ['gianluca@cartechinishop.com'];
            $tocc = ['jurif@hotmail.com'];
            $emailRepo = \Monkey::app()->repoFactory->create('Email');
            $emailRepo->newMail('Iwes Services Department <services@iwes.it>',$to,[],[],'Cancellazione Attività #' . $planningWorkId,'L\'attivià è stata cancellata',null,null,null,'mailGun',false,null);
            $emailRepo = \Monkey::app()->repoFactory->create('Email');
            $emailRepo->newMail('Iwes Services Department <services@iwes.it>',$tocc,[],[],'Cancellazione Attività #' . $planningWorkId,'L\'attivià è stata cancellata',null,null,null,'mailGun',false,null);
            $planningWorkEvent = \Monkey::app()->repoFactory->create('PlanningWorkEvent')->findBy(['planningWorkId' => $planningWorkId]);
            if ($planningWorkEvent) {
                foreach ($planningWorkEvent as $event) {
                    $event->delete();
                }

            }
            $planningWork = \Monkey::app()->repoFactory->create('PlanningWork')->findOneBy(['id' => $planningWorkId]);
            $planningWork->delete();

            return 'Attivita #' . $planningWorkId . '  cancellata';
        } catch (\Throwable $e) {
            return 'Errore:' . $e;
        }


    }


}
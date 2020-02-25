<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CGainPlanPassiveMovement;

/**
 * Class CGainPlanPassiveMovementDisableManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/12/2019
 * @since 1.0
 */
class CGainPlanMovementDisableManage extends AAjaxController
{
    /**
     * @return string
     */
    public function put()
    {

        $request = \Monkey::app()->router->request();
        $id = $request->getRequestData('id');
        try {
            $gainPlanMovement = \Monkey::app()->repoFactory->create('GainPlan')->findOneBy(['id'=>$id]);
            $gainPlanMovement->isVisible='0';
            $gainPlanMovement->update();
            $res = 'Disabilitazione Eseguita con Successo';

        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CGainPlanMovementDisableManage','Error','disable Gainplan Movement',$e,'');
            $res = 'OOPs Qualcosa è andato storto';
        }
        return $res;
    }
    public function post()
    {

        $request = \Monkey::app()->router->request();
        $id = $request->getRequestData('id');
        try {
            $gainPlanMovement = \Monkey::app()->repoFactory->create('GainPlan')->findOneBy(['id'=>$id]);
            $gainPlanMovement->isVisible=1;
            $gainPlanMovement->update();
            $res = 'Disabilitazione Eseguita con Successo';

        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CGainPlanMovementDisableManage','Error','disable Gainplan Movement',$e,'');
            $res = 'OOPs Qualcosa è andato storto';
        }
        return $res;
    }




}
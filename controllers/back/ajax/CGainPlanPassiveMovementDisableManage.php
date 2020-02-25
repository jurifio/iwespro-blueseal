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
class CGainPlanPassiveMovementDisableManage extends AAjaxController
{
    /**
     * @return string
     */
    public function put()
    {

        $request = \Monkey::app()->router->request();
        $id = $request->getRequestData('id');
        try {
            $gainPlanPassiveMovement = \Monkey::app()->repoFactory->create('GainPlanPassiveMovement')->findOneBy(['id'=>$id]);
            $gainPlanPassiveMovement->isActive=null;
            $gainPlanPassiveMovement->update();
            $res = 'abilitazione Eseguita con Successo';

        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CGainPlanPassiveMovementDisableManage','Error','enable Passive Movement',$e,'');
            $res = 'OOPs Qualcosa è andato storto';
        }
        return $res;
    }
    public function post()
    {

        $request = \Monkey::app()->router->request();
        $id = $request->getRequestData('id');
        try {
            $gainPlanPassiveMovement = \Monkey::app()->repoFactory->create('GainPlanPassiveMovement')->findOneBy(['id'=>$id]);
            $gainPlanPassiveMovement->isActive='0';
            $gainPlanPassiveMovement->update();
            $res = 'disabilitazione Eseguita con Successo';

        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CGainPlanPassiveMovementDisableManage','Error','disable Passive Movement',$e,'');
            $res = 'OOPs Qualcosa è andato storto';
        }
        return $res;
    }



}
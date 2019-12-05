<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CGainPlanPassiveMovement;

/**
 * Class CGainPlanPassiveMovementManage
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
class CGainPlanPassiveMovementManage extends AAjaxController
{
    /**
     * @return string
     */
    public function post()
    {

        $request = \Monkey::app()->router->request();
        $invoice = $request->getRequestData('invoice');
        $dateMovement = $request->getRequestData('dateMovement');
        $dateMovement = strtotime($dateMovement);
        $newdate = date('Y-m-d H:i:s',$dateMovement);
        $gainPlanId = $request->getRequestData('gainPlanId');
        $checked = $request->getRequestData('checked');
        $fornitureName = $request->getRequestData('fornitureName');
        $serviceName = $request->getRequestData('serviceName');
        $shopId = $request->getRequestData('shop');
        $amount = $request->getRequestData('amount');
        $gainPlanPassiveMovement = \Monkey::app()->repoFactory->create('GainPlanPassiveMovement')->getEmptyEntity();
        try {
            $gainPlanPassiveMovement->invoice = $invoice;
            $gainPlanPassiveMovement->amount = $amount;
            $gainPlanPassiveMovement->gainPlanId = $gainPlanId;
            $gainPlanPassiveMovement->shopId = $shopId;
            $gainPlanPassiveMovement->fornitureName = $fornitureName;
            $gainPlanPassiveMovement->serviceName = $serviceName;
            $gainPlanPassiveMovement->dateMovement = $newdate;
            $gainPlanPassiveMovement->isActive = $checked;
            $gainPlanPassiveMovement->insert();
            $res = 'inserimento Eseguito con Successo';

        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CGainPlanPassiveMovementManage','Error','post Passive Movement',$e,'');
            $res = 'OOPs Qualcosa è andato storto';
        }
        return $res;
    }

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put()
    {

        $request = \Monkey::app()->router->request();
        $id = $request->getRequestData('idMovement');
        $invoice = $request->getRequestData('invoice');
        $dateMovement = $request->getRequestData('dateMovement');
        $dateMovement = strtotime($dateMovement);
        $newdate = date('Y-m-d H:i:s',$dateMovement);
        $gainPlanId = $request->getRequestData('gainPlanId');
        $gainPlan=$gainPlanId[0];
        $checked = $request->getRequestData('checked');
        $fornitureName = $request->getRequestData('fornitureName');
        $serviceName = $request->getRequestData('serviceName');
        $shopId = $request->getRequestData('shop');
        $shop=$shopId[0];
        $amount = $request->getRequestData('amount');
        try {
            $gainPlanPassiveMovement = \Monkey::app()->repoFactory->create('GainPlanPassiveMovement')->findOneBy(['id'=>$id]);
            $gainPlanPassiveMovement->invoice = $invoice;
            $gainPlanPassiveMovement->amount = $amount;
            $gainPlanPassiveMovement->gainPlanId = $gainPlan;
            $gainPlanPassiveMovement->shopId = $shop;
            $gainPlanPassiveMovement->fornitureName = $fornitureName;
            $gainPlanPassiveMovement->serviceName = $serviceName;
            $gainPlanPassiveMovement->dateMovement = $newdate;
            $gainPlanPassiveMovement->isActive = $checked;
            $gainPlanPassiveMovement->update();
            $res = 'aggiornamento Eseguito con Successo';

        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CGainPlanPassiveMovementManage','Error','put Passive Movement',$e,'');
            $res = 'OOPs Qualcosa è andato storto';
        }
        return $res;


    }

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function delete()
    {
        $request = \Monkey::app()->router->request();
        $id = $request->getRequestData('id');
        try {
            $gainPlanPassiveMovement = \Monkey::app()->repoFactory->create('GainPlanPassiveMovement')->findOneBy(['id'=>$id]);
            $gainPlanPassiveMovement->delete();
            $res = 'Cancellazione Eseguita con Successo';

        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CGainPlanPassiveMovementManage','Error','delete Passive Movement',$e,'');
            $res = 'OOPs Qualcosa è andato storto';
        }
        return $res;
    }
}
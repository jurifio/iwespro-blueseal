<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CGainPlanPassiveMovementEditController
 * @package bamboo\blueseal\controllers
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
class CGainPlanPassiveMovementEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "gainplanpassivemovement_edit";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/gainplanpassivemovement_edit.php');
        $gppmRepo=\Monkey::app()->repoFactory->create('GainPlanPassiveMovement');
        $id =  \Monkey::app()->router->getMatchedRoute()->getComputedFilter('id');
        $gppm=$gppmRepo->findOneBy(['id'=>$id]);
        $invoice=$gppm->invoice;
        $amount=$gppm->amount;
        $dateMovement=$gppm->dateMovement;
        $fornitureName=$gppm->fornitureName;
        $serviceName=$gppm->serviceName;
        $check=$gppm->isActive;
        $iva=$gppm->amountVat;
        $shops=\Monkey::app()->repoFactory->create('Shop')->findAll();
        $gainPlans=\Monkey::app()->repoFactory->create('GainPlan')->findAll();
        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'invoice'=>$invoice,
            'amount'=>$amount,
            'dateMovement'=>$dateMovement,
            'fornitureName'=>$fornitureName,
            'serviceName'=>$serviceName,
            'iva'=>$iva,
            'check'=>$check,
            'gppm'=>$gppm,
            'shops'=>$shops,
            'gainPlans'=>$gainPlans,
            'sidebar' => $this->sidebar->build()
        ]);

    }
}
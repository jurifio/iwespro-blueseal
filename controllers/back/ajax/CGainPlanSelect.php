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
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CGainPlanSelect extends AAjaxController
{
    public function get()
    {
        $invoiceRepo=\Monkey::app()->repoFactory->create('Invoice');
        $gainPlans=\Monkey::app()->repoFactory->create('GainPlan')->findAll();

        $collects = [];


            foreach ($gainPlans as $gainPlan) {
                if($gainPlan->typeMovement==1) {
                    $id = $gainPlan->id;
                    $invoice = $invoiceRepo->findOneBy(['id' => $gainPlan->invoiceId]);
                    if($invoice!=null) {
                        $invoices = $invoice->invoiceType . '-' . $invoice->invoiceNumber . ' ' . $invoice->invoiceDate;
                    }else{
                        $invoices='';
                    }
                    $customerName = $gainPlan->customerName;
                }else{
                    $id=$gainPlan->id;
                    $invoices=$gainPlan->invoiceExternal;
                    $customerName=$gainPlan->customerName;
                }
               array_push($collects,['id'=>$id,'invoices'=>$invoices,'customerName'=>$customerName]);

            }

        return json_encode($collects);
    }

}
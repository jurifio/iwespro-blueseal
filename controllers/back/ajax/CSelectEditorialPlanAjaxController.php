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
 * Class CSelectCampaignAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 07/01/2020
 * @since 1.0
 */
class CSelectEditorialPlanAjaxController extends AAjaxController
{
    public function get()
    {
        $collectEditorialPlan = [];
        $editorialPlans=\Monkey::app()->repoFactory->create('EditorialPlan')->findAll();
        $contractsRepo=\Monkey::app()->repoFactory->create('Contracts');
        $foisonRepo=\Monkey::app()->repoFactory->create('Foison');
            foreach ($editorialPlans as $editorialPlan) {
                $contracts=$contractsRepo->findOneBy(['id'=>$editorialPlan->contractId]);
                if(count($contracts)>0){
                    $foison=$foisonRepo->findOneBy(['id'=>$contracts->foisonId]);
                    $foisonId=$foison->id;
                    $foisonName=$foison->name.' '.$foison->surname;
                    $contractId=$editorialPlan->contractId;
                }else{
                    $foisonId='';
                    $contractId='';
                    $foisonName='';
                }


                $collectEditorialPlan[] = ['id' => $editorialPlan->id,'name' => $editorialPlan->name,'foisonId'=>$foisonId,'contractId'=>$contractId,'foisonName'=>$foisonName];
            }

        return json_encode($collectEditorialPlan);
    }
}
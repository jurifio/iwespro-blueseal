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
class CSelectEditorialPlanFilteredAjaxController extends AAjaxController
{
    public function get()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $editorialPlanIdSelected = $data['editorialPlanIdSelected'];
        $editorialPlans=\Monkey::app()->repoFactory->create('EditorialPlan')->findOneBy(['id'=>$editorialPlanIdSelected]);
        $contractsRepo=\Monkey::app()->repoFactory->create('Contracts');
        $foisonRepo=\Monkey::app()->repoFactory->create('Foison');
                $contracts=$contractsRepo->findOneBy(['id'=>$editorialPlans->contractId]);
                if(count($contracts)>0){
                    $foison=$foisonRepo->findOneBy(['id'=>$contracts->foisonId]);
                    $foisonId=$foison->id;
                }else{
                    $foisonId='';
                }




        return  $foisonId;
    }
}
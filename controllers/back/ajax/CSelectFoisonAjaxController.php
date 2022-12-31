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
class CSelectFoisonAjaxController extends AAjaxController
{
    public function get()
    {
        $data = $this->app->router->request()->getRequestData();

        $editorialPlanArgumentId = $data['editorialPlanArgumentId'];
        $collectFoison = [];
        $foisons=\Monkey::app()->repoFactory->create('Foison')->findAll();
        $foisonHasInterestRepo=\Monkey::app()->repoFactory->create('FoisonHasInterest');
        $editorialPlanArgument=\Monkey::app()->repoFactory->create('EditorialPlanArgument')->findOneBy(['id'=>$editorialPlanArgumentId]);
        $workCategoryId=$editorialPlanArgument->workCategoryId;
            foreach ($foisons as $foison) {
                $foisonHasInterest=$foisonHasInterestRepo->findOneBy(['foisonId'=>$foison->id,'workCategoryId'=>$workCategoryId]);
                if ($foisonHasInterest!=null) {
                    if($foisonHasInterest->foisonStatusId<4) {
                        $collectFoison[] = ['id' => $foison->id,'name' => $foison->name . ' ' . $foison->surname,'rank' => $foison->rank];
                    }
                }
            }
            if(count($collectFoison)==0){
                foreach ($foisons as $foison) {
                            $collectFoison[] = ['id' => $foison->id,'name' => $foison->name . ' ' . $foison->surname,'rank' => $foison->rank];
                        }
                    }


        return json_encode($collectFoison);
    }
}
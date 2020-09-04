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
class CSelectContractAjaxController extends AAjaxController
{
    public function get()
    {
        $collectContract = [];
        $contracts=\Monkey::app()->repoFactory->create('Contracts')->findAll();
        $foisonRepo=\Monkey::app()->repoFactory->create('Foison');
            foreach ($contracts as $contract) {
$foison=$foisonRepo->findOneBy(['id'=>$contract->foisonId]);
$operator=$foison->name.' '.$foison->surname;
                $collectContract[] = ['id' => $contract->id,'name' => $contract->name,'operator' => $operator];
            }

        return json_encode($collectContract);
    }
}
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
 * Class CGainPlanPassiveMovementAssocAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/02/2020
 * @since 1.0
 */
class CGainPlanPassiveMovementAssocAjaxController extends AAjaxController
{
    public function put()
    {

        $gainPlanId = $this -> app -> router -> request() -> getRequestData('id');
        $id = $this -> app -> router -> request() -> getRequestData('movPassId');
        $gppm=\Monkey::app()->repoFactory->create('GainPlanPassiveMovement')->findOneBY(['id'=>$id]);
        $gppm->gainPlanId=$gainPlanId;
        $gppm->update();

        return 'Associazione Eseguita';
    }
}
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
class CSelectCampaignFilterAjaxController extends AAjaxController
{
    public function get()
    {
        $collectCampaigns = [];
        $remoteShopId = \Monkey::app()->router->request()->getRequestData('remoteShopId');
        $campaigns=\Monkey::app()->repoFactory->create('Campaign')->findBy(['remoteShopId'=>$remoteShopId]);
            foreach ($campaigns as $campaign) {
                if($campaign->isActive==1){
                    $isActive='Attiva';
                }else{
                    $isActive='Non Attiva';
                }
                array_push($collectCampaigns,['id'=>$campaign->id,'campaignName'=>$campaign->name,'shop'=>$campaign->remoteShopId,'isActive'=>$isActive]);
            }

        return json_encode($collectCampaigns);
    }
}
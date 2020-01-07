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
class CSelectCampaignAjaxController extends AAjaxController
{
    public function get()
    {
        $collectCampaigns = [];
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
        $campaigns=\Monkey::app()->repoFactory->create('Campaign')->findBy(['isActive'=>1]);
            foreach ($campaigns as $campaign) {
                $shop=$shopRepo->findOneBy(['id'=>$campaign->remoteShopId]);
                array_push($collectCampaigns,['id'=>$campaign->id,'name'=>$campaign->name,'shop'=>$shop->name]);
            }

        return json_encode($collectCampaigns);
    }
}
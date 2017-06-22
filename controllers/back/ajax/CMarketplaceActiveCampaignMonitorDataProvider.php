<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CCampaign;
use bamboo\utils\time\STimeToolbox;


/**
 * Class CMarketplaceActiveCampaignMonitorDataProvider
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CMarketplaceActiveCampaignMonitorDataProvider extends AAjaxController
{
    public function get()
    {
        $period = $this->app->router->request()->getRequestData('period');

        switch ($period) {
            case 'today': {
                $time = strtotime('midnight');
                break;
            }
            default:
                $time = strtotime($period);
        }

        $sql = "SELECT DISTINCT cv.campaignId
                FROM CampaignVisit cv 
                WHERE cv.timestamp > ?
                GROUP BY cv.id";

        $res = $this->app->dbAdapter->query($sql, [STimeToolbox::DbFormattedDateTime(\DateTime::createFromFormat('U', $time))], true)->fetchAll();

        $res2 = [];
        foreach ($res as $campaignId) {
            /** @var CCampaign $campaign */
            $campaign = $this->app->repoFactory->create('Campaign')->findOneByStringId($campaignId['campaignId']);
            $res2[] = $campaign;
        }

        return json_encode($res2);
    }
}
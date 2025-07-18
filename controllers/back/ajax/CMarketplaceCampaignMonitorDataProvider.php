<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CCampaign;
use bamboo\utils\time\STimeToolbox;


/**
 * Class AMarketplaceAccountAjaxController
 * @package bamboo\blueseal\controllers\ajax
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
class CMarketplaceCampaignMonitorDataProvider extends AAjaxController
{
    public function get()
    {
        $campaignId = $this->app->router->request()->getRequestData('campaignId');
        $period = $this->app->router->request()->getRequestData('period');

        switch ($period) {
            case 'today': {
                $time = strtotime('midnight');
                $elapsed = $this->normalizeTo100($time,strtotime('+1 day midnight'),time());
                break;
            }
            default:
                $time = strtotime($period);
                $elapsed = 0;
        }

        $sql = "SELECT
                  round(round(sum(cv.cost),2))             AS cost,
                  ifnull(count(cv.id), 0)         AS visits,
                  ifnull(round(sum(distinct o.netTotal),2),0)       AS ordersValue,
                  ifnull(count(DISTINCT o.id), 0) AS orders
                FROM Campaign c
                  JOIN CampaignVisit cv ON cv.campaignId = c.id
                  LEFT JOIN (
                      CampaignVisitHasOrder cvho
                      JOIN `Order` o ON o.id = cvho.orderId
                    ) ON cvho.campaignId = c.id AND o.orderDate > cv.timestamp
                WHERE c.id = ?
                  AND cv.timestamp > ?
                GROUP BY c.id";

        $res = $this->app->dbAdapter->query($sql, [$campaignId, STimeToolbox::DbFormattedDateTime(\DateTime::createFromFormat('U', $time))], true)->fetchAll();

        /** @var CCampaign $campaign */
        $campaign = \Monkey::app()->repoFactory->create('Campaign')->findOneByStringId($campaignId);

        if (empty($res)) $res = ['cost' => 0, 'visits' => 0 ,'ordersValue'=> 0, 'orders'=> 0];
        else $res = $res[0];
        $res['elapsed'] = $elapsed;
        $res['campaignName'] = $campaign->marketplaceAccount ? $campaign->marketplaceAccount->name : $campaign->name;
        return json_encode($res);
    }

    /**
     * @param $min
     * @param $max
     * @param $value
     * @return float|int
     */
    protected function normalizeTo100($min,$max,$value) {
        return ($value-$min)/($max-$min) * 100;
    }
}
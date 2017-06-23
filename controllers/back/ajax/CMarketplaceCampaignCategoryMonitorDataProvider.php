<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CCampaign;
use bamboo\domain\entities\CProductCategory;
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
class CMarketplaceCampaignCategoryMonitorDataProvider extends AAjaxController
{
    public function get()
    {
        $period = $this->app->router->request()->getRequestData('period');
        $campaignId = $this->app->router->request()->getRequestData('campaignId');
        /** @var CCampaign $campaign */
        $campaign = $this->app->repoFactory->create('Campaign')->findOneByStringId($campaignId);

        switch ($period) {
            case 'today': {
                $time = strtotime('midnight');
                break;
            }
            default:
                $time = strtotime($period);
        }

        $sql = "SELECT
                  c.id,
                  c.name                      AS nome,
                  parent.slug,
                  parent.id                   AS productCategoryId,
                  (SELECT (COUNT(DISTINCT parent2.id) - 1) AS depth
                   FROM ProductCategory AS node2,
                     ProductCategory AS parent2
                   WHERE node2.lft BETWEEN parent2.lft AND parent2.rght
                         AND node2.id = parent.id
                   GROUP BY node2.id
                   ORDER BY node2.lft)        AS depth,
                  sum(cv.cost)                AS cost,
                  count(DISTINCT cv.id)       AS visits,
                  ifnull(sum(ol.netPrice), 0) AS exactOrdersValue,
                  count(DISTINCT ol.orderId)  AS exactOrders,
                  ifnull(sum(o2.netTotal), 0) AS ordersValue,
                  count(DISTINCT o2.id)       AS orders
                FROM Campaign c
                  JOIN CampaignVisit cv ON c.id = cv.campaignId
                  JOIN CampaignVisitHasProduct cvhp ON cv.id = cvhp.campaignVisitId AND cv.campaignId = cvhp.campaignId
                  JOIN ProductHasProductCategory phpc
                    ON (cvhp.productId, cvhp.productVariantId) = (phpc.productId, phpc.productVariantId)
                  JOIN (
                      ProductCategory node
                      JOIN ProductCategory parent ON parent.id != 1
                                                     AND node.lft BETWEEN parent.lft AND parent.rght)
                    ON phpc.productCategoryId = node.id
                  LEFT JOIN (
                      CampaignVisitHasOrder cvho2
                      JOIN `Order` o2 ON cvho2.orderId = o2.id
                      JOIN OrderLine ol2 ON o2.id = ol2.orderId
                      JOIN ProductHasProductCategory phpc2 ON (ol2.productId, ol2.productVariantId) = (phpc2.productId, phpc2.productVariantId)
                    ) ON cvho2.campaignId = cv.campaignId AND date(cv.timestamp) = date(o2.orderDate) AND phpc2.productCategoryId = node.id
                  LEFT JOIN (
                      CampaignVisitHasOrder cvho
                      JOIN OrderLine ol ON cvho.orderId = ol.orderId
                    ) ON (cvho.campaignVisitId, cvho.campaignId) = (cv.id, cv.campaignId) AND
                         (ol.productId, ol.productVariantId) = (phpc.productId, phpc.productVariantId)
                WHERE
                  cv.campaignId = ? AND
                  cv.timestamp > ?
                GROUP BY c.id, parent.id
                HAVING depth < 4
                ORDER BY cost DESC, node.lft ASC
                LIMIT 50";

        $res = $this->app->dbAdapter->query($sql, [$campaignId,STimeToolbox::DbFormattedDateTime(\DateTime::createFromFormat('U', $time))], true)->fetchAll();

        $res2 = [];
        $categoryRepo = $this->app->repoFactory->create('ProductCategory');
        foreach ($res as $data) {
            /** @var CProductCategory $productCategory */
            $productCategory = $categoryRepo->findOne([$data['productCategoryId']]);
            $data['categoryPath'] = $productCategory->getLocalizedPath();

            $res2[] = $data;
        }

        return json_encode($res2);
    }
}
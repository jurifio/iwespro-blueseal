<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\traits\TMySQLTimestamp;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CMarketplaceAccount;
use bamboo\domain\entities\CMarketplace;
use bamboo\domain\entities\CCampaign;
use bamboo\domain\repositories\CCampaignRepo;
use bamboo\domain\repositories\CCampaignVisitRepo;
use bamboo\domain\entities\CCampaignVisitHasProduct;
use bamboo\domain\entities\CCampaignVisitHasOrder;


use bamboo\domain\repositories\COrderRepo;
use bamboo\domain\repositories\CShipmentRepo;

/**
 * Class CSelectAggregatorStatisticRenderAjaxController
 * @package bamboo\controllers\back\ajax
 */
class CSelectAggregatorStatisticRenderAjaxController extends AAjaxController
{
    public function get()
    {
        $data  = $this->app->router->request()->getRequestData();
        $marketplaceAccountId = $data["marketplaceAccountId"];
        $resultjson=[];
        /** @var CMarketplaceAccount $markeplaceAccount */
        $marketplaceAccount=\Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneBy(['id'=>$marketplaceAccountId]);
        $marketplaceId=$marketplaceAccount->marketplaceId;
        $sql="SELECT
             (select COUNT(*) id from CampaignVisit cv1 join Campaign c1 ON c1.id=cv1.campaignId WHERE cv1.timestamp between '2019-12-01' and '2020-12-31' ) as totCampaignVisit,
         (select sum(cost) from CampaignVisit cv2 join Campaign c2 on c2.id=cv2.campaignId WHERE cv2.timestamp between '2019-12-01' and '2020-12-31' ) as totCampaignCost,
         (select sum(costCustomer) from CampaignVisit cv3 join Campaign c3 ON c3.id=cv3.campaignId where cv3.timestamp between '2019-12-01' and '2020-12-31' ) as totCampaignCostCustomer,			 	          
       
          m.id                                            AS marketplaceId,
          ma.id                                           AS marketplaceAccountId,
          m.name                                          AS marketplace,
          ma.name                                         AS marketplaceAccount,
          c.id                                            AS campaignId,
          c.name                                          AS campaign,
          m.type                                          AS marketplaceType,
         (COUNT(ol.id)/(select COUNT(*) id from CampaignVisit cv1 join Campaign c1 ON c1.id=cv1.campaignId WHERE cv1.timestamp between '2019-12-01' and '2020-12-31' )*100) AS conversionRateQty,
			 (sum(ifnull(o.netTotal, 0))/(select sum(cost) from CampaignVisit cv2 join Campaign c2 on c2.id=cv2.campaignId WHERE cv2.timestamp between '2019-12-01' and '2020-12-31' )) AS conversionRateOrderTot,
			(sum(ifnull(o.netTotal, 0))/(select sum(costCustomer) from CampaignVisit cv2 join Campaign c2 on c2.id=cv2.campaignId WHERE cv2.timestamp between '2019-12-01' and '2020-12-31' )) AS conversionRateCustomerOrderTot,			 	          
        DATE_FORMAT(cv.timestamp, '%d %m %Y')                                      as timestamp,  
          (SELECT count(DISTINCT mahp.productId, mahp.productVariantId)
           FROM MarketplaceAccountHasProduct mahp
           WHERE ma.id = mahp.marketplaceAccountId AND
                 ma.marketplaceId = mahp.marketplaceId AND mahp.isDeleted = 0 AND
                 mahp.isToWork = 0 AND mahp.hasError = 0) AS productCount,
          count(distinct cv.id)                                    AS visits,
          count(cv.id) AS totalVisits,
          round(sum(cv.cost), 2)                          AS cost,
          ROUND(SUM(cv.costCustomer),2) AS costCustomer,
          count(distinct o.id)                                     AS orders,
          sum(ifnull(o.netTotal, 0))                      AS orderTotal,
          group_concat(DISTINCT o.id)                     AS ordersIds
        FROM Marketplace m
          JOIN MarketplaceAccount ma ON m.id = ma.marketplaceId
          LEFT JOIN Campaign c ON c.marketplaceId = ma.marketplaceId AND c.marketplaceAccountId = ma.id
          LEFT JOIN CampaignVisit cv ON c.id = cv.campaignId
          LEFT JOIN (CampaignVisitHasOrder cvho
            JOIN `Order` o ON o.id = cvho.orderId) ON cv.campaignId = cvho.campaignId AND cv.id = cvho.campaignVisitId
            LEFT JOIN OrderLine ol ON o.id=ol.orderId 
        WHERE  (
          isnull(c.id) OR (
            cv.timestamp BETWEEN IFNULL('2019-12-01', cv.timestamp) AND ifnull('2019-12-31', cv.timestamp) OR
            o.orderDate BETWEEN ifnull('2019-12-01', o.orderDate) AND ifnull('2019-12-31', o.orderDate))) AND  m.id=".$marketplaceId." AND ma.id=".$marketplaceAccountId." 
        GROUP BY ma.id, ma.marketplaceId, cv.timestamp";
        $res = $this -> app -> dbAdapter -> query($sql, []) -> fetchAll();
        foreach($res as $result){
            array_push($resultjson,
                                        ['productCount'=>$result['productCount'],
                                        'visits'=>$result['visits'],
                                        'totalVisits'=>$result['totalVisits'],
                                         'cost'=>$result['cost'],
                                         'orderTotal'=>$result['orderTotal'],
                                         'costCustomer'=>$result['costCustomer'],
                                         'date'=>$result['timestamp'],
                                            'totCampaignVisit'=>$result['totCampaignVisit'],
                                            'totCampaignCost'=>$result['totCampaignCost'],
                                            'totCampaignCostCustomer'=>$result['totCampaignCostCustomer'],
                                            'conversionRateQty'=>$result['conversionRateQty'],
                                            'conversionRateOrderTot'=>$result['conversionRateOrderTot'],
                                            'conversionRateCustomerOrderTot'=>$result['conversionRateCustomerOrderTot']]);
    }

        return json_encode($resultjson);
    }
}
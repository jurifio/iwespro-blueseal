<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProduct;

/**
 * Class CCampaignListAjaxController
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
class CCampaignListAjaxController extends AAjaxController
{
    public function get()
    {
        $sample = $this->app->repoFactory->create('Campaign')->getEmptyEntity();

        $query = "SELECT
                      c.id as campaignId,
                      c.code as campaignCode,
                      c.name as campaignName,
                      c.type as campaignType,
                      (select min(timestamp) from CampaignVisit where CampaignVisit.campaignId = c.id) as firstVisit,
                      count(distinct cv.id) as visits,
                      count(distinct cvhp.campaignVisitId) as productVisits,
                      count(o.id) as conversions,
                      sum(o.netTotal) as totConversion,
                      avg(o.netTotal) as scontrinoMedio
                    FROM
                      Campaign c  
                      LEFT JOIN CampaignVisit cv on cv.campaignId = c.id  
                      LEFT JOIN CampaignVisitHasProduct cvhp ON cvhp.campaignId = cv.campaignId AND cvhp.campaignVisitId = cv.id
                      LEFT JOIN (
                            CampaignVisitHasOrder cvho 
                            JOIN `Order` o ON cvho.orderId = o.id
                            JOIN OrderLine ol ON o.id = ol.orderId )
                                ON cvho.campaignVisitId = cv.id and 
                                   cvho.campaignId = cv.campaignId
                    WHERE
                      ifnull(timestamp,1) >= ifnull(?, ifnull(timestamp,1))
                      AND ifnull(timestamp,1) <= ifnull(?, ifnull(timestamp,1)) 
                    GROUP BY c.id";


        //IL PROBLEMA é IL DIOCANE DI TIMESTAMP CHE RIMANE NULL DI MERDA DI DIO
        $timeFrom = \DateTime::createFromFormat('Y-m-d', $this->app->router->request()->getRequestData('startDate'));
        $timeTo = \DateTime::createFromFormat('Y-m-d', $this->app->router->request()->getRequestData('endDate'));
        $timeFrom = $timeFrom ? $timeFrom->format('Y-m-d') : null;
        $timeTo = $timeTo ? $timeTo->format('Y-m-d') : null;
        $queryParameters = [$timeFrom, $timeTo];

        $datatable = new CDataTables($query, $sample->getPrimaryKeys(), $_GET, true);

        $campaigns = $this->app->dbAdapter->query($datatable->getQuery(false, true), array_merge($queryParameters, $datatable->getParams()))->fetchAll();
        $count = $sample->em()->findCountBySql($datatable->getQuery(true), array_merge($queryParameters, $datatable->getParams()));
        $totalCount = $sample->em()->findCountBySql($datatable->getQuery('full'), array_merge($queryParameters, $datatable->getParams()));

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($campaigns as $campaignData) {

            $row = $campaignData;

            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}
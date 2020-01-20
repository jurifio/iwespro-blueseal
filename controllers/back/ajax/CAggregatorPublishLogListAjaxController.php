<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CAggregatorPublishLog;
use bamboo\domain\entities\CMarketplaceAccount;
use bamboo\domain\entities\CMarketplace;
use bamboo\domain\entities\CCampaign;

use bamboo\core\intl\CLang;
use DateTime;

/**
 * Class CAggregatorPublishLogListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 20/01/2020
 * @since 1.0
 */
class CAggregatorPublishLogListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT
                    `a`.`id`           AS `id`,
                    `a`.`dateCreate`       AS `dateCreate`,
                    `a`.`marketplaceAccountId` AS `marketplaceAccountId`,
                    `a`.`marketplaceId` as `marketplaceId`,
                    `a`.`subject` as `subject`,
                    `a`.`email` as `email`,
                    `a`.`result` as `result`,
                    `a`.`action` as `action`,
                    `m`.`name` as  ` marketplaceName`,
                    `ma`.`name` as `marketplaceAccountName`,
                    `c`.`name` as `campaingnName`
                    FROM AggregatorPublishLog a 
                        left join Campaign c on `a`.`marketplaceId`=`c`.`marketplaceId` and `a`.`marketplaceAccountId`=`c`.`marketplaceAccountId` 
                    left JOIN Marketplace m on `a`.`marketplaceId`=`m`.`id`
                    left JOIN MarketplaceAccount ma on `a`.`marketplaceAccountId` = `ma`.`id`";

        $datatable = new CDataTables($sql,['id'],$_GET,true);
        $datatable->doAllTheThings('true');

        $logs = \Monkey::app()->repoFactory->create('AggregatorPublishLog')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('AggregatorPublishLog')->em()->findCountBySql($datatable->getQuery(true),$datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('AggregatorPublishLog')->em()->findCountBySql($datatable->getQuery('full'),$datatable->getParams());


        foreach ($datatable->getResponseSetData() as $key => $row) {
            $log = \Monkey::app()->repoFactory->create('AggregatorPublishLog')->findOneBy($row);
            $row["DT_RowId"] = $log->printId();
            $row["DT_RowClass"] = 'colore';
            $row['id'] = $log->printId();
            $dateCreate = new DateTime($log->dateCreate);
            $row['dateCreate'] = $dateCreate->format('d/m/Y H:i:s');
            $campaign = \Monkey::app()->repoFactory->create('Campaign')->findOneBy(['marketplaceId' => $log->marketplaceId,'marketplaceAccountId' => $log->marketplaceAccountId]);
            if ($campaign != null) {
                $row['campaignName'] = $campaign->name;
            } else {
                $row['campaignName'] = 'nessuna campagna associata';
            }
            if ($log->result == 'success') {
                $row['result'] = 'Eseguita con Successo';
            } else {
                $row['result'] = 'Fallito';
            }
            if ($log->action == 'Publish') {
                $row['action'] = 'Pubblicazione';
            } else {
                $row['action'] = 'Depubblicazione';
            }

            $datatable->setResponseDataSetRow($key,$row);
        }



        return $datatable->responseOut();
    }
}
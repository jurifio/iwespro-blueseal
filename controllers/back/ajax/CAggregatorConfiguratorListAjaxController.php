<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CMarketplaceAccount;
use bamboo\domain\entities\CGainPlanPassiveMovement;

/**
 * Class CAggregatorConfiguratorListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/12/2019
 * @since 1.0
 */
class CAggregatorConfiguratorListAjaxController extends AAjaxController
{
    public function get()
    {

        $sql = 'SELECT id as id,
       marketplaceId as marketplaceId,
       name as name
       from MarketplaceAccount  ORDER BY id asc
        ';
        $datatable = new CDataTables($sql,['id','marketplaceId'],$_GET,true);

        $datatable->doAllTheThings('true');


        $marketplaceAccount = \Monkey::app()->repoFactory->create('marketplaceAccount')->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('marketplaceAccount')->em()->findCountBySql($datatable->getQuery(true),$datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('marketplaceAccount')->em()->findCountBySql($datatable->getQuery('full'),$datatable->getParams());



        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var $val CGainPlan */
            $val = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneBy($row);
            $row['DT_RowId'] = $val->printId();
            $row['id'] = '<a href="/blueseal/marketplace/account/?id=' . $val -> printId() .'">' . $val -> printId() . '</a>';

            $row['marketplaceId'] = $val->marketplaceId;
            $row['name']=$val->name;
            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}
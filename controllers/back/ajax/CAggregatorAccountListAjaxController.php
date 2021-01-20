<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;

/**
 * Class CAggregatorAccountListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CAggregatorAccountListAjaxController extends AMarketplaceAccountAjaxController
{

    public function get()
    {
        $sql="SELECT
          ma.id                                           AS id,
          m.id                                            AS marketplaceId,
          ma.id                                           AS marketplaceAccountId,
          m.name                                          AS marketplace,
          ma.name                                         AS marketplaceAccount,
         m.type as marketplaceType,
          if(ma.isActive='0','si','no') as isActive 
        FROM Marketplace m
          JOIN MarketplaceAccount ma ON m.id = ma.marketplaceId
        where m.type='cpc'
        GROUP BY ma.id, ma.marketplaceId";

        $datatable = new CDataTables($sql,['id','marketplaceId'],$_GET,true);

        $datatable->doAllTheThings();




        $mapRepo=\Monkey::app()->repoFactory->create('MarketplaceAccount');

        foreach ($datatable->getResponseSetData() as $key => $row) {


            $marketplaceAccount = $mapRepo->findOneBy($row);
                $row["DT_RowId"] = $marketplaceAccount->printId();
                $row['code'] = $marketplaceAccount->printId();
                $row['marketplace'] = $marketplaceAccount->marketplace->name;
                $row['marketplaceAccount'] = '<a href="/blueseal/prodotti/marketplace/account/' . $marketplaceAccount->printId() . '">' . $marketplaceAccount->name . '</a>';
                $row['marketplaceType'] = $marketplaceAccount->marketplace->type;
               $row['isActive'] = ($marketplaceAccount->isActive==1)?'si':'no';

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }

}
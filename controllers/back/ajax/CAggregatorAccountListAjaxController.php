<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;

/**
 * Class CAggregatorAccountListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
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
        $mpRepo=\Monkey::app()->repoFactory->create('Marketplace');

        foreach ($datatable->getResponseSetData() as $key => $row) {

            /** @var  $marketplaceAccount CMarketplaceAccount */
            $marketplaceAccount = $mapRepo->findOneBy($row);
                $row["DT_RowId"] = $marketplaceAccount->printId();
                $row['code'] = $marketplaceAccount->printId();
                $marketplace=$mpRepo->findOneBy(['id'=>$marketplaceAccount->marketplaceId]);
                $row['marketplace'] = $marketplace->name;
                $row['marketplaceAccount'] = '<a href="/blueseal/prodotti/marketplace/account/' . $marketplaceAccount->printId() . '">' . $marketplaceAccount->name . '</a>';
                $row['marketplaceType'] = $marketplace->type;
               $row['isActive'] = ($marketplaceAccount->isActive==1)?'si':'no';

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }

}
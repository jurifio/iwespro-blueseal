<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;

/**
 * Class CJobListAjaxController
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
class CCarrierCountryListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                    ca.name as carrier,
                    ca.id as carrierId,
                    co.id as countryId,
                    co.name as country,
                    if(chc.countryId is null, 'no', 'sisì') as isActive,
                    shipmentMinTime,
                    shipmentMaxTime,
                    shipmentCost,
                    shipmentPrice
                FROM Carrier ca
                LEFT JOIN Country co ON 1=1
                LEFT JOIN CarrierHasCountry chc ON ca.id = chc.carrierId AND co.id = chc.countryId";

        $datatable = new CDataTables($sql, ['carrierId','countryId'], $_GET, true);
        $datatable->doAllTheThings(true);

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}
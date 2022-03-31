<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;

/**
 * Class CJobListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes Team <juri@iwes.it>
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
                    chc.id as id,
                    ca.name as carrier,
                    ca.id as carrierId,
                    co.id as countryId,
                    co.name as country,    
                    chc.minWeight as minWeight,
                    chc.maxWeight as maxWeight,  
                    concat(chc.minWeight,'-',chc.maxWeight) as rangeWeight, 
                    if(chc.isActive is not null and chc.isActive = 1, 'sisì','no' ) as isActive,
                    shipmentMinTime,
                    shipmentMaxTime,
                    shipmentCost,
                    shipmentPrice
                FROM Carrier ca
                 JOIN Country co ON 1=1
                 JOIN CarrierHasCountry chc ON ca.id = chc.carrierId AND co.id = chc.countryId order by chc.carrierId,chc.countryId,chc.minWeight asc";

        $datatable = new CDataTables($sql, ['id','carrierId','countryId'], $_GET, true);
        $datatable->doAllTheThings(true);

        foreach ($datatable->getResponseSetData() as $key=>$row) {
            if($row['isActive'] == 'sisì') $row['isActive'] = 'sì';
            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }

    public function put() {
        $carrierHasCountryRepo = \Monkey::app()->repoFactory->create('CarrierHasCountry');
        $data = \Monkey::app()->router->request()->getRequestData('data');
        foreach (\Monkey::app()->router->request()->getRequestData('selectedRows') as $key=>$val ) {
            $carrierHasCountry = $carrierHasCountryRepo->findOneBy([
                'carrierId'=>$val['carrierId'],
                'countryId'=>$val['countryId'],
                'id'=>$val['id']
            ]);
            if($carrierHasCountry) {
            $carrierHasCountry->minWeight = empty($data['minWeight']) ? null : $data['minWeight'];
            $carrierHasCountry->maxWeight = empty($data['maxWeight']) ? null : $data['maxWeight'];
            $carrierHasCountry->shipmentMinTime = empty($data['shipmentMinTime']) ? null : $data['shipmentMinTime'];
            $carrierHasCountry->shipmentMaxTime = empty($data['shipmentMaxTime']) ? null : $data['shipmentMaxTime'];
            $carrierHasCountry->shipmentCost = empty($data['shipmentCost']) ? null : $data['shipmentCost'];
            $carrierHasCountry->shipmentPrice = empty($data['shipmentPrice']) ? null : $data['shipmentPrice'];
            $carrierHasCountry->isActive = empty($data['isActive'] ?? '') ? false : (bool) $data['isActive'];
            $carrierHasCountry->update();
            }
        }
    }
    public function post() {
        $carrierHasCountryRepo = \Monkey::app()->repoFactory->create('CarrierHasCountry');
        $data = \Monkey::app()->router->request()->getRequestData('data');

            $carrierHasCountry = $carrierHasCountryRepo->findOneBy([
                'carrierId'=>$data['carrierId'],
                'countryId'=>$data['countryId'],
                'minWeight' => $data['minWeight'],
                'maxWeight' => $data['maxWeight']
            ]);
            if($carrierHasCountry === null) {
                $carrierHasCountry = $carrierHasCountryRepo->getEmptyEntity();
                $carrierHasCountry->carrierId = $data['carrierId'];
                $carrierHasCountry->countryId = $data['countryId'];
                $carrierHasCountry->minWeight = empty($data['minWeight']) ? null : $data['minWeight'];
                $carrierHasCountry->maxWeight = empty($data['maxWeight']) ? null : $data['maxWeight'];
                $carrierHasCountry->shipmentMinTime = empty($data['shipmentMinTime']) ? null : $data['shipmentMinTime'];
                $carrierHasCountry->shipmentMaxTime = empty($data['shipmentMaxTime']) ? null : $data['shipmentMaxTime'];
                $carrierHasCountry->shipmentCost = empty($data['shipmentCost']) ? null : $data['shipmentCost'];
                $carrierHasCountry->shipmentPrice = empty($data['shipmentPrice']) ? null : $data['shipmentPrice'];
                $carrierHasCountry->isActive = empty($data['isActive'] ?? '') ? false : (bool) $data['isActive'];
                $carrierHasCountry->smartInsert();
            }

        }

}
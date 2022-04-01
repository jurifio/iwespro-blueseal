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
class CCarrierCountryCloneZoneAjaxController extends AAjaxController
{
    public function get()
    {

    }

    public function put()
    {
        $carrierHasCountryRepo = \Monkey::app()->repoFactory->create('CarrierHasCountry');
        $countryRepo = \Monkey::app()->repoFactory->create('Country');
        $data = \Monkey::app()->router->request()->getRequestData('data');
        $extraue = \Monkey::app()->router->request()->getRequestData('extraue');
        foreach (\Monkey::app()->router->request()->getRequestData('selectedRows') as $key => $val) {
            $carrierId = $val['carrierId'];
            $countryId = $val['countryId'];
        }
        switch ($data['extraue']) {
            case 1:
                $countrys = $countryRepo->findBy(['id' => 110]);
                foreach ($countrys as $country) {

                    $carrierHasCountry = $carrierHasCountryRepo->findOneBy([
                        'carrierId' => $carrierId,
                        'countryId' => $country->id,
                        'minWeight' => $data['minWeight'],
                        'maxWeight' => $data['maxWeight']
                    ]);
                    if ($carrierHasCountry === null) {
                        $carrierCountryInsert = $carrierHasCountryRepo->getEmptyEntity();
                        $carrierCountryInsert->carrierId = $carrierId;
                        $carrierCountryInsert->countryId = $country->id;
                        $carrierCountryInsert->minWeight = empty($data['minWeight']) ? null : $data['minWeight'];
                        $carrierCountryInsert->maxWeight = empty($data['maxWeight']) ? null : $data['maxWeight'];
                        $carrierCountryInsert->shipmentMinTime = empty($data['shipmentMinTime']) ? null : $data['shipmentMinTime'];
                        $carrierCountryInsert->shipmentMaxTime = empty($data['shipmentMaxTime']) ? null : $data['shipmentMaxTime'];
                        $carrierCountryInsert->shipmentCost = empty($data['shipmentCost']) ? null : $data['shipmentCost'];
                        $carrierCountryInsert->shipmentPrice = empty($data['shipmentPrice']) ? null : $data['shipmentPrice'];
                        $carrierCountryInsert->isActive = empty($data['isActive'] ?? '') ? false : (bool)$data['isActive'];
                        $carrierCountryInsert->smartInsert();
                    }
                }
                break;
            case 4:
                $countrys = $countryRepo->findAll();
                foreach ($countrys as $country) {

                    $carrierHasCountry = $carrierHasCountryRepo->findOneBy([
                        'carrierId' => $carrierId,
                        'countryId' => $country->id,
                        'minWeight' => $data['minWeight'],
                        'maxWeight' => $data['maxWeight']
                    ]);
                    if ($carrierHasCountry === null) {
                        $carrierCountryInsert = $carrierHasCountryRepo->getEmptyEntity();
                        $carrierCountryInsert->carrierId = $carrierId;
                        $carrierCountryInsert->countryId = $country->id;
                        $carrierCountryInsert->minWeight = empty($data['minWeight']) ? null : $data['minWeight'];
                        $carrierCountryInsert->maxWeight = empty($data['maxWeight']) ? null : $data['maxWeight'];
                        $carrierCountryInsert->shipmentMinTime = empty($data['shipmentMinTime']) ? null : $data['shipmentMinTime'];
                        $carrierCountryInsert->shipmentMaxTime = empty($data['shipmentMaxTime']) ? null : $data['shipmentMaxTime'];
                        $carrierCountryInsert->shipmentCost = empty($data['shipmentCost']) ? null : $data['shipmentCost'];
                        $carrierCountryInsert->shipmentPrice = empty($data['shipmentPrice']) ? null : $data['shipmentPrice'];
                        $carrierCountryInsert->isActive = empty($data['isActive'] ?? '') ? false : (bool)$data['isActive'];
                        $carrierCountryInsert->smartInsert();

                    }
                }
                break;
            case 3:
                $countrys = $countryRepo->findBy(['extraue' => 1]);
                foreach ($countrys as $country) {

                    $carrierHasCountry = $carrierHasCountryRepo->findOneBy([
                        'carrierId' => $carrierId,
                        'countryId' => $country->id,
                        'minWeight' => $data['minWeight'],
                        'maxWeight' => $data['maxWeight']
                    ]);
                    if ($carrierHasCountry === null) {
                        $carrierCountryInsert = $carrierHasCountryRepo->getEmptyEntity();
                        $carrierCountryInsert->carrierId = $carrierId;
                        $carrierCountryInsert->countryId = $country->id;
                        $carrierCountryInsert->minWeight = empty($data['minWeight']) ? null : $data['minWeight'];
                        $carrierCountryInsert->maxWeight = empty($data['maxWeight']) ? null : $data['maxWeight'];
                        $carrierCountryInsert->shipmentMinTime = empty($data['shipmentMinTime']) ? null : $data['shipmentMinTime'];
                        $carrierCountryInsert->shipmentMaxTime = empty($data['shipmentMaxTime']) ? null : $data['shipmentMaxTime'];
                        $carrierCountryInsert->shipmentCost = empty($data['shipmentCost']) ? null : $data['shipmentCost'];
                        $carrierCountryInsert->shipmentPrice = empty($data['shipmentPrice']) ? null : $data['shipmentPrice'];
                        $carrierCountryInsert->isActive = empty($data['isActive'] ?? '') ? false : (bool)$data['isActive'];
                        $carrierCountryInsert->smartInsert();
                    }
                }
                break;
            case 2:
                $countrys = $countryRepo->findBy(['extraue' => "0"]);
                foreach ($countrys as $country) {
                    if ($country->id == 110) {
                        continue;
                    } else {
                        $carrierHasCountry = $carrierHasCountryRepo->findOneBy([
                            'carrierId' => $carrierId,
                            'countryId' => $country->id,
                            'minWeight' => $data['minWeight'],
                            'maxWeight' => $data['maxWeight']
                        ]);
                        if ($carrierHasCountry === null) {
                            $carrierCountryInsert = $carrierHasCountryRepo->getEmptyEntity();
                            $carrierCountryInsert->carrierId = $carrierId;
                            $carrierCountryInsert->countryId = $country->id;
                            $carrierCountryInsert->minWeight = empty($data['minWeight']) ? null : $data['minWeight'];
                            $carrierCountryInsert->maxWeight = empty($data['maxWeight']) ? null : $data['maxWeight'];
                            $carrierCountryInsert->shipmentMinTime = empty($data['shipmentMinTime']) ? null : $data['shipmentMinTime'];
                            $carrierCountryInsert->shipmentMaxTime = empty($data['shipmentMaxTime']) ? null : $data['shipmentMaxTime'];
                            $carrierCountryInsert->shipmentCost = empty($data['shipmentCost']) ? null : $data['shipmentCost'];
                            $carrierCountryInsert->shipmentPrice = empty($data['shipmentPrice']) ? null : $data['shipmentPrice'];
                            $carrierCountryInsert->isActive = empty($data['isActive'] ?? '') ? false : (bool)$data['isActive'];
                            $carrierCountryInsert->smartInsert();
                        }
                    }
                }
                break;
        }
    }

    public function post()
    {
    }

}
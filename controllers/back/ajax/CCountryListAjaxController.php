<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;

/**
 * Class CCountryListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/01/2022
 * @since 1.0
 */
class CCountryListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                    c.id as id,
                    `c`.`name` as `name`,
                    c.shippingCost as shippingCost,
                    c.ISO as ISO,
                    c.ISO3 as ISO3,
                    c.continent as continent,
                    c.currencyDisplay as currencyDisplay,
                    `cu`.`code` as `codeCurrencyDisplay` ,
                    `cu2`.`code` as `codeCurrencyPayment`,
                    `l`.`name` as `LangName`,
                    concat(`l`.`lang`,'-',`l`.`countryISO`) AS countryIsoLang
                FROM Country c
                JOIN Currency cu on c.currencyDisplay= cu.id 
                  join  Currency cu2 on c.currencyPayment=cu2.id join Lang l on c.currentLang=l.id
               ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);
        $datatable->doAllTheThings(true);

        foreach ($datatable->getResponseSetData() as $key=>$row) {
            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }

    public function put() {
       /* $carrierHasCountryRepo = \Monkey::app()->repoFactory->create('CarrierHasCountry');
        $data = \Monkey::app()->router->request()->getRequestData('data');
        foreach (\Monkey::app()->router->request()->getRequestData('selectedRows') as $key=>$val ) {
            $carrierHasCountry = $carrierHasCountryRepo->findOneBy([
                'carrierId'=>$val['carrierId'],
                'countryId'=>$val['countryId']
            ]);
            if($carrierHasCountry === null) {
                $carrierHasCountry = $carrierHasCountryRepo->getEmptyEntity();
                $carrierHasCountry->carrierId = $val['carrierId'];
                $carrierHasCountry->countryId = $val['countryId'];
                $carrierHasCountry->smartInsert();
            }

            $carrierHasCountry->shipmentMinTime = empty($data['shipmentMinTime']) ? null : $data['shipmentMinTime'];
            $carrierHasCountry->shipmentMaxTime = empty($data['shipmentMaxTime']) ? null : $data['shipmentMaxTime'];
            $carrierHasCountry->shipmentCost = empty($data['shipmentCost']) ? null : $data['shipmentCost'];
            $carrierHasCountry->shipmentPrice = empty($data['shipmentPrice']) ? null : $data['shipmentPrice'];
            $carrierHasCountry->isActive = empty($data['isActive'] ?? '') ? false : (bool) $data['isActive'];
            $carrierHasCountry->update();
        }*/
    }
}
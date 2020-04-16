<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CBillRegistryProduct;
use bamboo\domain\entities\CBillRegistryGroupProduct;
use bamboo\domain\entities\CBillRegistryCategoryProduct;

/**
 * Class CCrmCompanyLikerListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/04/2020
 * @since 1.0
 */
class CCrmCompanyLikerListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                      `ccl`.`id`                                            AS `id`,
                       `ccl`.`companyName` as `companyName`,
                        `ccl`.`address` as address,
                        `ccl`.`postCode` as `postCode`,
                        `ccl`.`city`as `city`,
                        `ccl`.`countryName` as `countryName`,
                        `ccl`.`phone` as `phone`,
                        `ccl`.`phone2` as `phone2`,
                        `ccl`.`email` as `email`,
                        `ccl`.`contactName` as `contactName`,
                        `ccl`.`rating` as `rating`

                    FROM `CrmCompanyLiker` `ccl`
                      order by id Asc";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings();

        $crmCompanyLikerEdit = $this->app->baseUrl(false) . "/blueseal/crm/companyliker-modifica?id=";

        $crmCompanyLikerRepo = \Monkey::app()->repoFactory->create('CrmCompanyLiker');
        foreach ($datatable->getResponseSetData() as $key => $row) {
            $crmCompanyLiker = $crmCompanyLikerRepo->findOne([$row['id']]);
            $row = [];
            $row["DT_RowId"] = 'row__' . $crmCompanyLiker->printId();
            $row['id'] = $crmCompanyLiker->id;
            $row['companyName']=$crmCompanyLiker->companyName;
            $row['address']=$crmCompanyLiker->address;
            $row['postCode']=$crmCompanyLiker->postCode;
            $row['city']=$crmCompanyLiker->city;
            $row['countryName']=$crmCompanyLiker->countryName;
            $row['phone']=$crmCompanyLiker->phone;
            $row['phone2']=$crmCompanyLiker->phone2;
            $row['email']=$crmCompanyLiker->email;
            $row['rating']=$crmCompanyLiker->rating;

            $datatable->setResponseDataSetRow($key, $row);
        }

        return $datatable->responseOut();
    }
}
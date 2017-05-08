<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CAddressBook;

/**
 * Class CAddressBookListAjaxController
 * @package bamboo\controllers\back\ajax
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
class CAddressBookListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT ab.id,
                       ab.subject,
                       ab.name,
                       ab.phone,
                       ab.vatNumber,
                       ab.city,
                       ab.address,
                       ab.iban,
                       group_concat(sb.name) as billingShop,
                       group_concat(ss.name) as shippingShop
                FROM AddressBook ab 
                  LEFT JOIN Shop sb ON ab.id = sb.billingAddressBookId
                  LEFT JOIN (ShopHasShippingAddressBook shsab join Shop ss on shsab.shopId = ss.id) ON ab.id = shsab.addressBookId
                  GROUP BY ab.id";

        $datatable = new CDataTables($sql, ['id'], $_GET);
        $datatable->doAllTheThings(true);

        $addressBookRepo = $this->app->repoFactory->create('AddressBook');
        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var $addressBook CAddressBook */
            $addressBook = $addressBookRepo->findOneBy($row);

            $row["DT_RowId"] = $addressBook->printId();
            $row["DT_RowClass"] = empty($row['billingShop']) ? "" : "red";

            $datatable->setResponseDataSetRow($key, $row);
        }
        return $datatable->responseOut();
    }
}
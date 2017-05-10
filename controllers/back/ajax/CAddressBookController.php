<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CAddressBook;

/**
 * Class CAddressBookController
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
class CAddressBookController extends AAjaxController
{
    public function post() {
        $addressBookData = $this->app->router->request()->getRequestData();
        $this->app->repoFactory->create('AddressBook');

        $ok = false;
        foreach ($addressBookData as $field) {
            if (!empty($field)) {
                $ok = true;
                break;
            }
        }
        if (!$ok) return null;
        $addressBook = $this->app->repoFactory->create('AddressBook')->findOneByStringId($addressBookData['id']);
        if (is_null($addressBook)) $addressBook = $this->app->repoFactory->create('AddressBook')->getEmptyEntity();
        try {
            /** @var CAddressBook $addressBook */
            $addressBook->name = $addressBookData['name'] ?? null;
            $addressBook->subject = $addressBookData['subject'];
            $addressBook->address = $addressBookData['address'];
            $addressBook->extra = $addressBookData['extra'] ?? null;
            $addressBook->city = $addressBookData['city'];
            $addressBook->countryId = $addressBookData['countryId'];
            $addressBook->postcode = $addressBookData['postcode'];
            $addressBook->phone = $addressBookData['phone'] ?? null;
            $addressBook->cellphone = $addressBookData['cellphone'] ?? null;
            $addressBook->province = $addressBookData['province'] ?? null;
            $addressBook->iban = $addressBookData['iban'] ?? null;

            if(!isset($addressBook->id)) $addressBook->insert();
            else $addressBook->update();

        } catch (\Throwable $e) {
            return null;
        }

        return json_encode($addressBook);
    }
}
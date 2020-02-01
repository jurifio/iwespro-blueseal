<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CSelectAddressBookAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 31/01/2020
 * @since 1.0
 */
class CSelectAddressBookAjaxController extends AAjaxController
{
    public function get()
    {
        $addressBook=[];
       $id = $this -> app -> router -> request() -> getRequestData('id');
        $res = $this -> app -> dbAdapter -> query('SELECT `id`, `name`, `subject`, address,extra,city
,countryId
,postcode
,phone
,cellphone
,vatNumber
,province
,iban from AddressBook where id='.$id.'
        ', []) -> fetchAll();

        foreach ($res as $result) {

            $addressBook[] = ['id' => $result['id'],
                'name' =>  $result['name'],
                'subject' => $result['subject'],
                'address' => $result['address'],
                'extra' => $result['extra'],
                'city' => $result['city'],
                'countryId' => $result['countryId'],
                'postcode' => $result['postcode'],
                'phone' => $result['phone'],
                'cellphone' => $result['cellphone'],
                'vatNumber' => $result['vatNumber'],
                'province' => $result['province'],
                'iban' => $result['iban']
                ];
        }

        return json_encode($addressBook);
    }
}
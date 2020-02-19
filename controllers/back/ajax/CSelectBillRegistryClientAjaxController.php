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
 * Class CSelectBillRegistryClientAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/02/2020
 * @since 1.0
 */
class CSelectBillRegistryClientAjaxController extends AAjaxController
{
    public function get()
    {
        $client = [];
        $id = $this->app->router->request()->getRequestData('id');
        $res = $this->app->dbAdapter->query('SELECT `brc`.`id` as `id`,
        `brc`.`companyName` as `companyName`,
        `brc`.`address` as `address`, 
        `brc`.`zipcode` as `zipCode` ,
        `brc`.`extra` as `extra`,
        `brc`.`city` as `city`,
        `brc`.`province` as `province`,
        `brc`.`countryId` as `countryId`,
        `brc`.`vatNumber` as `vatNumber`,
        `brc`.`phone` as `phone`,
        `brc`.`mobile` as `mobile`,
        `brc`.`fax` as `fax`,
        `brc`.`userId` as `userId`,    
        `brc`.`contactName` as `contactName`,
        `brc`.`phoneAdmin` as `phoneAdmin`,
        `brc`.`mobileAdmin` as `mobileAdmin`,
        `brc`.`emailAdmin` as `emailAdmin`,
         `brc`.`website` as `website`,
         `brc`.`email` as `email`,      
         `brc`.`emailCc` as `emailCc`,
         `brc`.`emailCcn` as `emailCcn`,
         `brc`.`emailPec` as `emailPec`,
         `brc`.`note` as `note`,
         `bri`.`bankRegistryId` as `bankRegistryId`,
          `bri`.`currencyId` as `currencyId`,
          `bri`.`billRegistryTypePaymentId` as `billRegistryTypePaymentId`,   
          `bri`.`billRegistryTypeTaxesId` as `billRegistryTypeTaxesId`,
            `bri`.`iban` as `iban`,
           `bri`.`sdi` as `sdi`
    
from `BillRegistryClient` `brc` join `BillRegistryClientBillingInfo` `bri` on `brc`.`id`=`bri`.`billRegistryClientId`   where `brc`.`id`=' . $id . '
        ',[])->fetchAll();

        foreach ($res as $result) {

            $client[] = ['id' => $result['id'],
                'companyName' => $result['companyName'],
                'address' => $result['address'],
                'zipCode' => $result['zipCode'],
                'city' => $result['city'],
                'province' => $result['province'],
                'countryId' => $result['countryId'],
                'vatNumber' => $result['vatNumber'],
                'phone' => $result['phone'],
                'mobile' => $result['mobile'],
                'fax' => $result['fax'],
                'userId' => $result['userId'],
                'contactName' => $result['contactName'],
                'phoneAdmin'=>$result['phoneAdmin'],
                'mobileAdmin'=>$result['mobileAdmin'],
                'emailAdmin'=>$result['emailAdmin'],
                'email'=>$result['email'],
                'emailCc'=>$result['emailCc'],
                'emailCcn'=>$result['emailCcn'],
                'emailPec' => $result['emailPec'],
                'note'=>$result['note'],
                'bankRegistryId'=>$result['bankRegistryId'],
                'currencyId'=>$result['currencyId'],
                'billRegistryTypePaymentId'=>$result['billRegistryTypePaymentId'],
                'billRegistryTypeTaxesId'=>$result['billRegistryTypeTaxesId'],
                'iban'=>$result['iban'],
                'sdi'=>$result['sdi']
            ];
        }

        return json_encode($client);
    }
}
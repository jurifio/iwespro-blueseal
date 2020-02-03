<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CAddressBook;


/**
 * Class CRegistryClientLocationManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/02/2020
 * @since 1.0
 */
class CRegistryClientLocationManageAjaxController extends AAjaxController
{

    public function post()
    {
        $data = $this->app->router->request()->getRequestData();
        $name = $data["nameLocation"];
        $billRegistryclientId = $data["billRegistryclientId"];
        $address= $data["addressLocation"];
        $extra = $data["extraLocation"];
        $zipCode=$data["zipCodeLocation"];
        $city=$data["cityLocation"];
        $countryId=$data["countryIdLocation"];
        $vatNumber=$data["vatNumberLocation"];
        $signBoard=$data["signBoardLocation"];
        $province=$data["provinceLocation"];
        $sdi=$data["sdiLocation"];
        $contactName=$data["contactNameLocation"];
        $phone=$data["phoneLocation"];
        $fax=$data["faxLocation"];
        $email=$data["emailLocation"];
        $emailCc=$data["emailCcLocation"];
        $emailCcn=$data['emailCcnLocation'];
        $note=$data['noteLocation'];
        try{
            $brclInsert=\Monkey::app()->repoFactory->create('BillRegistryClientLocation')->getEmptyEntity();
            $brclInsert->name=$name;
            $brclInsert->address=$address;
            $brclInsert->extra=$extra;
            $brclInsert->zipCode=$zipCode;
            $brclInsert->city=$city;
            $brclInsert->countryId=$countryId;
            $brclInsert->vatNumber=$vatNumber;
            $brclInsert->signBoard=$signBoard;
            $brclInsert->province=$province;
            $brclInsert->sdi=$sdi;
            $brclInsert->contactName=$contactName;
            $brclInsert->phone=$phone;
            $brclInsert->fax=$fax;
            $brclInsert->email=$email;
            $brclInsert->emailCc=$emailCc;
            $brclInsert->emailCcn=$emailCcn;
            $brclInsert->note=$note;
            $brclInsert->insert();
            $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryClientLocation ',[])->fetchAll();
            foreach ($res as $result) {
                $lastId = $result['id'];
            }
            \Monkey::app()->applicationLog( 'CRegistryClientLocationManageAjaxController','Report','Insert','Insert location' . $lastId,'');
            return $lastId;
        }catch (\Throwable $e){
            \Monkey::app()->applicationLog( 'CRegistryClientLocationManageAjaxController' ,'Error','Insert','Insert location', $e);
            return 'Errore Inserimento'.$e;

        }
    }

}
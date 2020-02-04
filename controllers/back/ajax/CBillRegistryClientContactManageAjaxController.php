<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CAddressBook;


/**
 * Class CBillRegistryClientLocationManageAjaxController
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
class CBillRegistryClientContactManageAjaxController extends AAjaxController
{

    public function post()
    {
        $data = $this->app->router->request()->getRequestData();
        $name = $data["nameContact"];
        $billRegistryclientId = $data["billRegistryclientId"];
        $phone= $data["phoneContact"];
        $fax = $data["faxContact"];
        $email=$data["emailContact"];
        $mobile=$data["mobileContact"];
        $role=$data["roleContact"];

        try{
            $brcInsert=\Monkey::app()->repoFactory->create('BillRegistryContact')->getEmptyEntity();
            $brcInsert->billRegistryclientId=$billRegistryclientId;
            $brcInsert->name=$name;
            $brcInsert->phone=$phone;
            $brcInsert->fax=$fax;
            $brcInsert->email=$email;
            $brclInsert->mobile=$mobile;
            $brclInsert->role=$role;
            $brclInsert->insert();
            $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContact ',[])->fetchAll();
            foreach ($res as $result) {
                $lastId = $result['id'];
            }
            \Monkey::app()->applicationLog( 'CBillRegistryClientContactManageAjaxController','Report','Insert','Insert Contact' . $lastId,'');
            return $lastId;
        }catch (\Throwable $e){
            \Monkey::app()->applicationLog( 'CBillRegistryClientContactManageAjaxController' ,'Error','Insert','Insert contact', $e);
            return 'Errore Inserimento'.$e;

        }
    }

}
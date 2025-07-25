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
        $billRegistryClientId = $data["billRegistryClientId"];
        $phone= $data["phoneContact"];
        $fax = $data["faxContact"];
        $email=$data["emailContact"];
        $mobile=$data["mobileContact"];
        $role=$data["roleContact"];

        try{
            $brcInsert=\Monkey::app()->repoFactory->create('BillRegistryContact')->getEmptyEntity();
            $brcInsert->billRegistryClientId=$billRegistryClientId;
            $brcInsert->name=$name;
            $brcInsert->phone=$phone;
            $brcInsert->fax=$fax;
            $brcInsert->email=$email;
            $brcInsert->mobile=$mobile;
            $brcInsert->role=$role;
            $brcInsert->insert();
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

    public function get()
    {
        $data = $this->app->router->request()->getRequestData();
        $id=$data['id'];
        $contact=[];
        $brc=\Monkey::app()->repoFactory->create('BillRegistryContact')->findOneBy(['id'=>$id]);
        $contact[] = ['id' => $brc -> id, 'billRegistryClientId' => $brc -> billRegistryClientId, 'name' => $brc -> name, 'phone' => $brc -> phone, 'email' => $brc -> email, 'fax' => $brc -> fax, 'mobile' => $brc -> mobile, 'role' => $brc -> role];

        return json_encode($contact);

    }
    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $id=$data["id"];
        $name = $data["nameContact"];
        $billRegistryClientId = $data["billRegistryClientId"];
        $phone= $data["phoneContact"];
        $fax = $data["faxContact"];
        $email=$data["emailContact"];
        $mobile=$data["mobileContact"];
        $role=$data["roleContact"];

        try{
            $brcUpdate=\Monkey::app()->repoFactory->create('BillRegistryContact')->findOneBy(['id'=>$id]);
            $brcUpdate->billRegistryClientId=$billRegistryClientId;
            $brcUpdate->name=$name;
            $brcUpdate->phone=$phone;
            $brcUpdate->fax=$fax;
            $brcUpdate->email=$email;
            $brcUpdate->mobile=$mobile;
            $brcUpdate->role=$role;
            $brcUpdate->update();
            \Monkey::app()->applicationLog( 'CBillRegistryClientContactManageAjaxController','Report','update','Insert Contact' . $id,'');
            return $id;
        }catch (\Throwable $e){
            \Monkey::app()->applicationLog( 'CBillRegistryClientContactManageAjaxController' ,'Error','update','Insert contact', $e);
            return 'Errore Inserimento'.$e;

        }


        $data = $this->app->router->request()->getRequestData();
        $id=$data['id'];
        $contact=[];
        $brc=\Monkey::app()->repoFactory->create('BillRegistryContact')->findOneBy(['id'=>$id]);
        $contact[] = ['id' => $brc -> id, 'billRegistryClientId' => $brc -> billRegistryClientId, 'name' => $brc -> name, 'phone' => $brc -> phone, 'email' => $brc -> email, 'fax' => $brc -> fax, 'mobile' => $brc -> mobile, 'role' => $brc -> role];

        return json_encode($contact);

    }
    public function delete()
    {
        $data = $this->app->router->request()->getRequestData();
        $id = $data["id"];
        try{
            $brcDelete=\Monkey::app()->repoFactory->create('BillRegistryContact')->findOneBy(['id'=>$id]);
            $brcDelete->delete();
            \Monkey::app()->applicationLog( 'CBillRegistryClientContactManageAjaxController','Report','delete','delete contact' . $id,'');
            return 'Cancellazione Filiale con id: '.$id;
        }catch (\Throwable $e){
            \Monkey::app()->applicationLog( 'CBillRegistryClientContactManageAjaxController' ,'Error','delete','delete contact', $e);
            return 'Errore Cancellazione'.$e;

        }
    }

}
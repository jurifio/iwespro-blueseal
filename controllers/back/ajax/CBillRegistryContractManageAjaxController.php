<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CBillRegistryContract;
use bamboo\domain\entities\CBillRegistryContractRow;


/**
 * Class CBillRegistryContractManageAjaxController
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
class CBillRegistryContractManageAjaxController extends AAjaxController
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
            \Monkey::app()->applicationLog( 'CBillRegistryContractManageAjaxController','Report','Insert','Insert Contract' . $lastId,'');
            return $lastId;
        }catch (\Throwable $e){
            \Monkey::app()->applicationLog( 'CBillRegistryContractManageAjaxController' ,'Error','Insert','Insert contract', $e);
            return 'Errore Inserimento'.$e;

        }
    }

    public function get()
    {
        $data = $this->app->router->request()->getRequestData();
        $id=$data['id'];
        $contract=[];
        /* @var CBillRegistryContract $brc */
        $brc=\Monkey::app()->repoFactory->create('BillRegistryContract')->findOneBy(['id'=>$id]);
        /* @var \bamboo\domain\entities\CBillRegistryContractRow $brcr*/
        $brcr=\Monkey::app()->repoFactory->create('BillRegistryContractRow')->findOneBy(['billRegistryContractId'=>$brc->id]);
        $brp=\Monkey::app()->repoFactory->create('BillRegistryGroupProduct')->findOneBy(['id'=>$brcr->billRegistryGroupProductId]);
        $dateActivation = strtotime($brcr->dateActivation);
        $dateActivation = date('Y-m-d\TH:i',$dateActivation);
        $dateContractExpire = strtotime($brc -> dateContractExpire);
        $dateContractExpire = date('Y-m-d\TH:i',$dateContractExpire);
        $dateAlertRenewal = strtotime($brc ->dateAlertRenewal);
        $dateAlertRenewal = date('Y-m-d\TH:i',$dateAlertRenewal);

        $contract[] = ['id' => $brc -> id,
                       'billRegistryClientId' => $brc -> billRegistryClientId,
                       'billRegistryClientAccountId' => $brc -> billRegistryClientAccountId,
                       'typeContractId' => $brc -> typeContractId,
                       'typeValidityId' => $brc -> typeValidityId,
                       'fileContract' => $brc -> fileContract,
                       'dateContractExpire' =>  $dateContractExpire,
                       'nameProduct'=> $brp -> name,
                       'dateAlertRenewal' => $dateAlertRenewal,
                       'dateActivation'=>$dateActivation,
                        'billRegistryContractRowId'=>$brcr->id,
                       'billRegistryGroupProductId'=>$brcr->billRegistryGroupProductId

            ];

        return json_encode($contract);

    }
    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $id=$data["id"];
        $dateActivation = $data["dateActivation"];
        $dateContractExpire = $data["dateContractExpire"];
        $dateAlertRenewal= $data["dateAlertRenewal"];

        $typeValidityId=$data["typeValidityId"];
        $statusId=$data['statusId'];
        $billRegistryGroupProductId=$data["billRegistryGroupProductId"];
        $billRegistryContractRowId=$data["billRegistryContractRowId"];
        $dateActivation =strtotime($dateActivation);
        $dateActivation=date('Y-m-d H:i:s', $dateActivation);
        $dateAlertRenewal =strtotime($dateAlertRenewal);
        $dateAlertRenewal=date('Y-m-d H:i:s', $dateAlertRenewal);
        $dateContractExpire =strtotime($dateContractExpire);
        $dateContractExpire=date('Y-m-d H:i:s', $dateContractExpire);

        try{
            $brcUpdate=\Monkey::app()->repoFactory->create('BillRegistryContract')->findOneBy(['id'=>$id]);
            $brcUpdate->typeValidityId=$typeValidityId;
            $brcUpdate->dateContractExpire=$dateContractExpire;
            $brcUpdate->dateAlertRenewal=$dateAlertRenewal;
            $brcUpdate->update();
            $brcrUpdate=\Monkey::app()->repoFactory->create('BillRegistryContractRow')->findOneBy(['id'=>$billRegistryContractRowId,'billRegistryContractId'=>$id]);
            $brcrUpdate->dateActivation=$dateActivation;
            $brcrUpdate->statusId=$statusId;
            $brcrUpdate->update();
            \Monkey::app()->applicationLog( 'CBillRegistryContractManageAjaxController','Report','update','Modify Contract' . $id,'');
            return $id;
        }catch (\Throwable $e){
            \Monkey::app()->applicationLog( 'CBillRegistryContractManageAjaxController' ,'Error','update','Modify Contract', $e);
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
            $brcDelete=\Monkey::app()->repoFactory->create('BillRegistryContract')->findOneBy(['id'=>$id]);
            $brcDelete->delete();
            $brcrDelete=\Monkey::app()->repoFactory->create('BillRegistryContractRow')->findBy(['billRegistryContractId'=>$id]);
            foreach($brcrDelete as $contract) {
                $contract->delete();
            }
            \Monkey::app()->applicationLog( 'CBillRegistryContractManageAjaxController','Report','delete','delete contract' . $id,'');
            return 'Cancellazione Contratto con id: '.$id;
        }catch (\Throwable $e){
            \Monkey::app()->applicationLog( 'CBillRegistryContractManageAjaxController' ,'Error','delete','delete contract', $e);
            return 'Errore Cancellazione'.$e;

        }
    }

}
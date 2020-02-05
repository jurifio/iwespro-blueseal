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
class CBillRegistryClientLocationManageAjaxController extends AAjaxController
{

    public function post()
    {
        $data = $this->app->router->request()->getRequestData();
        $name = $data["nameLocation"];
        $billRegistryClientId = $data["billRegistryClientId"];
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
            $brclInsert->billRegistryClientId=$billRegistryClientId;
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
            \Monkey::app()->applicationLog( 'CBillRegistryClientLocationManageAjaxController','Report','Insert','Insert location' . $lastId,'');
            return $lastId;
        }catch (\Throwable $e){
            \Monkey::app()->applicationLog( 'CBillRegistryClientLocationManageAjaxController' ,'Error','Insert','Insert location', $e);
            return 'Errore Inserimento'.$e;

        }
    }
    public function get()
    {
        $data = $this->app->router->request()->getRequestData();
        $id=$data['id'];
        $location=[];
        $brcl=\Monkey::app()->repoFactory->create('BillRegistryClientLocation')->findOneBy(['id'=>$id]);
        $location[] = [ 'id' => $brcl -> id,
                        'billRegistryClientId' => $brcl -> billRegistryClientId,
                        'name' => $brcl -> name,
                        'typeLocation'=>$brcl ->typeLocation,
                        'signboard'=>$brcl->signboard,
                        'zipCode' => $brcl -> zipCode,
                        'address'=>$brcl->address,
                        'extra'=>$brcl->extra,
                        'city'=>$brcl->city,
                        'province' =>$brcl->province,
                        'countryId'=>$brcl->countryId,
                        'vatNumber'=>$brcl->vatNumber,
                        'sdi'=>$brcl->sdi,
                        'contactName'=>$brcl->contactName,
                        'phone'=>$brcl->phone,
                        'mobile'=>$brcl->mobile,
                        'fax'=>$brcl->fax,
                        'email'=>$brcl->email,
                        'emailCc'=>$brcl->emailCc,
                        'emailCcn'=>$brcl->emailCcn,
                        'note'=>$brcl->note];

        return json_encode($location);

    }
    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $id = $data["id"];
        $name = $data["nameLocation"];
        $billRegistryClientId = $data["billRegistryClientId"];
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
            $brclUpdate=\Monkey::app()->repoFactory->create('BillRegistryClientLocation')->findOneBy(['id'=>$id]);
            $brclUpdate->billRegistryClientId=$billRegistryClientId;
            $brclUpdate->name=$name;
            $brclUpdate->address=$address;
            $brclUpdate->extra=$extra;
            $brclUpdate->zipCode=$zipCode;
            $brclUpdate->city=$city;
            $brclUpdate->countryId=$countryId;
            $brclUpdate->vatNumber=$vatNumber;
            $brclUpdate->signBoard=$signBoard;
            $brclUpdate->province=$province;
            $brclUpdate->sdi=$sdi;
            $brclUpdate->contactName=$contactName;
            $brclUpdate->phone=$phone;
            $brclUpdate->fax=$fax;
            $brclUpdate->email=$email;
            $brclUpdate->emailCc=$emailCc;
            $brclUpdate->emailCcn=$emailCcn;
            $brclUpdate->note=$note;
            $brclUpdate->update();
            \Monkey::app()->applicationLog( 'CBillRegistryClientLocationManageAjaxController','Report','update','Modify location' . $id,'');
            return $id;
        }catch (\Throwable $e){
            \Monkey::app()->applicationLog( 'CBillRegistryClientLocationManageAjaxController' ,'Error','update','Modify location', $e);
            return 'Errore Modifica'.$e;

        }
    }
    public function delete()
    {
        $data = $this->app->router->request()->getRequestData();
        $id = $data["id"];
        try{
            $brclDelete=\Monkey::app()->repoFactory->create('BillRegistryClientLocation')->findOneBy(['id'=>$id]);
            $brclDelete->delete();
            \Monkey::app()->applicationLog( 'CBillRegistryClientLocationManageAjaxController','Report','delete','delete location' . $id,'');
            return 'Cancellazione Filiale con id: '.$id;
        }catch (\Throwable $e){
            \Monkey::app()->applicationLog( 'CBillRegistryClientLocationManageAjaxController' ,'Error','delete','delete location', $e);
            return 'Errore Cancellazione'.$e;

        }
    }

}
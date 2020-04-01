<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CAddressBook;


/**
 * Class CBillRegistryClientManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 11/01/2020
 * @since 1.0
 */
class CBillRegistryClientManageAjaxController extends AAjaxController
{

    public function post()
    {
        $billRegistryClientRepo = \Monkey::app()->repoFactory->create('BillRegistryClient');
        $billRegistryClientAccountRepo = \Monkey::app()->repoFactory->create('BillRegistryClientAccount');
        $billRegistryClientAccountHasProductRepo = \Monkey::app()->repoFactory->create('BillRegistryClientAccountHasProduct');
        $billRegistryClientBillingInfoRepo = \Monkey::app()->repoFactory->create('BillRegistryClientBillingInfo');

        $data = $this->app->router->request()->getRequestData();
        if ($_GET['companyName'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Ragione Sociale Cliente non inserita</i>';
        } else {
            $companyName = $_GET['companyName'];
        }
        if ($_GET['address'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">indirizzo non inserito</i>';
        } else {
            $address = $_GET['address'];
        }

        if ($_GET['extra'] == '') {
            $extra = '';
        } else {
            $extra = $_GET['extra'];
        }
        if ($_GET['city'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Cittaà Cliente non inserita</i>';
        } else {
            $city = $_GET['city'];
        }
        if ($_GET['zipCode'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Codice Avviamento Postale non inserito</i>';
        } else {
            $zipCode = $_GET['zipCode'];
        }
        if ($_GET['province'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Provincia Non inserita </i>';
        } else {
            $province = $_GET['province'];
        }
        if ($_GET['countryId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Stato Non Selezionato</i>';
        } else {
            $countryId = $_GET['countryId'];
        }
        if ($_GET['vatNumber'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Codice Fiscale o Partita iva non Valoraizzata</i>';
        } else {
            $vatNumber = $_GET['vatNumber'];
        }
        if ($_GET['phone'] == '') {
            $phone = '';
        } else {
            $phone = $_GET['phone'];
        }
        if ($_GET['fax'] == '') {
            $fax = '';
        } else {
            $fax = $_GET['fax'];
        }

        if ($_GET['mobile'] == "") {
            $mobile='';
        } else {
            $mobile = $_GET['mobile'];
        }
        if ($_GET['userId'] == "") {
            $userId = '';
        } else {
            $userId = $_GET['userId'];
        }
        if ($_GET['contactName'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Nome Contatto Non valorizzato</i>';
        } else {
            $contactName = $_GET['contactName'];
        }
        if ($_GET['phoneAdmin'] == "") {
            $phoneAdmin = '';
        } else {
            $phoneAdmin = $_GET['phoneAdmin'];
        }
        if ($_GET['mobileAdmin'] == '') {
            $mobileAdmin = '';
        } else {
            $mobileAdmin = $_GET['mobileAdmin'];
        }
        if ($_GET['emailAdmin'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Email Amministratore non Valorizzata</i>';
        } else {
            $emailAdmin = $_GET['emailAdmin'];
        }
        if ($_GET['website'] == '') {
            $website = '';
        } else {
            $website = $_GET['website'];
        }
        if ($_GET['email'] == '') {
            $email = '';
        } else {
            $email = $_GET['email'];
        }
        if ($_GET['emailCc'] == '') {
            $emailCc = '';
        } else {
            $emailCc = $_GET['emailCc'];
        }
        if ($_GET['emailCcn'] == '') {
            $emailCcn = '';
        } else {
            $emailCcn = $_GET['emailCcn'];
        }
        if ($_GET['emailPec'] == '') {
            $emailPec = '';
        } else {
            $emailPec = $_GET['emailPec'];
        }
        if ($_GET['note'] == '') {
            $note = '';
        } else {
            $note = $_GET['note'];
        }
        if ($_GET['bankRegistryId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Banca di Appoggio non Selezionata</i>';
        } else {
            $bankRegistryId = $_GET['bankRegistryId'];
        }
        if ($_GET['iban'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Iban Non Valorizzato</i>';
        } else {
            $iban = $_GET['iban'];
        }
        if ($_GET['currencyId'] == '') {
            $currencyId='1';
        } else {
            $currencyId = $_GET['currencyId'];
        }
        if ($_GET['billRegistryTypePaymentId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Tipo di pagamento Non Selezionato</i>';
        } else {
            $billRegistryTypePaymentId = $_GET['billRegistryTypePaymentId'];
        }
        if ($_GET['billRegistryTypeTaxesId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Regime Fiscale non Selezionato </i>';
        } else {
            $billRegistryTypeTaxesId = $_GET['billRegistryTypeTaxesId'];
        }
        if ($_GET['sdi'] == '') {
            $sdi='';
        } else {
            $sdi = $_GET['sdi'];
        }
        if ($_GET['shopId'] == '') {
            $shopId='';
        } else {
            $shopId = $_GET['shopId'];
        }
        if ($_GET['accountStatusId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Stato Account Non Selezionato</i>';
        } else {
            $accountStatusId = $_GET['accountStatusId'];
        }
        if ($_GET['typeClientId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Tipo Cliente Non Selezionato</i>';
        } else {
            $typeClientId = $_GET['typeClientId'];
        }
        if ($_GET['dateActivation'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Data Attivazione non Selezionata</i>';
        } else {
            $dateActivation =strtotime($_GET['dateActivation']);
            $dateActivation=date('Y-m-d H:i:s', $dateActivation);
        }
        if ($_GET['accountAsFriend'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Selezione se Friend Non eseguita</i>';
        } else {
            $accountAsFriend = $_GET['accountAsFriend'];
        }
        if ($_GET['typeFriendId'] == '') {
            $typeFriendId='';
        } else {
            $typeFriendId = $_GET['typeFriendId'];
        }
        if ($_GET['accountAsParallel'] == '' || $_GET['accountAsParallel'] == '0' ) {
            $accountAsParallel=0;
        } else {
            $accountAsParallel = $_GET['accountAsParallel'];
        }
        if ($_GET['accountAsParallelSupplier'] == '' || $_GET['accountAsParallelSupplier'] == '0' ) {
            $accountAsParallelSupplier=0;
        } else {
            $accountAsParallelSupplier = $_GET['accountAsParallelSupplier'];
        }
        if ($_GET['accountAsParallelSeller'] == '' || $_GET['accountAsParallelSeller'] == '0' ) {
            $accountAsParallelSeller=0;
        } else {
            $accountAsParallelSeller = $_GET['accountAsParallelSeller'];
        }
        if ($_GET['parallelFee'] == '') {
           $parallelFee=0;
        } else {
            $parallelFee = $_GET['parallelFee'];
        }
        if ($_GET['accountAsService'] == '' || $_GET['accountAsService'] == '0') {
            $accountAsService=0;
        } else {
            $accountAsService = $_GET['accountAsService'];
        }

        if ($_GET['productList'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Nessuna Prodotto Selezionato </i>';
        } else {
            $productList = $_GET['productList'];
        }
        $ratingAsFriend=0;
        switch($typeFriendId){
            case "1":
                $ratingAsFriend=1;
                break;
            case "2":
                $ratingAsFriend=2;
                break;
            case "3":
                $ratingAsFriend=3;
                break;
            case "4":
                $ratingAsFriend=4;
                break;
            case "5":
                $ratingAsFriend=5;
                break;
            default:
                $ratingAsFriend=$ratingAsFriend;


        }
        $products=explode(',',$productList);
        try {
            $brcInsert = $billRegistryClientRepo->getEmptyEntity();
            $brcInsert->companyName = $companyName;
            $brcInsert->address = $address;
            $brcInsert->extra = $extra;
            $brcInsert->zipcode = $zipCode;
            $brcInsert->city = $city;
            $brcInsert->province = $province;
            $brcInsert->countryId = $countryId;
            $brcInsert->phone = $phone;
            $brcInsert->mobile = $mobile;
            $brcInsert->typeClientId=$typeClientId;
            $brcInsert->vatNumber = $vatNumber;
            $brcInsert->fax = $fax;
            if($userId!=null){
                $brcInsert->userId=$userId;
            }
            $brcInsert->contactName = $contactName;
            $brcInsert->phoneAdmin = $phoneAdmin;
            $brcInsert->mobileAdmin = $mobileAdmin;
            $brcInsert->emailAdmin = $emailAdmin;
            $brcInsert->website = $website;
            $brcInsert->email = $email;
            $brcInsert->emailCc = $emailCc;
            $brcInsert->emailCcn = $emailCcn;
            $brcInsert->emailPec = $emailPec;
            $brcInsert->note = $note;
            $brcInsert->insert();
            $findClient = $billRegistryClientRepo->findOneBy(['vatNumber' => $vatNumber]);
            $billRegistryClientId = $findClient->id;

            $brcaInsert = $billRegistryClientAccountRepo->getEmptyEntity();
            $brcaInsert->billRegistryClientId=$billRegistryClientId;
            $brcaInsert->accountStatusId=$accountStatusId;
            $brcaInsert->accountAsFriend=$accountAsFriend;
            $brcaInsert->dateActivation=$dateActivation;
            $brcaInsert->typeFriendId=$typeFriendId;
            $brcaInsert->ratingAsFriend=$ratingAsFriend;
            $brcaInsert->accountAsService=$accountAsService;
            $brcaInsert->accountAsParallel=$accountAsParallel;
            $brcaInsert->accountAsParallelSupplier=$accountAsParallelSupplier;
            $brcaInsert->accountAsParallelSeller=$accountAsParallelSeller;
            $brcaInsert->parallelFee=$parallelFee;
            if($shopId!=null){
                $brcaInsert->shopId=$shopId;
            }
            $brcaInsert->insert();
            $findBillRegistryClientAccount=$billRegistryClientAccountRepo->findOneBy(['billRegistryClientId'=>$billRegistryClientId]);
            $billRegistryClientAccountId=$findBillRegistryClientAccount->id;
            $brcbiInsert=$billRegistryClientBillingInfoRepo->getEmptyEntity();
            $brcbiInsert->bankRegistryId=$bankRegistryId;
            $brcbiInsert->currencyId=$currencyId;
            $brcbiInsert->billRegistryTypePaymentId=$billRegistryTypePaymentId;
            $brcbiInsert->billRegistryTypeTaxesId=$billRegistryTypeTaxesId;
            $brcbiInsert->iban=$iban;
            $brcbiInsert->sdi=$sdi;
            $brcbiInsert->billRegistryClientId=$billRegistryClientId;
            $brcbiInsert->insert();
            foreach($products as $product ){
                $contractInsert=\Monkey::app()->repoFactory->create('BillRegistryContract')->getEmptyEntity();
                $contractInsert->billRegistryClientId=$billRegistryClientId;
                $contractInsert->billRegistryClientAccountId=$billRegistryClientAccountId;
                $contractInsert->typeContractId=1;
                $contractInsert->typeValidityId=1;
                $contractInsert->insert();
                $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryContract ',[])->fetchAll();
                foreach ($res as $result) {
                    $contractId = $result['id'];
                }
                $contractRowInsert=\Monkey::app()->repoFactory->create('BillRegistryContractRow')->getEmptyEntity();
                $contractRowInsert->billRegistryContractId=$contractId;
                $contractRowInsert->billRegistryGroupProductId=$product;
                $contractRowInsert->statusId=1;
                $contractRowInsert->billRegistryProductTableId=$product;
                $contractRowInsert->insert();





                $brcbahpInsert=$billRegistryClientAccountHasProductRepo->getEmptyEntity();
                $brcbahpInsert->billRegistryGroupProductId=$product;
                $brcbahpInsert->billRegistryClientAccountId=$billRegistryClientAccountId;
                $brcbahpInsert->isActive=1;
                $brcbahpInsert->insert();
            }
            \Monkey::app()->applicationLog('CRegistryClientManageAjaxController','Report','Insert Client','Insert id-Client Account ' . $billRegistryClientId . '-' . $companyName );
            return '1-'.$billRegistryClientId;

        }catch(\Throwable $e){
            \Monkey::app()->applicationLog('CRegistryClientManageAjaxController','error','insert Client', $e ,'');
            return 'Errore Inserimento'.$e;
        }

    }

    public function put()
    {


        $billRegistryClientRepo = \Monkey::app()->repoFactory->create('BillRegistryClient');
        $billRegistryClientAccountRepo = \Monkey::app()->repoFactory->create('BillRegistryClientAccount');
        $billRegistryClientAccountHasProductRepo = \Monkey::app()->repoFactory->create('BillRegistryClientAccountHasProduct');
        $billRegistryClientBillingInfoRepo = \Monkey::app()->repoFactory->create('BillRegistryClientBillingInfo');

        $data = $this->app->router->request()->getRequestData();
        if ($_GET['billRegistryClientId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Ragione Sociale Cliente non inserita</i>';
        } else {
            $billRegistryClientId = $_GET['billRegistryClientId'];
        }
        if ($_GET['companyName'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Ragione Sociale Cliente non inserita</i>';
        } else {
            $companyName = $_GET['companyName'];
        }
        if ($_GET['address'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">indirizzo non inserito</i>';
        } else {
            $address = $_GET['address'];
        }

        if ($_GET['extra'] == '') {
            $extra = '';
        } else {
            $extra = $_GET['extra'];
        }
        if ($_GET['city'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Cittaà Cliente non inserita</i>';
        } else {
            $city = $_GET['city'];
        }
        if ($_GET['zipCode'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Codice Avviamento Postale non inserito</i>';
        } else {
            $zipCode = $_GET['zipCode'];
        }
        if ($_GET['province'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Provincia Non inserita </i>';
        } else {
            $province = $_GET['province'];
        }
        if ($_GET['countryId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Stato Non Selezionato</i>';
        } else {
            $countryId = $_GET['countryId'];
        }
        if ($_GET['vatNumber'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Codice Fiscale o Partita iva non Valoraizzata</i>';
        } else {
            $vatNumber = $_GET['vatNumber'];
        }
        if ($_GET['phone'] == '') {
            $phone = '';
        } else {
            $phone = $_GET['phone'];
        }
        if ($_GET['fax'] == '') {
            $fax = '';
        } else {
            $fax = $_GET['fax'];
        }

        if ($_GET['mobile'] == "") {
            $mobile = '';
        } else {
            $mobile = $_GET['mobile'];
        }
        if ($_GET['userId'] == "") {
            $userId = '';
        } else {
            $userId = $_GET['userId'];
        }
        if ($_GET['contactName'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Nome Contatto Non valorizzato</i>';
        } else {
            $contactName = $_GET['contactName'];
        }
        if ($_GET['phoneAdmin'] == "") {
            $phoneAdmin = '';
        } else {
            $phoneAdmin = $_GET['phoneAdmin'];
        }
        if ($_GET['mobileAdmin'] == '') {
            $mobileAdmin = '';
        } else {
            $mobileAdmin = $_GET['mobileAdmin'];
        }
        if ($_GET['emailAdmin'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Email Amministratore non Valorizzata</i>';
        } else {
            $emailAdmin = $_GET['emailAdmin'];
        }
        if ($_GET['website'] == '') {
            $website = '';
        } else {
            $website = $_GET['website'];
        }
        if ($_GET['email'] == '') {
            $email = '';
        } else {
            $email = $_GET['email'];
        }
        if ($_GET['emailCc'] == '') {
            $emailCc = '';
        } else {
            $emailCc = $_GET['emailCc'];
        }
        if ($_GET['emailCcn'] == '') {
            $emailCcn = '';
        } else {
            $emailCcn = $_GET['emailCcn'];
        }
        if ($_GET['emailPec'] == '') {
            $emailPec = '';
        } else {
            $emailPec = $_GET['emailPec'];
        }
        if ($_GET['note'] == '') {
            $note = '';
        } else {
            $note = $_GET['note'];
        }
        if ($_GET['bankRegistryId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Banca di Appoggio non Selezionata</i>';
        } else {
            $bankRegistryId = $_GET['bankRegistryId'];
        }
        if ($_GET['iban'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Iban Non Valorizzato</i>';
        } else {
            $iban = $_GET['iban'];
        }
        if ($_GET['currencyId'] == '') {
            $currencyId = '1';
        } else {
            $currencyId = $_GET['currencyId'];
        }
        if ($_GET['billRegistryTypePaymentId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Tipo di pagamento Non Selezionato</i>';
        } else {
            $billRegistryTypePaymentId = $_GET['billRegistryTypePaymentId'];
        }
        if ($_GET['billRegistryTypeTaxesId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Regime Fiscale non Selezionato </i>';
        } else {
            $billRegistryTypeTaxesId = $_GET['billRegistryTypeTaxesId'];
        }
        if ($_GET['sdi'] == '') {
            $sdi = '';
        } else {
            $sdi = $_GET['sdi'];
        }
        if ($_GET['shopId'] == '') {
            $shopId = '';
        } else {
            $shopId = $_GET['shopId'];
        }
        if ($_GET['accountStatusId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Stato Account Non Selezionato</i>';
        } else {
            $accountStatusId = $_GET['accountStatusId'];
        }
        if ($_GET['typeClientId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Tipo Cliente Non Selezionato</i>';
        } else {
            $typeClientId = $_GET['typeClientId'];
        }
        if ($_GET['dateActivation'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Data Attivazione non Selezionata</i>';
        } else {
            $dateActivation = strtotime($_GET['dateActivation']);
            $dateActivation = date('Y-m-d H:i:s',$dateActivation);
        }
        if ($_GET['accountAsFriend'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Selezione se Friend Non eseguita</i>';
        } else {
            $accountAsFriend = $_GET['accountAsFriend'];
        }
        if ($_GET['typeFriendId'] == '') {
            $typeFriendId = '';
        } else {
            $typeFriendId = $_GET['typeFriendId'];
        }
        if ($_GET['accountAsParallel'] == '' || $_GET['accountAsParallel'] == '0') {
            $accountAsParallel = 0;
        } else {
            $accountAsParallel = $_GET['accountAsParallel'];
        }
        if ($_GET['accountAsParallelSupplier'] == '' || $_GET['accountAsParallelSupplier'] == '0') {
            $accountAsParallelSupplier = 0;
        } else {
            $accountAsParallelSupplier = $_GET['accountAsParallelSupplier'];
        }
        if ($_GET['accountAsParallelSeller'] == '' || $_GET['accountAsParallelSeller'] == '0') {
            $accountAsParallelSeller = 0;
        } else {
            $accountAsParallelSeller = $_GET['accountAsParallelSeller'];
        }
        if ($_GET['parallelFee'] == '') {
            $parallelFee = 0;
        } else {
            $parallelFee = $_GET['parallelFee'];
        }
        if ($_GET['accountAsService'] == '' || $_GET['accountAsService'] == '0') {
            $accountAsService = 0;
        } else {
            $accountAsService = $_GET['accountAsService'];
        }

        if ($_GET['productList'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Nessuna Prodotto Selezionato </i>';
        } else {
            $productList = $_GET['productList'];
        }
        $ratingAsFriend = 0;
        switch ($typeFriendId) {
            case "1":
                $ratingAsFriend = 1;
                break;
            case "2":
                $ratingAsFriend = 2;
                break;
            case "3":
                $ratingAsFriend = 3;
                break;
            case "4":
                $ratingAsFriend = 4;
                break;
            case "5":
                $ratingAsFriend = 5;
                break;
            default:
                $ratingAsFriend = $ratingAsFriend;


        }
        $products = explode(',',$productList);
        try {
            $brcInsert = $billRegistryClientRepo->findOneBy(['id'=>$billRegistryClientId]);
            $brcInsert->companyName = $companyName;
            $brcInsert->address = $address;
            $brcInsert->extra = $extra;
            $brcInsert->zipcode = $zipCode;
            $brcInsert->city = $city;
            $brcInsert->province = $province;
            $brcInsert->countryId = $countryId;
            $brcInsert->phone = $phone;
            $brcInsert->mobile = $mobile;
            $brcInsert->typeClientId = $typeClientId;
            $brcInsert->vatNumber = $vatNumber;
            $brcInsert->fax = $fax;
            if ($userId != null) {
                $brcInsert->userId = $userId;
            }
            $brcInsert->contactName = $contactName;
            $brcInsert->phoneAdmin = $phoneAdmin;
            $brcInsert->mobileAdmin = $mobileAdmin;
            $brcInsert->emailAdmin = $emailAdmin;
            $brcInsert->website = $website;
            $brcInsert->email = $email;
            $brcInsert->emailCc = $emailCc;
            $brcInsert->emailCcn = $emailCcn;
            $brcInsert->emailPec = $emailPec;
            $brcInsert->note = $note;
            $brcInsert->update();


            $brcaInsert = $billRegistryClientAccountRepo->findOneBy(['billRegistryClientId'=>$billRegistryClientId]);
            $brcaInsert->billRegistryClientId = $billRegistryClientId;
            $brcaInsert->accountStatusId = $accountStatusId;
            $brcaInsert->accountAsFriend = $accountAsFriend;
            $brcaInsert->dateActivation = $dateActivation;
            $brcaInsert->typeFriendId = $typeFriendId;
            $brcaInsert->ratingAsFriend = $ratingAsFriend;
            $brcaInsert->accountAsService = $accountAsService;
            $brcaInsert->accountAsParallel = $accountAsParallel;
            $brcaInsert->accountAsParallelSupplier = $accountAsParallelSupplier;
            $brcaInsert->accountAsParallelSeller = $accountAsParallelSeller;
            $brcaInsert->parallelFee = $parallelFee;
            $brcaInsert->bankRegistryId = $bankRegistryId;
            $brcaInsert->currencyId = $currencyId;
            $brcaInsert->billRegistryTypePaymentId = $billRegistryTypePaymentId;
            $brcaInsert->billRegistryTypeTaxesId = $billRegistryTypeTaxesId;
            $brcaInsert->iban = $iban;
            $brcaInsert->sdi = $sdi;
            if ($shopId != null) {
                $brcaInsert->shopId = $shopId;
            }
            $brcaInsert->update();

            $brcbi = $billRegistryClientBillingInfoRepo->findOneBy(['id'=>$_GET['billRegistryClientBillingInfoId']]);
            $brcbi->bankRegistryId=$bankRegistryId;
            $brcbi->currencyId=$currencyId;
            $brcbi->billRegistryTypePaymentId=$billRegistryTypePaymentId;
            $brcbi->billRegistryTypeTaxesId=$billRegistryTypeTaxesId;
            $brcbi->iban=$iban;
            $brcbi->sdi=$sdi;
            $brcbi->billRegistryClientId=$billRegistryClientId;
            $brci->update();



            \Monkey::app()->applicationLog('CRegistryClientManageAjaxController','Report','Update Client','update id-Client Account ' . $billRegistryClientId . '-' . $companyName);
            return '1-' . $billRegistryClientId;

        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CRegistryClientManageAjaxController','error','insert Client',$e,'');
            return 'Errore Aggiornamento' . $e;
        }
    }

}
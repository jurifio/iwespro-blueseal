<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CAddressBook;


/**
 * Class CProductShareHasShopDestinationInsertManage
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
class CProductShareHasShopDestinationInsertManage extends AAjaxController
{

    public function post()
    {

        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $data = $this->app->router->request()->getRequestData();
        if ($_GET['nameMarketPlace'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Nome Marketplace non inserito</i>';
        } else {
            $marketplace_account_name = $_GET['nameMarketPlace'];
        }
        if ($_GET['marketplaceId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Marketplace non Selezionato</i>';
        } else {
            $marketplaceId = $_GET['marketplaceId'];
        }
        if ($_GET['slug'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">slug non definito</i>';
        } else {
            $slug = $_GET['slug'];
        }

        if ($_GET['lang'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> linguaggio non selezionato</i>';
        } else {
            $lang = $_GET['lang'];
        }
        if ($_GET['shopId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Shop non valorizzato</i>';
        } else {
            $shopId = $_GET['shopId'];
        }
        if ($_GET['isActive'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai selezionato lo stato aggregatore </i>';
        } else {
            $isActive = $_GET['isActive'];
        }
        if ($_GET['isActiveShare'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai selezionato lo stato di Condivisione se attivo o no </i>';
        } else {
            $isActiveShare = $_GET['isActiveShare'];
        }
        if ($_GET['isActivePublish'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai selezionato lo stato di Pubblicazione se attivo o no </i>';
        } else {
            $isActivePublish = $_GET['isActivePublish'];
        }
        if ($_GET['productStatusId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai definito lo stato prdwefintito per la pubblicazione dei prodotti   su gli shop paralleli</i>';
        } else {
            $productStatusId = $_GET['productStatusId'];
        }
        if ($_GET['nameAdminister'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> intestazione Email Destinatario non valorizzato</i>';
        } else {
            $nameAdminister = $_GET['nameAdminister'];
        }
        if ($_GET['emailNotify'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Email Notifica  non valorizzata</i>';
        } else {
            $emailNotify = $_GET['emailNotify'];
        }

        if ($_GET['logoFile'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Logo non Inserito</i>';
        } else {
            $logoFile = $_GET['logoFile'];
        }

        if ($_GET['typeAssign'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non è stata selezionata Attribuita la la regola per i prezzi dei prodotti proprietari</i>';
        } else {
            $typeAssign = $_GET['typeAssign'];
        }
        if ($_GET['typeAssignParallel'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non è stata selezionata Attribuita la la regola per i prezzi dei prodotti paralleli</i>';
        } else {
            $typeAssignParallel = $_GET['typeAssignParallel'];
        }
        $brands=$_GET['brands'];
        if($brands!='0'){
            str_replace(',,',',',$brands);
            $brand=substr($brands,0,-1);
        }else{
            $brand=$brands;
        }
        $brandsParallel=$_GET['brandsParallel'];
        if($brandsParallel!='0'){
            str_replace(',,',',',$brandsParallel);
                $brandParallel=substr($brandsParallel,0,-1);
        }else{
            $brandParallel=$brandsParallel;
        }
        $findUrlSite = $shopRepo->findOneBy(['id' => $shopId]);

        $urlSite=$findUrlSite->urlSite;



        $collectUpdate = '{"nameMarketplace":"' . $marketplace_account_name . '","lang":"' . $lang . '","slug":"' . $slug . '","shop":"' . $shopId . '","isActive":"' . $isActive . '","marketplaceId":"' . $marketplaceId . '","logoFile":"' . $logoFile . '",';
        $collectUpdate .= ' "isActive":"' . $isActive . '","isActiveShare":"' . $isActiveShare . '","isActivePublish":"' . $isActivePublish . '","productStatusId":"' . $productStatusId  . '",';
        $collectUpdate .= '"typeAssignParallel":"' . $typeAssignParallel . '","brandParallel":"' . $brandParallel . '",';
        $collectUpdate .= '"nameAdminister":"' . $nameAdminister . '","emailNotify":"' . $emailNotify   . '","typeAssign":"' . $typeAssign . '",';
        $collectUpdate .= '"brands":"' . $brand . '"}';
        $collectUpdate = trim($collectUpdate," \t\n\r\0\x0B");

        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->getEmptyEntity();
        $marketplaceAccount->marketplaceId = $marketplaceId;
        $marketplaceAccount->name = $marketplace_account_name;
        $marketplaceAccount->config = $collectUpdate;
        $marketplaceAccount->urlSite = $urlSite;
        $marketplaceAccount->isActive = $isActive;
        $marketplaceAccount->insert();



        \Monkey::app()->applicationLog('MarketPlaceAccountHasShopInsert','Report','Insert','Insert Rules Shop Parallel Account HasShop ' . $marketplaceId . ' ' . $marketplace_account_name);
        return 'Inserimento Eseguito con Successo';
    }



        public function put()
    {

        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $data = $this->app->router->request()->getRequestData();
        if ($_GET['nameMarketPlace'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Nome Marketplace non inserito</i>';
        } else {
            $marketplace_account_name = $_GET['nameMarketPlace'];
        }
        if ($_GET['marketplaceId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Marketplace non Selezionato</i>';
        } else {
            $marketplaceId = $_GET['marketplaceId'];
        }
        if ($_GET['slug'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">slug non definito</i>';
        } else {
            $slug = $_GET['slug'];
        }

        if ($_GET['lang'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> linguaggio non selezionato</i>';
        } else {
            $lang = $_GET['lang'];
        }
        if ($_GET['shopId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Shop non valorizzato</i>';
        } else {
            $shopId = $_GET['shopId'];
        }
        if ($_GET['isActive'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai selezionato lo stato aggregatore </i>';
        } else {
            $isActive = $_GET['isActive'];
        }
        if ($_GET['isActiveShare'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai selezionato lo stato di Condivisione se attivo o no </i>';
        } else {
            $isActiveShare = $_GET['isActiveShare'];
        }
        if ($_GET['isActivePublish'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai selezionato lo stato di Pubblicazione se attivo o no </i>';
        } else {
            $isActivePublish = $_GET['isActivePublish'];
        }
        if ($_GET['productStatusId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai definito lo stato prdwefintito per la pubblicazione dei prodotti   su gli shop paralleli</i>';
        } else {
            $productStatusId = $_GET['productStatusId'];
        }
        if ($_GET['nameAdminister'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> intestazione Email Destinatario non valorizzato</i>';
        } else {
            $nameAdminister = $_GET['nameAdminister'];
        }
        if ($_GET['emailNotify'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Email Notifica  non valorizzata</i>';
        } else {
            $emailNotify = $_GET['emailNotify'];
        }

        if ($_GET['logoFile'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Logo non Inserito</i>';
        } else {
            $logoFile = $_GET['logoFile'];
        }

        if ($_GET['typeAssign'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non è stata selezionata Attribuita la la regola per i prezzi dei prodotti proprietari</i>';
        } else {
            $typeAssign = $_GET['typeAssign'];
        }
        if ($_GET['typeAssignParallel'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non è stata selezionata Attribuita la la regola per i prezzi dei prodotti paralleli</i>';
        } else {
            $typeAssignParallel = $_GET['typeAssignParallel'];
        }
        $brands=$_GET['brands'];
        if($brands!='0'){
            str_replace(',,',',',$brands);
            $brand=substr($brands,0,-1);
        }else{
            $brand=$brands;
        }
        $brandsParallel=$_GET['brandsParallel'];
        if($brandsParallel!='0'){
            str_replace(',,',',',$brandsParallel);
            $brandParallel=substr($brandsParallel,0,-1);
        }else{
            $brandParallel=$brandsParallel;
        }
        $findUrlSite = $shopRepo->findOneBy(['id' => $shopId]);

        $urlSite=$findUrlSite->urlSite;



        $collectUpdate = '{"nameMarketplace":"' . $marketplace_account_name . '","lang":"' . $lang . '","slug":"' . $slug . '","shop":"' . $shopId . '","isActive":"' . $isActive . '","marketplaceId":"' . $marketplaceId . '","logoFile":"' . $logoFile . '",';
        $collectUpdate .= ' "isActive":"' . $isActive . '","isActiveShare":"' . $isActiveShare . '","isActivePublish":"' . $isActivePublish . '","productStatusId":"' . $productStatusId  . '",';
        $collectUpdate .= '"typeAssignParallel":"' . $typeAssignParallel . '","brandParallel":"' . $brandParallel . '",';
        $collectUpdate .= '"nameAdminister":"' . $nameAdminister . '","emailNotify":"' . $emailNotify   . '","typeAssign":"' . $typeAssign . '",';
        $collectUpdate .= '"brands":"' . $brand . '"}';
        $collectUpdate = trim($collectUpdate," \t\n\r\0\x0B");
        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketPlaceAccount')->findOneBy(['id'=>$_GET['marketplaceAccountId']]);
        $marketplaceAccount->marketplaceId = $marketplaceId;
        $marketplaceAccount->name = $marketplace_account_name;
        $marketplaceAccount->config = $collectUpdate;
        $marketplaceAccount->urlSite = $urlSite;
        $marketplaceAccount->isActive = $isActive;
        $marketplaceAccount->update();



        \Monkey::app()->applicationLog('MarketPlaceAccountHasShopInsert','Report','Modify','Modify Rulus Shop Parallel  ' . $marketplaceId . ' ' . $marketplace_account_name);
        return 'Modifica Eseguita con Successo';
    }
    public function delete(){
        $id = $this->app->router->request()->getRequestData('id');
        $marketplaceAccount=\Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneBy(['id'=>$id]);
        $marketplaceAccount->delete();
        return 'MarketplaceAccount Cancellato definitivamente ricordati di disabilitare il job associato se esiste';

    }

}
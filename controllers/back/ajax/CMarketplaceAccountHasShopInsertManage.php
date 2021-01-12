<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CAddressBook;


/**
 * Class CMarketplaceAccountInsertManage
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
class CMarketplaceAccountHasShopInsertManage extends AAjaxController
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
        if ($_GET['marketplaceHasShopId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Marketplace non Selezionato</i>';
        } else {
            $marketplaceHasShopId = $_GET['marketplaceHasShopId'];
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
        if ($_GET['maxPercentSalePrice'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai impostato la percentuale massima per lo sconto sui marketplace</i>';
        } else {
            $maxPercentSalePrice = $_GET['maxPercentSalePrice'];
        }
        if ($_GET['isActive'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai selezionato lo stato aggregatore </i>';
        } else {
            $isActive = $_GET['isActive'];
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
        if ($_GET['activeFullPrice'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non è stata selezionata il tipo di Regola per il prezzo Non in Saldo</i>';
        } else {
            $activeFullPrice = $_GET['activeFullPrice'];
        }

        if ($_GET['logoFile'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Logo non Inserito</i>';
        } else {
            $logoFile = $_GET['logoFile'];
        }
        $signFullPrice = $_GET['signFullPrice'];
        $percentFullPrice = $_GET['percentFullPrice'];
        $optradio = $_GET['optradio'];
        $optradioactive = $_GET['optradioactive'];
        if ($_GET['activeSalePrice'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non è stata selezionata il tipo di Regola per il prezzo in Saldo</i>';
        } else {
            $activeSalePrice = $_GET['activeSalePrice'];
        }
        $signSale = $_GET['signSale'];
        $percentSalePrice = $_GET['percentSalePrice'];
        $optradioSalePrice = $_GET['optradioSalePrice'];

        if ($_GET['dateStartPeriod1'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Saldi inizio Periodo 1 non  Valorizzato</i>';
        } else {
            $dateStartPeriod1 = $_GET['dateStartPeriod1'];
        }
        if ($_GET['dateStartPeriod2'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Saldi inizio Periodo 2 non  Valorizzato</i>';
        } else {
            $dateStartPeriod2 = $_GET['dateStartPeriod2'];
        }
        if ($_GET['dateStartPeriod3'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Saldi inizio Periodo 3 non  Valorizzato</i>';
        } else {
            $dateStartPeriod3 = $_GET['dateStartPeriod3'];
        }
        if ($_GET['dateStartPeriod4'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Saldi inizio Periodo 4 non  Valorizzato</i>';
        } else {
            $dateStartPeriod4 = $_GET['dateStartPeriod4'];
        }
        if ($_GET['dateEndPeriod1'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Saldi Fine Periodo 1 non  Valorizzato</i>';
        } else {
            $dateEndPeriod1 = $_GET['dateEndPeriod1'];
        }
        if ($_GET['dateEndPeriod2'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Saldi Fine Periodo 2 non  Valorizzato</i>';
        } else {
            $dateEndPeriod2 = $_GET['dateEndPeriod2'];
        }
        if ($_GET['dateEndPeriod3'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Saldi Fine Periodo 3 non  Valorizzato</i>';
        } else {
            $dateEndPeriod3 = $_GET['dateEndPeriod3'];
        }
        if ($_GET['dateEndPeriod4'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Saldi Fine Periodo 4 non  Valorizzato</i>';
        } else {
            $dateEndPeriod4 = $_GET['dateEndPeriod4'];
        }
        $brandSaleExclusion = $_GET['brandSaleExclusion'];
        if ($brandSaleExclusion != '0') {
            str_replace(',,',',',$brandSaleExclusion);
            if (substr($brandSaleExclusion,-1) == ',') {
                $brandSale = substr($brandSaleExclusion,0,-1);
            } else {
                $brandSale = $brandSaleExclusion;
            }
        } else {
            $brandSale = $brandSaleExclusion;
        }

        if ($_GET['checkNameCatalog'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non è stata selezionata la regola per l\'impostazione del nome in saldo</i>';
        } else {
            $checkNameCatalog = $_GET['checkNameCatalog'];
        }
        $optradioName = $_GET['optradioName'];

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
        $brands = $_GET['brands'];
        if ($brands != '0') {
            str_replace(',,',',',$brands);
            if (substr($brands,-1) == ',') {
                $brand = substr($brands,0,-1);
            } else {
                $brand = $brands;
            }
        } else {
            $brand = $brands;
        }
        $brandsParallel = $_GET['brandsParallel'];
        if ($brandsParallel != '0') {
            str_replace(',,',',',$brandsParallel);
            if (substr($brandsParallel,-1) == ',') {
                $brandParallel = substr($brandsParallel,0,-1);
            } else {
                $brandParallel = $brandsParallel;
            }
        } else {
            $brandParallel = $brandsParallel;
        }


        $marketplaceHasShopFind = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['id' => $marketplaceHasShopId]);

        $marketplaceHasShopId = $marketplaceHasShopFind->id;
        $shopId = $marketplaceHasShopFind->id;
        $marketplaceId = $marketplaceHasShopFind->marketplaceId;
        $findUrlSite = $shopRepo->findOneBy(['id' => $shopId]);
        $urlSite = $findUrlSite->urlSite;

        $collectUpdate = '{"nameMarketplace":"' . $marketplace_account_name . '","lang":"' . $lang . '","slug":"' . $slug . '","shopId":"' . $shopId . '","isActive":"' . $isActive . '","marketplaceId":"' . $marketplaceId . '","logoFile":"' . $logoFile . '",';
        $collectUpdate .= ' "isActive":"' . $isActive . '","activeFullPrice":"' . $activeFullPrice . '","signSale":"' . $signSale . '","percentFullPrice":"' . $percentFullPrice . '","signFullPrice":"' . $signFullPrice . '",';
        $collectUpdate .= '"optradio":"' . $optradio . '","optradioactive":"' . $optradioactive . '","activeSalePrice":"' . $activeSalePrice . '",';
        $collectUpdate .= '"typeAssignParallel":"' . $typeAssignParallel . '","brandParallel":"' . $brandParallel . '",';
        $collectUpdate .= '"maxPercentSalePrice":"' . $maxPercentSalePrice . '","percentSalePrice":"' . $percentSalePrice . '","optradioSalePrice":"' . $optradioSalePrice . '","dateStartPeriod1":"' . $dateStartPeriod1 . '","dateEndPeriod1":"' . $dateEndPeriod1 . '","dateStartPeriod2":"' . $dateStartPeriod2 . '","dateEndPeriod2":"' . $dateEndPeriod2 . '",';
        $collectUpdate .= '"dateStartPeriod3":"' . $dateStartPeriod3 . '","dateEndPeriod3":"' . $dateEndPeriod3 . '","dateStartPeriod4":"' . $dateStartPeriod4 . '","dateEndPeriod4":"' . $dateEndPeriod4 . '",';
        $collectUpdate .= '"nameAdminister":"' . $nameAdminister . '","emailNotify":"' . $emailNotify . '","checkNameCatalog":"' . $checkNameCatalog . '","optradioName":"' . $optradioName . '","typeAssign":"' . $typeAssign . '",';
        if ($marketplaceId == 3) {
            $collectUpdate .= '"COD": "0",
    "shipping": [
        {
            "name": "IT_ExpressCourier",
            "cost": "5"
        }
    ],
    "shippingInternational": [
        {
            "name":"IT_StandardInternational",
            "cost": 10,
            "dest": "worldwide"
        }
    ],
    "appID": "Bamboosh-95f3-4ca8-9b04-b30231ed5a9d",
    "devID": "c9ba4236-32c9-4f1a-878c-30797535501d",
    "certID": "a835f162-0ddd-4d01-94e3-a6009ba7643b",
    "serverUrl": "https:\/\/api.ebay.com\/ws\/api.dll",
    "compatabilityLevel": "965",
    "siteID": "101",
    "userToken": "AgAAAA**AQAAAA**aAAAAA**mDD8Wg**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AFlIekDZKGpw2dj6x9nY+seQ**ECoDAA**AAMAAA**ZXkcpdsnzIff5OAWBzjD8620Mufn6RBRGnOjhyY0lvDeS7f2dPLljsrXYq69BYPZPVaJvnXMO1mLsYsTFcvsE0IF6qpGwVKzm5Ik+f9UIXHHgq0su//TOk+MVWs8vO43UG43gFgo8ffUOC+udNS8kV8RoKS/yEOia4xhyx8cF2VPIzRpa3tthkxBEW+qdVuMMF81XdRA7ixnHFRYtPIAWzgiFbYyKyWx0JcHMZVd9trMzDaMLWgP4ZnwIJgyAfuxWjqtcnaCKWA/1n593fXPxA2ooSxsl4LRvHMHBw+DGEilbqlHybcugGQjRMyaCxj189oK3NdlTCOWgv9vuw7COR0GMtp0jMVy6T76wIDRXgYTDEymu688ijWeHtxEUOLLP/KmE9RZ/DqhwhoZCd0IoXlrikIWY12GJfY9ghxNe2zccHMhr2dgTAEViSSzfZvEMmcLbMYO9nnzHjmHuWBGrxtq7wOGAIx82R+i7nUOgZZ7FCO2lXq3xE8uJw2TZLRll64d/mQHJYEitIYkX5pF8vlef8Fe6vQ+XlmWuHy6rxKqjoui0o0hsH0pdY5vCbPG4+c6hexiEwegTVGtmGmDtOFSnDfAmsByY6vltDFhYsQrLPhetcfZtFlp9baZwZ0Jn96lXZ7dzif8qPD5tuXYjDlJzZf6PLehW0tvA9dxqBKTLHAKZGny9b3IMFyOK7LRhJf7faSInfLFf23M+PB6blpb6RxHC4MZBSsWuSkYhY4krTlUwSNltoL9nyh279O5",
    "paypalEmail": "transazioni@cartechinishop.com",
    "modifier": "0",';
        }
        $collectUpdate .= '"dateUpdate":"2011-01-01 00:00:00",';
        $collectUpdate .= '"marketplaceHasShop":"' . $marketplaceHasShopId . '",';
        $collectUpdate .= '"brandSaleExclusion":"' . $brandSale . '",';
        $collectUpdate .= '"brands":"' . $brand . '"}';
        $collectUpdate = trim($collectUpdate," \t\n\r\0\x0B");

        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->getEmptyEntity();
        $marketplaceAccount->marketplaceId = $marketplaceId;
        $marketplaceAccount->name = $marketplace_account_name;
        $marketplaceAccount->config = $collectUpdate;
        $marketplaceAccount->urlSite = $urlSite;
        $marketplaceAccount->isActive = $isActive;
        $marketplaceAccount->insert();


        \Monkey::app()->applicationLog('MarketPlaceAccountHasShopInsert','Report','Insert','Insert Marketplace Account HasShop ' . $marketplaceId . ' ' . $marketplace_account_name);
        return 'Inserimento Eseguito con Successo';
    }


    public function put()
    {

        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $data = $this->app->router->request()->getRequestData();
        $dateUpdate = (new \DateTime())->format('Y-m-d H:i:s');
        if ($_GET['nameMarketPlace'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Nome Marketplace non inserito</i>';
        } else {
            $marketplace_account_name = $_GET['nameMarketPlace'];
        }
        if ($_GET['marketplaceHasShopId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Marketplace non Selezionato</i>';
        } else {
            $marketplaceHasShopId = $_GET['marketplaceHasShopId'];
        }
        if ($_GET['slug'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">slug non definito</i>';
        } else {
            $slug = $_GET['slug'];
        }
        if ($_GET['maxPercentSalePrice'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai impostato la percentuale massima per lo sconto sui marketplace</i>';
        } else {
            $maxPercentSalePrice = $_GET['maxPercentSalePrice'];
        }

        if ($_GET['lang'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> linguaggio non selezionato</i>';
        } else {
            $lang = $_GET['lang'];
        }

        if ($_GET['isActive'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai selezionato lo stato aggregatore </i>';
        } else {
            $isActive = $_GET['isActive'];
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
        if ($_GET['activeFullPrice'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non è stata selezionata il tipo di Regola per il prezzo Non in Saldo</i>';
        } else {
            $activeFullPrice = $_GET['activeFullPrice'];
        }

        if ($_GET['logoFile'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Logo non Inserito</i>';
        } else {
            $logoFile = $_GET['logoFile'];
        }
        $signFullPrice = $_GET['signFullPrice'];
        $percentFullPrice = $_GET['percentFullPrice'];
        $optradio = $_GET['optradio'];
        $optradioactive = $_GET['optradioactive'];
        if ($_GET['activeSalePrice'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non è stata selezionata il tipo di Regola per il prezzo in Saldo</i>';
        } else {
            $activeSalePrice = $_GET['activeSalePrice'];
        }
        $signSale = $_GET['signSale'];
        $percentSalePrice = $_GET['percentSalePrice'];
        $optradioSalePrice = $_GET['optradioSalePrice'];

        if ($_GET['dateStartPeriod1'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Saldi inizio Periodo 1 non  Valorizzato</i>';
        } else {
            $dateStartPeriod1 = $_GET['dateStartPeriod1'];
        }
        if ($_GET['dateStartPeriod2'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Saldi inizio Periodo 2 non  Valorizzato</i>';
        } else {
            $dateStartPeriod2 = $_GET['dateStartPeriod2'];
        }
        if ($_GET['dateStartPeriod3'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Saldi inizio Periodo 3 non  Valorizzato</i>';
        } else {
            $dateStartPeriod3 = $_GET['dateStartPeriod3'];
        }
        if ($_GET['dateStartPeriod4'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Saldi inizio Periodo 4 non  Valorizzato</i>';
        } else {
            $dateStartPeriod4 = $_GET['dateStartPeriod4'];
        }
        if ($_GET['dateEndPeriod1'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Saldi Fine Periodo 1 non  Valorizzato</i>';
        } else {
            $dateEndPeriod1 = $_GET['dateEndPeriod1'];
        }
        if ($_GET['dateEndPeriod2'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Saldi Fine Periodo 2 non  Valorizzato</i>';
        } else {
            $dateEndPeriod2 = $_GET['dateEndPeriod2'];
        }
        if ($_GET['dateEndPeriod3'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Saldi Fine Periodo 3 non  Valorizzato</i>';
        } else {
            $dateEndPeriod3 = $_GET['dateEndPeriod3'];
        }
        if ($_GET['dateEndPeriod4'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Saldi Fine Periodo 4 non  Valorizzato</i>';
        } else {
            $dateEndPeriod4 = $_GET['dateEndPeriod4'];
        }
        $brandSaleExclusion = $_GET['brandSaleExclusion'];
        if ($brandSaleExclusion != '0') {
            str_replace(',,',',',$brandSaleExclusion);
            if (substr($brandSaleExclusion,-1) == ',') {
                $brandSale = substr($brandSaleExclusion,0,-1);
            } else {
                $brandSale = $brandSaleExclusion;
            }
        } else {
            $brandSale = $brandSaleExclusion;
        }

        if ($_GET['checkNameCatalog'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non è stata selezionata la regola per l\'impostazione del nome in saldo</i>';
        } else {
            $checkNameCatalog = $_GET['checkNameCatalog'];
        }
        $optradioName = $_GET['optradioName'];
        if ($_GET['marketplaceAccountId'] = '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Prego Seleziona un Regola Valida torna alla lista </i>';
        } else {
            $marketplaceAccountId = $_GET['marketplaceAccountId'];
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
        $brands = $_GET['brands'];
        if ($brands != '0') {
            str_replace(',,',',',$brands);
            if (substr($brands,-1) == ',') {
                $brand = substr($brands,0,-1);
            } else {
                $brand = $brands;
            }
        } else {
            $brand = $brands;
        }
        $brandsParallel = $_GET['brandsParallel'];
        if ($brandsParallel != '0') {
            str_replace(',,',',',$brandsParallel);
            if (substr($brandsParallel,-1) == ',') {
                $brandParallel = substr($brandsParallel,0,-1);
            } else {
                $brandParallel = $brandsParallel;
            }
        } else {
            $brandParallel = $brandsParallel;
        }

        $marketplaceHasShopFind = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['id' => $marketplaceHasShopId]);

        $shopId = $marketplaceHasShopFind->shopId;
        $marketplaceId = $marketplaceHasShopFind->marketplaceId;
        $marketplaceHasShopId = $marketplaceHasShopFind->id;
        $findUrlSite = $shopRepo->findOneBy(['id' => $shopId]);

        $urlSite = $findUrlSite->urlSite;


        $collectUpdate = '{"nameMarketplace":"' . $marketplace_account_name . '","lang":"' . $lang . '","slug":"' . $slug . '","shopId":"' . $shopId . '","isActive":"' . $isActive . '","marketplaceId":"' . $marketplaceId . '","logoFile":"' . $logoFile . '",';
        $collectUpdate .= ' "isActive":"' . $isActive . '","activeFullPrice":"' . $activeFullPrice . '","signSale":"' . $signSale . '","percentFullPrice":"' . $percentFullPrice . '","signFullPrice":"' . $signFullPrice . '",';
        $collectUpdate .= '"optradio":"' . $optradio . '","optradioactive":"' . $optradioactive . '","activeSalePrice":"' . $activeSalePrice . '",';
        $collectUpdate .= '"typeAssignParallel":"' . $typeAssignParallel . '","brandParallel":"' . $brandParallel . '",';
        $collectUpdate .= '"maxPercentSalePrice":"' . $maxPercentSalePrice . '","percentSalePrice":"' . $percentSalePrice . '","optradioSalePrice":"' . $optradioSalePrice . '","dateStartPeriod1":"' . $dateStartPeriod1 . '","dateEndPeriod1":"' . $dateEndPeriod1 . '","dateStartPeriod2":"' . $dateStartPeriod2 . '","dateEndPeriod2":"' . $dateEndPeriod2 . '",';
        $collectUpdate .= '"dateStartPeriod3":"' . $dateStartPeriod3 . '","dateEndPeriod3":"' . $dateEndPeriod3 . '","dateStartPeriod4":"' . $dateStartPeriod4 . '","dateEndPeriod4":"' . $dateEndPeriod4 . '",';
        $collectUpdate .= '"nameAdminister":"' . $nameAdminister . '","emailNotify":"' . $emailNotify . '","checkNameCatalog":"' . $checkNameCatalog . '","optradioName":"' . $optradioName . '","typeAssign":"' . $typeAssign . '",';
        if ($marketplaceId == 3) {
            $collectUpdate .= '"COD": "0",
    "shipping": [
        {
            "name": "IT_ExpressCourier",
            "cost": "5"
        }
    ],
    "shippingInternational": [
        {
            "name":"IT_StandardInternational",
            "cost": 10,
            "dest": "worldwide"
        }
    ],
    "appID": "Bamboosh-95f3-4ca8-9b04-b30231ed5a9d",
    "devID": "c9ba4236-32c9-4f1a-878c-30797535501d",
    "certID": "a835f162-0ddd-4d01-94e3-a6009ba7643b",
    "serverUrl": "https:\/\/api.ebay.com\/ws\/api.dll",
    "compatabilityLevel": "965",
    "siteID": "101",
    "userToken": "AgAAAA**AQAAAA**aAAAAA**mDD8Wg**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AFlIekDZKGpw2dj6x9nY+seQ**ECoDAA**AAMAAA**ZXkcpdsnzIff5OAWBzjD8620Mufn6RBRGnOjhyY0lvDeS7f2dPLljsrXYq69BYPZPVaJvnXMO1mLsYsTFcvsE0IF6qpGwVKzm5Ik+f9UIXHHgq0su//TOk+MVWs8vO43UG43gFgo8ffUOC+udNS8kV8RoKS/yEOia4xhyx8cF2VPIzRpa3tthkxBEW+qdVuMMF81XdRA7ixnHFRYtPIAWzgiFbYyKyWx0JcHMZVd9trMzDaMLWgP4ZnwIJgyAfuxWjqtcnaCKWA/1n593fXPxA2ooSxsl4LRvHMHBw+DGEilbqlHybcugGQjRMyaCxj189oK3NdlTCOWgv9vuw7COR0GMtp0jMVy6T76wIDRXgYTDEymu688ijWeHtxEUOLLP/KmE9RZ/DqhwhoZCd0IoXlrikIWY12GJfY9ghxNe2zccHMhr2dgTAEViSSzfZvEMmcLbMYO9nnzHjmHuWBGrxtq7wOGAIx82R+i7nUOgZZ7FCO2lXq3xE8uJw2TZLRll64d/mQHJYEitIYkX5pF8vlef8Fe6vQ+XlmWuHy6rxKqjoui0o0hsH0pdY5vCbPG4+c6hexiEwegTVGtmGmDtOFSnDfAmsByY6vltDFhYsQrLPhetcfZtFlp9baZwZ0Jn96lXZ7dzif8qPD5tuXYjDlJzZf6PLehW0tvA9dxqBKTLHAKZGny9b3IMFyOK7LRhJf7faSInfLFf23M+PB6blpb6RxHC4MZBSsWuSkYhY4krTlUwSNltoL9nyh279O5",
    "paypalEmail": "transazioni@cartechinishop.com",
    "modifier": "0",';
        }
        $collectUpdate .= '"dateUpdate":"' . $dateUpdate . '",';
        $collectUpdate .= '"marketplaceHasShop":"' . $marketplaceHasShopId . '",';
        $collectUpdate .= '"brandSaleExclusion":"' . $brandSale . '",';
        $collectUpdate .= '"brands":"' . $brand . '"}';
        $collectUpdate = trim($collectUpdate," \t\n\r\0\x0B");
        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketPlaceAccount')->findOneBy(['id' => $marketplaceAccountId]);
        $marketplaceAccount->marketplaceId = $marketplaceId;
        $marketplaceAccount->name = $marketplace_account_name;
        $marketplaceAccount->config = $collectUpdate;
        $marketplaceAccount->urlSite = $urlSite;
        $marketplaceAccount->isActive = $isActive;
        $marketplaceAccount->update();


        \Monkey::app()->applicationLog('MarketPlaceAccountHasShopInsert','Report','Insert','Modify Marketplace Account HasShop ' . $marketplaceId . ' ' . $marketplace_account_name);
        return 'Modifica Eseguita con Successo';
    }

    public function delete()
    {
        $id = $this->app->router->request()->getRequestData('id');
        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneBy(['id' => $id]);
        $marketplaceAccount->delete();
        return 'MarketplaceAccount Cancellato definitivamente ricordati di disabilitare il job associato se esiste';

    }

}
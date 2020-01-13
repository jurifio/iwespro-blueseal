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
class CMarketplaceAccountInsertManage extends AAjaxController
{

    public function post()
    {
        $marketplaceRepo = \Monkey::app()->repoFactory->create('MarketPlace');
        $marketplaceAccountRepo = \Monkey::app()->repoFactory->create('MarketplaceAccount');
        $data = $this->app->router->request()->getRequestData();
        if (!isset($_GET['nameAggregator'])) {
            $marketplace_account_name = $_GET['nameAggregator'];
        } else {
            return 'nome Aggregatore non valorizzato';
        }

        if (!isset($_GET['slug'])) {
            return 'slug non valorizzato';
        } else {
            $slug = $_GET['slug'];
        }
        if (!isset($_GET['nameAdminister'])) {
            return 'intestazione Email Destinatario non valorizzato';
        } else {
            $nameAdminister = $_GET['nameAdminister'];
        }
        if (!isset($_GET['emailNotify'])) {
            return ' Email Notifica  non valorizzata';
        } else {
            $emailNotify = $_GET['emailNotify'];
        }
        if (!isset($_GET['isActive'])) {
         return ' Attivo non attivo non selezionato';
         }else{
            $isActive = $_GET['isActive'];
        }
        if (!isset($_GET['defaultCpcF'])) {
            return 'Cpc Fornitore Desktop non valorizzato  ';
        }else{
            $defaultCpcF = $_GET['defaultCpcF'];
        }
        if (!isset($_GET['logoFile'])) {
            return 'logo File non Inserito  ';
        }else{
            $logoFile = $_GET['logoFile'];
        }
        if (!isset($_GET['defaultCpcFM'])) {
            return 'Cpc Fornitore Mobile non valorizzato  ';
        }else{
            $defaultCpcFM = $_GET['defaultCpcFM'];
        }
        if (!isset($_GET['defaultCpcM'])) {
            return 'Cpc Default Mobile non valorizzato  ';
        }else{
            $defaultCpcM = $_GET['defaultCpcM'];
        }
        if (!isset($_GET['defaultCpc'])) {
            return 'Cpc Default Desktop non valorizzato  ';
        }else{
            $defaultCpc = $_GET['defaultCpc'];
        }
        if (!isset($_GET['budget01'])) {
            return 'Cpc Default Desktop non valorizzato  ';
        } else {
            $defaultCpc = $_GET['defaultCpc'];
        }


        $budget01 = $_GET['budget01'];
        $budget02 = $_GET['budget02'];
        $budget03 = $_GET['budget03'];
        $budget04 = $_GET['budget04'];
        $budget05 = $_GET['budget05'];
        $budget06 = $_GET['budget06'];
        $budget07 = $_GET['budget07'];
        $budget08 = $_GET['budget08'];
        $budget09 = $_GET['budget09'];
        $budget10 = $_GET['budget10'];
        $budget11 = $_GET['budget11'];
        $budget12 = $_GET['budget12'];
        $typeInsertion = $_GET['typeInsertion'];
        $marketplaceName = $_GET['marketplaceName'];
        $productCategoryIdEx1 = $_GET['productCategoryIdEx1'];
        $productCategoryIdEx2 = $_GET['productCategoryIdEx2'];
        $productCategoryIdEx3 = $_GET['productCategoryIdEx3'];
        $productCategoryIdEx4 = $_GET['productCategoryIdEx4'];
        $productCategoryIdEx5 = $_GET['productCategoryIdEx5'];
        $productCategoryIdEx6 = $_GET['productCategoryIdEx6'];
        $productSizeGroupEx1 = $_GET['productSizeGroupEx1'];
        $productSizeGroupEx2 = $_GET['productSizeGroupEx2'];
        $productSizeGroupEx3 = $_GET['productSizeGroupEx3'];
        $productSizeGroupEx4 = $_GET['productSizeGroupEx4'];
        $productSizeGroupEx5 = $_GET['productSizeGroupEx5'];
        $productSizeGroupEx6 = $_GET['productSizeGroupEx6'];
        $priceModifierRange1 = $_GET['priceModifierRange1'];
        $priceModifierRange2 = $_GET['priceModifierRange2'];
        $priceModifierRange3 = $_GET['priceModifierRange3'];
        $priceModifierRange4 = $_GET['priceModifierRange4'];
        $priceModifierRange5 = $_GET['priceModifierRange5'];
        $range1Cpc = $_GET['range1Cpc'];
        $range2Cpc = $_GET['range2Cpc'];
        $range3Cpc = $_GET['range3Cpc'];
        $range4Cpc = $_GET['range4Cpc'];
        $range5Cpc = $_GET['range5Cpc'];
        $productSizeGroup1 = $_GET['productSizeGroupId1'];
        $productSizeGroup2 = $_GET['productSizeGroupId2'];
        $productSizeGroup3 = $_GET['productSizeGroupId3'];
        $productSizeGroup4 = $_GET['productSizeGroupId4'];
        $productSizeGroup5 = $_GET['productSizeGroupId5'];
        $productCategoryId1 = $_GET['productCategoryId1'];
        $productCategoryId2 = $_GET['productCategoryId2'];
        $productCategoryId3 = $_GET['productCategoryId3'];
        $productCategoryId4 = $_GET['productCategoryId4'];
        $productCategoryId5 = $_GET['productCategoryId5'];
        if ($typeInsertion == 1) {
            $marketplace = $marketplaceRepo->findOneBy(['name' => $marketplaceName]);
            if ($marketplace == null) {
                $marketplaceInsert = $marketplaceRepo->getEmptyEntity();
                $marketplaceInsert->name = $marketplaceName;
                $marketplaceInsert->type = 'cpc';
                $marketplaceInsert->insert();
                $marketplaceFindLastId = $marketplaceRepo->findOneBy(['name' => $marketplaceName]);
                $marketplaceId = $marketplaceFindLastId->id;
            } else {
                return;
            }


        } else {
            $marketplaceId = $marketplaceName;
        }
        $maxCos1 = 0.1;
        $maxCos2 = 0.1;
        $maxCos3 = 0.1;
        $maxCos4 = 0.1;
        $maxCos5 = 0.1;
        $valueexcept1 = 0.1;
        $valueexcept2 = 0.1;
        $valueexcept3 = 0.1;
        $valueexcept4 = 0.1;
        $valueexcept5 = 0.1;
        $filePath = '/export/' . ucfirst($slug) . 'BetterFeedTemp' . $lang . '.xml';
        $feedUrl = '/services/feed/' . $lang . '/' . $slug;
        $timeRange = 7;
        $multiplierDefault = 0.1;
        $priceModifier = 0;


        $collectUpdate = '{"nameAggregator":"' . $marketplace_account_name . '",
                        "lang":"' . $lang . '", 
                        "slug":"' . $slug . '", 
                        "filePath":"' . $filePath . '", 
                        "feedUrl":"' . $feedUrl . '",
                        "logoFile":"' . $logoFile . '",
                        "activeAutomatic":' . $isActive . ',
                        "defaultCpc":' . $defaultCpc . ',
                        "defaultCpcM":' . $defaultCpcM . ',
                        "defaultCpcF":' . $defaultCpcF . ',
                        "defaultCpcFM":' . $defaultCpcFM . ',
                        "timeRange":' . $timeRange . ', 
                        "multiplierDefault":' . $multiplierDefault . ', 
                        "priceModifier":' . $priceModifier . ',
                        "budget01":' . $budget01 . ' ,
                        "budget02":' . $budget02 . ' ,
                        "budget03":' . $budget03 . ' ,
                        "budget04":' . $budget04 . ' ,
                        "budget05":' . $budget05 . ' ,
                        "budget06":' . $budget06 . ' ,
                        "budget07":' . $budget07 . ' ,
                        "budget08":' . $budget08 . ' ,
                        "budget09":' . $budget09 . ' ,
                        "budget10":' . $budget10 . ' ,
                        "budget11":' . $budget11 . ' ,
                        "budget12":' . $budget12 . ' ,
                        "nameAdminister":"' . $nameAdminister . '",
                        "emailNotify":"' . $emailNotify . '",
                        "productSizeGroupEx1":' . $productSizeGroupEx1 . ',
                        "productSizeGroupEx2":' . $productSizeGroupEx2 . ',
                        "productSizeGroupEx3":' . $productSizeGroupEx3 . ',
                        "productSizeGroupEx4":' . $productSizeGroupEx4 . ',
                        "productSizeGroupEx5":' . $productSizeGroupEx5 . ',
                        "productSizeGroupEx6":' . $productSizeGroupEx6 . ',
                        "productCategoryIdEx1":' . $productCategoryIdEx1 . ',
                        "productCategoryIdEx2":' . $productCategoryIdEx2 . ',
                        "productCategoryIdEx3":' . $productCategoryIdEx3 . ',
                        "productCategoryIdEx4":' . $productCategoryIdEx4 . ',
                        "productCategoryIdEx5":' . $productCategoryIdEx5 . ',
                        "productCategoryIdEx6":' . $productCategoryIdEx6 . ',
                        "priceModifierRange1":"' . $priceModifierRange1 . '",
                        "valueexcept1":' . $valueexcept1 . ',
                        "maxCos1":' . $maxCos1 . ',
                        "range1Cpc":' . $range1Cpc . ',
                        "productSizeGroup1":' . $productSizeGroup1 . ',
                        "productCategoryId1":' . $productCategoryId1 . ',
                        "priceModifierRange2":"' . $priceModifierRange2 . '",
                        "valueexcept2":' . $valueexcept2 . ',
                        "maxCos2":' . $maxCos2 . ',
                        "range2Cpc":' . $range2Cpc . ',
                        "productSizeGroup2":' . $productSizeGroup2 . ',
                        "productCategoryId2":' . $productCategoryId2 . ',
                        "priceModifierRange3":"' . $priceModifierRange3 . '",
                        "valueexcept3":' . $valueexcept3 . ',
                        "maxCos3":' . $maxCos3 . ',
                        "range3Cpc":' . $range3Cpc . ',
                        "productSizeGroup3":' . $productSizeGroup3 . ',
                        "productCategoryId3":' . $productCategoryId3 . ',
                        "priceModifierRange4":"' . $priceModifierRange4 . '",
                        "valueexcept4":' . $valueexcept4 . ',
                        "maxCos4":' . $maxCos4 . ',
                        "range4Cpc":' . $range4Cpc . ',
                        "productSizeGroup4":' . $productSizeGroup4 . ',
                        "productCategoryId4":' . $productCategoryId4 . ',
                        "priceModifierRange5":"' . $priceModifierRange5 . '",
                        "valueexcept5":' . $valueexcept5 . ',
                        "maxCos5":' . $maxCos5 . ',
                        "range5Cpc":' . $range5Cpc . ',
                        "productSizeGroup5":' . $productSizeGroup5 . ',
                        "productCategoryId5":' . $productCategoryId5 . '}';

        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketPlaceAccount')->getEmptyEntity();
        $marketplaceAccount->marketplaceId = $marketplaceId;
        $marketplaceAccount->name = $marketplace_account_name;
        $marketplaceAccount->config = $collectUpdate;
        $marketplaceAccount->insert();
        $markeplcaAccountIdFind = $marketplaceAccountRepo->findOneBy(['name' => $marketplace_account_name]);
        $marketplaceAccountId = $markeplcaAccountIdFind->id;
        \Monkey::app()->applicationLog('MarketPlaceAccount','Report','Insert','Insert Marketplace Account ' . $marketplaceAccountId . '-' . $marketplaceId . ' ' . $name);
        return 'Inserimento Eseguito con Successo';
    }
}
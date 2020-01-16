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
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
        $data = $this->app->router->request()->getRequestData();
        if ($_GET['nameAggregator']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Nome Aggregatore non inserito</i>';
        } else {
            $marketplace_account_name = $_GET['nameAggregator'];
        }
        if ($_GET['slug']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">slug non definito</i>';
        } else {
            $slug = $_GET['slug'];
        }

        if ($_GET['lang']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> linguaggio non selezionato</i>';
        } else {
            $lang = $_GET['lang'];
        }
        if ($_GET['shopId']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Shop non valorizzato</i>';
        } else {
            $shopId = $_GET['shopId'];
        }
        if ($_GET['isActive']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai selezionato lo stato aggregatore </i>';
        } else {
            $isActive = $_GET['isActive'];
        }
        if ($_GET['nameAdminister']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> intestazione Email Destinatario non valorizzato</i>';
        } else {
            $nameAdminister = $_GET['nameAdminister'];
        }
        if ($_GET['emailNotify']=='') {
            return  '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Email Notifica  non valorizzata</i>';
        } else {
            $emailNotify = $_GET['emailNotify'];
        }
        if ($_GET['activeAutomatic']=='') {
         return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Usa Fascia non selezionato</i>';
         }else{
            $activeAutomatic = $_GET['activeAutomatic'];
        }
        if ($_GET['logoFile']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Logo non Inserito</i>';
        }else{
            $logoFile = $_GET['logoFile'];
        }

        if ($_GET['defaultCpcFM']=="") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Cpc Fornitore Mobile non Valorizzato</i>';
        }else{
            $defaultCpcFM = $_GET['defaultCpcFM'];
        }
        if ($_GET['defaultCpcF']=="") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Cpc Fornitore Desktop non Valorizzato</i>';
        }else{
            $defaultCpcF = $_GET['defaultCpcF'];
        }
        if ($_GET['defaultCpcM']=="") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Default Cpc Mobile non Valorizzato</i>';
        }else{
            $defaultCpcM = $_GET['defaultCpcM'];
        }
        if ($_GET['defaultCpc']=="") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Default  Cpc Desktop non Valorizzato</i>';
        }else{
            $defaultCpc = $_GET['defaultCpc'];
        }
        if ($_GET['budget01']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Gennaio Valorizzato</i>';
        } else {
            $budget01 = $_GET['budget01'];
        }
        if ($_GET['budget02']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Febbraio Valorizzato</i>';
        } else {
            $budget02 = $_GET['budget02'];
        }
        if ($_GET['budget03']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Marzo Valorizzato</i>';
        } else {
            $budget03 = $_GET['budget03'];
        }
        if ($_GET['budget04']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Aprile Valorizzato</i>';
        } else {
            $budget04 = $_GET['budget04'];
        }
        if ($_GET['budget05']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Maggio Valorizzato</i>';
        } else {
            $budget05 = $_GET['budget05'];
        }
        if ($_GET['budget06']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Giugno Valorizzato</i>';
        } else {
            $budget06 = $_GET['budget06'];
        }
        if ($_GET['budget07']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Luglio Valorizzato</i>';
        } else {
            $budget07 = $_GET['budget07'];
        }
        if ($_GET['budget08']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Agosto Valorizzato</i>';
        } else {
            $budget08 = $_GET['budget08'];
        }
        if ($_GET['budget09']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Settembre Valorizzato</i>';
        } else {
            $budget09 = $_GET['budget09'];
        }
        if ($_GET['budget10']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Ottobre Valorizzato</i>';
        } else {
            $budget10 = $_GET['budget10'];
        }
        if ($_GET['budget11']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Novembre Valorizzato</i>';
        } else {
            $budget11 = $_GET['budget11'];
        }
        if ($_GET['budget12']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Dicembre Valorizzato</i>';
        } else {
            $budget12 = $_GET['budget12'];
        }
        if ($_GET['typeInsertion']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Selezione Aggregatore non eseguita</i>';
        } else {
            $typeInsertion = $_GET['typeInsertion'];
        }
        if ($_GET['marketplaceName']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Aggregatore non valorizzato</i>';
        } else {
            $marketplaceName = $_GET['marketplaceName'];
        }
        if ($_GET['productCategoryIdEx1']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 1 Prodotti non valorizzato </i>';
        } else {
            $productCategoryIdEx1 = $_GET['productCategoryIdEx1'];
        }
        if ($_GET['productCategoryIdEx2']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 2 Prodotti non valorizzato </i>';
        } else {
            $productCategoryIdEx2 = $_GET['productCategoryIdEx2'];
        }
        if ($_GET['productCategoryIdEx3']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 3 Prodotti non valorizzato </i>';
        } else {
            $productCategoryIdEx3 = $_GET['productCategoryIdEx3'];
        }
        if ($_GET['productCategoryIdEx4']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 4 Prodotti non valorizzato </i>';
        } else {
            $productCategoryIdEx4 = $_GET['productCategoryIdEx4'];
        }
        if ($_GET['productCategoryIdEx5']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 5 Prodotti  non valorizzato</i>';
        } else {
            $productCategoryIdEx5 = $_GET['productCategoryIdEx5'];
        }
        if ($_GET['productCategoryIdEx6']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 6 Prodotti  non valorizzato</i>';
        } else {
            $productCategoryIdEx6 = $_GET['productCategoryIdEx6'];
        }
        if ($_GET['productSizeGroupEx1']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 1 Prodotti  non valorizzato </i>';
        } else {
            $productSizeGroupEx1 = $_GET['productSizeGroupEx1'];
        }
        if ($_GET['productSizeGroupEx2']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 2 Prodotti non valorizzato </i>';
        } else {
            $productSizeGroupEx2 = $_GET['productSizeGroupEx2'];
        }
        if ($_GET['productSizeGroupEx3']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 3 Prodotti non valorizzato </i>';
        } else {
            $productSizeGroupEx3 = $_GET['productSizeGroupEx3'];
        }
        if ($_GET['productSizeGroupEx4']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 4 Prodotti non valorizzato </i>';
        } else {
            $productSizeGroupEx4 = $_GET['productSizeGroupEx4'];
        }
        if ($_GET['productSizeGroupEx5']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 5 Prodotti non valorizzato </i>';
        } else {
            $productSizeGroupEx5 = $_GET['productSizeGroupEx5'];
        }
        if ($_GET['productSizeGroupEx6']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 5 Prodotti non valorizzato </i>';
        } else {
            $productSizeGroupEx6 = $_GET['productSizeGroupEx6'];
        }
        if ($_GET['priceModifierRange1']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range prezzo 1 Non valorizzato </i>';
        } else {
            $priceModifierRange1 = $_GET['priceModifierRange1'];
        }
        if ($_GET['priceModifierRange2']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range prezzo 2 Non valorizzato </i>';
        } else {
            $priceModifierRange2 = $_GET['priceModifierRange2'];
        }
        if ($_GET['priceModifierRange3']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range prezzo 3 Non valorizzato </i>';
        } else {
            $priceModifierRange3 = $_GET['priceModifierRange3'];
        }
        if ($_GET['priceModifierRange4']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range prezzo 4 Non valorizzato </i>';
        } else {
            $priceModifierRange4 = $_GET['priceModifierRange4'];
        }
        if ($_GET['priceModifierRange5']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range prezzo 5 Non valorizzato </i>';
        } else {
            $priceModifierRange5 = $_GET['priceModifierRange5'];
        }
        if ($_GET['range1Cpc']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 1 Desktop Non valorizzato </i>';
        } else {
            $range1Cpc = $_GET['range1Cpc'];
        }
        if ($_GET['range2Cpc']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 2 Desktop Non valorizzato </i>';
        } else {
            $range2Cpc = $_GET['range2Cpc'];
        }
        if ($_GET['range3Cpc']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 3 Desktop Non valorizzato </i>';
        } else {
            $range3Cpc = $_GET['range3Cpc'];
        }
        if ($_GET['range4Cpc']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 4 Desktop Non valorizzato </i>';
        } else {
            $range4Cpc = $_GET['range4Cpc'];
        }
        if ($_GET['range5Cpc']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 5 Desktop Non valorizzato </i>';
        } else {
            $range5Cpc = $_GET['range5Cpc'];
        }
        if ($_GET['range1CpcM']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 1 Mobile Non valorizzato </i>';
        } else {
            $range1CpcM = $_GET['range1CpcM'];
        }
        if ($_GET['range2CpcM']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 2 Mobile Non valorizzato </i>';
        } else {
            $range2CpcM = $_GET['range2CpcM'];
        }
        if ($_GET['range3CpcM']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 3 Mobile Non valorizzato </i>';
        } else {
            $range3CpcM = $_GET['range3CpcM'];
        }
        if ($_GET['range4CpcM']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 4 Mobile Non valorizzato </i>';
        } else {
            $range4CpcM = $_GET['range4CpcM'];
        }
        if ($_GET['range5CpcM']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 5 Mobile Non valorizzato </i>';
        } else {
            $range5CpcM = $_GET['range5CpcM'];
        }
        if ($_GET['productSizeGroupId1']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Gruppo Taglia range 1 </i>';
        } else {
            $productSizeGroup1 = $_GET['productSizeGroupId1'];
        }
        if ($_GET['productSizeGroupId2']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Gruppo Taglia range 2 </i>';
        } else {
            $productSizeGroup2 = $_GET['productSizeGroupId2'];
        }
        if ($_GET['productSizeGroupId3']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Gruppo Taglia range 3 </i>';
        } else {
            $productSizeGroup3 = $_GET['productSizeGroupId3'];
        }
        if ($_GET['productSizeGroupId4']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Gruppo Taglia range 4 </i>';
        } else {
            $productSizeGroup4 = $_GET['productSizeGroupId4'];
        }
        if ($_GET['productSizeGroupId5']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Gruppo Taglia range 5 </i>';
        } else {
            $productSizeGroup5 = $_GET['productSizeGroupId5'];
        }
        if ($_GET['productCategoryId1']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Categoria range 1 </i>';
        } else {
            $productCategoryId1 = $_GET['productCategoryId1'];
        }
        if ($_GET['productCategoryId2']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Categoria range 2 </i>';
        } else {
            $productCategoryId2 = $_GET['productCategoryId2'];
        }
        if ($_GET['productCategoryId3']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Categoria range 3 </i>';
        } else {
            $productCategoryId3 = $_GET['productCategoryId3'];
        }
        if ($_GET['productCategoryId4']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Categoria range 4 </i>';
        } else {
            $productCategoryId4 = $_GET['productCategoryId4'];
        }
        if ($_GET['productCategoryId5']=='') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Categoria range 5 </i>';
        } else {
            $productCategoryId5 = $_GET['productCategoryId5'];
        }
        if($_GET['nameRule']==''){
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Nome Regola non Valorizzata </i>';
        }else{
            $nameRule = $_GET['nameRule'];
        }
        if($_GET['ruleOption']==''){
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Nessuna Selezione </i>';
        }else{
            $ruleOption = $_GET['ruleOption'];
        }

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
        $filePath = '/export/' . ucfirst($slug) . 'BetterFeedTemp.' . $lang . '.xml';
        $feedUrl = '/services/feed/' . $lang . '/' . $slug;
        $timeRange = 7;
        $multiplierDefault = 0.1;
        $priceModifier = 0;
        $ruleOption=str_replace('on,','',$ruleOption);


        $collectUpdate = '{"nameAggregator":"' . $marketplace_account_name . '",
                        "lang":"' . $lang . '", 
                        "slug":"' . $slug . '", 
                        "shop":'.$shopId.',
                        "isActive":"'.$isActive.'",
                        "filePath":"' . $filePath . '", 
                        "feedUrl":"' . $feedUrl . '",
                        "logoFile":"' . $logoFile . '",
                        "activeAutomatic":"' . $activeAutomatic . '",
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
                        "range1CpcM":' . $range1CpcM . ',
                        "productSizeGroup1":' . $productSizeGroup1 . ',
                        "productCategoryId1":' . $productCategoryId1 . ',
                        "priceModifierRange2":"' . $priceModifierRange2 . '",
                        "valueexcept2":' . $valueexcept2 . ',
                        "maxCos2":' . $maxCos2 . ',
                        "range2Cpc":' . $range2Cpc . ',
                        "range2CpcM":' . $range2CpcM . ',
                        "productSizeGroup2":' . $productSizeGroup2 . ',
                        "productCategoryId2":' . $productCategoryId2 . ',
                        "priceModifierRange3":"' . $priceModifierRange3 . '",
                        "valueexcept3":' . $valueexcept3 . ',
                        "maxCos3":' . $maxCos3 . ',
                        "range3Cpc":' . $range3Cpc . ',
                        "range3CpcM":' . $range3CpcM . ',
                        "productSizeGroup3":' . $productSizeGroup3 . ',
                        "productCategoryId3":' . $productCategoryId3 . ',
                        "priceModifierRange4":"' . $priceModifierRange4 . '",
                        "valueexcept4":' . $valueexcept4 . ',
                        "maxCos4":' . $maxCos4 . ',
                        "range4Cpc":' . $range4Cpc . ',
                        "range4CpcM":' . $range4CpcM . ',
                        "productSizeGroup4":' . $productSizeGroup4 . ',
                        "productCategoryId4":' . $productCategoryId4 . ',
                        "priceModifierRange5":"' . $priceModifierRange5 . '",
                        "valueexcept5":' . $valueexcept5 . ',
                        "maxCos5":' . $maxCos5 . ',
                        "range5Cpc":' . $range5Cpc . ',
                        "range5CpcM":' . $range5CpcM . ',
                        "productSizeGroup5":' . $productSizeGroup5 . ',
                        "productCategoryId5":' . $productCategoryId5 .',
                        "nameRule":"' . $nameRule .'",
                        "ruleOption":"'.$ruleOption.'"}';
        $collectUpdate=trim($collectUpdate," \t\n\r\0\x0B");
        $findUrlSite=$shopRepo->findOneBy(['id'=>$shopId]);
        if($findUrlSite->urlSite !=null) {
            $urlSite = $findUrlSite->urlSite . '/' . $lang;
        }else{
            $urlSite='';
        }


        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketPlaceAccount')->getEmptyEntity();
        $marketplaceAccount->marketplaceId = $marketplaceId;
        $marketplaceAccount->name = $marketplace_account_name;
        $marketplaceAccount->config = $collectUpdate;
        $marketplaceAccount->urlSite=$urlSite;
        $marketplaceAccount->insert();
        $markeplcaAccountIdFind = $marketplaceAccountRepo->findOneBy(['name' => $marketplace_account_name]);
        $marketplaceAccountId = $markeplcaAccountIdFind->id;
        \Monkey::app()->applicationLog('MarketPlaceAccount','Report','Insert','Insert Marketplace Account ' . $marketplaceAccountId . '-' . $marketplaceId . ' ' . $marketplace_account_name);
        return 'Inserimento Eseguito con Successo';
    }
}
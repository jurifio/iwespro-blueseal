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
        $campaignRepo = \Monkey::app()->repoFactory->create('Campaign');
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $data = $this->app->router->request()->getRequestData();
        if ($_GET['nameAggregator'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Nome Aggregatore non inserito</i>';
        } else {
            $marketplace_account_name = $_GET['nameAggregator'];
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
        if ($_GET['aggregatorHasShopId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Account Aggregatore non valorizzato</i>';
        } else {
            $aggregatorHasShopId = $_GET['aggregatorHasShopId'];
        }
        if ($_GET['isActive'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai selezionato lo stato aggregatore </i>';
        } else {
            $isActive = $_GET['isActive'];
        }
        if ($_GET['isActiveDepublisher'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai selezionato Se attivare  o meno la Depubblicazione </i>';
        } else {
            $isActiveDepublisher = $_GET['isActiveDepublisher'];
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
        if ($_GET['activeAutomatic'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Usa Fascia non selezionato</i>';
        } else {
            $activeAutomatic = $_GET['activeAutomatic'];
        }
        if ($_GET['logoFile'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Logo non Inserito</i>';
        } else {
            $logoFile = $_GET['logoFile'];
        }

        if ($_GET['defaultCpcFM'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Cpc Fornitore Mobile non Valorizzato</i>';
        } else {
            $defaultCpcFM = $_GET['defaultCpcFM'];
        }
        if ($_GET['defaultCpcF'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Cpc Fornitore Desktop non Valorizzato</i>';
        } else {
            $defaultCpcF = $_GET['defaultCpcF'];
        }
        if ($_GET['defaultCpcM'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Default Cpc Mobile non Valorizzato</i>';
        } else {
            $defaultCpcM = $_GET['defaultCpcM'];
        }
        if ($_GET['defaultCpc'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Default  Cpc Desktop non Valorizzato</i>';
        } else {
            $defaultCpc = $_GET['defaultCpc'];
        }
        if ($_GET['budget01'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Gennaio Valorizzato</i>';
        } else {
            $budget01 = $_GET['budget01'];
        }
        if ($_GET['budget02'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Febbraio Valorizzato</i>';
        } else {
            $budget02 = $_GET['budget02'];
        }
        if ($_GET['budget03'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Marzo Valorizzato</i>';
        } else {
            $budget03 = $_GET['budget03'];
        }
        if ($_GET['budget04'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Aprile Valorizzato</i>';
        } else {
            $budget04 = $_GET['budget04'];
        }
        if ($_GET['budget05'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Maggio Valorizzato</i>';
        } else {
            $budget05 = $_GET['budget05'];
        }
        if ($_GET['budget06'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Giugno Valorizzato</i>';
        } else {
            $budget06 = $_GET['budget06'];
        }
        if ($_GET['budget07'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Luglio Valorizzato</i>';
        } else {
            $budget07 = $_GET['budget07'];
        }
        if ($_GET['budget08'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Agosto Valorizzato</i>';
        } else {
            $budget08 = $_GET['budget08'];
        }
        if ($_GET['budget09'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Settembre Valorizzato</i>';
        } else {
            $budget09 = $_GET['budget09'];
        }
        if ($_GET['budget10'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Ottobre Valorizzato</i>';
        } else {
            $budget10 = $_GET['budget10'];
        }
        if ($_GET['budget11'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Novembre Valorizzato</i>';
        } else {
            $budget11 = $_GET['budget11'];
        }
        if ($_GET['budget12'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Dicembre Valorizzato</i>';
        } else {
            $budget12 = $_GET['budget12'];
        }
        if ($_GET['timeRange'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Giorni di depubblicazione non valorizzati</i>';
        } else {
            $timeRange = $_GET['timeRange'];
        }
        if ($_GET['multiplierDefault'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> moltiplicatore di Default non valorizzati</i>';
        } else {
            $multiplierDefault = $_GET['multiplierDefault'];
        }


        if ($_GET['typeInsertionCampaign'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Selezione Campagna non eseguita</i>';
        } else {
            $typeInsertionCampaign = $_GET['typeInsertionCampaign'];
        }
        if ($_GET['marketplaceName'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Aggregatore non valorizzato</i>';
        } else {
            $marketplaceName = $_GET['marketplaceName'];
        }
        if ($_GET['campaignName'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Campagna non valorizzato</i>';
        } else {
            $campaignName = $_GET['campaignName'];
        }
        if ($_GET['productCategoryIdEx1'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 1 Prodotti non valorizzato </i>';
        } else {
            $productCategoryIdEx1 = $_GET['productCategoryIdEx1'];
        }
        if ($_GET['productCategoryIdEx2'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 2 Prodotti non valorizzato </i>';
        } else {
            $productCategoryIdEx2 = $_GET['productCategoryIdEx2'];
        }
        if ($_GET['productCategoryIdEx3'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 3 Prodotti non valorizzato </i>';
        } else {
            $productCategoryIdEx3 = $_GET['productCategoryIdEx3'];
        }
        if ($_GET['productCategoryIdEx4'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 4 Prodotti non valorizzato </i>';
        } else {
            $productCategoryIdEx4 = $_GET['productCategoryIdEx4'];
        }
        if ($_GET['productCategoryIdEx5'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 5 Prodotti  non valorizzato</i>';
        } else {
            $productCategoryIdEx5 = $_GET['productCategoryIdEx5'];
        }
        if ($_GET['productCategoryIdEx6'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 6 Prodotti  non valorizzato</i>';
        } else {
            $productCategoryIdEx6 = $_GET['productCategoryIdEx6'];
        }
        if ($_GET['productSizeGroupEx1'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 1 Prodotti  non valorizzato </i>';
        } else {
            $productSizeGroupEx1 = $_GET['productSizeGroupEx1'];
        }
        if ($_GET['productSizeGroupEx2'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 2 Prodotti non valorizzato </i>';
        } else {
            $productSizeGroupEx2 = $_GET['productSizeGroupEx2'];
        }
        if ($_GET['productSizeGroupEx3'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 3 Prodotti non valorizzato </i>';
        } else {
            $productSizeGroupEx3 = $_GET['productSizeGroupEx3'];
        }
        if ($_GET['productSizeGroupEx4'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 4 Prodotti non valorizzato </i>';
        } else {
            $productSizeGroupEx4 = $_GET['productSizeGroupEx4'];
        }
        if ($_GET['productSizeGroupEx5'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 5 Prodotti non valorizzato </i>';
        } else {
            $productSizeGroupEx5 = $_GET['productSizeGroupEx5'];
        }
        if ($_GET['productSizeGroupEx6'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 5 Prodotti non valorizzato </i>';
        } else {
            $productSizeGroupEx6 = $_GET['productSizeGroupEx6'];
        }
        if ($_GET['priceModifierRange1'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range prezzo 1 Non valorizzato </i>';
        } else {
            $priceModifierRange1 = $_GET['priceModifierRange1'];
        }
        if ($_GET['priceModifierRange2'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range prezzo 2 Non valorizzato </i>';
        } else {
            $priceModifierRange2 = $_GET['priceModifierRange2'];
        }
        if ($_GET['priceModifierRange3'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range prezzo 3 Non valorizzato </i>';
        } else {
            $priceModifierRange3 = $_GET['priceModifierRange3'];
        }
        if ($_GET['priceModifierRange4'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range prezzo 4 Non valorizzato </i>';
        } else {
            $priceModifierRange4 = $_GET['priceModifierRange4'];
        }
        if ($_GET['priceModifierRange5'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range prezzo 5 Non valorizzato </i>';
        } else {
            $priceModifierRange5 = $_GET['priceModifierRange5'];
        }
        if ($_GET['range1Cpc'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 1 Desktop Non valorizzato </i>';
        } else {
            $range1Cpc = $_GET['range1Cpc'];
        }
        if ($_GET['range2Cpc'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 2 Desktop Non valorizzato </i>';
        } else {
            $range2Cpc = $_GET['range2Cpc'];
        }
        if ($_GET['range3Cpc'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 3 Desktop Non valorizzato </i>';
        } else {
            $range3Cpc = $_GET['range3Cpc'];
        }
        if ($_GET['range4Cpc'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 4 Desktop Non valorizzato </i>';
        } else {
            $range4Cpc = $_GET['range4Cpc'];
        }
        if ($_GET['range5Cpc'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 5 Desktop Non valorizzato </i>';
        } else {
            $range5Cpc = $_GET['range5Cpc'];
        }
        if ($_GET['range1CpcM'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 1 Mobile Non valorizzato </i>';
        } else {
            $range1CpcM = $_GET['range1CpcM'];
        }
        if ($_GET['range2CpcM'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 2 Mobile Non valorizzato </i>';
        } else {
            $range2CpcM = $_GET['range2CpcM'];
        }
        if ($_GET['range3CpcM'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 3 Mobile Non valorizzato </i>';
        } else {
            $range3CpcM = $_GET['range3CpcM'];
        }
        if ($_GET['range4CpcM'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 4 Mobile Non valorizzato </i>';
        } else {
            $range4CpcM = $_GET['range4CpcM'];
        }
        if ($_GET['range5CpcM'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 5 Mobile Non valorizzato </i>';
        } else {
            $range5CpcM = $_GET['range5CpcM'];
        }
        if ($_GET['productSizeGroupId1'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Gruppo Taglia range 1 </i>';
        } else {
            $productSizeGroup1 = $_GET['productSizeGroupId1'];
        }
        if ($_GET['productSizeGroupId2'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Gruppo Taglia range 2 </i>';
        } else {
            $productSizeGroup2 = $_GET['productSizeGroupId2'];
        }
        if ($_GET['productSizeGroupId3'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Gruppo Taglia range 3 </i>';
        } else {
            $productSizeGroup3 = $_GET['productSizeGroupId3'];
        }
        if ($_GET['productSizeGroupId4'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Gruppo Taglia range 4 </i>';
        } else {
            $productSizeGroup4 = $_GET['productSizeGroupId4'];
        }
        if ($_GET['productSizeGroupId5'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Gruppo Taglia range 5 </i>';
        } else {
            $productSizeGroup5 = $_GET['productSizeGroupId5'];
        }
        if ($_GET['productCategoryId1'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Categoria range 1 </i>';
        } else {
            $productCategoryId1 = $_GET['productCategoryId1'];
        }
        if ($_GET['productCategoryId2'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Categoria range 2 </i>';
        } else {
            $productCategoryId2 = $_GET['productCategoryId2'];
        }
        if ($_GET['productCategoryId3'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Categoria range 3 </i>';
        } else {
            $productCategoryId3 = $_GET['productCategoryId3'];
        }
        if ($_GET['productCategoryId4'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Categoria range 4 </i>';
        } else {
            $productCategoryId4 = $_GET['productCategoryId4'];
        }
        if ($_GET['productCategoryId5'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Categoria range 5 </i>';
        } else {
            $productCategoryId5 = $_GET['productCategoryId5'];
        }
        if ($_GET['nameRule'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Nome Regola non Valorizzata </i>';
        } else {
            $nameRule = $_GET['nameRule'];
        }
        if ($_GET['ruleOption'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Nessuna Selezione </i>';
        } else {
            $ruleOption = $_GET['ruleOption'];
        }
    $aggregatorHasShop=\Monkey::app()->repoFactory->create('AggregatorHasShop')->findOneBy(['id'=>$aggregatorHasShopId]);
        $marketplaceId=$aggregatorHasShop->marketplaceId;
        $shopId=$aggregatorHasShop->shopId;

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
        $priceModifier = 0;
        $ruleOption = str_replace('on,','',$ruleOption);


        $collectUpdate = '{"nameAggregator":"' . $marketplace_account_name . '","lang":"' . $lang . '","slug":"' . $slug . '","shopId":' . $shopId . ',"aggregatorHasShopId":' . $aggregatorHasShopId . ',"isActive":"' . $isActive . '","filePath":"' . $filePath . '","feedUrl":"' . $feedUrl . '","logoFile":"' . $logoFile . '",';
        $collectUpdate .= ' "activeAutomatic":"' . $activeAutomatic . '","defaultCpc":' . $defaultCpc . ',"defaultCpcM":' . $defaultCpcM . ',"defaultCpcF":' . $defaultCpcF . ',"defaultCpcFM":' . $defaultCpcFM . ',';
        $collectUpdate .= '"timeRange":"' . $timeRange . '","multiplierDefault":"' . $multiplierDefault . '","priceModifier":' . $priceModifier . ',';
        $collectUpdate .= '"budget01":' . $budget01 . ',"budget02":' . $budget02 . ' ,"budget03":' . $budget03 . ' ,"budget04":' . $budget04 . ' ,"budget05":' . $budget05 . ' ,"budget06":' . $budget06 . ' ,';
        $collectUpdate .= '"budget07":' . $budget07 . ' ,"budget08":' . $budget08 . ' ,"budget09":' . $budget09 . ' ,"budget10":' . $budget10 . ' ,"budget11":' . $budget11 . ' ,"budget12":' . $budget12 . ' ,';
        $collectUpdate .= '"nameAdminister":"' . $nameAdminister . '","emailNotify":"' . $emailNotify . '","productSizeGroupEx1":' . $productSizeGroupEx1 . ',"productSizeGroupEx2":' . $productSizeGroupEx2 . ',"productSizeGroupEx3":' . $productSizeGroupEx3 . ',"productSizeGroupEx4":' . $productSizeGroupEx4 . ',"productSizeGroupEx5":' . $productSizeGroupEx5 . ',"productSizeGroupEx6":' . $productSizeGroupEx6 . ',';
        $collectUpdate .= '"isActiveDepublisher":"' . $isActiveDepublisher . '",';
        $collectUpdate .= '"productCategoryIdEx1":' . $productCategoryIdEx1 . ',';
        $collectUpdate .= '"productCategoryIdEx2":' . $productCategoryIdEx2 . ',';
        $collectUpdate .= '"productCategoryIdEx3":' . $productCategoryIdEx3 . ',';
        $collectUpdate .= '"productCategoryIdEx4":' . $productCategoryIdEx4 . ',';
        $collectUpdate .= '"productCategoryIdEx5":' . $productCategoryIdEx5 . ',';
        $collectUpdate .= '"productCategoryIdEx6":' . $productCategoryIdEx6 . ',';
        $collectUpdate .= '"priceModifierRange1":"' . $priceModifierRange1 . '",';
        $collectUpdate .= '"valueexcept1":' . $valueexcept1 . ',';
        $collectUpdate .= '"maxCos1":' . $maxCos1 . ',';
        $collectUpdate .= '"range1Cpc":' . $range1Cpc . ',';
        $collectUpdate .= '"range1CpcM":' . $range1CpcM . ',';
        $collectUpdate .= '"productSizeGroup1":' . $productSizeGroup1 . ',';
        $collectUpdate .= '"productCategoryId1":' . $productCategoryId1 . ',';
        $collectUpdate .= '"priceModifierRange2":"' . $priceModifierRange2 . '",';
        $collectUpdate .= '"valueexcept2":' . $valueexcept2 . ',';
        $collectUpdate .= '"maxCos2":' . $maxCos2 . ',';
        $collectUpdate .= '"range2Cpc":' . $range2Cpc . ',';
        $collectUpdate .= '"range2CpcM":' . $range2CpcM . ',';
        $collectUpdate .= '"productSizeGroup2":' . $productSizeGroup2 . ',';
        $collectUpdate .= '"productCategoryId2":' . $productCategoryId2 . ',';
        $collectUpdate .= '"priceModifierRange3":"' . $priceModifierRange3 . '",';
        $collectUpdate .= '"valueexcept3":' . $valueexcept3 . ',';
        $collectUpdate .= '"maxCos3":' . $maxCos3 . ',';
        $collectUpdate .= '"range3Cpc":' . $range3Cpc . ',';
        $collectUpdate .= '"range3CpcM":' . $range3CpcM . ',';
        $collectUpdate .= '"productSizeGroup3":' . $productSizeGroup3 . ',';
        $collectUpdate .= '"productCategoryId3":' . $productCategoryId3 . ',';
        $collectUpdate .= '"priceModifierRange4":"' . $priceModifierRange4 . '",';
        $collectUpdate .= '"valueexcept4":' . $valueexcept4 . ',';
        $collectUpdate .= '"maxCos4":' . $maxCos4 . ',';
        $collectUpdate .= '"range4Cpc":' . $range4Cpc . ',';
        $collectUpdate .= '"range4CpcM":' . $range4CpcM . ',';
        $collectUpdate .= '"productSizeGroup4":' . $productSizeGroup4 . ',';
        $collectUpdate .= '"productCategoryId4":' . $productCategoryId4 . ',';
        $collectUpdate .= '"priceModifierRange5":"' . $priceModifierRange5 . '",';
        $collectUpdate .= '"valueexcept5":' . $valueexcept5 . ',';
        $collectUpdate .= '"maxCos5":' . $maxCos5 . ',';
        $collectUpdate .= '"range5Cpc":' . $range5Cpc . ',';
        $collectUpdate .= '"range5CpcM":' . $range5CpcM . ',';
        $collectUpdate .= '"productSizeGroup5":' . $productSizeGroup5 . ',';
        $collectUpdate .= '"productCategoryId5":' . $productCategoryId5 . ',';
        $collectUpdate .= '"nameRule":"' . $nameRule . '",';
        $collectUpdate .= '"dateUpdate":"2011-01-01 00:00:00",';
        $collectUpdate .= '"ruleOption":"' . $ruleOption . '"}';
        $collectUpdate = trim($collectUpdate," \t\n\r\0\x0B");
        $findUrlSite = $shopRepo->findOneBy(['id' => $shopId]);
        if ($findUrlSite->urlSite != null) {
            $urlSite = $findUrlSite->urlSite . '/' . $lang;
        } else {
            $urlSite = '';
        }


        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketPlaceAccount')->getEmptyEntity();
        $marketplaceAccount->marketplaceId = $marketplaceId;
        $marketplaceAccount->name = $marketplace_account_name;
        $marketplaceAccount->config = $collectUpdate;
        $marketplaceAccount->urlSite = $urlSite;
        $marketplaceAccount->isActive = $isActive;
        $marketplaceAccount->insert();
        $marketplaceAccountIdFind = $marketplaceAccountRepo->findOneBy(['name' => $marketplace_account_name]);
        $marketplaceAccountId = $marketplaceAccountIdFind->id;
        $campaignUpdate = $campaignRepo->findOneBy(['id' => $campaignId]);
        $campaignUpdate->code = 'MarketplaceAccount' . $marketplaceAccountId . '-' . $marketplaceId;
        $campaignUpdate->marketplaceAccountId = $marketplaceAccountId;
        if ($typeInsertionCampaign == 1) {
            $campaign = $campaignName->findOneBy(['name' => $campaignName]);
            if ($campaign == null) {
                $campaignInsert = $campaignRepo->getEmptyEntity();
                $campaignInsert->name = $campaignName;
                $campaignInsert->defaultCpc = $defaultCpc;
                $campaignInsert->defaultCpcF = $defaultCpcF;
                $campaignInsert->defaultCpcM = $defaultCpcM;
                $campaignInsert->defaultCpcFM = $defaultCpcFM;
                $campaignInsert->remoteShopId = $shopId;
                $campaignInsert->isActive = $isActive;
                $campaignInsert->code = 'MarketplaceAccount' . $marketplaceAccountId . '-' . $marketplaceId;
                $campaignInsert->marketplaceAccountId = $marketplaceAccountId;
                $campaignInsert->marketplaceId = $marketplaceId;
                $campaingInsert->remoteShopId = $shopId;
                $campaignInsert->insert();

            } else {
                return;
            }


        } else {
            $campaignUpdate = $campaignRepo->findOneBy(['id' => $campaignName]);
            $campaignUpdate->marketplaceAccountId = $marketplaceAccountId;
            $campaignUpdate->marketplaceId = $marketplaceId;
            $campaignUpdate->update();
        }


        \Monkey::app()->applicationLog('MarketPlaceAccount','Report','Insert','Insert Marketplace Account ' . $marketplaceAccountId . '-' . $marketplaceId . ' ' . $marketplace_account_name);
        return 'Inserimento Eseguito con Successo';
    }

    public function put()
    {

        $campaignRepo = \Monkey::app()->repoFactory->create('Campaign');
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $data = $this->app->router->request()->getRequestData();
        $dateUpdate = (new \DateTime())->format('Y-m-d H:i:s');
        $marketplaceAccountId = $data['marketplaceAccountId'];
        $marketplaceId = $data['marketplaceId'];
        if ($_GET['nameAggregator'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Nome Aggregatore non inserito</i>';
        } else {
            $marketplace_account_name = $_GET['nameAggregator'];
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
        if ($_GET['aggregatorHasShopId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Aggregatore Account non valorizzato</i>';
        } else {
            $aggregatorHasShopId = $_GET['aggregatorHasShopId'];
        }
        if ($_GET['isActive'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai selezionato lo stato aggregatore </i>';
        } else {
            $isActive = $_GET['isActive'];
        }
        if ($_GET['isActiveDepublisher'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai selezionato Se attivare  o meno la Depubblicazione </i>';
        } else {
            $isActiveDepublisher = $_GET['isActiveDepublisher'];
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
        if ($_GET['activeAutomatic'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Usa Fascia non selezionato</i>';
        } else {
            $activeAutomatic = $_GET['activeAutomatic'];
        }
        if ($_GET['logoFile'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Logo non Inserito</i>';
        } else {
            $logoFile = $_GET['logoFile'];
        }

        if ($_GET['defaultCpcFM'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Cpc Fornitore Mobile non Valorizzato</i>';
        } else {
            $defaultCpcFM = $_GET['defaultCpcFM'];
        }
        if ($_GET['defaultCpcF'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Cpc Fornitore Desktop non Valorizzato</i>';
        } else {
            $defaultCpcF = $_GET['defaultCpcF'];
        }
        if ($_GET['defaultCpcM'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Default Cpc Mobile non Valorizzato</i>';
        } else {
            $defaultCpcM = $_GET['defaultCpcM'];
        }
        if ($_GET['defaultCpc'] == "") {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Default  Cpc Desktop non Valorizzato</i>';
        } else {
            $defaultCpc = $_GET['defaultCpc'];
        }
        if ($_GET['budget01'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Gennaio Valorizzato</i>';
        } else {
            $budget01 = $_GET['budget01'];
        }
        if ($_GET['budget02'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Febbraio Valorizzato</i>';
        } else {
            $budget02 = $_GET['budget02'];
        }
        if ($_GET['budget03'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Marzo Valorizzato</i>';
        } else {
            $budget03 = $_GET['budget03'];
        }
        if ($_GET['budget04'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Aprile Valorizzato</i>';
        } else {
            $budget04 = $_GET['budget04'];
        }
        if ($_GET['budget05'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Maggio Valorizzato</i>';
        } else {
            $budget05 = $_GET['budget05'];
        }
        if ($_GET['budget06'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Giugno Valorizzato</i>';
        } else {
            $budget06 = $_GET['budget06'];
        }
        if ($_GET['budget07'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Luglio Valorizzato</i>';
        } else {
            $budget07 = $_GET['budget07'];
        }
        if ($_GET['budget08'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Agosto Valorizzato</i>';
        } else {
            $budget08 = $_GET['budget08'];
        }
        if ($_GET['budget09'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Settembre Valorizzato</i>';
        } else {
            $budget09 = $_GET['budget09'];
        }
        if ($_GET['budget10'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Ottobre Valorizzato</i>';
        } else {
            $budget10 = $_GET['budget10'];
        }
        if ($_GET['budget11'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Novembre Valorizzato</i>';
        } else {
            $budget11 = $_GET['budget11'];
        }
        if ($_GET['budget12'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> budget Dicembre Valorizzato</i>';
        } else {
            $budget12 = $_GET['budget12'];
        }

        if ($_GET['campaignName'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Campagna non valorizzato</i>';
        } else {
            $campaignName = $_GET['campaignName'];
        }
        if ($_GET['productCategoryIdEx1'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 1 Prodotti non valorizzato </i>';
        } else {
            $productCategoryIdEx1 = $_GET['productCategoryIdEx1'];
        }
        if ($_GET['productCategoryIdEx2'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 2 Prodotti non valorizzato </i>';
        } else {
            $productCategoryIdEx2 = $_GET['productCategoryIdEx2'];
        }
        if ($_GET['productCategoryIdEx3'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 3 Prodotti non valorizzato </i>';
        } else {
            $productCategoryIdEx3 = $_GET['productCategoryIdEx3'];
        }
        if ($_GET['productCategoryIdEx4'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 4 Prodotti non valorizzato </i>';
        } else {
            $productCategoryIdEx4 = $_GET['productCategoryIdEx4'];
        }
        if ($_GET['productCategoryIdEx5'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 5 Prodotti  non valorizzato</i>';
        } else {
            $productCategoryIdEx5 = $_GET['productCategoryIdEx5'];
        }
        if ($_GET['productCategoryIdEx6'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione categoria 6 Prodotti  non valorizzato</i>';
        } else {
            $productCategoryIdEx6 = $_GET['productCategoryIdEx6'];
        }
        if ($_GET['productSizeGroupEx1'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 1 Prodotti  non valorizzato </i>';
        } else {
            $productSizeGroupEx1 = $_GET['productSizeGroupEx1'];
        }
        if ($_GET['productSizeGroupEx2'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 2 Prodotti non valorizzato </i>';
        } else {
            $productSizeGroupEx2 = $_GET['productSizeGroupEx2'];
        }
        if ($_GET['productSizeGroupEx3'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 3 Prodotti non valorizzato </i>';
        } else {
            $productSizeGroupEx3 = $_GET['productSizeGroupEx3'];
        }
        if ($_GET['productSizeGroupEx4'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 4 Prodotti non valorizzato </i>';
        } else {
            $productSizeGroupEx4 = $_GET['productSizeGroupEx4'];
        }
        if ($_GET['productSizeGroupEx5'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 5 Prodotti non valorizzato </i>';
        } else {
            $productSizeGroupEx5 = $_GET['productSizeGroupEx5'];
        }
        if ($_GET['productSizeGroupEx6'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Esclusione Gruppo Taglia 5 Prodotti non valorizzato </i>';
        } else {
            $productSizeGroupEx6 = $_GET['productSizeGroupEx6'];
        }
        if ($_GET['priceModifierRange1'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range prezzo 1 Non valorizzato </i>';
        } else {
            $priceModifierRange1 = $_GET['priceModifierRange1'];
        }
        if ($_GET['priceModifierRange2'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range prezzo 2 Non valorizzato </i>';
        } else {
            $priceModifierRange2 = $_GET['priceModifierRange2'];
        }
        if ($_GET['priceModifierRange3'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range prezzo 3 Non valorizzato </i>';
        } else {
            $priceModifierRange3 = $_GET['priceModifierRange3'];
        }
        if ($_GET['priceModifierRange4'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range prezzo 4 Non valorizzato </i>';
        } else {
            $priceModifierRange4 = $_GET['priceModifierRange4'];
        }
        if ($_GET['priceModifierRange5'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range prezzo 5 Non valorizzato </i>';
        } else {
            $priceModifierRange5 = $_GET['priceModifierRange5'];
        }
        if ($_GET['range1Cpc'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 1 Desktop Non valorizzato </i>';
        } else {
            $range1Cpc = $_GET['range1Cpc'];
        }
        if ($_GET['range2Cpc'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 2 Desktop Non valorizzato </i>';
        } else {
            $range2Cpc = $_GET['range2Cpc'];
        }
        if ($_GET['range3Cpc'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 3 Desktop Non valorizzato </i>';
        } else {
            $range3Cpc = $_GET['range3Cpc'];
        }
        if ($_GET['range4Cpc'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 4 Desktop Non valorizzato </i>';
        } else {
            $range4Cpc = $_GET['range4Cpc'];
        }
        if ($_GET['range5Cpc'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 5 Desktop Non valorizzato </i>';
        } else {
            $range5Cpc = $_GET['range5Cpc'];
        }
        if ($_GET['range1CpcM'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 1 Mobile Non valorizzato </i>';
        } else {
            $range1CpcM = $_GET['range1CpcM'];
        }
        if ($_GET['range2CpcM'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 2 Mobile Non valorizzato </i>';
        } else {
            $range2CpcM = $_GET['range2CpcM'];
        }
        if ($_GET['range3CpcM'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 3 Mobile Non valorizzato </i>';
        } else {
            $range3CpcM = $_GET['range3CpcM'];
        }
        if ($_GET['range4CpcM'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 4 Mobile Non valorizzato </i>';
        } else {
            $range4CpcM = $_GET['range4CpcM'];
        }
        if ($_GET['range5CpcM'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Range CPC 5 Mobile Non valorizzato </i>';
        } else {
            $range5CpcM = $_GET['range5CpcM'];
        }
        if ($_GET['productSizeGroupId1'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Gruppo Taglia range 1 </i>';
        } else {
            $productSizeGroup1 = $_GET['productSizeGroupId1'];
        }
        if ($_GET['productSizeGroupId2'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Gruppo Taglia range 2 </i>';
        } else {
            $productSizeGroup2 = $_GET['productSizeGroupId2'];
        }
        if ($_GET['productSizeGroupId3'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Gruppo Taglia range 3 </i>';
        } else {
            $productSizeGroup3 = $_GET['productSizeGroupId3'];
        }
        if ($_GET['productSizeGroupId4'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Gruppo Taglia range 4 </i>';
        } else {
            $productSizeGroup4 = $_GET['productSizeGroupId4'];
        }
        if ($_GET['productSizeGroupId5'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Gruppo Taglia range 5 </i>';
        } else {
            $productSizeGroup5 = $_GET['productSizeGroupId5'];
        }
        if ($_GET['productCategoryId1'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Categoria range 1 </i>';
        } else {
            $productCategoryId1 = $_GET['productCategoryId1'];
        }
        if ($_GET['productCategoryId2'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Categoria range 2 </i>';
        } else {
            $productCategoryId2 = $_GET['productCategoryId2'];
        }
        if ($_GET['productCategoryId3'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Categoria range 3 </i>';
        } else {
            $productCategoryId3 = $_GET['productCategoryId3'];
        }
        if ($_GET['productCategoryId4'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Categoria range 4 </i>';
        } else {
            $productCategoryId4 = $_GET['productCategoryId4'];
        }
        if ($_GET['productCategoryId5'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Inclusione Categoria range 5 </i>';
        } else {
            $productCategoryId5 = $_GET['productCategoryId5'];
        }
        if ($_GET['nameRule'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Nome Regola non Valorizzata </i>';
        } else {
            $nameRule = $_GET['nameRule'];
        }
        if ($_GET['ruleOption'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Nessuna Selezione </i>';
        } else {
            $ruleOption = $_GET['ruleOption'];
        }
        if ($_GET['maxCos1'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> maxCos1 no Valorizzato</i>';
        } else {
            $maxCos1 = $_GET['maxCos1'];
        }
        if ($_GET['maxCos2'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> maxCos2 no Valorizzato</i>';
        } else {
            $maxCos2 = $_GET['maxCos2'];
        }
        if ($_GET['maxCos3'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> maxCos3 no Valorizzato</i>';
        } else {
            $maxCos3 = $_GET['maxCos3'];
        }
        if ($_GET['maxCos4'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> maxCos4 no Valorizzato</i>';
        } else {
            $maxCos4 = $_GET['maxCos4'];
        }
        if ($_GET['maxCos5'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> maxCos5 no Valorizzato</i>';
        } else {
            $maxCos5 = $_GET['maxCos5'];
        }

        if ($_GET['valueexcept1'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Moltiplicatore 1 non Valorizzato</i>';
        } else {
            $valueexcept1 = $_GET['valueexcept1'];
        }
        if ($_GET['valueexcept2'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Moltiplicatore 2  non Valorizzato</i>';
        } else {
            $valueexcept2 = $_GET['valueexcept2'];
        }
        if ($_GET['valueexcept3'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Moltiplicatore 3 non Valorizzato</i>';
        } else {
            $valueexcept3 = $_GET['valueexcept3'];
        }
        if ($_GET['valueexcept4'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Moltiplicatore 4 non Valorizzato</i>';
        } else {
            $valueexcept4 = $_GET['valueexcept4'];
        }
        if ($_GET['valueexcept5'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Moltiplicatore 5 non Valorizzato</i>';
        } else {
            $valueexcept5 = $_GET['valueexcept5'];
        }
        if ($_GET['multiplierDefault'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Moltiplicatore di default non Valorizzato</i>';
        } else {
            $multiplierDefault = $_GET['multiplierDefault'];
        }
        if ($_GET['timeRange'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> periodo di giorni di calcolo non Valorizzato</i>';
        } else {
            $timeRange = $_GET['timeRange'];
        }
        $aggregatorHasShop=\Monkey::app()->repoFactory->create('AggregatorHasShop')->findOneBy(['id'=>$aggregatorHasShopId]);
        $marketplaceId=$aggregatorHasShop->marketplaceId;
        $shopId=$aggregatorHasShop->shopId;



        $filePath = '/export/' . ucfirst($slug) . 'BetterFeedTemp.' . $lang . '.xml';
        $feedUrl = '/services/feed/' . $lang . '/' . $slug;
        $priceModifier = 0;
        $ruleOption = str_replace('on,','',$ruleOption);



        $collectUpdate = '{"nameAggregator":"' . $marketplace_account_name . '","lang":"' . $lang . '","slug":"' . $slug . '","shopId":' . $shopId . ',"aggregatorHasShopId":' . $aggregatorHasShopId . ',"isActive":"' . $isActive . '","filePath":"' . $filePath . '","feedUrl":"' . $feedUrl . '","logoFile":"' . $logoFile . '",';
        $collectUpdate .= ' "activeAutomatic":"' . $activeAutomatic . '","defaultCpc":' . $defaultCpc . ',"defaultCpcM":' . $defaultCpcM . ',"defaultCpcF":' . $defaultCpcF . ',"defaultCpcFM":' . $defaultCpcFM . ',';
        $collectUpdate .= '"timeRange":"' . $timeRange . '","multiplierDefault":"' . $multiplierDefault . '","priceModifier":' . $priceModifier . ',';
        $collectUpdate .= '"budget01":' . $budget01 . ',"budget02":' . $budget02 . ' ,"budget03":' . $budget03 . ' ,"budget04":' . $budget04 . ' ,"budget05":' . $budget05 . ' ,"budget06":' . $budget06 . ' ,';
        $collectUpdate .= '"budget07":' . $budget07 . ' ,"budget08":' . $budget08 . ' ,"budget09":' . $budget09 . ' ,"budget10":' . $budget10 . ' ,"budget11":' . $budget11 . ' ,"budget12":' . $budget12 . ' ,';
        $collectUpdate .= '"nameAdminister":"' . $nameAdminister . '","emailNotify":"' . $emailNotify . '","productSizeGroupEx1":' . $productSizeGroupEx1 . ',"productSizeGroupEx2":' . $productSizeGroupEx2 . ',"productSizeGroupEx3":' . $productSizeGroupEx3 . ',"productSizeGroupEx4":' . $productSizeGroupEx4 . ',"productSizeGroupEx5":' . $productSizeGroupEx5 . ',"productSizeGroupEx6":' . $productSizeGroupEx6 . ',';
        $collectUpdate .= '"isActiveDepublisher":"' . $isActiveDepublisher . '",';
        $collectUpdate .= '"productCategoryIdEx1":' . $productCategoryIdEx1 . ',';
        $collectUpdate .= '"productCategoryIdEx2":' . $productCategoryIdEx2 . ',';
        $collectUpdate .= '"productCategoryIdEx3":' . $productCategoryIdEx3 . ',';
        $collectUpdate .= '"productCategoryIdEx4":' . $productCategoryIdEx4 . ',';
        $collectUpdate .= '"productCategoryIdEx5":' . $productCategoryIdEx5 . ',';
        $collectUpdate .= '"productCategoryIdEx6":' . $productCategoryIdEx6 . ',';
        $collectUpdate .= '"priceModifierRange1":"' . $priceModifierRange1 . '",';
        $collectUpdate .= '"valueexcept1":' . $valueexcept1 . ',';
        $collectUpdate .= '"maxCos1":' . $maxCos1 . ',';
        $collectUpdate .= '"range1Cpc":' . $range1Cpc . ',';
        $collectUpdate .= '"range1CpcM":' . $range1CpcM . ',';
        $collectUpdate .= '"productSizeGroup1":' . $productSizeGroup1 . ',';
        $collectUpdate .= '"productCategoryId1":' . $productCategoryId1 . ',';
        $collectUpdate .= '"priceModifierRange2":"' . $priceModifierRange2 . '",';
        $collectUpdate .= '"valueexcept2":' . $valueexcept2 . ',';
        $collectUpdate .= '"maxCos2":' . $maxCos2 . ',';
        $collectUpdate .= '"range2Cpc":' . $range2Cpc . ',';
        $collectUpdate .= '"range2CpcM":' . $range2CpcM . ',';
        $collectUpdate .= '"productSizeGroup2":' . $productSizeGroup2 . ',';
        $collectUpdate .= '"productCategoryId2":' . $productCategoryId2 . ',';
        $collectUpdate .= '"priceModifierRange3":"' . $priceModifierRange3 . '",';
        $collectUpdate .= '"valueexcept3":' . $valueexcept3 . ',';
        $collectUpdate .= '"maxCos3":' . $maxCos3 . ',';
        $collectUpdate .= '"range3Cpc":' . $range3Cpc . ',';
        $collectUpdate .= '"range3CpcM":' . $range3CpcM . ',';
        $collectUpdate .= '"productSizeGroup3":' . $productSizeGroup3 . ',';
        $collectUpdate .= '"productCategoryId3":' . $productCategoryId3 . ',';
        $collectUpdate .= '"priceModifierRange4":"' . $priceModifierRange4 . '",';
        $collectUpdate .= '"valueexcept4":' . $valueexcept4 . ',';
        $collectUpdate .= '"maxCos4":' . $maxCos4 . ',';
        $collectUpdate .= '"range4Cpc":' . $range4Cpc . ',';
        $collectUpdate .= '"range4CpcM":' . $range4CpcM . ',';
        $collectUpdate .= '"productSizeGroup4":' . $productSizeGroup4 . ',';
        $collectUpdate .= '"productCategoryId4":' . $productCategoryId4 . ',';
        $collectUpdate .= '"priceModifierRange5":"' . $priceModifierRange5 . '",';
        $collectUpdate .= '"valueexcept5":' . $valueexcept5 . ',';
        $collectUpdate .= '"maxCos5":' . $maxCos5 . ',';
        $collectUpdate .= '"range5Cpc":' . $range5Cpc . ',';
        $collectUpdate .= '"range5CpcM":' . $range5CpcM . ',';
        $collectUpdate .= '"productSizeGroup5":' . $productSizeGroup5 . ',';
        $collectUpdate .= '"productCategoryId5":' . $productCategoryId5 . ',';
        $collectUpdate .= '"dateUpdate":"' . $dateUpdate . '",';
        $collectUpdate .= '"nameRule":"' . $nameRule . '",';
        $collectUpdate .= '"ruleOption":"' . $ruleOption . '"}';
        $collectUpdate = trim($collectUpdate,"\t\n\r\0\x0B");
        $findUrlSite = $shopRepo->findOneBy(['id' => $shopId]);
        if ($findUrlSite->urlSite != null) {
            $urlSite = $findUrlSite->urlSite . '/' . $lang;
        } else {
            $urlSite = '';
        }


        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneBy(['id' => $marketplaceAccountId,'marketplaceId' => $marketplaceId]);
        $marketplaceAccount->marketplaceId = $marketplaceId;
        $marketplaceAccount->name = $marketplace_account_name;
        $marketplaceAccount->config = $collectUpdate;
        $marketplaceAccount->urlSite = $urlSite;
        $marketplaceAccount->isActive = $isActive;
        $marketplaceAccount->update();


            $campaignUpdate = $campaignRepo->findOneBy(['name' => $campaignName]);
            $campaignUpdate->marketplaceAccountId = $marketplaceAccountId;
            $campaignUpdate->marketplaceId = $marketplaceId;
            $campaignUpdate->update();



        \Monkey::app()->applicationLog('MarketPlaceAccount','Report','Insert','Update Marketplace Account ' . $marketplaceAccountId . '-' . $marketplaceId . ' ' . $marketplace_account_name);
        return 'Modifica Eseguito con Successo';
    }

}
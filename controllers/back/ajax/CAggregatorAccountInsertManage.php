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
class CAggregatorAccountInsertManage extends AAjaxController
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
        $activeAutomatic=1;

        $collectUpdate = '{"nameAggregator":"' . $marketplace_account_name . '","lang":"' . $lang . '","slug":"' . $slug . '","shopId":' . $shopId . ',"aggregatorHasShopId":' . $aggregatorHasShopId . ',"isActive":"' . $isActive . '","filePath":"' . $filePath . '","feedUrl":"' . $feedUrl . '","logoFile":"' . $logoFile . '",';
        $collectUpdate .= ' "activeAutomatic":"' . $activeAutomatic . '",';
        $collectUpdate .= '"nameAdminister":"' . $nameAdminister . '","emailNotify":"' . $emailNotify . '",';
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
        $campaignUpdate = $campaignRepo->findOneBy(['id' => $campaignName]);
        $campaignUpdate->code = 'MarketplaceAccount' . $marketplaceAccountId . '-' . $marketplaceId;
        $campaignUpdate->marketplaceAccountId = $marketplaceAccountId;
        if ($typeInsertionCampaign == 1) {
            $campaign = $campaignName->findOneBy(['name' => $campaignName]);
            if ($campaign == null) {
                $campaignInsert = $campaignRepo->getEmptyEntity();
                $campaignInsert->name = $campaignName;
                $campaignInsert->defaultCpc = 0;
                $campaignInsert->defaultCpcF = 0;
                $campaignInsert->defaultCpcM = 0;
                $campaignInsert->defaultCpcFM = 0;
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
        $marketplaceAccountId = $_GET['marketplaceAccountId'];
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

            $activeAutomatic = 1;

        if ($_GET['logoFile'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Logo non Inserito</i>';
        } else {
            $logoFile = $_GET['logoFile'];
        }



        if ($_GET['campaignName'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Campagna non valorizzato</i>';
        } else {
            $campaignName = $_GET['campaignName'];
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



        $filePath = '/export/' . ucfirst($slug) . 'BetterFeedTemp.' . $lang . '.xml';
        $feedUrl = '/services/feed/' . $lang . '/' . $slug;
        $priceModifier = 0;
        $ruleOption = str_replace('on,','',$ruleOption);



        $collectUpdate = '{"nameAggregator":"' . $marketplace_account_name . '","lang":"' . $lang . '","slug":"' . $slug . '","shopId":' . $shopId . ',"aggregatorHasShopId":' . $aggregatorHasShopId . ',"isActive":"' . $isActive . '","filePath":"' . $filePath . '","feedUrl":"' . $feedUrl . '","logoFile":"' . $logoFile . '",';
        $collectUpdate .= ' "activeAutomatic":"' . $activeAutomatic . '",';
        $collectUpdate .= '"nameAdminister":"' . $nameAdminister . '","emailNotify":"' . $emailNotify . '",';
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


        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketPlaceAccount')->findOneBy(['id' => $marketplaceAccountId,'marketplaceId' => $marketplaceId]);
        $marketplaceAccount->marketplaceId = $marketplaceId;
        $marketplaceAccount->name = $marketplace_account_name;
        $marketplaceAccount->config = $collectUpdate;
        $marketplaceAccount->urlSite = $urlSite;
        $marketplaceAccount->isActive = $isActive;
        $marketplaceAccount->update();

            $campaignUpdate = $campaignRepo->findOneBy(['id' => $campaignName]);
            $campaignUpdate->marketplaceAccountId = $marketplaceAccountId;
            $campaignUpdate->marketplaceId = $marketplaceId;
            $campaignUpdate->update();



        \Monkey::app()->applicationLog('MarketPlaceAccount','Report','Insert','Update Marketplace Account ' . $marketplaceAccountId . '-' . $marketplaceId . ' ' . $marketplace_account_name);
        return 'Modifica Eseguito con Successo';
    }

}
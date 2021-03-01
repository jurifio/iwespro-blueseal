<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CAddressBook;


/**
 * Class CMarketplaceAccountShopInsertManage
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
class CMarketplaceAccountShopInsertManage extends AAjaxController
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
        if ($_GET['shopId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">shop non selezionato</i>';
        } else {
            $slug = $_GET['shopId'];
        }

        if ($_GET['isActive'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai selezionato lo stato Marketplace Account </i>';
        } else {
            $isActive = $_GET['isActive'];
        }

        if ($_GET['logoFile'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Logo non Inserito</i>';
        } else {
            $logoFile = $_GET['logoFile'];
        }

        $marketplaceFind = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['shopId' => $shopId,'marketplaceId'=>$marketplaceId]);
        if($marketplaceFind){
            return 'Esiste gia il marketplace Account per lo shop selezionato';
        }else{
            $marketplaceInsert=\Monkey::app()->repoFactory->create('MarketplaceHasShop')->getEmptyEntity();
            $marketplaceInsert->name=$marketplace_account_name;
            $marketplaceInsert->marketplaceId=$marketplaceId;
            $marketplaceInsert->shopId=$shopId;
            $marketplaceInsert->typeSync=1;
            $marketplaceInsert->imgMarketplace=$logoFile;
            $marketplaceInsert->isPriceHub=1;
            $marketplaceInsert->isActive=$isActive;
            $marketplaceInsert->insert();
            $marketplaceUpdate=\Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['shopId' => $shopId,'marketplaceId'=>$marketplaceId]);
            $marketplaceId=$marketplaceUpdate->id;
            $marketplaceUpdate->prestashopId=$marketplaceId;
            $marketplaceUpdate->update();
            \Monkey::app()->applicationLog('MarketPlaceAccountShopInsert','Report','Insert','Insert Marketplace Account HasShop ' . $marketplaceId . ' ' . $marketplace_account_name);
            return 'Creazione MarketplaceAccount '.$marketplace_account_name.' con '.$marketplaceId;

        }

    }


    public function put()
    {

        $data = $this->app->router->request()->getRequestData();
        $marketplaceHasShopId = $_GET['marketplaceHasShopId'];
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
        if ($_GET['shopId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">shop non selezionato</i>';
        } else {
            $shopId = $_GET['shopId'];
        }

        if ($_GET['isActive'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai selezionato lo stato Marketplace Account </i>';
        } else {
            $isActive = $_GET['isActive'];
        }

        if ($_GET['logoFile'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Logo non Inserito</i>';
        } else {
            $logoFile = $_GET['logoFile'];
        }

        $marketplaceFind = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['id' => $marketplaceHasShopId]);
        $mp = \Monkey::app()->repoFactory->create('Marketplace')->findOneBy(['id' => $marketplaceFind->marketplaceId]);
        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findBy(['marketplaceId' => $mp->id]);
        foreach ($marketplaceAccount as $mpa) {
            if (isset($mpa->config['marketplaceHasShopId']) == $marketplaceHasShopId) {

                $marketplaceAccountId = $mpa->id;
                $marketplaceId = $mpa->marketplaceId;
                $slugJob = $marketplaceAccountId . '-' . $marketplaceId;

                break;
            } else {
                continue;
            }
        }
        if ($marketplaceFind) {

            $marketplaceFind->name = $marketplace_account_name;
            $marketplaceFind->marketplaceId = $marketplaceId;
            $marketplaceFind->shopId = $shopId;
            $marketplaceFind->imgMarketplace = $logoFile;
            if ($isActive == '1') {
                $markeplaceAccountFind=\Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneBy(['id'=>$marketplaceAccountId,'marketplaceId'=>$marketplaceId]);
                $markeplaceAccountFind->isActive=1;
                $markeplaceAccountFind->update();
            } else {
                $markeplaceAccountFind=\Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneBy(['id'=>$marketplaceAccountId,'marketplaceId'=>$marketplaceId]);
                $markeplaceAccountFind->isActive=0;
                $markeplaceAccountFind->update();
            }
            $marketplaceFind->isActive = $isActive;
            $marketplaceFind->update();

            \Monkey::app()->applicationLog('MarketPlaceAccountShopInsert','Report','update','update Marketplace Account HasShop ' . $marketplaceId . ' ' . $marketplace_account_name);
            return 'Creazione MarketplaceAccount ' . $marketplace_account_name . ' con ' . $marketplaceId;
        }else{
            return 'problemi con la modifica del marketplace Account';
        }
    }

    public function delete()
    {
        $id = \Monkey::app()->router->request()->getRequestData('id');
        $marketplaceHasShop = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['id' => $id]);
        $marketplaceHasShop->delete();
        return 'MarketplaceAccount Cancellato definitivamente ricordati di cancellare le regole ';

    }

}
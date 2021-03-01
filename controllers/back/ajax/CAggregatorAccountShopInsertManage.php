<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CAddressBook;


/**
 * Class CAggregatorAccountShopInsertManage
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
class CAggregatorAccountShopInsertManage extends AAjaxController
{

    public function post()
    {

        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $data = $this->app->router->request()->getRequestData();
        if ($_GET['nameMarketPlace'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Nome Aggregatore non inserito</i>';
        } else {
            $marketplace_account_name = $_GET['nameMarketPlace'];
        }
        if ($_GET['marketplaceId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Aggregatore non Selezionato</i>';
        } else {
            $marketplaceId = $_GET['marketplaceId'];
        }
        if ($_GET['shopId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">shop non selezionato</i>';
        } else {
            $shopId = $_GET['shopId'];
        }

        if ($_GET['isActive'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai selezionato lo stato aggregatore Account </i>';
        } else {
            $isActive = $_GET['isActive'];
        }

        if ($_GET['logoFile'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Logo non Inserito</i>';
        } else {
            $logoFile = $_GET['logoFile'];
        }

        $AggregatorFind = \Monkey::app()->repoFactory->create('AggregatorHasShop')->findOneBy(['shopId' => $shopId,'marketplaceId' => $marketplaceId]);
        if ($AggregatorFind) {
            return 'Esiste gia un aggregatore Account per lo shop selezionato';
        } else {
            $aggregatorInsert = \Monkey::app()->repoFactory->create('AggregatorHasShop')->getEmptyEntity();
            $aggregatorInsert->name = $marketplace_account_name;
            $aggregatorInsert->marketplaceId = $marketplaceId;
            $aggregatorInsert->shopId = $shopId;
            $aggregatorInsert->typeSync = 1;
            $aggregatorInsert->imgAggregator = $logoFile;
            $aggregatorInsert->isPriceHub = 1;
            $aggregatorInsert->isActive = $isActive;
            $aggregatorInsert->insert();
            $aggregatorUpdate = \Monkey::app()->repoFactory->create('AggregatorHasShop')->findOneBy(['shopId' => $shopId,'marketplaceId' => $marketplaceId]);
            $marketplaceId = $aggregatorUpdate->id;
            $aggregatorUpdate->update();
            \Monkey::app()->applicationLog('CAggregatorAccountShopInsertManage','Report','Insert','Insert Aggregator Account HasShop ' . $marketplaceId . ' ' . $marketplace_account_name);
            return 'Creazione aggregatore Account ' . $marketplace_account_name . ' con ' . $marketplaceId;

        }

    }


    public function put()
    {

        $data = $this->app->router->request()->getRequestData();
        $marketplaceHasShopId = $_GET['marketplaceHasShopId'];
        if ($_GET['nameMarketPlace'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Nome Aggregatore non inserito</i>';
        } else {
            $marketplace_account_name = $_GET['nameMarketPlace'];
        }
        if ($_GET['marketplaceId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Aggregatore non Selezionato</i>';
        } else {
            $marketplaceId = $_GET['marketplaceId'];
        }
        if ($_GET['shopId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">shop non selezionato</i>';
        } else {
            $shopId = $_GET['shopId'];
        }

        if ($_GET['isActive'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Non hai selezionato lo stato aggregatore </i>';
        } else {
            $isActive = $_GET['isActive'];
        }

        if ($_GET['logoFile'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Logo non Inserito</i>';
        } else {
            $logoFile = $_GET['logoFile'];
        }

        $aggregatorFind = \Monkey::app()->repoFactory->create('AggregatorHasShop')->findOneBy(['id' => $marketplaceHasShopId]);
        $mp = \Monkey::app()->repoFactory->create('Marketplace')->findOneBy(['id' => $aggregatorFind->marketplaceId]);
        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findBy(['marketplaceId' => $mp->id]);
        foreach ($marketplaceAccount as $mpa) {
            if ($mpa->config['aggregatorHasShopId'] == $marketplaceHasShopId) {

                $marketplaceAccountId = $mpa->id;
                $slugJob = $marketplaceAccountId . '-' . $marketplaceHasShopId;
                $marketplaceId = $mpa->marketplaceId;
                break;
            } else {
                continue;
            }
        }

        if ($aggregatorFind) {

            $aggregatorFind->name = $marketplace_account_name;
            $aggregatorFind->marketplaceId = $marketplaceId;
            $aggregatorFind->shopId = $shopId;
            $aggregatorFind->imgAggregator = $logoFile;
            $aggregatorFind->isActive = $isActive;
            if ($isActive == '1') {
                $jobs = \Monkey::app()->repoFactory->create('Job')->findBy(['scope' => 'feedAggregator']);
                foreach ($jobs as $job) {
                    if ($job->defaultArgs['marketplaceAccountId'] == $slugJob) {
                        $job->isActive = 1;
                        $job->update();
                        break;
                    } else {
                        continue;
                    }
                }
                $markeplaceAccountFind=\Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneBy(['id'=>$marketplaceAccountId,'marketplaceId'=>$marketplaceId]);
                $markeplaceAccountFind->isActive=1;
                $markeplaceAccountFind->update();
            } else {
                $jobs = \Monkey::app()->repoFactory->create('Job')->findBy(['scope' => 'feedAggregator']);
                foreach ($jobs as $job) {
                    if ($job->defaultArgs['marketplaceAccountId'] == $slugJob) {
                        $job->isActive = 0;
                        $job->update();
                        break;
                    } else {
                        continue;
                    }
                }
                $markeplaceAccountFind=\Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneBy(['id'=>$marketplaceAccountId,'marketplaceId'=>$marketplaceId]);
                $markeplaceAccountFind->isActive=0;
                $markeplaceAccountFind->update();
            }
            $aggregatorFind->update();

            \Monkey::app()->applicationLog('CAggregatorAccountShopInsertManage','Report','update','update Marketplace Account HasShop ' . $marketplaceId . ' ' . $marketplace_account_name);
            return 'Creazione MarketplaceAccount ' . $marketplace_account_name . ' con ' . $marketplaceId;
        }
    }

    public function delete()
    {
        $id = \Monkey::app()->router->request()->getRequestData('id');
        $aggregatorHasShop = \Monkey::app()->repoFactory->create('AggregatorHasShop')->findOneBy(['id' => $id]);
        $marketplaceHasShop->delete();
        return 'Aggregatore Account  Cancellato definitivamente ricordati di cancellare le regole ';

    }

}
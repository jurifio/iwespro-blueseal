<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CSelectProductBrandTranslationAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 17/04/2021
 * @since 1.0
 */
class CSelectProductBrandTranslationAjaxController extends AAjaxController
{
    public function get()
    {
        $listTranslation = [];
        $productBrandId = $this->app->router->request()->getRequestData('productBrandId');
        $typeCall = $this->app->router->request()->getRequestData('typeCall');
        $productBrandTranslationRepo = \Monkey::app()->repoFactory->create('productBrandTranslation');
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $langRepo = \Monkey::app()->repoFactory->create('Lang');
        if ($typeCall == '1') {
            $productBrandTranslation = $productBrandTranslationRepo->findBy(['productBrandId' => $productBrandId]);
            if (count($productBrandTranslation) > 0) {
                foreach ($productBrandTranslation as $brandTranslation) {
                    $shop = $shopRepo->findOneBy(['id' => $brandTranslation->remoteShopId]);
                    $shopName = $shop->name;
                    $remoteShopId = $shop->id;
                    $lang = $langRepo->findOneBy(['id' => $brandTranslation->langId]);
                    $langName = $lang->name;
                    $text = $brandTranslation->text;
                    $listTranslation[] = ['idTranslation' => $brandTranslation->id,
                        'productBrandId' => $brandTranslation->productBrandId,
                        'text' => $text,
                        'remoteShopId' => $remoteShopId,
                        'remoteShopName' => $shopName,
                        'langId' => $brandTranslation->langId,
                        'langName' => $langName,
                        'responseOk' => '1'
                    ];
                }
            } else {
                $listTranslation[] = ['idTranslation' => '',
                    'productBrandId' => '',
                    'text' => '',
                    'remoteShopId' => '',
                    'remoteShopName' => '',
                    'langId' => '',
                    'langName' => '',
                    'responseOk' => '2'
                ];
            }
        } else {
            $currentUser = $this->app->getUser()->getId();
            $userHasShop = \Monkey::app()->repoFactory->create('UserHasShop')->findOneBy(['userId' => $currentUser]);
            $shopId = $userHasShop->shopId;
            $productBrandTranslation = $productBrandTranslationRepo->findBy(['productBrandId' => $productBrandId,'remoteShopId' => $shopId]);
            if (count($productBrandTranslation) > 0) {
                foreach ($productBrandTranslation as $brandTranslation) {
                    $shop = $shopRepo->findOneBy(['id' => $brandTranslation->remoteShopId]);
                    $shopName = $shop->name;
                    $remoteShopId = $shop->id;
                    $lang = $langRepo->findOneBy(['id' => $brandTranslation->langId]);
                    $langName = $lang->name;
                    $text = $brandTranslation->text;
                    $listTranslation[] = ['idTranslation' => $brandTranslation->id,
                        'productBrandId' => $brandTranslation->productBrandId,
                        'text' => $text,
                        'remoteShopId' => $remoteShopId,
                        'remoteShopName' => $shopName,
                        'langId' => $brandTranslation->langId,
                        'langName' => $langName,
                        'responseOk' => '1'
                    ];
                }
            } else {
                $langs=$langRepo->findBy(['isActive'=>"1"]);
                foreach($langs as $lang){
                    $productBrandTranslationInsert=$productBrandTranslationRepo->getEmptyEntity();
                    $productBrandTranslationInsert->productBrandId=$productBrandId;
                    $productBrandTranslationInsert->langId=$lang->id;
                    $productBrandTranslationInsert->text='';
                    $productBrandTranslationInsert->remoteShopId=$shopId;
                    $productBrandTranslationInsert->insert();
                    $shop = $shopRepo->findOneBy(['id' => $brandTranslation->remoteShopId]);
                    $shopName = $shop->name;
                    $res = \Monkey::app()->dbAdapter->query('select max(id) as id from ProductBrandTranslation',[])->fetchAll();
                    foreach ($res as $result) {
                        $lastId = $result['id'];
                    }
                    $listTranslation[] = ['idTranslation' =>$lastId,
                        'productBrandId' => $productBrandId,
                        'text' => '',
                        'remoteShopId' => $shopId,
                        'remoteShopName' => $shopName,
                        'langId' => $lang->id,
                        'langName' => $lang->name,
                        'responseOk' => '1'
                    ];
                }
            }

        }


        return json_encode($listTranslation);
    }

    public function put()
    {
        try {
            $data = $this->app->router->request()->getRequestData('rows');
            $productBrandTranslationRepo = \Monkey::app()->repoFactory->create('ProductBrandTranslation');
            foreach ($data as $ts) {
                $trans=explode('-',$ts);
                $id=$trans[0];
                $langId=$trans[1];
                $text=$trans[2];
                $shopId=$trans[3];
                $pbt = $productBrandTranslationRepo->findOneBy(['id' => $id]);
                $pbt->langId = $langId;
                $pbt->text = $text;
                $pbt->remoteShopId = $shopId;
                $pbt->update();
            }
            return 'Aggiornamento eseguito con successo';
        }catch (\Throwable $e){
            \Monkey::app()->applicationLog('CSelectProductBrandTranslationAjaxController','Error','Problem update Translation',$e->getMessage(),$e->getLine());
            return 'errore aggiornamento';
        }
    }

}
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
 * Class CProductBrandAddTranslationAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/04/2021
 * @since 1.0
 */
class CProductBrandAddTranslationAjaxController extends AAjaxController
{
    public function get()
    {
        $listTranslation = [];
        $productBrandId = $this->app->router->request()->getRequestData('productBrandId');
        $typeCall = $this->app->router->request()->getRequestData('typeCall');
        $productBrandTranslationRepo = \Monkey::app()->repoFactory->create('productBrandTranslation');
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $langRepo = \Monkey::app()->repoFactory->create('Lang');
        $langs=$langRepo->findBy(['isActive'=>1]);
        foreach($langs as $lang){
            $productBrandTranslationInsert=$productBrandTranslationRepo->getEmptyEntity();
            $productBrandTranslationInsert->productBrandId=$productBrandId;
            $productBrandTranslationInsert->langId=$lang->id;
            $productBrandTranslationInsert->text='';
            $productBrandTranslationInsert->remoteShopId=44;
            $productBrandTranslationInsert->insert();
            $shop = $shopRepo->findOneBy(['id' => 44]);

            $res = \Monkey::app()->dbAdapter->query('select max(id) as id from ProductBrandTranslation',[])->fetchAll();
            foreach ($res as $result) {
                $lastId = $result['id'];
            }
            $listTranslation[] = ['idTranslation' => $lastId,
                'productBrandId' => $productBrandId ,
                'text' => '',
                'remoteShopId' => 44,
                'remoteShopName' => $shop->name,
                'langId' => $lang->id,
                'langName' => $lang->name,
                'responseOk' => '1'
            ];
        }


        return json_encode($listTranslation);
    }

    public function put()
    {
        try {
            $data = $this->app->router->request()->getRequestData('rows');
            $shopId=$this->app->router->request()->getRequestData('shopId');
            $productBrandTranslationRepo = \Monkey::app()->repoFactory->create('ProductBrandTranslation');
            foreach ($data as $ts) {
                $trans=explode('-',$ts);
                $id=$trans[0];
                $langId=$trans[1];
                $text=$trans[2];
                $pbt = $productBrandTranslationRepo->findOneBy(['id' => $id]);
                $pbt->langId = $langId;
                $pbt->text = $text;
                $pbt->remoteShopId = $shopId;
                $pbt->update();
            }
            return 'Traduzione aggiunta con successo';
        }catch (\Throwable $e){
            \Monkey::app()->applicationLog('CProductBrandAddTranslationAjaxController','Error','Problem insert Translation',$e->getMessage(),$e->getLine());
            return 'errore aggiornamento';
        }
    }

}
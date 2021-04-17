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
        $listTranslation=[];
       $productBrandId = $this -> app -> router -> request() -> getRequestData('productBrandId');
       $typeCall=$this -> app -> router -> request() -> getRequestData('typeCall');
       $productBrandTranslationRepo=\Monkey::app()->repoFactory->create('productBrandTranslation');
       $shopRepo=\Monkey::app()->repoFactory->create('Shop');
       $langRepo=\Monkey::app()->repoFactory->create('Lang');
       if($typeCall=='1'){
           $productBrandTranslation=$productBrandTranslationRepo->findBy(['productBrandId'=>$productBrandId]);
           if(count($productBrandTranslation)>0) {
               foreach ($productBrandTranslation as $brandTranslation) {
                    $shop=$shopRepo->findOneBy(['id'=>$brandTranslation->remoteShopId]);
                    $shopName=$shop->name;
                    $remoteShopId=$shop->id;
                    $lang=$langRepo->findOneBy(['id'=>$brandTranslation->langId]);
                    $langName=$lang->name;
                    $text=$brandTranslation->text;
                    $listTranslation[]=['idTranslation'=>$brandTranslation->id,
                                        'productBrandId'=>$brandTranslation->productBrandId,
                                        'text'=>$text,
                                        'remoteShopId'=>$remoteShopId,
                                        'remoteShopName'=>$shopName,
                                        'langId'=>$brandTranslation->langId,
                                        'langName'=>$langName,
                                        'responseOk'=>'1'
               ];
               }
           }else{
               $listTranslation[]=['idTranslation'=>'',
                   'productBrandId'=>'',
                   'text'=>'',
                   'remoteShopId'=>'',
                   'remoteShopName'=>'',
                   'langId'=>'',
                   'langName'=>'',
                   'responseOk'=>'2'
               ];
           }
       }else{
           $currentUser=$this->app->getUser()->getId();
           $userHasShop=\Monkey::app()->repoFactory->create('UserHasShop')->findOneBy(['userId'=>$currentUser]);
           $shopId=$userHasShop->shopId;
           $productBrandTranslation=$productBrandTranslationRepo->findBy(['productBrandId'=>$productBrandId,'remoteShopId'=>$shopId]);
           if(count($productBrandTranslation)>0) {
               foreach ($productBrandTranslation as $brandTranslation) {
                   $shop=$shopRepo->findOneBy(['id'=>$brandTranslation->remoteShopId]);
                   $shopName=$shop->name;
                   $remoteShopId=$shop->id;
                   $lang=$langRepo->findOneBy(['id'=>$brandTranslation->langId]);
                   $langName=$lang->name;
                   $text=$brandTranslation->text;
                   $listTranslation[]=['idTranslation'=>$brandTranslation->id,
                       'productBrandId'=>$brandTranslation->productBrandId,
                       'text'=>$text,
                       'remoteShopId'=>$remoteShopId,
                       'remoteShopName'=>$shopName,
                       'langId'=>$brandTranslation->langId,
                       'langName'=>$langName,
                       'responseOk'=>'1'
                   ];
               }
           }else{
               $listTranslation[]=['idTranslation'=>'',
                   'productBrandId'=>'',
                   'text'=>'',
                   'remoteShopId'=>'',
                   'remoteShopName'=>'',
                   'langId'=>'',
                   'langName'=>'',
                   'responseOk'=>'2'
               ];
           }

       }


        return json_encode($listTranslation);
    }
}
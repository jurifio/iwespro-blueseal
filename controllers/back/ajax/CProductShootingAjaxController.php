<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CShooting;
use bamboo\domain\entities\CShop;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\domain\repositories\CProductHasShootingRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CShootingRepo;
use bamboo\domain\repositories\CShopRepo;


/**
 * Class CProductShootingAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/03/2018
 * @since 1.0
 */
class CProductShootingAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooInvoiceException
     */
    public function post()
    {
        $res = "";
        $data = \Monkey::app()->router->request()->getRequestData();

        $friendDdt = $data["friendDdt"];
        $note = $data["note"];
        $productsIds = $data["products"];
        $shopId = $data["friendId"];
        $pieces = $data["pieces"];

        if(empty($friendDdt)){
            $res = "Devi inserire il ddt del friend";
            return $res;
        }

        //creo shooting
        /** @var CShootingRepo $shootingRepo */
        $shootingRepo = \Monkey::app()->repoFactory->create('Shooting');

        $shootingId = $shootingRepo->createShooting($productsIds, $friendDdt, $note, $shopId, $pieces);
        $res = "Hai inserito correttamente i prodotti nello shooting con codice: ".$shootingId;



        return $res;
    }

    public function get(){
        $res = [];


        $shops = \Monkey::app()->router->request()->getRequestData('shop');

        //elenco array
        $result = [];
        foreach ($shops as $subarray) {
            $result = array_merge($result, $subarray);
        }

        $uShops = array_unique($result);

        if(count($uShops) == 1){

            /** @var CDocumentRepo $dRepo */
            $dRepo = \Monkey::app()->repoFactory->create('Document');

            /** @var CObjectCollection $shootings */
            $shootings = \Monkey::app()->repoFactory->create('Shooting')->findBy(['shopId'=>$uShops]);

            $z = 0;
            /** @var CShooting $shooting */
            foreach ($shootings as $shooting){
                if($z == 0) {
                    $lastShooting = $shooting;
                    $z++;
                    continue;
                }
                if($shooting->date > $lastShooting->date){
                    $lastShooting = $shooting;
                    $z++;
                } else {
                    $z++;
                }
            }
            $res["-lastDdt"] = $dRepo->findShootingFriendDdt($lastShooting);
            $res["-pieces"] = $lastShooting->pieces;
        }



        /** @var CShopRepo $shopRepo */
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');

        $i = 0;

        foreach ($uShops as $shopId){
            /** @var CShop $shop */
            $shop = $shopRepo->findOneBy(['id'=>$shopId]);

            $res[$i]["id"] = $shop->id;
            $res[$i]["name"] = $shop->name;
            $i++;
        }

        return json_encode($res);

    }

}
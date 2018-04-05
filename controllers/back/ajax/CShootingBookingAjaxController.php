<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CInvoiceType;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CSectional;
use bamboo\domain\entities\CShooting;
use bamboo\domain\entities\CShootingBooking;
use bamboo\domain\entities\CShootingProductType;
use bamboo\domain\entities\CShop;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CProductHasShootingRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CSectionalRepo;
use bamboo\domain\repositories\CShootingBookingRepo;
use bamboo\domain\repositories\CShootingRepo;
use bamboo\domain\repositories\CShopRepo;


/**
 * Class CShootingBookingAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/04/2018
 * @since 1.0
 */
class CShootingBookingAjaxController extends AAjaxController
{
    /**
     *
     */
    public function get()
    {

        $r = [];
        $s = [];

        $ar = [];

        /** @var ARepo $cat */
        $cat = \Monkey::app()->repoFactory->create('ShootingProductType');

        $allType = $cat->findAll();

        $i = 0;
        /** @var CShootingProductType $type */
        foreach ($allType as $type){
            $r[$i]["id"] = $type->id;
            $r[$i]["name"] = $type->name;
            $i++;
        }

        /** @var CUser $user */
        $user = \Monkey::app()->getUser();

        $shops = $user->getAuthorizedShops();

        $z = 0;
        /** @var CShop $shop */
        foreach ($shops as $shop){
            $s[$z]["id"] = $shop->id;
            $s[$z]["name"] = $shop->name;
            $z++;
        }

        $ar["tp"] = $r;
        $ar["sh"] = $s;

        return json_encode($ar);

    }

    /**
     * @return string
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post(){

        $data = \Monkey::app()->router->request()->getRequestData();
        $date = $data["date"];
        $cat = $data["cat"];
        $shop = $data["shop"];

        /** @var CShootingBookingRepo $sbRepo */
        $sbRepo = \Monkey::app()->repoFactory->create('ShootingBooking');

        $sbId = $sbRepo->insertNewShootingBooking($date, $shop, $cat);

        if(!empty($sbId)){
            /** @var CShop $shp */
            $shp = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id'=>$shop]);
            $shopName = $shp->name;

            $body = "Lo shop ".$shopName." ha inserito dei prodotti nello shooting ".$sbId;

            /** @var CEmailRepo $mailRepo */
            $mailRepo = \Monkey::app()->repoFactory->create('Email');
            $mailRepo->newMail('it@iwes.it', ["it@iwes.it"],[],[],"Nuova prenotazione shooting", $body);

            $res = "Shooting prenotato con successo. Codice prenotazione: ".$sbId;
        }


        return $res;


    }



}
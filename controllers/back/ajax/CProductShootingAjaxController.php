<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CInvoiceType;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CSectional;
use bamboo\domain\entities\CShooting;
use bamboo\domain\entities\CShootingBooking;
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
        $friend = $data["friend"];
        $friendDdt = $data["friendDdt"];
        $productsIds = $data["products"];
        $pieces = $data["pieces"];
        $booking = $data["booking"];
        $productsInformation = $data["productsInformation"];


        if ($friend == 0) {
            $note = $data["note"];
        } else {
            $note = "DDT inserito da friend";
        }

        if(empty($friendDdt)){
            $res = "Devi inserire il DDT";
            return $res;
        } else if(empty($booking)){
            $res = "Devi selezionare una prenotazione";
            return $res;
        } else if(empty($productsInformation)){
            $res = "Se non desideri inserire la quantità o la taglia per ogni prodotto clicca sul pulsante per inserire la quantità di default";
            return $res;
        }

        foreach ($productsInformation as $s){
            if(empty($s)){
                $res = "Se non desideri inserire la quantità o la taglia per ogni prodotto clicca sul pulsante per inserire la quantità di default";
                return $res;
            }
        }

        //creo shooting
        /** @var CShootingRepo $shootingRepo */
        $shootingRepo = \Monkey::app()->repoFactory->create('Shooting');

        /** @var CShootingBooking $sb */
        $sb = \Monkey::app()->repoFactory->create('ShootingBooking')->findOneBy(['id'=>$booking]);

        $res = $shootingRepo->createShooting($productsIds, $friendDdt, $note, $sb->shop->billingAddressBook->id, $pieces, $booking, $sb, $productsInformation);


        return $res;
    }


    /**
     * @return string
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function get(){
        $res = [];
        $resLast = [];


        $shops = \Monkey::app()->router->request()->getRequestData('shop');
        $step = \Monkey::app()->router->request()->getRequestData('step');

        if($step == 1){

            $shopArr = [];
            foreach ($shops as $shopId){
                $shopArr[] = $shopId[0];
            }

            $uniqueShop = array_unique($shopArr);

            $nShop = count($uniqueShop);

            //se lo è stato selezionato un solo shop lo memorizzo temporaneamente e lo ripropongo fino a quando non si cambia!
            if($nShop == 1){

                /** @var CShootingBookingRepo $allSBRepo */
                $allSBRepo = \Monkey::app()->repoFactory->create('ShootingBooking');

                /** @var CShootingBooking $allSb */
                $allSb = $allSBRepo->findLastSelected($uniqueShop[0]);

                if($allSb != false){
                    $resLast["lastId"] = $allSb->id;
                    $resLast["lastDate"] = $allSb->date;
                    $resLast["lastShop"] = $allSb->shop->name;

                    $res["last"] = $resLast;
                }
            }

            //trovo gli shop autorizzati per l'utente
            /** @var CUser $user */
            $user = \Monkey::app()->getUser();

            $shopsAuth = $user->getAuthorizedShops();

            $allShops = [];
            /** @var CShop $shp */
            foreach ($shopsAuth as $shp){
                $allShops[] = $shp->id;
            }

            $booked = [];
            //trovo tutte le prenotazioni per gli shop gestiti dall'utente y
            foreach ($allShops as $shopId){

                /** @var CObjectCollection $sbS */
                $sbS = \Monkey::app()->repoFactory->create('ShootingBooking')->findBy(['shopId'=>$shopId]);
                $booked[] = $sbS;
            }

            $allowedBooking = [];
            $k = 0;
            /** @var CObjectCollection $singleBooking */
            //ciclo tutte le prenotazioni disponibili e elimino le collezioni vuote
            foreach ($booked as $singleBooking){
                if($singleBooking->isEmpty()) continue;

                /** @var CShootingBooking $bookingObj */
                foreach ($singleBooking as $bookingObj){
                    if($bookingObj->status == "o" || $bookingObj->status == "c") continue;

                    $allowedBooking[$k]["id"] = $bookingObj->id;
                    $allowedBooking[$k]["date"] = $bookingObj->date;
                    $allowedBooking[$k]["shop"] = $bookingObj->shop->name;
                    $k++;
                }

            }

            $res["-booked"] = $allowedBooking;
            return json_encode($res);
        }

        if($step == 2){

            $selectedBooking = \Monkey::app()->router->request()->getRequestData('selectedBooking');

            /** @var CShootingBooking $bs */
            $bs = \Monkey::app()->repoFactory->create('ShootingBooking')->findOneBy(['id'=>$selectedBooking]);

            /** @var CShooting $s */
            $s = $bs->shooting;

            if(!is_null($s)) {
                /** @var CDocumentRepo $documentRepo */
                $documentRepo = \Monkey::app()->repoFactory->create('Document');
                $res["-pieces"] = $s->pieces;
                $res["-lastDdt"] = $documentRepo->findShootingFriendDdt($s);
            }

            /** @var CSectionalRepo $secRepo */
            $secRepo = \Monkey::app()->repoFactory->create('Sectional');
            $res["-nextDdt"] = $secRepo->calculateNewSectionalCodeFromShop($bs->shopId, CInvoiceType::DDT_SHOOTING);

            $date = date("Y-m-d H:i:s");
            $dateTime = new \DateTime($date);
            $bs->lastSelection = $dateTime->format('Y-m-d  H:i:s');;
            $bs->update();

            return json_encode($res);

        }

    }

}
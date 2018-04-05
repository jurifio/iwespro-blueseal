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
use bamboo\domain\repositories\CShootingRepo;
use bamboo\domain\repositories\CShopRepo;


/**
 * Class CProductShootingPrintDdtAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 05/04/2018
 * @since 1.0
 */
class CProductShootingPrintDdtAjaxController extends AAjaxController
{

    public function get(){

        $res = [];
        $shootingId = \Monkey::app()->router->request()->getRequestData('shootingId');

        /** @var CShooting $s */
        $s = \Monkey::app()->repoFactory->create('Shooting')->findOneBy(['id'=>$shootingId]);

        /** @var CDocumentRepo $dRepo */
        $dRepo = \Monkey::app()->repoFactory->create('Document');

        /** @var CDocument $d */
        $d = $dRepo->findOneBy(['id' => $s->friendDdt]);

        $invBin = $d->invoiceBin;

        if(is_null($invBin)){
            $res["response"] = "no-pdf";
            $res["message"] = "Il ddt non ha associato nessun pdf";
            return json_encode($res);
        }

        if ($s->printed == 1){
            $res["response"] = "printed";
            $res["message"] = "Il file è stato già stampato, sicuro di voler ristampare? L'eventuale ristampa verrà comunicata a mezzo mail presso Iwes SNC";
        } else if($s->printed == 0){
            $res["response"] = "not-printed";
            $res["message"] = "Il file non è stato mai stampato, con questa procedura si da conferma e ufficialità al documento";
        }

        return json_encode($res);
    }


    /**
     * @return string
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post(){
        $shootingId = \Monkey::app()->router->request()->getRequestData('shootingId');

        /** @var CShooting $s */
        $s = \Monkey::app()->repoFactory->create('Shooting')->findOneBy(['id'=>$shootingId]);

        /** @var CDocumentRepo $dRepo */
        $dRepo = \Monkey::app()->repoFactory->create('Document');

        /** @var CEmailRepo $mailRepo */
        $mailRepo = \Monkey::app()->repoFactory->create('Email');

        /** @var CDocument $d */
        $d = $dRepo->findOneBy(['id' => $s->friendDdt]);

        $invBin = $d->invoiceBin;

        if(is_null($invBin)){
            $res = "no";
        } else {
            if($s->printed == 1){
                $subject = "RISTAMPA DDT da parte di ".$s->shootingBooking->shop->name;
                $body = "Il friend ".$s->shootingBooking->shop->name." ha STAMPATO NUOVAMENTE il DDT ".$d->number;
                $mailRepo->newMail('it@iwes.it', ['it@iwes.it'], [], [], $subject, $body);


            } else if($s->printed == 0){
                $subject = "Prima stampa ddt da parte di ".$s->shootingBooking->shop->name;
                $body = "Il friend ".$s->shootingBooking->shop->name." ha STAMPATO PER LA PRIMA VOLTA il DDT ".$d->number;
                $mailRepo->newMail('it@iwes.it', ['it@iwes.it'], [], [], $subject, $body);

                $s->printed = 1;
                $s->update();

                /** @var CShootingBooking $bs */
                $bs = $s->shootingBooking;
                $bs->status = "c";
                $bs->update();
            }


            $res = "yes";
        }

        return $res;
    }

}
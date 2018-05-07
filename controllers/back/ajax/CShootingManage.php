<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CShooting;
use bamboo\domain\entities\CShootingBooking;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductHasShootingRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CSectionalRepo;
use bamboo\domain\repositories\CShootingRepo;
use bamboo\domain\repositories\CUserRepo;


/**
 * Class ChootingManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 07/05/2018
 * @since 1.0
 */
class CShootingManage extends AAjaxController
{
    /**
     * @return bool
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function delete()
    {

        $shootings = \Monkey::app()->router->request()->getRequestData('shootings');

        /** @var CShootingRepo $sRepo */
        $sRepo = \Monkey::app()->repoFactory->create('Shooting');

        $result = $sRepo->deleteShooting($shootings);

        return json_encode($result);

    }


    /**
     * @return string
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put(){
        $newDdt = \Monkey::app()->router->request()->getRequestData('newDdt');
        $shootingId = \Monkey::app()->router->request()->getRequestData('shootingId');


        if(empty($newDdt)){
            return "Inserisci tutti i dati";
        }

        /** @var CShooting $shooting */
        $shooting = \Monkey::app()->repoFactory->create('Shooting')->findOneBy(['id'=>$shootingId]);

        /** @var CDocumentRepo $docRepo */
        $docRepo = \Monkey::app()->repoFactory->create('Document');

        /** @var CDocument $document */
        $document = $docRepo->findOneBy(['id'=>$shooting->friendDdt]);

        if(!is_null($document)){
            /** @var CDocument $extDoc */
            $extDoc = $docRepo->findOneBy(['shopRecipientId'=>$document->shopRecipientId, 'number'=>$newDdt, 'year'=>$document->year]);

            if(is_null($extDoc)){
                $document->number = $newDdt;
                $document->update();
                $res = "Numero ddt aggiornato con successo";
            } else {
                $res = "Il numero di ddt inserito è già esistente";
            }

            return $res;
        }

        return "Non è stato trovato alcun documento per lo shooting scelto";
    }

    /**
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post(){

        $bookings = \Monkey::app()->router->request()->getRequestData('bookings');

        foreach ($bookings as $bookingId){

            /** @var CShootingBooking $booking */
            $booking = \Monkey::app()->repoFactory->create('ShootingBooking')->findOneBy(['id'=>$bookingId]);

            $booking->status = 'c';
            $booking->update();
        }

        return "Shooting chiusi con successo";

    }


}
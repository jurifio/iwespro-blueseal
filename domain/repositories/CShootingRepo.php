<?php

namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\entities\CShooting;

/**
 * Class CShootingRepo
 * @package bamboo\domain\repositories
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
class CShootingRepo extends ARepo
{


    /**
     * @param $productsIds
     * @param $friendDdtNumber
     * @param $note
     * @param $shopId
     * @return mixed
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooInvoiceException
     */
    public function createShooting($productsIds, $friendDdtNumber, $note, $shopId, $pieces){

        /** @var CDocumentRepo $documentRepo */
        $documentRepo = \Monkey::app()->repoFactory->create('Document');
        $documentId = $documentRepo->createEmptyDdtDocument($shopId, $friendDdtNumber);

        if(is_numeric($documentId)){
            $date = date("Y-m-d");
            $dateTime = new \DateTime($date);

            $shooting = $this->getEmptyEntity();
            $shooting->friendDdt = $documentId;
            $shooting->note = $note;
            $shooting->phase = "accepted";
            $shooting->shopId = $shopId;
            $shooting->year = $dateTime->format('Y');
            $shooting->pieces = $pieces;
            $shooting->smartInsert();
        } else if(is_object($documentId)) {
            /** @var CShooting $shooting */
            $shooting = $this->findOneBy(['friendDdt'=>$documentId->id, 'shopId'=>$documentId->shopRecipientId, 'year'=>$documentId->year]);
        }


        /** @var CProductHasShootingRepo $pHsRepo */
        $pHsRepo = \Monkey::app()->repoFactory->create('ProductHasShooting');


        if($pHsRepo->associateNewProductsToShooting($productsIds, $shooting->id)) {
            return $shooting->id;
        }

    }
}
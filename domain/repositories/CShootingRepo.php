<?php

namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CInvoiceBin;
use bamboo\domain\entities\CShooting;
use bamboo\domain\entities\CShootingBooking;
use bamboo\domain\entities\CShootingBookingHasProductType;

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
     * @param null $note
     * @param $shopId
     * @param $pieces
     * @param $booking
     * @param $sb
     * @param $productsInformation
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooInvoiceException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function createShooting($productsIds, $friendDdtNumber, $note = null, $shopId, $pieces, $booking, $sb, $productsInformation){

        /** @var CDocumentRepo $documentRepo */
        $documentRepo = \Monkey::app()->repoFactory->create('Document');
        $documentId = $documentRepo->createEmptyDdtDocument($shopId, $friendDdtNumber, $sb);

        if(is_numeric($documentId)){
            $date = date("Y-m-d");
            $dateTime = new \DateTime($date);

            $shooting = $this->getEmptyEntity();
            $shooting->friendDdt = $documentId;
            $shooting->note = $note;
            $shooting->year = $dateTime->format('Y');
            $shooting->pieces = $pieces;
            $shooting->smartInsert();
        } else if(is_object($documentId)) {
            /** @var CShooting $shooting */
            $shooting = $this->findOneBy(['friendDdt'=>$documentId->id, 'year'=>$documentId->year]);
        }


        //completo la tabella shootingbooking
        /** @var CShootingBooking $sb */
        $sb = \Monkey::app()->repoFactory->create('ShootingBooking')->findOneBy(['id'=>$booking]);
        $sb->shootingId = $shooting->id;
        $sb->update();


        /** @var CProductHasShootingRepo $pHsRepo */
        $pHsRepo = \Monkey::app()->repoFactory->create('ProductHasShooting');

        $association = $pHsRepo->associateNewProductsToShooting($productsIds, $shooting->id, $productsInformation);


        if(empty($association["info"]) && !empty($association["existent"])){
            return "<div style='background-color: red; color: white'>Shooting ".$shooting->id.':<br />Prodotti esistenti:<br />'.implode(' | ', $association["existent"]).'</div>';

        } else if (empty($association["existent"]) && !empty($association["info"])){
            $res =  $this->fillProductTableInfo($shooting, $association["info"]);
            return $res;
        } else if(!empty($association["existent"]) && !empty($association["info"])){

            $res1 = "<div style='background-color: red; color: white'>Shooting ".$shooting->id.':<br />Prodotti esistenti:<br />'.implode(' | ', $association["existent"]).'</div>';
            $res2 = $this->fillProductTableInfo($shooting, $association["info"]);

            return $res1.'<br />'.$res2;

        }
    }

    private function fillProductTableInfo($shooting, $association){
        $table = "";
        foreach ($association as $singleProductInfo){

            $table .= "
                 <div style='
                     margin-bottom: 20px;
                     margin-top: 20px;
                     border: 1px solid #000;
                '>
                 <table style='width:80%'>
                  <tr>
                    <th>Codice</th>
                    <th>Cod. For</th> 
                    <th>Id Or.</th>
                    <th>Brand</th>
                  </tr>
                  <tr>
                    <td>$singleProductInfo[0]</td>
                    <td>$singleProductInfo[1]</td> 
                    <td>$singleProductInfo[2]</td>
                    <td>$singleProductInfo[3]</td>
                  </tr>
                </table>
                <p style='font-size: 14px'>Inserisci il codice: <strong>$singleProductInfo[4]</strong></p>
                </div>
                ";

        }

        return "Hai inserito correttamente i prodotti nello shooting con codice: $shooting->id".$table;
    }

    /**
     * @param $shootingId
     * @param $pieces
     * @return bool
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function updatePieces($shootingId, $pieces){

        /** @var CShooting $shooting */
        $shooting = $this->findOneBy(['id'=>$shootingId]);

        if($shooting->pieces == $pieces){
            return;
        }

        $shooting->pieces = $pieces;
        $shooting->update();

        return true;
    }

    /**
     * @param array $shootings
     * @return bool
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function deleteShooting(array $shootings): array {


        $result = [];
        $notEmpty = [];
        $empty = [];
        $errShooting = [];
        foreach ($shootings as $shootingId){

            /** @var CShooting $shooting */
            $shooting = \Monkey::app()->repoFactory->create('Shooting')->findOneBy(['id' => $shootingId]);

            if($shooting->product->count() != 0) {
                $notEmpty[] = $shooting->id;
                continue;
            }

            // ---------------- Cancello ShootingBookingHasProductType ----------------
            /** @var CShootingBookingHasProductTypeRepo $sbhptRepo */
            $sbhptRepo = \Monkey::app()->repoFactory->create('ShootingBookingHasProductType');
            $sbhptRepo->deleteShootingBookingHasProductType($shooting->shootingBooking);
            // ------------------------------------------------------------------------



            // ---------------- Cancello ShootingBooking ------------------------------
            /** @var CShootingBookingRepo $sbRepo */
            $sbRepo = \Monkey::app()->repoFactory->create('ShootingBooking');

            /** @var CShootingBooking $sbhpt */
            $sbhpt = $sbRepo->findOneBy(['shootingId'=>$shootingId]);

            if(!is_null($sbhpt)){
                $sbhpt->delete();
            } else {
                $errShooting[] = $shooting->id;
            }
            // ------------------------------------------------------------------------


            /** @var CRepo $invoiceBinRepo */
            $invoiceBinRepo = \Monkey::app()->repoFactory->create('InvoiceBin');
            /** @var CDocumentRepo $document */
            $documentRepo = \Monkey::app()->repoFactory->create('Document');


            // ---------------- Cancello Shooting -------------------------------------
            $shooting->delete();
            // ------------------------------------------------------------------------


            // ---------------- Cancello Document (Picky) -----------------------------
            if(!is_null($shooting->pickyDdt)) {

                /** @var CInvoiceBin $pickyDocContent */
                $pickyDocContent = $invoiceBinRepo->findOneBy(['invoiceId' => $shooting->pickyDdt]);
                if (!is_null($pickyDocContent)) {
                    $pickyDocContent->delete();
                }

                /** @var CDocument $pickyDocument */
                $pickyDocument = $documentRepo->findOneBy(['id' => $shooting->pickyDdt]);
                $pickyDocument->delete();

            }
            // ------------------------------------------------------------------------


            // ---------------- Cancello Document (Friend) -----------------------------
            if(!is_null($shooting->friendDdt)) {

                /** @var CInvoiceBin $friendDocContent */
                $friendDocContent = $invoiceBinRepo->findOneBy(['invoiceId' => $shooting->friendDdt]);
                if (!is_null($friendDocContent)) {
                    $friendDocContent->delete();
                }

                /** @var CDocument $friendDocument */
                $friendDocument = $documentRepo->findOneBy(['id' => $shooting->friendDdt]);
                $friendDocument->delete();

            }
            // ------------------------------------------------------------------------

            $empty[] = $shooting->id;
        }

        $result["deleted"] = $empty;
        $result["notDeleted"] = $notEmpty;
        $result["errShooting"] = $errShooting;
        return $result;

    }

}
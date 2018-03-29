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

        $association = $pHsRepo->associateNewProductsToShooting($productsIds, $shooting->id);


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
}
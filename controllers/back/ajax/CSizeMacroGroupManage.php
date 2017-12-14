<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductSize;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\repositories\CProductSizeRepo;


/**
 * Class CProductSizeGroupManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CSizeMacroGroupManage extends AAjaxController
{
    /**
     * @return int
     * @throws BambooException
     * @throws \Exception
     */
    public function post()
    {
            $data = \Monkey::app()->router->request()->getRequestData();
            $name = $data['name'];

            /** @var CProductSizeMacroGroup $productSizeMacroGroupRepo */
            $productSizeMacroGroupRepo = \Monkey::app()->repoFactory->create('ProductSizeMacroGroup')->getEmptyEntity();


            // Controllo se esiste un macrogruppo con lo stesso nome (case sensitive)
            /** @var CRepo $checkProductSizeMacroGroupRepo */
            $checkProductSizeMacroGroupRepo = \Monkey::app()->repoFactory->create('ProductSizeMacroGroup');


            /** @var CProductSizeRepo $checkName */
            $checkName = $checkProductSizeMacroGroupRepo->findOneBy(['name' => $name ]);

            $check = $checkName;

            if(empty($check)){
                //riempi il database
                $productSizeMacroGroupRepo->name = $name;
                $productSizeMacroGroupRepo->smartInsert();

                //restituisci messaggio di avvenuto inserimento
                $res = "Macrogruppo aggiunto con successo";
            } else {
                //restituisci messaggio di errore
                $res = "Il macrogruppo inserito è già presente";
            }

            return $res;
    }

    public function delete(){

        $data = \Monkey::app()->router->request()->getRequestData();
        $idMacroGroupToDelete = $data['idMacroGroup'];
        /** @var CProductSizeMacroGroup $productSizeMacroGroup */
        $productSizeMacroGroup = \Monkey::app()->repoFactory->create('ProductSizeMacroGroup')->findOneBy(['id'=>$idMacroGroupToDelete]);

        if($productSizeMacroGroup->productSizeGroup->isEmpty()){
            $res = "Cancella sto macroooo";
        } else {
            $res = "Ci sono gruppi collegati al macrogruppo";
        }
/*
        if(!$productSizeGroup->product->isEmpty()) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return json_encode([
                'products'=> $productSizeGroup->product
            ]);
        }

        foreach ($productSizeGroup->productSizeGroupHasProductSize as $productSizeGroupHasProductSize) {
            $productSizeGroupHasProductSize->delete();
        }

        $productSizeGroup->delete();
*/
        return $res;

    }

}
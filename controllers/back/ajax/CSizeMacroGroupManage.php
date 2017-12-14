<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\repositories\CProductSizeGroupRepo;
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
            //prendo i dati passati in input
            $data = \Monkey::app()->router->request()->getRequestData();
            $name = $data['name'];
            $productSizeGroupName = $data['productSizeGroupName'];
            $locale = $data['locale'];


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

                //prendi id macrogruppo inserito
                $findIdMacroGroup = $checkProductSizeMacroGroupRepo->findOneBy(['name' => $name ]);
                $idMacroGroup = $findIdMacroGroup->id;

                //creo il gruppo taglia
                /** @var CProductSizeGroup $productSizeGroup */
                $productSizeGroup = \Monkey::app()->repoFactory->create('ProductSizeGroup')->getEmptyEntity();
                $productSizeGroup->productSizeMacroGroupId = $idMacroGroup;
                $productSizeGroup->name = $productSizeGroupName;
                $productSizeGroup->locale = $locale;
                $productSizeGroup->smartInsert();


                //restituisci messaggio di avvenuto inserimento
                $res = "Macrogruppo e gruppo aggiunti con successo";
            } else {
                //restituisci messaggio di errore
                $res = "Il macrogruppo inserito è già presente";
            }

            return $res;
    }

    /**
     * @return string
     * @throws BambooException
     * @throws \Exception
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function delete(){

        $data = \Monkey::app()->router->request()->getRequestData();
        $idMacroGroupToDelete = $data['idMacroGroup'];
        /** @var CProductSizeMacroGroup $productSizeMacroGroup */
        $productSizeMacroGroup = \Monkey::app()->repoFactory->create('ProductSizeMacroGroup')->findOneBy(['id'=>$idMacroGroupToDelete]);

        $checkMacroGroup = $productSizeMacroGroup;

        if (empty($checkMacroGroup)) {
            $res = "<p style='color:red'>Il macrogruppo che stai cercando di cancellare non esiste</p>";
        } else if ($productSizeMacroGroup->productSizeGroup->isEmpty()){
            $productSizeMacroGroup->delete();
            $res = "Cancellazione del macrogruppo avvenuta con successo";

        } else if (!$productSizeMacroGroup->productSizeGroup->isEmpty()) {
            $res = "<p style='color:red'>Impossibile cancellare il macrogruppo perché ci sono gruppi taglia collegati.</p>";
        }

        return $res;

    }

}
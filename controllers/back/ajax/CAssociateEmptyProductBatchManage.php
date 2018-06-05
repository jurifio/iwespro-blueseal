<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchDetailsRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CSectionalRepo;
use bamboo\domain\repositories\CUserRepo;


/**
 * Class CAssociateEmptyProductBatchManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 17/05/2018
 * @since 1.0
 */
class CAssociateEmptyProductBatchManage extends AAjaxController
{
    /**
     * @return string
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $productBatchId = $data["productBatchId"];
        $foisonId = $data["foisonId"];
        $contractDetailsId = $data["contractDetailsId"];
        $deliveryDate = $data["deliveryDate"];


        /** @var CProductBatchRepo $pbRepo */
        $pbRepo = \Monkey::app()->repoFactory->create('ProductBatch');

        /** @var CProductBatch $pb */
        $pb = $pbRepo->findOneBy(['id'=>$productBatchId]);

        if(!is_null($pb->contractDetailsId)) return 'Il lotto che stai associando è gia di un\'altro Fason';

        $elems = $pb->getElements();
        $numP = $elems->count();


        //Costo
        /** @var CContractDetails $contractDetails */
        $contractDetails = \Monkey::app()->repoFactory->create('ContractDetails')->findOneBy(['id'=>$contractDetailsId]);;
        $unitPrice = $contractDetails->workPriceList->price;
        $value = $unitPrice * $numP;

        /** @var CProductBatch $pBatch */
        $pBatch = $pbRepo->associateProductBatch($pb, $deliveryDate, $value, $contractDetailsId);
        if(is_object($pBatch)){

            /** @var CEmailRepo $mailRepo */
            $mailRepo = \Monkey::app()->repoFactory->create('Email');

            /** @var CFoison $fason*/
            $fason = \Monkey::app()->repoFactory->create('Foison')->findOneBy(['id'=>$foisonId]);

            $name = $fason->user->getFullName();
            $to = $fason->email;
            $batchId = $pBatch->id;


            $body = "Gentilissimo sig. $name,<br />
                Le confermiamo l'avvenuta creazione del lotto n. $batchId<br /><br />
                Cordiali saluti;<br /><br />
                Gianluca Cartechini<br />
                Iwes";

            $mailRepo->newMail('gianluca@iwes.it', [$to], [], [], 'Conferma creazione lotto', $body);


            $res = "Lotto creato con successo";
        } else {
            $res = "Errore durante la creazione";
        }

        return $res;
    }

    public function get(){
        $contractDetailId = \Monkey::app()->router->request()->getRequestData('contractDetail');
        $productBatchId = \Monkey::app()->router->request()->getRequestData('productBatchId');

        /** @var CProductBatch $pb */
        $pb = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(['id'=>$productBatchId]);

        /** @var CObjectCollection $elems */
        $elems = $pb->getElements();

        /** @var CContractDetailsRepo $contractDetailRepo */
        $contractDetailRepo = \Monkey::app()->repoFactory->create('ContractDetails');
        /** @var CContractDetails $contractDetails */
        $contractDetails = $contractDetailRepo->findOneBy(['id'=>$contractDetailId]);

        //Costo stimato
        $unitPrice = $contractDetails->workPriceList->price;
        $cost = $unitPrice * $elems->count();

        //Lotto sezionale stimato
        /** @var CSectionalRepo $sectionalRepo */
        $sectionalCodeId = $contractDetails->workCategory->sectionalCodeId;

        $sectionalRepo = \Monkey::app()->repoFactory->create('Sectional');
        $sectional = $sectionalRepo->calculateNextSectionalNumber($sectionalCodeId);

        $result = [];
        $result["cost"] = $cost;
        $result["sectional"] = $sectional;

        return json_encode($result);
    }

}
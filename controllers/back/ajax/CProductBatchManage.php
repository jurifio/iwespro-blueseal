<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CUser;
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
 * Class CProductBatchManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/03/2018
 * @since 1.0
 */
class CProductBatchManage extends AAjaxController
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
        $products = $data["products"];
        $foisonId = $data["foisonId"];
        $contractDetailsId = $data["contractDetailsId"];
        $deliveryDate = $data["deliveryDate"];
        $numberOfProduct = $data["numberOfProduct"];

        //Costo
        /** @var CContractDetails $contractDetails */
        $contractDetails = \Monkey::app()->repoFactory->create('ContractDetails')->findOneBy(['id'=>$contractDetailsId]);;
        $unitPrice = $contractDetails->workPriceList->price;
        $value = $unitPrice * $numberOfProduct;

        /** @var CProductBatchRepo $productBatchRepo */
        $productBatchRepo = \Monkey::app()->repoFactory->create('ProductBatch');

        /** @var CProductBatch $pBatch */
        $pBatch = $productBatchRepo->createNewProductBatch($deliveryDate, $value, $contractDetailsId, $products);
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


            $res = "Lotto creato con sucecsso";
        } else {
            $res = "Errore durante la creazione";
        }

        return $res;
    }

    /**
     * @return string
     */
    public function get(){
        $contractDetailId = \Monkey::app()->router->request()->getRequestData('contractDetail');
        $numberOfProduct = \Monkey::app()->router->request()->getRequestData('numberOfProduct');

        /** @var CContractDetailsRepo $contractDetailRepo */
        $contractDetailRepo = \Monkey::app()->repoFactory->create('ContractDetails');
        /** @var CContractDetails $contractDetails */
        $contractDetails = $contractDetailRepo->findOneBy(['id'=>$contractDetailId]);

        //Costo stimato
        $unitPrice = $contractDetails->workPriceList->price;
        $cost = $unitPrice * $numberOfProduct;

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

    /**
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put(){
        $ids = \Monkey::app()->router->request()->getRequestData('productBatchIds');
        $emails = \Monkey::app()->router->request()->getRequestData('foisons');

        /** @var CProductBatchRepo $pbRepo */
        $pbRepo = \Monkey::app()->repoFactory->create('ProductBatch');


        foreach ($ids as $id) {
            /** @var CProductBatch $pb */
            $pb = $pbRepo->findOneBy(['id' => $id]);
            if(!$pb->isComplete()) return 'Impossibile chiudere il lotto, il fason non ha normalizzato tutti i prodotti';

            $pbRepo->closeProductBatch($id);
        }

        /** @var CEmailRepo $mailRepo */
        $mailRepo = \Monkey::app()->repoFactory->create('Email');
        foreach ($emails as $email){

            /** @var CFoison $foison */
            $foison = \Monkey::app()->repoFactory->create('Foison')->findOneBy(['email' => $email]);
            $foisonFullName = $foison->user->getFullName();
            $url = \Monkey::app()->baseUrl(false) . "/blueseal/work/lotti";

            $body = "Gentilissimo Sig. $foisonFullName<br /><br />
            Prego prendere nota che il lotto è stato confermato, proceda pure ad emettere fattura ed inserirla nel portale al seguente link: $url
            <br /><br />
            Cordiali saluti<br />
            Iwes
            ";

            $mailRepo->newMail('gianluca@iwes.it', [$email], [], [], "Lotto confermato e chiuso con successo", $body);

        }

        $res = "Lotti chiusi con successo";

        return $res;
    }

    public function delete(){
        $products = \Monkey::app()->router->request()->getRequestData('products');
        $productBatchId = \Monkey::app()->router->request()->getRequestData('productBatchId');
        $emptyBatch = \Monkey::app()->router->request()->getRequestData('emptyBatch');

        $e = ($emptyBatch == 'empty' ? true : false);

        /** @var CProductBatchDetailsRepo $pbdRepo */
        $pbdRepo = \Monkey::app()->repoFactory->create('ProductBatchDetails');


        $d = $pbdRepo->deleteProductFromBatch($productBatchId, $products, $e);

        if($d) return 'Prodotti eliminati con successo dal lotto';

    }

}
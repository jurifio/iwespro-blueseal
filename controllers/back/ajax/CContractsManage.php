<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\email\CEmail;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CContracts;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CUserRepo;


/**
 * Class CContractsManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/03/2018
 * @since 1.0
 */
class CContractsManage extends AAjaxController
{
    /**
     * @return int
     * @throws BambooException
     * @throws \Exception
     */
    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $foisonId = $data["foisonId"];
        $nContract = $data["nContract"];
        $dContract = $data["dContract"];

        if(empty($foisonId) || empty($nContract) || empty($dContract)){
            $res = "Inserisci tutti i dati";
            return $res;
        }

        /** @var CFoison $foison */
        $foison = \Monkey::app()->repoFactory->create('Foison')->findOneBy(['id'=>$foisonId]);

        if($foison->getContract()) return "Il Fason ha un contratto in essere";
        /** @var CContractsRepo $contractsRepo */
        $contractsRepo = \Monkey::app()->repoFactory->create('Contracts');

        /** @var CContracts $contr */
        $contr = $contractsRepo->createNewContract($foison, $nContract, $dContract);
        if(is_object($contr)){
            $res = "Contratto creato con successo";
        } else {
            return "Errore durante la creazione del contratto. Contattare l'assistenza tecnica.";
        };

        $cId = $contr->id;
        $body = "
        Le confermiamo che il contratto $cId-$nContract è stato creato con successo.<br/>
        Cordiali saluti,<br /><br />
        Gianluca Cartechini<br />
        Iwes
        ";
        /** @var CEmailRepo $mail */
        $mail = \Monkey::app()->repoFactory->create('Email');
        $foisonMail = $foison->email;
        $mail->newMail('gianluca@iwes.it', [$foisonMail], [], [], "Contratto creato con successo", $body);

        return $res;
    }


    public function put() {
        $cId = \Monkey::app()->router->request()->getRequestData('contractId');

        /** @var CContracts $contract */
        $contract = \Monkey::app()->repoFactory->create('Contracts')->findOneBy(["id"=>$cId]);

        if($contract->isActive == 0) return "Il contratto è gia stato chiuso";

        /** @var CObjectCollection $contractDetails */
        $contractDetails = $contract->contractDetails;

        $unClosedProductBatch=[];

        /** @var CContractDetails $contractDetail */
        foreach ($contractDetails as $contractDetail) {

            /** @var CObjectCollection $pbS */
            $pbS = $contractDetail->productBatch;

            /** @var CProductBatch $pb */
            foreach ($pbS as $pb) {
                if ($pb->closingDate == 0) {
                    $unClosedProductBatch[] = $pb->id;
                }
            }
        }

        if(!empty($unClosedProductBatch)) {
            $unclosed = implode(', ', $unClosedProductBatch);
            return "Non è stato possibile chiudere il contratto in quanto sono ancora attivi i lotti: $unclosed";
        }

        $contract->isActive = 0;
        $contract->update();
        return "Contratto chiuso con successo";
    }

}
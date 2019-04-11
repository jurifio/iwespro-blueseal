<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CContracts;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CFoisonHasInterest;
use bamboo\domain\entities\CFoisonSubscribeRequest;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CUserRepo;
use bamboo\domain\repositories\CWorkCategoryRepo;


/**
 * Class CFoisonSubscribeRequestInterest
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/09/2018
 * @since 1.0
 */
class CFoisonSubscribeRequestInterest extends AAjaxController
{
    public function get()
    {

        $rId = \Monkey::app()->router->request()->getRequestData('id');

        $r = \Monkey::app()->repoFactory->create('FoisonSubscribeRequest')->findOneBy(["id" => $rId]);

        $cat = [];

        $interests = $r->workCategory;
        $a = 0;
        /** @var CWorkCategory $interest */
        foreach ($interests as $interest) {
            $cat[$a]["id"] = $interest->id;
            $cat[$a]["interestName"] = $interest->interestName;
            $a++;
        }

        return json_encode($cat);
    }

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function post()
    {

        try {
            \Monkey::app()->dbAdapter->beginTransaction();


            $idRequest = \Monkey::app()->router->request()->getRequestData('idRequest');
            $idsInterest = \Monkey::app()->router->request()->getRequestData('idsInterest');


            //Accept the request
            /** @var CFoisonSubscribeRequest $request */
            $request = \Monkey::app()->repoFactory->create('FoisonSubscribeRequest')->findOneBy(['id' => $idRequest]);
            $request->status = 'accepted';
            $request->update();


            //User creation
            /** @var CUserRepo $userRepo */
            $userRepo = \Monkey::app()->repoFactory->create('User');
            $user = $userRepo->insertUserFromData($request->email, $request->name, $request->surname, $request->birthday, $request->gender, $request->phone, 'Fason request');


            //Fason creation and rbacRole assignment
            /** @var CFoisonRepo $foisonRepo */
            $foisonRepo = \Monkey::app()->repoFactory->create('Foison');
            /** @var CFoison $foison */
            $foison = $foisonRepo->assignUser($user['user']->userDetails->name, $user['user']->userDetails->surname, $user['user']->email, $user['user']->id);


            //Interest population
            /** @var CRepo $foisonInterestRepo */
            $foisonInterestRepo = \Monkey::app()->repoFactory->create('FoisonHasInterest');
            foreach ($idsInterest as $idInterest) {
                $foisonInterest = $foisonInterestRepo->getEmptyEntity();
                $foisonInterest->foisonId = $foison->id;
                $foisonInterest->workCategoryId = $idInterest;
                $foisonInterest->foisonStatusId = 1;
                $foisonInterest->smartInsert();
            }


            //Generic contract creation
            /** @var CContractsRepo $contractRepo */
            $contractRepo = \Monkey::app()->repoFactory->create('Contracts');
            $userName = $user['user']->getFullName();
            /** @var CContracts $contract */
            $contract = $contractRepo->createNewContract($foison, CContracts::DEFAULT_CONTRACT_NAME_REQUEST_FASON . $userName, null, 0);


            //Contracts details creation
            /** @var CContractDetailsRepo $contractDetailsRepo */
            $contractDetailsRepo = \Monkey::app()->repoFactory->create('ContractDetails');
            /** @var CWorkCategoryRepo $workCategoryRepo */
            $workCategoryRepo = \Monkey::app()->repoFactory->create('WorkCategory');
            $interestForEmail = "";
            foreach ($idsInterest as $idInterest) {
                $interest = $workCategoryRepo->findOneBy(['id' => $idInterest])->interestName;
                $interestForEmail .= $interest . ', ';
                $detailsName = "Dettaglio contratto per l'interesse: $interest";
                $contractDetailsRepo->createNewContractDetail('v', $contract->id, $idInterest, null, $detailsName, null, null);
            }


            \Monkey::app()->dbAdapter->commit();

            /** @var CEmailRepo $emailRepo */
            $emailRepo = \Monkey::app()->repoFactory->create('Email');
            $subject = "Conferma creazione utente e accettazione competenze";
            $linkContractDetailAccept = $url = \Monkey::app()->baseUrl(false) . "/blueseal/work/contratti/".$contract->id;
            $pw = $user['pw'];
            $linkUser = "www.pickyshop.com/blueseal/work/foison/$foison->id";
            $body = "Salve,<br>Le comunichiamo con piacere che il contratto con n. $contract->id è stato accettato.
                        <br>Le categorie d'interesse che Le abbiamo impostato sono: $interestForEmail.
                        <br><br>Prima di inizare ad accettare i lotti deve accettare il contratto al seguente link: $linkContractDetailAccept
                        <br><br>Puo effettuare l'accesso utilizzando le seguenti credenziali:
                        <br>Username: $foison->email
                        <br>Password: $pw
                        <br>Potrà cambiare la sua password seguendo il link: $linkUser
                        <br><br>Cordiali saluti
                        <br>Gianluca Cartechini
                        <br><br>Iwes s.n.c.";

            $emailRepo->newMail('gianluca@iwes.it', [$foison->email], [], [], $subject, $body);

            return "Il Fason <strong>$userName</strong> è stato creato correttamente.
                <br>Numero di contratto: $contract->id";

        } catch (\Throwable $e) {
            \Monkey::app()->dbAdapter->rollBack();
            return $e->getMessage();
        }

    }
}
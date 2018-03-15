<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CUserRepo;


/**
 * Class CFoisonManage
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
class CFoisonManage extends AAjaxController
{
    /**
     * @return int
     * @throws BambooException
     * @throws \Exception
     */
    public function post()
    {
     $data = \Monkey::app()->router->request()->getRequestData();
     $emailData = $data["email"];
     $iban = $data["iban"];

     if(is_null($emailData) || is_null($iban)){
         $res = "Inserisci tutti i dati";
         return $res;
     }

     $emailData = trim($emailData);

     /** @var CFoisonRepo $foisonRepo */
     $foisonRepo = \Monkey::app()->repoFactory->create('Foison');

     /** @var CUserRepo $userRepo */
     $userRepo = \Monkey::app()->repoFactory->create('User');

     /** @var CUser $user */
     $user = $userRepo->findOneBy(['email'=>$emailData]);

     if(is_null($user)){
         $res = "L'utente che stai associando non esiste";
     } else {
         //se trovo l'utente assegnalo
         $userId = $user->id;
         $name = $user->userDetails->name;
         $surname = $user->userDetails->surname;
         $email = $user->getEmail();
         $createdFaison = $foisonRepo->assignUser($name, $surname, $email, $iban, $userId);

         if($createdFaison){
             $res = "Utente associato con successo";
         } else {
             $res = "Utente non associato, contattare il reparto tecnico";
         }
     }

     return $res;
    }


}
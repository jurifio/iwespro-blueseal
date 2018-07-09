<?php

namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CNewsletterInsertion;
use bamboo\domain\repositories\CNewsletterInsertionRepo;


/**
 * Class CNewsletterInsertionManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 09/07/2018
 * @since 1.0
 */
class CNewsletterInsertionManage extends AAjaxController
{
   public function post(){

       $name = \Monkey::app()->router->request()->getRequestData('name');
       $eventId = \Monkey::app()->router->request()->getRequestData('eventId');

       if(empty($name)) return "Non hai inserito il nome per l'inserzione";


       /** @var CNewsletterInsertionRepo $niRepo */
       $niRepo = \Monkey::app()->repoFactory->create('NewsletterInsertion');

       if($niRepo->createNewInsertion($eventId, $name)) return 'Evento inserito con successo';

       return "Errore durante l'inserimento";
   }

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
   public function put(){
       $name = \Monkey::app()->router->request()->getRequestData('name');
       $eventId = \Monkey::app()->router->request()->getRequestData('eventId');

       if(empty($name)) return "Non hai inserito il nuovo nome per l'inserzione";

       /** @var CNewsletterInsertionRepo $niRepo */
       $niRepo = \Monkey::app()->repoFactory->create('NewsletterInsertion');

       if($niRepo->modifyInsertion($eventId, $name)) return "Evento modificato con successo";

       return "Errore durante la modifica";
   }

   public function delete(){

   }
}
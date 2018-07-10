<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CNewsletterCampaign;
use bamboo\domain\entities\CNewsletterEvent;
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

       $res = null;
       $data = \Monkey::app()->router->request()->getRequestData();


       if(empty($data['nameInsertion'])) return false;

       /** @var CNewsletterInsertionRepo $niRepo */
       $niRepo = \Monkey::app()->repoFactory->create('NewsletterInsertion');

       if($data["type"] == 1){
           if($niRepo->createNewInsertion($data['eventId'], $data['nameInsertion'])) {$res = $data['eventId'];}
       } else {
           if(empty($data['nameCampaign']) || empty($data['startDate']) || empty($data['endDate']) || empty($data['nameEvent'])) return false;

           /** @var CRepo $cRepo */
           $cRepo = \Monkey::app()->repoFactory->create('NewsletterCampaign');
           /** @var CNewsletterCampaign $c */
           $c = $cRepo->getEmptyEntity();
           $c->name = $data['nameCampaign'];
           $c->dateCampaignStart = $data['startDate'];
           $c->dateCampaignFinish = $data['endDate'];
           $c->smartInsert();

           /** @var CRepo $eRepo */
           $eRepo = \Monkey::app()->repoFactory->create('NewsletterEvent');
           /** @var CNewsletterEvent $e */
           $e = $eRepo->getEmptyEntity();
           $e->name = $data["nameEvent"];
           $e->newsletterCampaignId = $c->id;
           $e->smartInsert();

           if($niRepo->createNewInsertion($e->id, $data['nameInsertion'])) {$res = $e->id;}
       }

       return $res;
   }

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
   public function put(){
       $name = \Monkey::app()->router->request()->getRequestData('name');
       $insertionId = \Monkey::app()->router->request()->getRequestData('insertionId');

       if(empty($name)) return "Non hai inserito il nuovo nome per l'inserzione";

       /** @var CNewsletterInsertionRepo $niRepo */
       $niRepo = \Monkey::app()->repoFactory->create('NewsletterInsertion');

       if($niRepo->modifyInsertion($insertionId, $name)) return "Inserzione modificata con successo";

       return "Errore durante la modifica";
   }

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
   public function delete(){

       $insertionId = \Monkey::app()->router->request()->getRequestData('insertionId');

       /** @var CNewsletterInsertionRepo $niRepo */
       $niRepo = \Monkey::app()->repoFactory->create('NewsletterInsertion');

       if($niRepo->deleteInsertion($insertionId)) return 'Inserzione eliminata con successo';

       return "Stai cercando di cancellare un'inserzione con delle newsletter";
   }
}
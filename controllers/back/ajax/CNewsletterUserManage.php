<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CNewsletterTemplate;
use bamboo\domain\entities\CNewsletterUser;
use bamboo\domain\repositories\CNewsletterRepo;


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
class CNewsletterUserManage extends AAjaxController
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
        $fromEmailAddressId    = $data['fromEmailAddressId'];
        $sendAddressDate       = $data['sendAddressDate'];
        $newsletterEmailListId = $data['newsletterEmailListId'];
        $newsletterTemplateId  = $data['newsletterTemplateId'];
        $subject               = $data['subject'];
        $dataDescription       = $data['dataDescription'];
        $preCompiledTemplate   = $data['preCompiledTemplate'];
        $campaignId            = $data['campaignId'];

        /** @var CRepo $newsletterTemplateRepo */
        $newsletterTemplateRepo =\Monkey::app()->repoFactory->create('NewsletterTemplate');

        /** @var CNewsletterTemplate $newsletterTemplate */
        $newsletterTemplate =  $newsletterTemplateRepo->findOneBy(['template'=>$newsletterTemplateId]);
        $newsletterTemplateId= $newsletterTemplate->id;

        /** @var CRepo $newsletterUserRepo */
        $newsletterUserRepo = \Monkey::app()->repoFactory->create('Newsletter');

        /** @var CNewsletterUser $newsletterUser */
        $newsletterUser = $newsletterUserRepo->findOneBy(['name' => $name]);


        if (empty($newsletterUser)){
            //se la variabile non è istanziata inserisci in db

        /** @var CNewsletterUser $newsletterUserInsert   */
        $newsletterUserInsert = \Monkey::app()->repoFactory->create('Newsletter')->getEmptyEntity();
        //popolo la tabella

         $newsletterUserInsert->name = $name;
         $newsletterUserInsert->fromEmailAddressId     = $fromEmailAddressId;
         $newsletterUserInsert->sendAddressDate        = $sendAddressDate;
         $newsletterUserInsert->newsletterEmailListId  = $newsletterEmailListId;
         $newsletterUserInsert->newsletterTemplateId   = $newsletterTemplateId;
         $newsletterUserInsert->subject                = $subject;
         $newsletterUserInsert->dataDescription        = $dataDescription;
         $newsletterUserInsert->preCompiledTemplate    = $preCompiledTemplate;
         $newsletterUserInsert->campaignId             = $campaignId;
        // eseguo la commit sulla tabella;

         $newsletterUserInsert->smartInsert();

            $res = "Newsletter inserita con successo!";

        }else{
            //Se hai trovato qualcosa allora restituitsci messaggio di errore
            $res = "Esiste già una newsletter con lo stesso nome";
        }

        return $res;












    }



}
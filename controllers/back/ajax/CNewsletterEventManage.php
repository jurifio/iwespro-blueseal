<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CNewsletterEvent;
use bamboo\domain\repositories\CNewsletterEventRepo;


/**
 * Class CNewsletterEventManage
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
class CNewsletterEventManage extends AAjaxController
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
        $campaignId = $data['campaignId'];






        /** @var CRepo $newsletterEventRepo */
        $newsletterEventRepo = \Monkey::app()->repoFactory->create('NewsletterEvent');

        /** @var CNewsletterEvent $newsletterEvent*/
        $newsletterEventRepo = $newsletterEventRepo->findOneBy(['name' => $name]);


        if (empty($newsletterEvent)){
            //se la variabile non è istanziata inserisci in db

            /** @var CNewsletterEvent $newsletterEventInsert   */
            $newsletterEventInsert = \Monkey::app()->repoFactory->create('NewsletterEvent')->getEmptyEntity();
            //popolo la tabella

            $newsletterEventInsert->name = $name ;
            $newsletterEventInsert->newsletterCampaignId = $campaignId;




            // eseguo la commit sulla tabella;

            $newsletterEventInsert->smartInsert();

            $res = "Evento Campagna Newsletter inserita con successo!";

        }else{
            //Se hai trovato qualcosa allora restituitsci messaggio di errore
            $res = "Esiste già un Evento Campagna Newsletter  con lo stesso nome";
        }

        return $res;












    }



}
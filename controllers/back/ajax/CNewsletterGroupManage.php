<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CNewsletterGroup;
use bamboo\domain\repositories\CNewsletterGroupRepo;


/**
 * Class CNewsletterGroupManage
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
class CNewsletterGroupManage extends AAjaxController
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
        $sql = $data['sql'];






        /** @var CRepo $newsletterGroupRepo */
        $newsletterGroupRepo = \Monkey::app()->repoFactory->create('NewsletterGroup');

        /** @var CNewsletterGroup $newsletterGroup*/
        $newsletterGroupRepo = $newsletterGroupRepo->findOneBy(['name' => $name]);


        if (empty($newsletterGroup)){
            //se la variabile non è istanziata inserisci in db

            /** @var CNewsletterGroup $newsletterGroupInsert   */
            $newsletterGroupInsert = \Monkey::app()->repoFactory->create('NewsletterGroup')->getEmptyEntity();
            //popolo la tabella

            $newsletterGroupInsert->name = $name ;
            $newsletterGroupInsert->sql = $sql;




            // eseguo la commit sulla tabella;

            $newsletterGroupInsert->smartInsert();

            $res = "Gruppo Destinatari Newsletter inserito con successo!";

        }else{
            //Se hai trovato qualcosa allora restituitsci messaggio di errore
            $res = "Esiste già un Gruppo Destinatari Newsletter  con lo stesso nome";
        }

        return $res;












    }



}
<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CNewsletterTemplate;
use bamboo\domain\repositories\CNewsletterTemplateRepo;


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
class CNewsletterTemplateManage extends AAjaxController
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
        $template = $data['template'];




        /** @var CRepo $newsletterTemplateRepo */
        $newsletterTemplateRepo = \Monkey::app()->repoFactory->create('NewsletterTemplate');

        /** @var CNewsletterTemplate $newsletterTemplate */
        $newsletterTemplateRepo = $newsletterTemplateRepo->findOneBy(['name' => $name]);


        if (empty($newsletterTemplate)){
            //se la variabile non è istanziata inserisci in db

            /** @var CNewsletterTemplate $newsletterTemplateInsert   */
            $newsletterTemplateInsert = \Monkey::app()->repoFactory->create('NewsletterTemplate')->getEmptyEntity();
            //popolo la tabella

            $newsletterTemplateInsert->name = $name ;
            $newsletterTemplateInsert->template = $template;


            // eseguo la commit sulla tabella;

            $newsletterTemplateInsert->smartInsert();

            $res = "Template inserito con successo!";

        }else{
            //Se hai trovato qualcosa allora restituitsci messaggio di errore
            $res = "Esiste già un file  template  con lo stesso nome";
        }

        return $res;

    }
    public function put()
    {
        //prendo i dati passati in input
        $data = \Monkey::app()->router->request()->getRequestData();
        $name = $data['name'];
        $template = $data['template'];
        $id = $data['id'];


        /** @var CRepo $newsletterTemplateRepo */
        $newsletterTemplateRepo = \Monkey::app()->repoFactory->create('NewsletterTemplate');

        /** @var CNewsletterTemplate $newsletterTemplate */
        $newsletterTemplate = $newsletterTemplateRepo->findOneBy(['id' => $id]);


        if (!empty($newsletterTemplate)){
            //se la variabile non è istanziata inserisci in db

            $newsletterTemplate->name = $name ;
            $newsletterTemplate->template = $template;


            // eseguo la commit sulla tabella;

            $newsletterTemplate->update();

            $res = "Template Modifcato con successo!";

        }else{
            //Se hai trovato qualcosa allora restituitsci messaggio di errore
            $res = "non è stato trovato il template";
        }

        return $res;



    }



}
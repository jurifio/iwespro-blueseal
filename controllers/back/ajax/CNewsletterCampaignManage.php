<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CNewsletterCampaign;
use bamboo\domain\repositories\CNewsletterCampaignRepo;


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
class CNewsletterCampaignManage extends AAjaxController
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
        $dateCampaignStart = $data['dateCampaignStart'];
        $dateCampaignFinish = $data['dateCampaignFinish'];





        /** @var CRepo $newsletterCampaignRepo */
        $newsletterCampaignRepo = \Monkey::app()->repoFactory->create('NewsletterCampaign');

        /** @var CNewsletterCampaign $newsletterCampaign*/
        $newsletterCampaignRepo = $newsletterCampaignRepo->findOneBy(['name' => $name]);


        if (empty($newsletterCampaign)){
            //se la variabile non è istanziata inserisci in db

            /** @var CNewsletterCampaign $newsletterCampaignInsert   */
            $newsletterCampaignInsert = \Monkey::app()->repoFactory->create('NewsletterCampaign')->getEmptyEntity();
            //popolo la tabella

            $newsletterCampaignInsert->name = $name ;
            $newsletterCampaignInsert->dateCampaignStart = $dateCampaignStart;
            $newsletterCampaignInsert->dateCampaignFinish = $dateCampaignFinish;



            // eseguo la commit sulla tabella;

            $newsletterCampaignInsert->smartInsert();

            $res = "Campagna Newsletter inserita con successo!";

        }else{
            //Se hai trovato qualcosa allora restituitsci messaggio di errore
            $res = "Esiste già un Campagna Newsletter  con lo stesso nome";
        }

        return $res;
    }


    /**
     * @return mixed
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put()
    {
       $data  = $this->app->router->request()->getRequestData();
       $id = $data["id"];
       $name = $data["name"];
       $startDate = $data["dateCampaignStart"];
       $finishDate = $data["dateCampaignFinish"];

       /** @var CRepo $newsletterCampaign */
       $newsletterCampaign = \Monkey::app()->repoFactory->create('NewsletterCampaign');

       /** @var CNewsletterCampaign $newsletter */
       $newsletter = $newsletterCampaign->findOneBy(['id'=>$id]);

        $newsletter->name = $name;
        $newsletter->dateCampaignStart = $startDate;
        $newsletter->dateCampaignFinish = $finishDate;


        $newsletter->update();

        $res = "Campagna aggiornata";
        return $res;
    }



}
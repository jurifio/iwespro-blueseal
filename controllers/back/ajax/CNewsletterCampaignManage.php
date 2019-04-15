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
        $nameShop = $data["nameShop"];

        if(empty($name) || empty($nameShop)) return "Inserisci tutti i campi";


        /** @var CRepo $newsletterCampaignRepo */
        $newsletterCampaignRepo = \Monkey::app()->repoFactory->create('NewsletterCampaign');

        /** @var CNewsletterCampaign $newsletterCampaign*/
        $newsletterCampaign = $newsletterCampaignRepo->findOneBy(['name' => $name]);


        if (empty($newsletterCampaign)){
            //se la variabile non è istanziata inserisci in db

            /** @var CNewsletterCampaign $newsletterCampaignInsert   */
            $newsletterCampaignInsert = $newsletterCampaignRepo->getEmptyEntity();

            $newsletterCampaignInsert->name = $name;
            $newsletterCampaignInsert->dateCampaignStart = $dateCampaignStart;
            $newsletterCampaignInsert->dateCampaignFinish = $dateCampaignFinish;
            $newsletterCampaignInsert->newsletterShopId = $nameShop;
            $newsletterCampaignInsert->smartInsert();

            $res = "La campagna è stata creata con successo";

        } else {
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
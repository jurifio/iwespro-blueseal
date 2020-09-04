<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CNewsletterCampaign;
use bamboo\domain\repositories\CNewsletterCampaignRepo;


/**
 * Class CEditorialPlanManage
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
class CEditorialPlanManage extends AAjaxController
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
        $startDate = $data['startDate'];
        $endDate = $data['endDate'];
        $shopId = $data['shopId'];
        $contractId=$data['contract'];


        /** @var CRepo $editorialPlanRepo */
        $editorialPlanRepo = \Monkey::app()->repoFactory->create('EditorialPlan');

        /** @var CEditorialPlan $editorialPlan */
        $editorialPlanRepo = $editorialPlanRepo->findOneBy(['name' => $name]);


        if (empty($editorialPlan)) {
            //se la variabile non è istanziata inserisci in db

            /** @var CEditorialPlan $editorialPlanInsert */
            $editorialPlanInsert = \Monkey::app()->repoFactory->create('EditorialPlan')->getEmptyEntity();
            //popolo la tabella

            $editorialPlanInsert->name = $name;
            $editorialPlanInsert->shopId = $shopId;
            $editorialPlanInsert->startDate = $startDate;
            $editorialPlanInsert->endDate = $endDate;
            $editorialPlanInsert->contractId=$contractId;


            // eseguo la commit sulla tabella;

            $editorialPlanInsert->smartInsert();

            $res = "Piano Editoriale inserito con successo!";

        } else {
            //Se hai trovato qualcosa allora restituitsci messaggio di errore
            $res = "Esiste già un Piano Editoriale con lo stesso nome";
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
        $data = $this->app->router->request()->getRequestData();
        $id = $data["id"];

        $name = $data["name"];
        $shopId = $data['shopId'];
        $startDate = $data["dateStartDate"];
        $endDate = $data["dateEndDate"];
        $contractId=$data['contract'];


        /** @var CRepo $editorialPlan */
        $editorialPlan = \Monkey::app()->repoFactory->create('EditorialPlan');

        /** @var CEditorialPlan $editorialPlan */
        $editorialPlan = $editorialPlan->findOneBy(['id' => $id]);
        $editorialPlan->name = $name;
        if ($startDate!="0000-00-00 00:00:00") {
            $editorialPlan->startDate = $startDate;
        }
        if ($endDate!="0000-00-00 00:00:00") {
            $editorialPlan->endDate = $endDate;
        }
        if ($shopId!="") {
            $editorialPlan->shopId = $shopId;
        }
        if ($contractId!=""){
            $editorialPlan->contractId = $contractId;
        }


        $editorialPlan->update();

        $res = "Piano Editoriale  aggiornato";
        return $res;
    }


}
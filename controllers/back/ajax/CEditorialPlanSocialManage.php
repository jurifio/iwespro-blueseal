<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanSocial;
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
class CEditorialPlanSocialManage extends AAjaxController
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
        $iconSocial = $data['iconSocial'];


        /** @var CRepo $editorialPlanSocialRepo */
        $editorialPlanSocialRepo = \Monkey::app()->repoFactory->create('EditorialPlanSocial');

        /** @var CEditorialPlanSocial $editorialPlanSocial */
        $editorialPlanSocial = $editorialPlanSocialRepo->findOneBy(['name' => $name]);


        if (empty($editorialPlanSocial)) {
            //se la variabile non è istanziata inserisci in db

            /** @var CEditorialPlanSocial $editorialPlanSocialInsert */
            $editorialPlanSocialInsert = \Monkey::app()->repoFactory->create('EditorialPlanSocial')->getEmptyEntity();
            //popolo la tabella

            $editorialPlanSocialInsert->name = $name;
            $editorialPlanSocialInsert->iconSocial = $iconSocial;


            // eseguo la commit sulla tabella;

            $editorialPlanSocialInsert->smartInsert();

            $res = "Media inserito con successo!";

        } else {
            //Se hai trovato qualcosa allora restituitsci messaggio di errore
            $res = "Esiste già un Media con lo stesso nome";
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
        if (strlen($id)>10) {
            $finalpositionId = strrpos($id, '</a>');
            $initialpositionId = strrpos($id, '" >');
            $finalpositionId = $finalpositionId;
            $initialpositionId = $initialpositionId + 2;
            $lenghtposition = $finalpositionId - $initialpositionId;
            $id = substr($id, $initialpositionId, $lenghtposition);
            $id =str_replace('>','',$id);
        }
        $name = $data["name"];
        $iconSocial =$data["iconSocial"];


        /** @var CRepo $editorialPlanSocialRepo */
        $editorialPlanSocialRepo = \Monkey::app()->repoFactory->create('EditorialPlanSocial');

        /** @var CEditorialPlanSocial $editorialPlanSocial */
        $editorialPlanSocial = $editorialPlanSocialRepo->findOneBy(['id' => $id]);
        $editorialPlanSocial->name = $name;
        $editorialPlanSocial->iconSocial = $iconSocial;



        $editorialPlanSocial->update();

        $res = "Media   aggiornato";
        return $res;
    }


}
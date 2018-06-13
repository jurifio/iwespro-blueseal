<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanArgument;
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
class CEditorialPlanArgumentManage extends AAjaxController
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
        $titleArgument = $data['titleArgument'];
        $type = $data['type'];
        $descriptionArgument =$data['descriptionArgument'];




        /** @var CRepo $editorialPlanArgumentRepo */
        $editorialPlanArgumentRepo = \Monkey::app()->repoFactory->create('EditorialPlanArgument');

        /** @var CEditorialPlanArgument $editorialPlanArgument */
        $editorialPlanArgument = $editorialPlanArgumentRepo->findOneBy(['titleArgument' => $titleArgument]);


        if (empty($editorialPlanArgument)) {
            //se la variabile non è istanziata inserisci in db

            /** @var CEditorialPlanArgument $editorialPlanArgumentInsert */
            $editorialPlanArgumentInsert = \Monkey::app()->repoFactory->create('EditorialPlanArgument')->getEmptyEntity();
            //popolo la tabella

            $editorialPlanArgumentInsert->titleArgument = $titleArgument;
            $editorialPlanArgumentInsert->type = $type;
            $editorialPlanArgumentInsert->descriptionArgument = $descriptionArgument;



            // eseguo la commit sulla tabella;

            $editorialPlanArgumentInsert->smartInsert();

            $res = "Argomento inserito con successo!";

        } else {
            //Se hai trovato qualcosa allora restituitsci messaggio di errore
            $res = "Esiste già un Argomento con lo stesso nome";
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
        $titleArgument = $data["titleArgument"];
        $type=$data["type"];
        $descriptionArgument =$data["descriptionArgument"];


        /** @var CRepo $editorialPlanArgumentRepo */
        $editorialPlanArgumentRepo = \Monkey::app()->repoFactory->create('EditorialPlanArgument');

        /** @var CEditorialPlanArgument $editorialPlanArgument */
        $editorialPlanArgument = $editorialPlanArgumentRepo->findOneBy(['id' => $id]);
        $editorialPlanArgument->titleArgument = $titleArgument;
        $editorialPlanArgument->type = $type;
        $editorialPlanArgument->descriptionArgument = $descriptionArgument;



        $editorialPlanArgument->update();

        $res = "Argomento   aggiornato";
        return $res;
    }


}
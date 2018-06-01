<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchDetailsRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CSectionalRepo;
use bamboo\domain\repositories\CUserRepo;


/**
 * Class CProductBatchDetailsManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 21/03/2018
 * @since 1.0
 */
class CProductBatchDetailsManage extends AAjaxController
{
    /**
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {
        $ids = \Monkey::app()->router->request()->getRequestData('ids');

        if(empty($ids)){
            $res = "[error on php procedure]- Non sono stati passati gli id. Contattare l'assistenza tecnica";
            return $res;
        }

        /** @var CProductBatchDetailsRepo $pbDRepo */
        $pbDRepo = \Monkey::app()->repoFactory->create('ProductBatchDetails');

        foreach ($ids as $id){

            /** @var CProductBatchDetails $pbd */
            $pbd = $pbDRepo->findOneBy(['id'=>$id]);

            $catToChange = $pbd->workCategoryStepsId;

            $pbDRepo->goToNextStep($id);


            if($catToChange == CProductBatchDetails::UNFIT_NORM){


                /** @var CProductBatch $pb */
                $pb = $pbd->productBatch;

                if($pb->isValid() == 'ok'){
                    $pb->isFixed = 1;
                    $pb->unfitDate = date('Y-m-d H:i:s');
                    $pb->update();
                }
            }
        }

        $res = "Procedura di aggiornamento fase di lavoro completata con successo";
        return $res;
    }

    /**
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function get(){

        $isComplete = true;

        $productBatchId = \Monkey::app()->router->request()->getRequestData('productBatchId');

        /** @var CProductBatch $productBatch */
        $productBatch = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(['id'=>$productBatchId]);

        /** @var CObjectCollection $pbDetails */
        $pbDetails = $productBatch->productBatchDetails;

        /** @var CProductBatchDetails $singleProductBatchDetails */
        foreach ($pbDetails as $singleProductBatchDetails){
            if(!is_null($singleProductBatchDetails->workCategorySteps->rgt)){
                $isComplete = false;
                break;
            }
        }

        if($isComplete){
            /** @var CUser $user */
            $user = \Monkey::app()->getUser();
            $name = $user->getFullName();
            $email = $user->getEmail();

            /** @var CEmailRepo $emailRepo */
            $emailRepo = \Monkey::app()->repoFactory->create('Email');

            $subject = "Termine del lotto n. ".$productBatchId;

            $body = "Il Foison ".$name."(". $email .")"." chiede la revisione del lotto n. ".$productBatchId." per presunto fine lavoro";


            $emailRepo->newMail('gianluca@iwes.it', ['gianluca@iwes.it'], [], [], $subject, $body);

            $res = "Mail inviata con successo";

        } else {
            $res = "Non hai completato tutte le normalizzazioni";
        }

        return $res;

    }


    /**
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put(){
        $ids = \Monkey::app()->router->request()->getRequestData('prod');
        $catId = \Monkey::app()->router->request()->getRequestData('cat');
        $pb = \Monkey::app()->router->request()->getRequestData('pb');

        foreach ($ids as $id){

            /** @var CProductBatchDetails $pd */
            $pd = \Monkey::app()->repoFactory->create('ProductBatchDetails')->findOneBy(['id'=>$id]);

            $pd->workCategoryStepsId = $catId;
            $pd->update();


            /** @var CProductBatch $pb */
            $pba = $pd->productBatch;
            if($catId == CProductBatchDetails::UNFIT_NORM){
                $pba->isFixed = 0;
                $pba->update();
            }
        }



        return 'Stati aggiornati con successo';
    }

}
<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CProductBatchRepo;


/**
 * Class CUnfitProductBatchManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/06/2018
 * @since 1.0
 */
class CUnfitProductBatchManage extends AAjaxController
{
    /**
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {

       $pbs = \Monkey::app()->router->request()->getRequestData('pB');

       /** @var CProductBatchRepo $pBatchRepo */
       $pBatchRepo = \Monkey::app()->repoFactory->create('ProductBatch');

       /** @var CEmailRepo $mailRepo */
       $mailRepo = \Monkey::app()->repoFactory->create('Email');

       foreach ($pbs as $pb){

           unset($res, $body, $unfitProduct);
           $unfitProduct = '';
           /** @var CProductBatch $pBatch */
           $pBatch = $pBatchRepo->findOneBy(['id'=>$pb['batch']]);

           $res = $pBatch->isValid();

           if($res === 'ok') continue;

           foreach ($res as $val){
               $unfitProduct .= $val.'<br>';
           }


           if(is_null($pBatch->unfitDate)){
               $pBatchRepo->qualityRank($pBatch);
           }

           $pBatch->unfitDate = date('Y-m-d H:i:s');
           $pBatch->update();



           if($pBatch->isUnassigned == 1) {
               $pBatchRepo->duplicateProductBatchFromCancelled($pBatch);

               /** @var CFoison $foison */
               $foison = $pBatch->contractDetails->contracts->foison;
               $foison->totalRank(true);

           }

           if(ENV == 'prod' && $pBatch->isUnassigned == 0){
               $body = "I seguenti prodotti non sono idonei:<br>
                    $unfitProduct";


               $mailRepo->newMail('gianluca@iwes.it', [$pb['fason']], [], [], "Prodotti non idonei", $body);
           }


       }

       return 'Notifiche inviate con successo';

    }

}
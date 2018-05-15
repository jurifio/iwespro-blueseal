<?php

namespace bamboo\controllers\back\ajax;


use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CProductBatchRepo;


/**
 * Class CProductBatchAccept
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/03/2018
 * @since 1.0
 */
class CProductBatchAccept extends AAjaxController
{

    /**
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {
      $ids = [];
      $pbIds = \Monkey::app()->router->request()->getRequestData('productBatchIds');

      /** @var CProductBatchRepo $pbRepo */
      $pbRepo = \Monkey::app()->repoFactory->create('ProductBatch');

      foreach ($pbIds as $pbId){
          $ids[] = $pbRepo->acceptProductBatch($pbId);
      }

      /** @var CProductBatch $prBatch */
      $prBatch = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(['id'=>$ids[0]]);

      /** @var CFoison $foison */
      $foison = $prBatch->contractDetails->contracts->foison;

      $name = $foison->user->getFullName();
      $idH = implode(', ', $ids);


      /** @var CEmailRepo $mailRepo */
      $mailRepo = \Monkey::app()->repoFactory->create('Email');

        $body = "Il fason $name ha accettato i lotti con id: $idH";

        $mailRepo->newMail('gianluca@iwes.it', ['gianluca@iwes.it'], [], [], 'Conferma accettazione lotto lotto', $body);

      $res = "Lotto accettato con successo";
      return $res;

    }


}
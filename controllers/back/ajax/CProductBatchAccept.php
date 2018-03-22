<?php

namespace bamboo\controllers\back\ajax;


use bamboo\core\exceptions\BambooException;
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
      $pbIds = \Monkey::app()->router->request()->getRequestData('productBatchIds');

      /** @var CProductBatchRepo $pbRepo */
      $pbRepo = \Monkey::app()->repoFactory->create('ProductBatch');

      foreach ($pbIds as $pbId){
          $pbRepo->acceptProductBatch($pbId);
      }

      $res = "Lotto accettato con successo";
      return $res;

    }


}
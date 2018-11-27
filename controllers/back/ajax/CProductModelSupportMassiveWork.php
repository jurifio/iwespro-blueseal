<?php

namespace bamboo\controllers\back\ajax;


use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CProductBatchRepo;


/**
 * Class CProductModelSupportMassiveWork
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 26/11/2018
 * @since 1.0
 */
class CProductModelSupportMassiveWork extends AAjaxController
{

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function get()
    {
      $vals = \Monkey::app()->router->request()->getRequestData('values');

      $sql = "SELECT id FROM ProductSheetModelPrototypeSupport WHERE 1=1";


      foreach ($vals as $vs){
          foreach ($vs as $k=>$v)
              if(!empty($v)) {
                  $sql .= ' and ' . $k . ' RLIKE ' . '"' . $v . '"';
              }
      }

      $r = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();

      return json_encode($r);
    }


}
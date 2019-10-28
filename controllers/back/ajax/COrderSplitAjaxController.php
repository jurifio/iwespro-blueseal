<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\blueseal\business\CDownloadFileFromDb;
use bamboo\core\exceptions\BambooException;
use bamboo\core\intl\CLang;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\domain\entities\CInvoiceDocument;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CUser;
use bamboo\utils\price\SPriceToolbox;
use bamboo\domain\entities\CUserAddress;
use PDO;
use PDOException;

/**
 * Class COrderSplitAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 28/10/2019
 * @since 1.0
 */
class COrderSplitAjaxController extends AAjaxController
{


    public function POST()
    {
        $orderId = \Monkey::app()->router->request()->getRequestData('orderId');
      $orderRepo=\Monkey::app()->repoFactory->create('Order');
      $orderLineRepo=\Monkey::app()->repoFactory->create('OrderLine');
      $cartRepo=\Monkey::app()->repoFactory->create('Cart');
      $cartLineRepo=\Monkey::app()->repoFactory->create('CartLine');
      $originalOrder=$orderRepo->findOneBy(['id'=>'$orderId']);
      $orderLineCollect=$orderLineRepo->findBy(['orderId'=>$orderId]);
        $orderLineWorking = ['ORD_WAIT','ORD_PENDING','ORD_LAB','ORD_FRND_OK','ORD_FRND_SENT','ORD_CHK_IN','ORD_PCK_CLI','ORD_FRND_SNDING','ORD_MAIL_PREP_C','ORD_FRND_ORDSNT'];
        $orderLineShipped = ['ORD_ARCH','ORD_SENT','ORD_FRND_PYD'];
        $orderLineCancel = ['ORD_FRND_CANC','ORD_MISSNG','ORD_CANCEL','ORD_QLTY_KO','ORD_ERR_SEND'];

      foreach($orderLineCollect as $orderLines){
          if(in_array($orderLines->status,$orderLineCancel,true)){


          }else{
              continue;
          }

      }


return $res='ok';
    }
}
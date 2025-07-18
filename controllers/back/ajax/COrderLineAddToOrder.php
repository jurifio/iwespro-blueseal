<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\exceptions\BambooException;
use bamboo\core\traits\TMySQLTimestamp;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\COrder;
use bamboo\domain\repositories\COrderLineStatisticsRepo;
use bamboo\domain\repositories\COrderRepo;
use bamboo\domain\repositories\CProductSkuRepo;
use bamboo\domain\repositories\CStorehouseOperationRepo;

/**
 * Class CGetPermissionsForUser
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class COrderLineAddToOrder extends AAjaxController
{
    use TMySQLTimestamp;

    public function post()
    {
	    $productSkuStringId = \Monkey::app()->router->request()->getRequestData('productSkuStringId');
	    $orderId = \Monkey::app()->router->request()->getRequestData('orderId');
        /** @var COrderRepo $oR */
        $oR = \Monkey::app()->repoFactory->create('Order');
        /** @var CProductSkuRepo $psR */
        $psR = \Monkey::app()->repoFactory->create('ProductSku');
        /** @var CStorehouseOperationRepo $soR */
        $soR = \Monkey::app()->repoFactory->create('StorehouseOperation');

        $dba = \Monkey::app()->dbAdapter;
        \Monkey::app()->repoFactory->beginTransaction();
        try {
            /** @var CProductSku $ps */
            $ps = $psR->findOneByStringId($productSkuStringId);
            /** @var COrder $o */
            $o = $oR->findOne([$orderId]);

            $soR->registerEcommerceSale($ps->shopId, [$ps], null, true);

            $ol = $oR->AddOrderLineToOrder($o, $ps);

            \Monkey::app()->repoFactory->commit();

            return $ol->stringId();
        } catch(BambooException $e){
            \Monkey::app()->repoFactory->rollback();
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessate();
        }
    }
}
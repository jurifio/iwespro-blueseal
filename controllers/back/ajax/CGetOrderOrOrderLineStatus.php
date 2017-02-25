<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\exceptions\BambooException;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CFriendOrderRecordInvoice
 * @package bamboo\blueseal\controllers\ajax
 */
class CGetOrderOrOrderLineStatus extends AAjaxController
{
    public function get() {
        $stringId = \Monkey::app()->router->request()->getRequestData('stringId');
        $entityName = \Monkey::app()->router->request()->getRequestData('entityName');
        try {
            if ('OrderLine' !== $entityName && 'Order' !== $entityName) throw new BambooException('entityName must be "Order" or "OrderLine"');
            $o = \Monkey::app()->repoFactory->create($entityName)->findOneByStringId($stringId);

            return json_encode($o->statusLog);
        } catch (BambooException $e) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessage();
        }
    }
}
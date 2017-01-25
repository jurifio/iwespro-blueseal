<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\core\exceptions\BambooException;

/**
 * Class CFriendOrderPayInvoices
 * @package bamboo\blueseal\controllers\ajax
 */
class CFriendToZero extends AAjaxController
{
    /**
     * @transaction
     */
    public function post()
    {
        /** @var \bamboo\domain\repositories\CStorehouseOperationRepo $soR */
        $soR = \Monkey::app()->repoFactory->create('StorehouseOperation');
        $friendId = \Monkey::app()->router->request()->getRequestData('shopId');
        $dba = \Monkey::app()->dbAdapter;
        $dba->beginTransaction();
        try {
            var_dump($soR->AllSkusByFriendToZero($friendId));
            $dba->commit();
            return "Friend Offline!";
        } catch(BambooException $e) {
            $dba->rollBack();
            return $e->getMessage();
        }
    }
}
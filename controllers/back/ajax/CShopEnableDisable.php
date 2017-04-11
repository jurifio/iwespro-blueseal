<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooStorehouseOperationException;

/**
 * Class CFriendOrderPayInvoices
 * @package bamboo\blueseal\controllers\ajax
 */
class CShopEnableDisable extends AAjaxController
{
    /**
     * @return string
     */
    public function get() {
        try {
            $shp = \Monkey::app()->repoFactory->create('Shop')->findAll();
            $res = [];
            foreach ($shp as $v) {
                $single = [];
                $single['id'] = $v->id;
                $single['isActive'] = $v->isActive;
                $single['title'] = $v->title;
                $res[] = $single;
            }
            return json_encode($res);
        } catch (BambooException $e) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessage();
        }
    }
    /**
     * @transaction
     */
    public function post()
    {
        /** @var \bamboo\domain\repositories\CStorehouseOperationRepo $soR */
        $shpR = \Monkey::app()->repoFactory->create('Shop');
        $shopId = \Monkey::app()->router->request()->getRequestData('shopId');
        $action = \Monkey::app()->router->request()->getRequestData('action');

        $shop = $shpR->findOneByStringId($shopId);

        $dba = \Monkey::app()->dbAdapter;
        $dba->beginTransaction();
        $message = 'OOPS! Non ho fatto nulla';
        try {
            if ('stop' == $action) {
                $shpR->shutdownFriend($shopId);
                if($shop->importer) $message = 'Friend Offline, ricordati di disattivare il job o le quantità potrebbero essere ripristinate!';
                else $message = 'Friend Offline!';
            } elseif ('start' == $action) {
                $shpR->restartFriend($shopId);
                if($shop->importer) $message = 'Friend Online, ricordati di riattivare il job o le quantità potrebbero essere non essere aggiornate!';
                else $message = 'Friend Online!';
            }
            $dba->commit();
            return $message;
        } catch(BambooStorehouseOperationException $e) {
            $dba->rollBack();
            return $e->getMessage();
        } catch(BambooException $e) {
            $dba->rollBack();
            return $e->getMessage();
        }
    }
}
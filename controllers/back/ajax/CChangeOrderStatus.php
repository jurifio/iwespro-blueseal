<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\repositories\COrderRepo;
use PDO;
use PDOException;

/**
 * Class CChangeOrderStatus
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CChangeOrderStatus extends AAjaxController
{

    public function get() {
        try {
            $osOC = \Monkey::app()->repoFactory->create('OrderStatus')->findAll();
            $ret = [];
            $ret['statuses'] = [];
            foreach($osOC as $v) {
                $trans = $v->orderStatusTranslation->findOneByKey('langId', 1);
                $ret['statuses'][$v->id] = $trans->title;
            }
            return json_encode($ret);
        } catch(BambooException $e) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessate();
        }
    }

    /**
     * @return bool|string
     */
    public function put()
    {
        $dba = \Monkey::app()->dbAdapter;
        try {
            \Monkey::app()->repoFactory->beginTransaction();
            /** @var COrderRepo $oR */
            $oR = \Monkey::app()->repoFactory->create('Order');
            $datas = $this->data;
            $orders = new CObjectCollection();

            if (array_key_exists('order_id', $datas)) {
                $orders = $oR->findBy(['id' => $datas['order_id']]);
            } else {
                $orders = $oR->findBySql('SELECT id FROM `Order` WHERE id in ( ? )', [implode(',', $datas['orders'])]);
            }
            $now=new \DateTime();
            $dateNow=$now->format('Y-m-d H:i:s');
            foreach($orders as $order) {
                $oR->updateStatus($order, $datas['order_status']);
                $orderStatus=\Monkey::app()->repoFactory->create('OrderStatus')->findOneBy(['id'=>$datas['order_status']]);
                $codeStatus=$orderStatus->code;
                $remoteShopSellerId=$order->remoteShopSellerId;
                $remoteOrderSellerId=$order->remoteOrderSellerId;
                $shop = \Monkey ::app() -> repoFactory -> create('Shop') -> findOneBy(['id' => $remoteShopSellerId]);
                $db_host = $shop -> dbHost;
                $db_name = $shop -> dbName;
                $db_user = $shop -> dbUsername;
                $db_pass = $shop -> dbPassword;
                try {

                    $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
                    $db_con -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $res = " connessione ok <br>";
                } catch (PDOException $e) {
                    $res = $e -> getMessage();
                }
                try{
                    $stmtUpdateOrder=$db_con->prepare('UPDATE `Order` set `status`=\''.$codeStatus.'\' WHERE id='.$remoteOrderSellerId);
                    $stmtUpdateOrder->execute();
                    if ($codeStatus=='ORD_CANCEL' || $codeStatus=='ORD_RETURNED'){
                        if ($codestatus='ORD_CANCEL'){
                            $codeToDelete=2;
                        }else{
                            $codeToDelete=3;
                        }
                        $stmtUpdateRemoteShopMovements=$db_con->prepare("INSERT INTO ShopMovements (orderId,returnId,shopRefundRequestId,amount,`date`,valueDate,typeId,shopWalletId,note,isVisible,remoteIwesOrderId)
                    values(
                         '".$remoteOrderSellerId."',
                          null,
                          null,
                          '".$order->netTotal."',
                          '".$dateNow."',
                          '".$dateNow."',
                         '".$codeToDelete."',
                          1,
                          'ordine Cancellato',
                          1,
                          '".$order->id."'
                                                                                                                                                                                
                                                                                                                                                               
) ");
                        $stmtUpdateRemoteShopMovements->execute();
                    }
                }catch (\Throwable $e){
                    \Monkey::app()->applicationLog('CChangeStatusOrder','error', 'error change Status  remote Order',$e);

                }




                $order->note = $datas['order_note'] ?? null;
                $order->shipmentNote = $datas['order_shipmentNote'] ?? null;
                $order->isShippingToIwes=$datas['isShippingToIwes'] ?? null;
                $order->update();
            }
            \Monkey::app()->repoFactory->commit();
            return true;
        } catch (BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessage();
        }
    }
}
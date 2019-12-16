<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrder;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CChangeOrderPaymentMethodAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 13/12/2019
 * @since 1.0
 */
class CChangeOrderPaymentMethodAjaxController extends AAjaxController
{
    public function get()
    {
        $paymentMethod = [];
        $orderPaymentMethods = \Monkey ::app() -> repoFactory -> create('OrderPaymentMethod') -> findAll();

        foreach ($orderPaymentMethods as $orderPaymentMethod) {
            array_push($paymentMethod, [
                'id' => $orderPaymentMethod -> id,
                'name' => $orderPaymentMethod -> name
            ]);
        }

        return json_encode($paymentMethod);
    }

    /**
     * @return BambooException|BambooOrderLineException|\Exception|string
     * @transaction
     */
    public function post()
    {

        $request = \Monkey ::app() -> router -> request();
        $orderId = $request -> getRequestData('orderId');
        $orderPaymentMethodId = $request -> getRequestData('orderPaymentMethod');
        try {
            $order = \Monkey ::app() -> repoFactory -> create('Order') -> findOneBy(['id' => $orderId]);
            $order -> orderPaymentMethodId = $orderPaymentMethodId;
            $remoteOrderSellerId=$order->remoteOrderSellerId;
            $remoteShopSellerId=$order->remoteShopSellerId;
            $order -> update();
            $shopRepo = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $remoteShopSellerId]);
            $db_host = $shopRepo->dbHost;
            $db_name = $shopRepo->dbName;
            $db_user = $shopRepo->dbUsername;
            $db_pass = $shopRepo->dbPassword;
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $res = ' connessione ok <br>';
            } catch (PDOException $e) {
                $res = $e->getMessage();
            }
            try {
                if (ENV === 'prod') {
                    $stmtOrder = $db_con->prepare("UPDATE `Order` SET `orderPaymentMethodId`='" . $orderPaymentMethodId . "' WHERE id=" . $remoteOrderSellerId);
                    $stmtOrder->execute();
                    \Monkey::app()->applicationLog('CChangeOrderPaymentMethodAjaxController','Report', 'Change Order Payment Method '.$remoteOrderSellerId ,'','');
                }
            }catch (\Throwable $e){
                \Monkey::app()->applicationLog('CChangeOrderPaymentMethodAjaxController','Error', 'Change Order Remote  Payment Method '.$remoteOrderSellerId ,$e,'');
            }
            \Monkey::app()->applicationLog('CChangeOrderPaymentMethodAjaxController','Report', 'Change Order Payment Method '.$orderId ,'','');
            $res='Ordine' .$orderId.' aggiornato con il nuvo metodo di pagamento';
        }catch (\Throwable $e){
            $res='Errore c\'e\' stato un problema ';
            \Monkey::app()->applicationLog('CChangeOrderPaymentMethodAjaxController','Error', 'Change Order Payment Method '.$orderId ,$e,'');

        }
        return $res;
    }
}
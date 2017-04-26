<?php
namespace bamboo\blueseal\jobs;

use bamboo\domain\entities\COrder;
use bamboo\core\jobs\ACronJob;

/**
 * Class CCleanOrders
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CCleanOrders extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        if(!is_null($args) && !empty($args)){
            $this->report('Deleting Manual', "Id ".$args);
            $this->deleteOrder($args);
        }
        $this->report('Deleting carts', "Starting to delete carts");
        $this->deleteCarts();
    }

    /**
     * @param $order
     * @return bool
     */
    public function deleteOrder($order)
    {
        if($order instanceof COrder){
            $orderId = $order->id;
        } elseif(is_array($order)) {
            $orderId = $order['id'];
        } else{
            $orderId = $order;
        }
        $res = $this->app->dbAdapter->delete('OrderHistory',['orderId'=>$orderId]);
        $res = $this->app->dbAdapter->delete('UserSessionHasOrder',['orderId'=>$orderId]);
        $res = $this->app->dbAdapter->delete('OrderLine',['orderId'=>$orderId]);
        $res = $this->app->dbAdapter->delete('Order',['id'=>$orderId]);
        if($res>0) return true;
        return false;
    }

    public function deleteCarts()
    {
        $time = 1728000; //seconds to 20 days
        $query = "SELECT id
                  FROM Cart
                  where cartTypeId in (1,2) and ( lastUpdate < ? or ( lastUpdate is null and creationDate < ?)) LIMIT 1000";
        $timestamp = date('Y-m-d H:i:s',( time() - $time));
        $i=0;
	    $k=0;
        while(count($res = $this->app->dbAdapter->query($query,[$timestamp,$timestamp])->fetchAll()) > 100){
            $this->report('Delete Start', "To do: ".count($res));

            foreach($res as $order){
                if($k%100 == 0) $this->app->dbAdapter->beginTransaction();
	            $k++;
                $resp = $this->deleteOrder($order);
                if($k%100 == 0) $this->app->dbAdapter->commit();
                if($resp) $i++;
            }
            $this->report('Delete End', "Deleted: ".$i);
        }
        if($this->app->dbAdapter->hasTransaction()) $this->app->dbAdapter->commit();
    }
}
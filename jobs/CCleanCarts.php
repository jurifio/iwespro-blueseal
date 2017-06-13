<?php
namespace bamboo\blueseal\jobs;

use bamboo\domain\entities\CCart;
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
class CCleanCarts extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        if(!is_null($args) && !empty($args)){
            $this->report('Deleting Manual', "Id ".$args);
            $this->deleteCart($args);
        }
        $this->report('Deleting carts', "Starting to delete carts");
        $this->deleteCarts();
    }

    /**
     * @param $order
     * @return bool
     */
    public function deleteCart($cart)
    {
        if($cart instanceof CCart){
            $cartId = $cart->id;
        } elseif(is_array($cart)) {
            $cartId = $cart['id'];
        } else{
            $cartId = $cart;
        }
        $res = $this->app->dbAdapter->delete('CartHistory',['cartId'=>$cartId]);
        $res = $this->app->dbAdapter->delete('UserSessionHasCart',['cartId'=>$cartId]);
        $res = $this->app->dbAdapter->delete('CartLine',['cartId'=>$cartId]);
        $res = $this->app->dbAdapter->delete('Cart',['id'=>$orderId]);
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

            foreach($res as $cart){
                if($k%100 == 0) $this->app->dbAdapter->beginTransaction();
	            $k++;
                $resp = $this->deleteCart($cart);
                if($k%100 == 0) $this->app->dbAdapter->commit();
                if($resp) $i++;
            }
            $this->report('Delete End', "Deleted: ".$i);
        }
        if($this->app->dbAdapter->hasTransaction()) $this->app->dbAdapter->commit();
    }
}
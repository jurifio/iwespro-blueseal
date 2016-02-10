<?php


namespace bamboo\blueseal\business;

use bamboo\core\ecommerce\IBillingLogic;
use bamboo\ecommerce\events\EGenericEvent;
use bamboo\ecommerce\domain\entities\COrderLine;
use bamboo\ecommerce\domain\entities\COrderLineStatus;
use bamboo\core\application\AApplication;
use bamboo\ecommerce\domain\repositories\COrderLineStatusRepo;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\ecommerce\domain\entities\CProductSku;
use bamboo\core\exceptions\RedPandaException;

class COrderLineManager
{
    /**
     * @var AApplication
     */
    protected $app;
    /**
     * @var COrderLine
     */
    protected $orderLine;

    /**
     * @param AApplication $app
     * @param COrderLine $orderLine
     */
    public function __construct(AApplication $app, COrderLine $orderLine)
    {
        $this->app = $app;
        $this->orderLine = $orderLine;
    }

    /**
     * @param $newStatus
     * @return bool
     * @throws RedPandaException
     */
    public function changeStatus($newStatus)
    {
        /** @var COrderLineStatusRepo $repo */
        $repo = $this->app->repoFactory->create('OrderLineStatus');
        if (!($newStatus instanceof COrderLineStatus)) {
            $newStatus = $repo->em()->findBy(['id' => $newStatus])->getFirst();
        }
        if (!($newStatus instanceof COrderLineStatus)) {
            $newStatus = $repo->em()->findBy(['code' => $newStatus])->getFirst();
        }
        if (!($newStatus instanceof COrderLineStatus)) {
            throw new RedPandaException("Can't find the status you are speaking about");
        }
        //var_dump($possible);
        //$possible = $this->possibleNext();
        //FIXME la ricerca è rotta perchè i puntatori definiti nella query durano per piu di una query... verificare se è possibile definirli per una singola query
        //if ($possible->findOneByKey('id', $newStatus->id)) {
        /** @var  $this ->app->dbAdapter CMySQLAdapter */
        $this->log("Change Line", "Changing Status to ".$newStatus->code);
        $res = $this->app->eventManager->trigger(new EGenericEvent("orderLineStatusChange", ['orderLine' => $this->orderLine, 'newStatus' => $newStatus]));

        try {
            $orderLine = $this->app->repoFactory->create("OrderLine")->findOneBy(['id' => $this->orderLine->id, 'orderId' => $this->orderLine->orderId]);
            $orderLine->status = $newStatus->code;
            $this->app->repoFactory->create("OrderLine")->update($orderLine);
        } catch (\Exception $e) {
            $this->app->router->response()->raiseUnauthorized();
        }
      //  $rows = $this->app->dbAdapter->update('OrderLine', ['status' => $newStatus->code,], ['id' => $this->orderLine->id, 'orderId' => $this->orderLine->orderId]);
    //    if ($rows == 1) return true;
        //}
       // return false;
    }

    /**
     * @param $event
     * @param $description
     */
    public function log($event, $description)
    {
        $this->app->dbAdapter->insert('OrderHistory', ["orderId"=>$this->orderLine->orderId,"event"=>$event,"description"=>$description,"status"=>$this->orderLine->status]);
    }

    /**
     * @return \redpanda\core\base\CObjectCollection
     */
    public function possibleNext()
    {
        /** @var COrderLineStatusRepo $repo */
        $repo = $this->app->repoFactory->create('OrderLineStatus');
        return $repo->listByPossibleStatuses($this->orderLine->status);
    }

    /**
     * @return null!COrderLineStatus
     */
    public function nextOk()
    {
        /** @var COrderLineStatusRepo $repo */
        $repo = $this->app->repoFactory->create('OrderLineStatus');
        return isset($this->orderLine->orderLineStatus->nextOrderLineStatusId) ? $repo->findOne([$this->orderLine->orderLineStatus->nextOrderLineStatusId]) : null;
    }

    /**
     * @return null!COrderLineStatus
     */
    public function nextErr()
    {
        /** @var COrderLineStatusRepo $repo */
        $repo = $this->app->repoFactory->create('OrderLineStatus');
        return empty($this->orderLine->orderLineStatus->errOrderLineStatusId) ? null : $repo->findOne([$this->orderLine->orderLineStatus->errOrderLineStatusId]);
    }

    /**
     * @return bool
     */
    public function isFriendChangable()
    {
        if ($this->orderLine->orderLineStatus->phase == 3) {
            $conto = $this->app->dbAdapter->query("SELECT count(DISTINCT productId, productVariantId, productSizeId, shopId) AS conto
                                          FROM ProductSku
                                          WHERE productId = ? AND
                                                productVariantId = ? AND
                                                productSizeId = ? AND
                                                stockQty > 0 AND
                                                shopId <> ? ", [$this->orderLine->productId,
                $this->orderLine->productVariantId,
                $this->orderLine->productSizeId,
                $this->orderLine->shopId])->fetchAll()[0]['conto'];
            if ($conto > 0) return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isFriendValueChangable()
    {
        return ($this->orderLine->orderLineStatus->phase == 4);
    }

    /**
     * @return bool|mixed
     */
    public function isStatusManageable()
    {
        return (bool) (isset($this->orderLine->orderLineStatus->isManageable) ? $this->orderLine->orderLineStatus->isManageable : false);
    }

    /**
     * @return \redpanda\core\base\CObjectCollection
     */
    public function getAlternativesSkus()
    {
        /** @var CRepo $repo */
        $repo = $this->app->repoFactory->create('ProductSku');
        return $repo->em()->findBySql('SELECT DISTINCT productId, productVariantId, productSizeId, shopId FROM ProductSku WHERE productId = ? AND productVariantId = ? AND productSizeId = ? AND stockQty > 0 AND shopId <> ? ', [$this->orderLine->productId, $this->orderLine->productVariantId, $this->orderLine->productSizeId, $this->orderLine->shopId]);
    }

    /**
     * @return CProductSku|null
     */
    public function getSelectedSku()
    {
        /** @var CRepo $repo */
        $repo = $this->app->repoFactory->create('ProductSku');
        return $repo->findOne(["productId" => $this->orderLine->productId, "productVariantId" => $this->orderLine->productVariantId, "productSizeId" => $this->orderLine->productSizeId, "shopId" => $this->orderLine->shopId]);
    }

    /**
     * @param $sku
     * @return bool
     * @throws RedPandaException
     */
    public function setNewSku($sku)
    {
        if(!$sku instanceof CProductSku){
            $sku = $this->app->repoFactory->create('ProductSku')->findOne(['productId'=>$this->orderLine->productId,'productVariantId'=>$this->orderLine->productVariantId,'productSizeId'=>$this->orderLine->productSizeId,'shopId'=>$sku]);
        }
        if($sku == null) return false;

        $this->log('Change Line', 'Switching to new Shop: '.$sku->shopId.' from '.$this->orderLine->shopId);
        try {

            $orderLine = $this->app->repoFactory->create("OrderLine")->findOneBy(['id' => $this->orderLine->id, 'orderId' => $this->orderLine->orderId]);
            $orderLine->shopId = $sku->shopId;
            $orderLine->frozenProduct = serialize($sku);
            $this->app->repoFactory->create("OrderLine")->update($orderLine);
        } catch (\Exception $e) {
            $this->app->router->response()->raiseUnauthorized();
        }

       // $this->app->dbAdapter->update('OrderLine', ['shopId'=>$sku->shopId,'frozenProduct'=>serialize($sku)],['id'=>$this->orderLine->id,'orderId'=>$this->orderLine->orderId]);
        $this->orderLine = $this->app->repoFactory->create('OrderLine')->findOne(['id'=>$this->orderLine->id,'orderId'=>$this->orderLine->orderId]);
        if(!$this->orderLine instanceof COrderLine || $this->orderLine->shopId != $sku->shopId){
            throw new RedPandaException('Order Line Change Failed');
        }
        $pricer = $sku->shop->billingLogic;
        /** @var IBillingLogic $pricer */
        $pricer = new $pricer($this->app);
        $this->orderLine->friendRevenue = $pricer->calculateFriendReturn($this->orderLine);

        try {

            $orderLine = $this->app->repoFactory->create("OrderLine")->findOneBy(['id' => $this->orderLine->id, 'orderId' => $this->orderLine->orderId]);
            $orderLine->friendRevenue = $this->orderLine->friendRevenue;
            $this->app->repoFactory->create("OrderLine")->update($orderLine);
        } catch (\Exception $e) {
            $this->app->router->response()->raiseUnauthorized();
        }
        //$this->app->dbAdapter->update('OrderLine', ['friendRevenue'=>$this->orderLine->friendRevenue],['id'=>$this->orderLine->id,'orderId'=>$this->orderLine->orderId]);
        //return true;
    }

    /**
     * @param $price
     * @return bool
     */
    public function changeFriendRevenue($price)
    {
        if(is_string($price)){
            $price = floatval($price);
        }
        if(is_float($price)){
            try {

                $orderLine = $this->app->repoFactory->create("OrderLine")->findOneBy(['id' => $this->orderLine->id, 'orderId' => $this->orderLine->orderId]);
                $orderLine->friendRevenue = round($price,2);
                $this->app->repoFactory->create("OrderLine")->update($orderLine);

            } catch (\Exception $e) {
                $this->app->router->response()->raiseUnauthorized();
            }

           // $res = $this->app->dbAdapter->update('OrderLine',['friendRevenue'=>round($price,2)],['id'=>$this->orderLine->id,'orderId'=>$this->orderLine->orderId]);
        } else return false;
        return true;
    }
}
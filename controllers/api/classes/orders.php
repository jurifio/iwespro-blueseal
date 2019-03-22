<?php

namespace bamboo\controllers\api\classes;
use bamboo\domain\entities\CDirtySku;
use bamboo\domain\entities\COrderLine;


/**
 * Class orders
 * @package bamboo\controllers\api
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 20/07/2018
 * @since 1.0
 */
class orders extends AApi
{
    private $shop;
    private $uniqueId;

    /**
     * orders constructor.
     * @param $app
     * @param $data
     * @throws \bamboo\core\exceptions\BambooConfigException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\RedPandaCookieException
     */
    public function __construct($app, $data)
    {
        parent::__construct($app, $data);
        $this->shop = \Monkey::app()->repoFactory->create('SiteApi')->findOneBy(['id'=>$this->id]);
        $this->uniqueId = uniqid();
    }

    public function createAction($action)
    {
        if(!is_null($this->auth)){
            return $this->auth;
        }
        return $this->{$action}();
    }

    /**
     * @return array
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function get(){
        $fromDate = str_replace('T', ' ',$this->data['fromDate']);
        $toDate = str_replace('T', ' ',$this->data['toDate']);

        $this->report($this::GET, 'Orders','report', 'Init get order', 'Order from ' . $fromDate . ' to ' . $toDate, $this->uniqueId, $this->id);

        $orderLines = \Monkey::app()->repoFactory->create('OrderLine')->findBySql
        (
           'SELECT *
           FROM OrderLine ol
           WHERE ol.creationDate > ? AND ol.creationDate < ? AND ol.shopId = ?',
           [$fromDate, $toDate, $this->shop->shopId]
        );

        $orderInfo = [];

        $i = 0;
        /** @var COrderLine $orderLine */
        foreach ($orderLines as $orderLine){

            /** @var CDirtySku $dirtySku */
            $dirtySku = $orderLine->productSku->findRightDirtySku(false, false);

            $orderInfo[$i]['id'] = $orderLine->id . '-' . $orderLine->orderId;
            $orderInfo[$i]['amount'] = $orderLine->friendRevenue;
            $orderInfo[$i]['referenceId'] = $dirtySku->dirtyProduct->extId;
            $orderInfo[$i]['var'] = $dirtySku->dirtyProduct->var;
            $orderInfo[$i]['size'] = $dirtySku->size;
            $orderInfo[$i]['ean'] = $dirtySku->barcode;
            $orderInfo[$i]['barcodeInt'] = $dirtySku->barcode_int;
            $orderInfo[$i]['date'] = $orderLine->creationDate;
            $i++;
        }

        $this->report($this::GET, 'Orders','report', 'End get order', 'Order from ' . $fromDate . ' to ' . $toDate, $this->uniqueId, $this->id);

        return $orderInfo;
    }

    public function post(){
    }

    public function put(){
    }

    public function delete(){
    }

}
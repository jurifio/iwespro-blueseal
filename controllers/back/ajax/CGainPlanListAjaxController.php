<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CGainPlan;
use bamboo\domain\entities\CGainPlanPassiveMovement;

/**
 * Class CGainPlanListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 30/11/2019
 * @since 1.0
 */
class CGainPlanListAjaxController extends AAjaxController
{
    public function get()
    {
        $datatable = new CDataTables("CGainPlanListAjaxController",['id'],$_GET,false);
        $datatable->addCondition('id',\Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser());

        $gainPlans = \Monkey::app()->repoFactory->create('GainPlan')->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('GainPlan')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('GainPlan')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());
        $invoiceRepo=\Monkey::app()->repoFactory->create('Invoice');
        $orderRepo=\Monkey::app()->repoFactory->create('Order');
        $orderLineRepo=\Monkey::app()->repoFactory->create('OrderLine');
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
        $userRepo=\Monkey::app()->repoFactory->create('User');
        $gpsmRepo=\Monkey::app()->repoFactory->create('GainPlanPassiveMovement');
        $seasonRepo=\Monkey::app()->repoFactory->create('ProductSeason');
        $orderPaymentMethod=\Monkey::app()->repoFactory->create('OrderPaymentMethod');


        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];
        /** @var CGainPlan $val */
        foreach($gainPlans as $val){
            $row = [];
            $row['DT_RowId'] = $val->printId();
            $row['id'] = '<a href="/blueseal/prodotti/season-aggiungi?id='.$val->printId().'">'.$val->printId().'</a>';
            $season=$seasonRepo->findOneBy(['id'=>$val->seasonId]);
            $row['season']=$season->name;
            $order="";
            $orders=$orderRepo->findOneBy(['id'=>$val->orderId]);
            if($orders!=null){
                $order=$orders->id;
            }
            $row['orderId']=$order;
            $row['userId'] = $val->userId;
            $invoice=$invoiceRepo->findOneBy(['id'=>$val->invoiceId]);
            $row['invoice']=$invoice->invoiceType.'-'.$invoice->invoiceNumber.'/'.$invoice->invoiceDate;
            $row['amount']=$val->amount;
            $cost=0;
            $rowCost='';
            $collectCost=$gpsmRepo->findBy(['gainPlanId'=>$val->id]);
            foreach($collectCost as $costs){
                $cost+=$costs->amount;
                $rowCost.='id:'.$costs->id. ' fattura:'.$costs->invoice.'dataMovivento:'.$costs->dateMovement.' Fornitore'.$costs->fornitureName.'<br>';

            }
            $amount=0;
            $commission=0;
            $deliveryCost=0;
            $margin=0;
            $paymentcommission=0;
            $shippingCost=0;
            $sellingFee=0;

            switch(true) {
                case $val->typeMovement == 1;//ordine
                    $orderLines = $orderLineRepo->findOneBy(['orderId' => $val->orderId]);
                    foreach ($orderLines as $orderLine) {
                        if ($orderLine->status != 'ORD_CANCEL' || $orderLine->status != 'ORD_FRND_CANC' || $orderLine->status != 'ORD_MISSING') {
                            $paymentType=
                            if ($orderLine->remoteShopSellerId == 44) {
                                $amount += $orderLine->netPrice;
                                $cost += $orderLine->friendRevenue;
                                $paymentCommission+=$orderLine-



                            }

                        }
                    }
            }
                    /*$row['amount']=$order->grossTotal;
                    $row['cost']=$order->
                    $row['MovementPassiveCollect'] = $rowCost;
                    $row['deliveryCost'] = $val->deliveryCost;*/




            $row['dateStart'] = $season->dateStart;
            $row['dateEnd'] = $season->dateEnd;
            $row['isActive'] = ($season->isActive==0)? 'no' : 'si';
            $row['order'] = $season->order;
            $response['data'][] = $row;
        }
        return json_encode($response);
    }
}
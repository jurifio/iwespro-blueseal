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

        $sql = 'SELECT gp.id as id,
                        gp.seasonId as seasonId,
                        if(ol.shopId!=ol.remoteShopSellerId,concat("P | ",gp.orderId),gp.orderId) as orderId,
       gp.userId as userId,
       gp.shopId as shopId,
       s.name as shopName,
       gp.customerName as customerName,
       gp.invoiceId as invoiceid,
        if(gp.typeMovement=1,"Ordini","Servizi") as typeMovement,
       gp.amount as amount,
       gp.imp as imp,
       gp.cost as cost,
       gp.deliveryCost as deliveryCost,
       gp.commission as commission,
       gp.commissionSell as commissionSell,
       gp.transParallel as transparallel,
       gp.profit as profit,
       gp.dateCreate as dateCreate,
       gp.dateMovement as dateMovement,
       gp.externalId as externalId,
       gp.invoiceExternal as invoiceExternal,
       if(gp.isActive=1,"Si","No") as isActive
       from GainPlan gp  left join Shop s on s.id=gp.shopId
        left join `Order` o on gp.orderId=o.id
        left join `OrderLine` ol on o.id=ol.orderId
         group by gp.orderId ORDER  BY dateMovement  DESC 
      
        ';
        $datatable = new CDataTables($sql,['id'],$_GET,true);

        $datatable->doAllTheThings('true');


        $gainPlans = \Monkey::app()->repoFactory->create('GainPlan')->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('GainPlan')->em()->findCountBySql($datatable->getQuery(true),$datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('GainPlan')->em()->findCountBySql($datatable->getQuery('full'),$datatable->getParams());
        $invoiceRepo = \Monkey::app()->repoFactory->create('Invoice');
        $orderRepo = \Monkey::app()->repoFactory->create('Order');
        $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $userRepo = \Monkey::app()->repoFactory->create('User');
        $countryRepo = \Monkey::app()->repoFactory->create('Country');
        $gpsmRepo = \Monkey::app()->repoFactory->create('GainPlanPassiveMovement');
        $seasonRepo = \Monkey::app()->repoFactory->create('ProductSeason');
        $orderPaymentMethodRepo = \Monkey::app()->repoFactory->create('OrderPaymentMethod');


        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var $val CGainPlan */
            $val = \Monkey::app()->repoFactory->create('GainPlan')->findOneBy($row);
            $row['DT_RowId'] = $val->printId();
            $row['id'] = $val->printId();
            if($val->isActive==1) {
                $isActive = 'Si';
            }else {
                $isActive = 'No';
            }
            $row['isActive']=$isActive;
            $season = $seasonRepo->findOneBy(['id' => $val->seasonId]);
            $row['season'] = $season->name;
            $order = "";
            $orders = $orderRepo->findOneBy(['id' => $val->orderId]);
            if ($val->shopId != 0) {
                $findShopOrder = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $val->shopId]);
                $shopOrder = $findShopOrder->name;
            } else {
                $shopOrder='';
            }
            if ($orders != null) {
                $order = $orders->id;
            } else {
                $order = '';
            }
            $row['userId'] = $val->userId;

            $row['amount'] = $val->amount;
            $cost = 0;
            $rowCost = '';
            $collectCost = $gpsmRepo->findBy(['gainPlanId' => $val->id]);
            foreach ($collectCost as $costs) {
                if($costs->isActive==1) {
                    if ($costs->typeMovement == 2) {
                        $cost += $costs->amount;
                    }
                    $rowCost .=  $costs->fornitureName . '|' . $costs->invoice . '<br>';
                }
            }

            $amount = 0;
            $paymentCommission = 0;
            $shippingCost = 0;
            $imp = 0;
            $customer = '';
            $nation = '';
            $commissionSell=0;
            $profit=0;
            $transParallel=0;

            switch ($val->typeMovement) {
                case 1:
                    $orderLines = $orderLineRepo->findBy(['orderId' => $val->orderId]);
                    if($orderLines!=null){
                        $typeMovement = 'Ordini';
                        if ($orderLines != null) {
                            $invoice = $invoiceRepo->findOneBy(['id' => $val->invoiceId]);
                            if ($invoice != null) {
                                $findInvoice = $invoice->invoiceNumber . '/' . $invoice->invoiceType;
                            } else {
                                $findInvoice = '';
                            }
                            $customer=$val->customerName;

                            foreach ($orderLines as $orderLine) {
                                if ($orderLine->status != 'ORD_CANCEL' && $orderLine->status != 'ORD_FRND_CANC' && $orderLine->status != 'ORD_MISSNG') {
                                    $orderPaymentMethod = $orderPaymentMethodRepo->findOneBy(['id' => $orders->orderPaymentMethodId]);
                                    $paymentCommissionRate = $orderPaymentMethod->paymentCommissionRate;

                                    if ($orderLine->remoteShopSellerId == 44) {
                                        $typeOrder = 'dettaglio Prodotto Diretto';
                                        $amount += $orderLine->netPrice;
                                        $imp +=  $orderLine->netPrice - $orderLine->vat;
                                        $cost += $orderLine->friendRevenue;
                                        $paymentCommission += ($orderLine->netPrice / 100) * $paymentCommissionRate;
                                        $transParallel+=0;
                                        $shippingCost += $orderLine->shippingCharge;
                                        $commissionSell=0;
                                        $profit=$imp-$cost-$shippingCost-$paymentCommission;


                                    } else {
                                        if ($orderLine->remoteShopSellerId != $orderLine->shopId) {
                                            $shop = $shopRepo->findOneBy(['id' => $orderLine->shopId]);
                                            $paralellFee = $shop->paralellFee;
                                            $imp +=  $orderLine->netPrice - $orderLine->vat;
                                            $par=$orderLine->netPrice/100*$paralellFee;
                                            $transParallel+=(($orderLine->netPrice-$par)*100/122)-$orderLine->friendRevenue;
                                            $amount += $orderLine->netPrice;
                                            $paymentCommission += ($orderLine->netPrice / 100) * $paymentCommissionRate;
                                            $cost += 0;
                                            $shippingCost=$orderLine->shippingCharge;
                                            $commissionSell+=round($orderLine->netPrice * 0.11,2);
                                            $profit+=$commissionSell+$transParallel-$paymentCommission-$shippingCost;
                                            $order='<i style="color:green"><b>P | '.$order.'</b></i>';

                                        }else{
                                            $shop = $shopRepo->findOneBy(['id' => $orderLine->shopId]);
                                            $paralellFee = $shop->paralellFee;
                                            $imp +=  $orderLine->netPrice - $orderLine->vat;
                                            $transParallel=0;
                                            $amount += $orderLine->netPrice;
                                            $paymentCommission += ($orderLine->netPrice / 100) * $paymentCommissionRate;
                                            $cost += 0;
                                            $shippingCost=$orderLine->shippingCharge;
                                            $commissionSell+=round($orderLine->netPrice * 0.11,2);
                                            $profit+=$commissionSell-$paymentCommission-$shippingCost;

                                        }
                                    }

                                }
                            }
                        }
                    }
                    break;
                case "2":
                    $findInvoice = $val->invoiceExternal;
                    $amount += $val->amount;
                    $cost += $val->cost;
                    $shippingCost += $val->deliveryCost;
                    $paymentCommission += $val->commission;
                    $customer = $val->customerName;
                    $typeMovement = 'Servizi';
                    $commissionSell+=0;
                    $transParallel+=0;
                    $profit+=$amount-$cost;

                    break;

            }
            $row['invoiceId'] = $findInvoice;
            $row['shoId']=$shopOrder;
            $row['country'] = $nation;
            $row['customerName'] = $customer;
            $row['amount'] = number_format($amount) . ' &euro;';
            $row['cost'] = number_format($cost+$shippingCost+$paymentCommission) . ' &euro;';
            $row['imp'] = number_format($imp) . ' &euro;';
            $row['MovementPassiveCollect'] = $rowCost;
            $row['deliveryCost'] = number_format($shippingCost) . ' &euro;';
            $row['paymentCommission'] = number_format($paymentCommission) . ' &euro;';
            $row['profit'] = number_format($profit) . ' &euro;';
            $row['commissionSell']=number_format($commissionSell);
            $row['transParallel']=number_format($transParallel);
            $row['typeMovement'] = $typeMovement;
            $dateMovement=strtotime($val->dateMovement);
            $dateMovement=date('d/m/Y',$dateMovement);
            $row['dateMovement'] =$dateMovement;

            $row['orderId'] = $order;
            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}
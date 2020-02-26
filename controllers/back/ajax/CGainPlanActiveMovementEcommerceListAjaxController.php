<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CGainPlan;
use bamboo\domain\entities\CGainPlanPassiveMovement;

/**
 * Class CGainPlanActiveMovementEcommerceListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 25/02/2020
 * @since 1.0
 */
class CGainPlanActiveMovementEcommerceListAjaxController extends AAjaxController
{
    public function get()
    {

        $sql = 'SELECT gp.id as id,
                        gp.seasonId as seasonId,
                        gp.orderId as orderId,
       gp.userId as userId,
       gp.shopId as shopId,
       gp.customerName as customerName,
       gp.invoiceId as invoiceid,
        if(gp.typeMovement=1,"Ordini","Servizi") as typeMovement,
       gp.amount as amount,
       gp.imp as imp,
       gp.cost as cost,
       gp.deliveryCost as deliveryCost,
       gp.commission as commission,
       gp.profit as profit,
       gp.dateCreate as dateCreate,
       gp.dateMovement as dateMovement,
       gp.externalId as externalId,
       gp.invoiceExternal as invoiceExternal,
       if(gp.isActive=1,"Si","No") as isActive
       from GainPlan gp  where gp.orderId !="0"  ORDER  BY dateMovement DESC
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
            if($val->isActive==1) {
                $isActive = 'Si';
            }else {
                $isActive = 'No';
            }
            $row['isActive']=$isActive;
            $row['DT_RowId'] = $val->printId();
            $row['id'] = $val->printId();
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
                    $rowCost .= ' fattura:' . $costs->invoice . ' -' . $costs->fornitureName . '<br>';
                }
            }

            $amount = 0;
            $paymentCommission = 0;
            $shippingCost = 0;
            $imp = 0;
            $customer = '';
            $nation = '';

            switch ($val->typeMovement) {
                case "1":
                    $typeMovement = 'Ordini';
                    $orderLines = $orderLineRepo->findBy(['orderId' => $val->orderId]);
                    if ($orderLines != null) {
                        $invoice = $invoiceRepo->findOneBy(['id' => $val->invoiceId]);
                        if ($invoice != null) {
                            $findInvoice = $invoice->invoiceNumber . '/' . $invoice->invoiceType;
                        } else {
                            $findInvoice = '';
                        }

                        if ($orders != null) {
                            $userAddress = json_decode($orders->frozenBillingAddress,false);
                            if($userAddress->countryId!=null){
                                $countryId=$userAddress->countryId;
                            }else{
                                $countryId='101';
                            }
                            $country = $countryRepo->findOneBy(['id' => $countryId]);
                            $extraue = ($country->extraue == 1) ? 'yes' : 'no';
                            $customer = $userAddress->name . ' ' . $userAddress->surname . ' ' . $userAddress->company;

                            $nation = $country->name;
                        }

                        foreach ($orderLines as $orderLine) {
                            if ($orderLine->status != 'ORD_CANCEL' || $orderLine->status != 'ORD_FRND_CANC' || $orderLine->status != 'ORD_MISSING') {
                                $orderPaymentMethod = $orderPaymentMethodRepo->findOneBy(['id' => $orders->orderPaymentMethodId]);
                                $paymentCommissionRate = $orderPaymentMethod->paymentCommissionRate;

                                if ($orderLine->remoteShopSellerId == 44) {
                                    $typeOrder = 'dettaglio Prodotto Diretto';
                                    $amount += $orderLine->netPrice;
                                    $imp = ($country->extraue == 1) ? $orderLine->netPrice : $orderLine->netPrice - $orderLine->vat;
                                    $cost += $orderLine->friendRevenue;
                                    $paymentCommission += ($orderLine->netPrice / 100) * $paymentCommissionRate;
                                    $shippingCost = $orderLine->shippingCharge;


                                } else {
                                    if ($orderLine->remoteOrderSupplierId != null) {
                                        $shop = $shopRepo->findOneBy(['id' => $orderLine->shopId]);
                                        $paralellFee = $shop->paralellFee;
                                        $amount += $orderLine->netPrice - ($orderLine->netPrice / 100 * $paralellFee);
                                        $imp+= $amount*100/122;
                                        $paymentCommission += ($orderLine->netPrice / 100) * $paymentCommissionRate;
                                        $cost += $orderLine->friendRevenue;

                                    } else {
                                        $shop = $shopRepo->findOneBy(['id' => $orderLine->shopId]);
                                        $paralellFee = $shop->paralellFee;
                                        $cost += $orderLine->friendRevenue;
                                        $paymentCommission += ($orderLine->netPrice / 100) * $paymentCommissionRate;
                                        $shippingCost = $orderLine->shippingCharge;
                                        $imp += round($orderLine->netPrice * 0.11,2) + $paymentCommission;
                                        $amount += (round($orderLine->netPrice * 0.11,2) + $paymentCommission)+((round($orderLine->netPrice * 0.11,2) + $paymentCommission)/100*22);

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

                    break;

            }
            $row['invoiceId'] = $findInvoice;
            $row['shoId']=$shopOrder;
            $row['country'] = $nation;
            $row['customerName'] = $customer;
            $row['amount'] = money_format('%.2n',$amount) . ' &euro;';
            $row['cost'] = money_format('%.2n',$cost) . ' &euro;';
            $row['imp'] = money_format('%.2n',$imp) . ' &euro;';
            $row['MovementPassiveCollect'] = $rowCost;
            $row['deliveryCost'] = money_format('%.2n',$shippingCost) . ' &euro;';
            $row['paymentCommission'] = money_format('%.2n',$paymentCommission) . ' &euro;';
            $row['profit'] = money_format('%.2n',$imp - $cost - $shippingCost - $paymentCommission) . ' &euro;';
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
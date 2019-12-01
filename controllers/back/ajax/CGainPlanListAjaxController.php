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
                        gp.orderId as orderId,
       gp.userId as userId,
       gp.shopId as shopId,
       gp.customerName as customerName,
       gp.invoiceId as invoiceid,
       gp.typeMovement as typeMovement,
       gp.amount as amount,
       gp.imp as imp,
       gp.cost as cost,
       gp.deliveryCost as deliveryCost,
       gp.commission as commission,
       gp.profit as profit,
       gp.dateCreate as dateCreate,
       gp.dateMovement as dateMovement
       from GainPlan gp ORDER BY dateMovement DESC
                
    
        ';
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings('true');


        $gainPlans = \Monkey::app()->repoFactory->create('GainPlan')->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('GainPlan')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('GainPlan')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());
        $invoiceRepo=\Monkey::app()->repoFactory->create('Invoice');
        $orderRepo=\Monkey::app()->repoFactory->create('Order');
        $orderLineRepo=\Monkey::app()->repoFactory->create('OrderLine');
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
        $userRepo=\Monkey::app()->repoFactory->create('User');
        $countryRepo=\Monkey::app()->repoFactory->create('Country');
        $gpsmRepo=\Monkey::app()->repoFactory->create('GainPlanPassiveMovement');
        $seasonRepo=\Monkey::app()->repoFactory->create('ProductSeason');
        $orderPaymentMethodRepo=\Monkey::app()->repoFactory->create('OrderPaymentMethod');


        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var $val CGainPlan */
            $val = \Monkey::app()->repoFactory->create('GainPlan')->findOneBy($row);
                $row['DT_RowId'] = $val->printId();
                $row['id'] = '<a href="/blueseal/prodotti/season-aggiungi?id='.$val->printId().'">'.$val->printId().'</a>';
                $season=$seasonRepo->findOneBy(['id'=>$val->seasonId]);
                $row['season']=$season->name;
                $order="";
                $orders=$orderRepo->findOneBy(['id'=>$val->orderId]);
                if($orders!=null){
                    $order=$orders->id;
                }
                $row['userId'] = $val->userId;
                $invoice=$invoiceRepo->findOneBy(['id'=>$val->invoiceId]);
                $row['invoiceId']=$invoice->invoiceType.'-'.$invoice->invoiceNumber.'/'.$invoice->invoiceDate;
                $row['amount']=$val->amount;
                $cost=0;
                $rowCost='';
                $collectCost=$gpsmRepo->findBy(['gainPlanId'=>$val->id]);
                foreach($collectCost as $costs){
                    $cost+=$costs->amount;
                    $rowCost.='id:'.$costs->id. ' fattura:'.$costs->invoice.'dataMovivento:'.$costs->dateMovement.' Fornitore'.$costs->fornitureName.'<br>';

                }

                $amount=0;
                $paymentCommission=0;
                $shippingCost=0;
                $imp=0;
                $customer='';
                $nation='';

                switch($val->typeMovement) {
                    case 1:
                        $orderLines = $orderLineRepo->findOneBy(['orderId' => $val->orderId]);
                        $userAddress = \bamboo\domain\entities\CUserAddress::defrost($orders->frozenBillingAddress);
                        $country=$countryRepo->findOneBy(['id'=>$userAddress->countryId]);
                        $extraue=($country->extraue==1)? 'yes':'no';
                        $customer=$userAddress->name. ' '.$userAddress->surname.' '.$userAddress->company;
                        $typeMovement='Ordini';
                        $nation=$country->name;

                        foreach ($orderLines as $orderLine) {
                            if ($orderLine->status != 'ORD_CANCEL' || $orderLine->status != 'ORD_FRND_CANC' || $orderLine->status != 'ORD_MISSING') {
                                $orderPaymentMethod=$orderPaymentMethodRepo->findOneBy(['id'=>$orders->orderPaymentMethodId]);
                                $paymentCommissionRate=$orderPaymentMethod->paymentCommissionRate;

                                if ($orderLine->remoteShopSellerId == 44) {
                                    $amount += $orderLine->netPrice;
                                    $imp=($country->extraue==1)?$orderLine->netPrice : $orderLine->netPrice-$orderLine->vat;
                                    $cost += $orderLine->friendRevenue;
                                    $paymentCommission+=($orderLine->netPrice/100)*$paymentCommissionRate;
                                    $shippingCost=$orderLine->shippingCarge;



                                }else{
                                    if($orderLine->remoteOrderSupplierId!=null){
                                        $shop=$shopRepo->finOneBy(['id'=>$orderLine->shopId]);
                                        $paralellFee=$shop->paralellFee;
                                        $amount+=$orderLine->activePrice-($orderLine->activePrice/100*$paralellFee) - $orderLine->friendRevenue;
                                        $imp=$amount;
                                        $paymentCommission+=($orderLine->netPrice/100)*$paymentCommissionRate;
                                        $cost+=$ordeLine->friendRevenute;
                                        $shippingCost=$orderLine->shippingCarge;

                                    }else{
                                        $shop=$shopRepo->finOneBy(['id'=>$orderLine->shopId]);
                                        $paralellFee=$shop->paralellFee;
                                        $cost+=$ordeLine->friendRevenute;
                                        $paymentCommission+=($orderLine->netPrice/100)*$paymentCommissionRate;
                                        $shippingCost=$orderLine->shippingCarge;
                                        $imp+=round($orderLine->netPrice*0.11,2)+$paymentCommission;
                                        $amount+=round($orderLine->netPrice*0.11,2)+$paymentCommission;

                                    }
                                }

                            }
                        }
                        break;
                    case 2:
                        $amount+=$val->amount;
                        $cost+=$val->cost;
                        $shippingCost+=$val->deliveryCost;
                        $paymentCommission+=$val->commission;
                        $customer=$val->customerName;
                        $typeMovement='Servizi';

                        break;

                }
                $row['country']=$nation;
                $row['customerName']=$customer;
                $row['amount']=$amount;
                $row['cost']=$cost;
                $row['imp']=$imp;
                $row['MovementPassiveCollect'] = $rowCost;
                $row['deliveryCost'] = $shippingCost;
                $row['paymentCommission'] = $paymentCommission;
                $row['profit']=$amount-$cost-$shippingCost-$paymentCommission;
                $row['typeMovement']=$typeMovement;
                $row['dateMovement']=$val->dateMovement;

                $row['orderId'] = $order;
                $datatable->setResponseDataSetRow($key,$row);
            }

            return $datatable->responseOut();
        }
    }
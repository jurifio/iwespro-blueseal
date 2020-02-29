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
class CGainPlanPassiveMovementListAjaxController extends AAjaxController
{
    public function get()
    {

        $sql = 'SELECT  gppm.id as id,
                        gppm.invoice as invoice,
                        ps.name as seasonName,
                        gppm.amount as amount,
                        gppm.amountVat as amountVat,
                        gppm.amountTotal as amountTotal,
                        if( gppm.gainPlanId is null, "non Associato", gppm.gainPlanId) as gainPlanId,
                        if(gppm.gainPlanId is null, "non Associato",concat(gp.invoiceExternal, " | ",gp.customerName)) as  invoiceExternal,
                        if(gppm.gainPlanId is null, "non Associato",gp.amount) as  gpAmount,
                        gppm .fornitureName as fornitureName,
                        gppm.serviceName as serviceName,
                         if(gp.isActive=1,"Si","No") as isActive,
                        gppm.dateCreate as dateCreate,
                        gppm.shopId as shopId,
                        s.name as ShopName,
                        gppm.dateMovement as dateMovement
        from GainPlanPassiveMovement gppm left join GainPlan gp on gppm.gainPlanId=gp.id 
                                         left  join ProductSeason ps on  gppm.seasonId=ps.id 
                                            left join Shop s on s.id=gppm.shopId ORDER BY dateMovement DESC
        ';
        $datatable = new CDataTables($sql, ['id'], $_GET, true);
        $datatable -> doAllTheThings('true');
        $gainPlanPassiveMovements = \Monkey ::app() -> repoFactory -> create('GainPlanPassiveMovement') -> findBySql($datatable -> getQuery(), $datatable -> getParams());
        $count = \Monkey ::app() -> repoFactory -> create('GainPlanPassiveMovement') -> em() -> findCountBySql($datatable -> getQuery(true), $datatable -> getParams());
        $totalCount = \Monkey ::app() -> repoFactory -> create('GainPlanPassiveMovement') -> em() -> findCountBySql($datatable -> getQuery('full'), $datatable -> getParams());
        $invoiceRepo = \Monkey ::app() -> repoFactory -> create('Invoice');
        $orderRepo = \Monkey ::app() -> repoFactory -> create('Order');
        $orderLineRepo = \Monkey ::app() -> repoFactory -> create('OrderLine');
        $shopRepo = \Monkey ::app() -> repoFactory -> create('Shop');
        $userRepo = \Monkey ::app() -> repoFactory -> create('User');
        $countryRepo = \Monkey ::app() -> repoFactory -> create('Country');
        $gpsmRepo = \Monkey ::app() -> repoFactory -> create('GainPlanPassiveMovement');
        $seasonRepo = \Monkey ::app() -> repoFactory -> create('ProductSeason');
        $orderPaymentMethodRepo = \Monkey ::app() -> repoFactory -> create('OrderPaymentMethod');
        $gainPlanRepo=\Monkey::app()->repoFactory ->create('GainPlan');
        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var $val CGainPlanPassiveMovement */
            $val = \Monkey ::app() -> repoFactory -> create('GainPlanPassiveMovement') -> findOneBy($row);
            $row['DT_RowId'] = $val -> printId();
            $row['id'] = '<a href="/blueseal/gainplan/gainplan-passivo/modifica/' . $val -> printId() . '">' . $val -> printId() . '</a>';
            $row['dateMovement'] = $val -> dateMovement;

            $row['invoice'] = $val -> invoice;
            if($val->gainPlanId!=null){
                $gainPlan=$gainPlanRepo->findOneBy(['id'=>$val->gainPlanId]);

                  $invoiceExternal=$gainPlan->invoiceExternal.'/'.$gainPlan->customerName;
                  $gpAmount=$val->amount-($gainPlan->amount*100/122);
                  $gpTotalAmount=$gainPlan->amount*100/122;

            }else{
                $invoiceExternal='Non Associato';
                $gpAmount=0;
                $gpTotalAmount=0;
            }
            $row['invoiceExternal'] = $invoiceExternal;
            $row['gpTotalAmount']=number_format($gpTotalAmount,'2',',','.') . ' &euro;';
            $row['gpAmount']=number_format($gpAmount,'2',',','.') . ' &euro;';
            $row['amount'] = number_format($val -> amount,'2',',','.') . ' &euro;';
            $row['amountVat']= number_format($val->amountVat,'2',',','.') . ' &euro;';
            $row['amountTotal']= number_format($val->amountTotal,'2',',','.') . ' &euro;';
            $row['serviceName'] = $val -> serviceName;
            $row['fornitureName'] = $val -> fornitureName;
            $shop='';
            if ($val -> shopId != null && $val -> shopId != 0  ) {
                $shops = $shopRepo -> findOneBy(['id' => $val -> shopId]);
                $shop = $shops -> name;
            } else {
                $shop = '';
            }
            $row['shopName'] = $shop;
            if($val->isActive==1) {
                $isActive = 'Si';
            }else {
                $isActive = 'No';
            }
            $row['isActive']=$isActive;

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}
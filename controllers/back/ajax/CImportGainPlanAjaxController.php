<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CGainPlan;
use bamboo\domain\entities\CGainPlanPassiveMovement;


class CImportGainPlanAjaxController extends AAjaxController
{
    public function get()
    {

        try {
            $invoiceRepo = \Monkey::app()->repoFactory->create('Invoice');
            $orderRepo = \Monkey::app()->repoFactory->create('Order');

            $seasonRepo = \Monkey::app()->repoFactory->create('ProductSeason');
            $gainPlanRepo = \Monkey::app()->repoFactory->create('GainPlan');
            $sql = "select * from `Order`";

            $orders = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
            foreach ($orders as $order) {
                $orderId = $order['id'];
                strpos($orderId, '11791398');


                $invoice = $invoiceRepo->findOneBy(['orderId' => $orderId]);

                if ($invoice != null) {

                    $invoiceId = $invoice->id;
                }
                if ($order['frozenBillingAddress'] != null) {
                    $userAddress = json_decode($order['frozenBillingAddress'],false);
                if ($userAddress!=null ) {
                    if($userAddress->company==null){
                    $customer = addslashes($userAddress->name . ' ' . $userAddress->surname );
                }else{
                    $customer= addslashes($userAddress->name . ' ' . $userAddress->surname. ' ' . $userAddress->company);
                    }
                }
                } else {
                    $customer = '';
                }

                $shopId = $order['remoteShopSellerId'];
                $seasons = $seasonRepo->findAll();
                foreach ($seasons as $season) {
                    $dateStart = strtotime($season->dateStart);
                    $dateEnd = strtotime($season->dateEnd);
                    $orderDate = strtotime($order['creationDate']);
                    if ($orderDate >= $dateStart && $orderDate <= $dateEnd) {
                        $seasonId = $season->id;
                        break;
                    }
                }
                $gainPlanFind = \Monkey::app()->repoFactory->create('GainPlan')->findOneBy(['orderId' => $orderId]);
                if ($gainPlanFind == null) {
                    $gainPlanInsert = $gainPlanRepo->getEmptyEntity();
                    if ($invoice != null) {
                        $gainPlanInsert->invoiceId = $invoiceId;
                    }
                    $gainPlanInsert->orderId = $orderId;
                    $gainPlanInsert->seasonId = $seasonId;
                    $gainPlanInsert->customerName = $customer;
                    $gainPlanInsert->typeMovement = 1;
                    $gainPlanInsert->dateMovement = $order['creationDate'];
                    $gainPlanInsert->shopId = $shopId;
                    $gainPlanInsert->insert();
                }

            }

        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CImportGainPlanAjaxController','error','Import Gain Plan Error',$e);
        }
        return 'prova';
    }
}
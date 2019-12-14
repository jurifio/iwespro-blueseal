<?php

namespace bamboo\blueseal\jobs;

use bamboo\core\jobs\ACronJob;
use PDO;
use PDOException;

/**
 * Class CUpdateStatusToMixOrderLine
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 28/10/2019
 * @since 1.0
 */
class CUpdateStatusToMixOrderLine extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {
        $this->UpdateStatusToMixOrderLine();
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function UpdateStatusToMixOrderLine()
    {
        $orderRepo = \Monkey::app()->repoFactory->create('Order');
        $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');
        $orderLineWorking = ['ORD_WAIT','ORD_PENDING','ORD_LAB','ORD_FRND_OK','ORD_FRND_SENT','ORD_CHK_IN','ORD_PCK_CLI','ORD_FRND_SNDING','ORD_MAIL_PREP_C','ORD_FRND_ORDSNT'];
        $orderLineShipped = ['ORD_ARCH','ORD_SENT','ORD_FRND_PYD'];
        $orderLineCancel = ['ORD_FRND_CANC','ORD_MISSNG','ORD_CANCEL','ORD_QLTY_KO','ORD_ERR_SEND'];

        $order = $orderRepo->findAll();
        foreach ($order as $orders) {
            $orderLine = $orderLineRepo->findBy(['orderId' => $orders->id]);
            $countStatusWorking = 0;
            $countStatusShipped = 0;
            $countStatusCancel = 0;
            $countOrderLine = 0;
            foreach ($orderLine as $orderLines) {
                switch (true) {
                    case in_array($orderLines->status,$orderLineWorking,true):
                        $countStatusWorking = $countStatusWorking + 1;
                        break;
                    case in_array($orderLines->status,$orderLineShipped,true):
                        $countStatusShipped = $countStatusShipped + 1;
                        break;
                    case in_array($orderLines->status,$orderLineCancel,true):
                        $countStatusCancel = $countStatusCancel + 1;
                        break;

                }
                $countOrderLine = $countOrderLine + 1;
            }
            $this->report('UpdateStatusToMixOrderLine','count OrderLine' . $orders->id . ' Order','');

            if ($countOrderLine > 1) {
                if ($countStatusWorking > 0 && $countStatusCancel > 0 && $countStatusShipped > 0) {
                    $orders->status = 'ORD_MIX';
                    $statusForRemote='ORD_MIX';
                    $orders->update();
                    $this->report('UpdateStatusToMixOrderLine','Updated status to ORD_MIX ' . $orders->id . ' Order','');
                } elseif ($countStatusWorking > 0 && $countStatusCancel > 0 && $countStatusShipped == 0) {
                    $orders->status = 'ORD_MIX';
                    $statusForRemote='ORD_MIX';
                    $orders->update();
                    $this->report('UpdateStatusToMixOrderLine','Updated status to ORD_MIX ' . $orders->id . ' Order','');
                } elseif ($countStatusWorking > 0 && $countStatusShipped > 0 && $countStatusCancel == 0) {
                    $orders->status = 'ORD_MIX';
                    $statusForRemote='ORD_MIX';
                    $orders->update();
                    $this->report('UpdateStatusToMixOrderLine','Updated status to ORD_MIX ' . $orders->id . ' Order','');
                } elseif ($countStatusCancel > 0 && $countStatusShipped > 0 && $countStatusWorking == 0) {
                    $orders->status = 'ORD_MIX';
                    $statusForRemote='ORD_MIX';
                    $orders->update();
                    $this->report('UpdateStatusToMixOrderLine','Updated status to ORD_MIX ' . $orders->id . ' Order','');
                } elseif ($countStatusCancel > 0 && $countStatusShipped == 0 && $countStatusWorking == 0) {
                    $orders->status = 'ORD_CANC';
                    $statusForRemote='CANC';
                    $orders->update();
                    $this->report('UpdateStatusToMixOrderLine','Updated status to ORD_CANC' . $orders->id . ' Order','');
                } elseif ($countStatusCancel == 0 && $countStatusShipped > 0 && $countStatusWorking == 0) {
                    $orders->status = 'ORD_SHIPPED';
                    $statusForRemote='ORD_SHIPPED';
                    $orders->update();
                    $this->report('UpdateStatusToMixOrderLine','Updated status to ORD_SHIPPED ' . $orders->id . ' Order','');
                } elseif ($countStatusCancel == 0 && $countStatusShipped == 0 && $countStatusWorking > 0) {
                    $orders->status = 'ORD_WORK';
                    $statusForRemote='ORD_WORK';
                    $orders->update();
                    $this->report('UpdateStatusToMixOrderLine','Updated status to ORD_WORK ' . $orders->id . ' Order','');
                } else {

                }
            } else {
                if ($countStatusCancel ==1 && $countStatusShipped == 0 && $countStatusWorking == 0) {
                    $orders->status = 'ORD_CANC';
                    $statusForRemote='ORD_CANC';
                    $orders->update();
                    $this->report('UpdateStatusToMixOrderLine','Updated status to ORD_CANC' . $orders->id . ' Order','');
                } elseif ($countStatusCancel == 0 && $countStatusShipped == 1 && $countStatusWorking == 0) {
                    $orders->status = 'ORD_SHIPPED';
                    $statusForRemote='ORD_SHIPPED';
                    $orders->update();
                    $this->report('UpdateStatusToMixOrderLine','Updated status to ORD_SHIPPED ' . $orders->id . ' Order','');
                } elseif ($countStatusCancel == 0 && $countStatusShipped == 0 && $countStatusWorking ==1) {
                    $orders->status = 'ORD_WORK';
                    $statusForRemote='ORD_WORK';
                    $orders->update();
                    $this->report('UpdateStatusToMixOrderLine','Updated status to ORD_WORK ' . $orders->id . ' Order','');
                } else {

                }

            }
            $remoteShopSellerId=$orders->remoteShopSellerId;
            $remoteOrderId=$orders->remoteOrderSellerId;
            $shopRepo = \Monkey ::app() -> repoFactory -> create('Shop') -> findOneBy(['id' => $remoteShopSellerId]);
            $db_host = $shopRepo -> dbHost;
            $db_name = $shopRepo -> dbName;
            $db_user = $shopRepo -> dbUsername;
            $db_pass = $shopRepo -> dbPassword;
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
                $db_con -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $res = " connessione ok <br>";
            } catch (PDOException $e) {
                $res = $e -> getMessage();
            }
            try{
                $stmtUpdateOrder=$db_con->prepare('UPDATE `Order` set `status`=\''.$statusForRemote.'\' WHERE id='.$remoteOrderId);
                $stmtUpdateOrder->execute();
            }catch (\Throwable $e){
                $this->report('CUpdateStatusToMixOrderLine', 'error',$e);

            }


        }

    }
}
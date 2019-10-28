<?php

namespace bamboo\blueseal\jobs;

use bamboo\core\jobs\ACronJob;

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
        $this->UpdateStatusToMixOrdeLine();
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function UpdateStatusToMixOrdeLine()
    {
        $orderRepo = \Monkey::app()->repoFactory->create('Order');
        $ordeLineRepo = \Monkey::app()->repoFactory->create('OrderLine');
        $orderLineWorking = ['ORD_WAIT','ORD_PENDING','ORD_LAB','ORD_FRND_OK','ORD_FRND_SENT','ORD_CHK_IN','ORD_PCK_CLI','ORD_FRND_SNDING','ORD_MAIL_PREP_C','ORD_FRND_ORDSNT'];
        $orderLineShipped = ['ORD_ARCH','ORD_SENT','ORD_FRND_PYD'];
        $orderLineCancel = ['ORD_FRND_CANC','ORD_MISSNG','ORD_CANCEL','ORD_QLTY_KO','ORD_ERR_SEND'];

        $order = $ordeRepo->findAll();
        foreach ($order as $orders) {
            $orderLine = $orderLineRepo->findBy(['orderId' => $orderId->id]);
            $countStatuWorking = 0;
            $countStatusShipped = 0;
            $countStatusCancel = 0;
            $countOrderLine = 0;
            foreach ($orderLine as $orderLines) {
                switch (true) {
                    case in_array($orderLines->status,$orderLineWorking,true):
                        $countStatuWorking = $countStatuWorking + 1;
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
            if ($counterOrderLine > 1) {
                if ($countStatusWorking > 0 && $countStatusCancel > 0 && $countStatusShipped > 0) {
                    $orders->status = 'ORD_MIX';
                    $orders->update();
                    $this->report('UpdateStatusToMixOrdeLine','Updated status to ' . $order->id . ' Order');
                } elseif ($countStatusWorking > 0 && $countStatusCancel > 0 && $countStatusShipped==0) {
                    $orders->status = 'ORD_MIX';
                    $orders->update();
                    $this->report('UpdateStatusToMixOrdeLine','Updated status to ' . $order->id . ' Order');
                } elseif ($countStatusWorking > 0 && $countStatusShipped > 0 &&$countStatusCancel==0) {
                    $orders->status = 'ORD_MIX';
                    $orders->update();
                    $this->report('UpdateStatusToMixOrdeLine','Updated status to ' . $order->id . ' Order');
                } elseif ($countStatusCancel > 0 && $countStatusShipped > 0 && $countStatuWorking==0 ) {
                    $orders->status = 'ORD_MIX';
                    $orders->update();
                    $this->report('UpdateStatusToMixOrdeLine','Updated status to ' . $order->id . ' Order');
                } else{

                }
            }


        }

    }
}
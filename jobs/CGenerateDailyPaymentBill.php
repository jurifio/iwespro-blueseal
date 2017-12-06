<?php
namespace bamboo\blueseal\jobs;

use bamboo\domain\entities\COrder;
use bamboo\core\jobs\ACronJob;

/**
 * Class CCleanOrders
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CGenerateDailyPaymentBill extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $plafond = \Monkey::app()->repoFactory->create('Configuration')->fetchConfigurationValue('paymentBillPlafond');
        $this->report('Creating PaymentBills','Plafond: '.$plafond);
        $res = \Monkey::app()->repoFactory->create('PaymentBill')->createFillingBill($plafond);
        $this->report('Creating PaymentBills','Created bill to fit last day '.count($res).' bills',$res);
        $res = \Monkey::app()->repoFactory->create('PaymentBill')->defaultPaymentBillsCreation($plafond);
        $this->report('Creating PaymentBills','Created normal bills'.count($res).' bills',$res);
    }

}
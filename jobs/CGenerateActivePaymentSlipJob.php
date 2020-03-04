<?php
namespace bamboo\blueseal\jobs;

use bamboo\domain\entities\COrder;
use bamboo\core\jobs\ACronJob;

/**
 * Class CGenerateActivePaymentSlipJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/03/2020
 * @since 1.0
 */
class CGenerateActivePaymentSlipJob extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        try {
            $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
            $billRegistryTimeTableRepo = \Monkey::app()->repoFactory->create('BillRegistryTimeTable');
            $billRegistryClientRepo = \Monkey::app()->repoFactory->create('BillRegistryClient');
            $billRegistryTypePaymentRepo = \Monkey::app()->repoFactory->create('BillRegistryTypePayment');
            $billRegistryActivePaymentSlipRepo = \Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlip');
            $res = $this->app->dbAdapter->query('SELECT group_concat(btt.id) as id,SUM(btt.amountPayment) AS amountPayment,MAX(btt.dateEstimated) AS paymentDate FROM BillRegistryTimeTable btt 
JOIN BillRegistryInvoice bri ON btt.billRegistryInvoiceId=bri.id where btt.amountPaid =0 and datepayment is null
GROUP BY bri.billRegistryClientId,date_format(btt.dateEstimated,"%d-%c-%Y"),bri.billRegistryTypePaymentId',[])->fetchAll();
            $today = new \DateTime();
            $creationDate = $today->format('Y-m-d H:i:s');
            foreach ($res as $result) {
                $numberDocument = $this->app->dbAdapter->query("SELECT ifnull(MAX(id),0)+1 as new
            FROM PaymentBill",[])->fetchAll()[0]['new'];

                $braps = $billRegistryActivePaymentSlipRepo->getEmptyEntity();

                $braps->amount = $result['amountPayment'];
                $braps->numberSlip = $numberDocument;
                $braps->creationDate = $creationDate;
                $braps->paymentDate = $result['paymentDate'];
                $braps->statusId = 6;
                $braps->insert();
                $numberActivePayment = $this->app->dbAdapter->query("SELECT max(id)  as billRegistryActivePaymentSlipId
            FROM billRegistryActivePaymentSlip",[])->fetchAll()[0]['billRegistryActivePaymentSlipId'];
                $array = explode(',',$result['id']);
                foreach ($array as $values) {
                    $btt = $billRegistryTimeTableRepo->findOneBy(['id' => $values]);
                    $btt->billRegistryActivePaymentSlipId = $numberActivePayment;
                    $btt->update();
                }
                $newNumber = $numberDocument + 1;
                $this->app->dbAdapter->query("ALTER TABLE PaymentBill auto_increment=" . $newNumber,[])->fetchAll();

            }

        }catch(\Throwable $e){
            \Monkey::app()->applicationLog('CGenerateActivePaymentSlipJob','error', 'Active ',$e,'');
        }

    }

}
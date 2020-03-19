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
JOIN BillRegistryInvoice bri ON btt.billRegistryInvoiceId=bri.id where btt.amountPaid =0 and btt.datePayment is null
GROUP BY bri.billRegistryClientId,bri.billRegistryTypePaymentId,bri.dateEstimated',[])->fetchAll();
            $today = new \DateTime();
            $creationDate = $today->format('Y-m-d H:i:s');
            $numberPaymentBankSlip=$this->app->dbAdapter->query("SELECT ifnull(MAX(bankSlipNumberId),0)+1 as bankSlipNumberId
            FROM BillRegistryActivePaymentSlip",[])->fetchAll()[0]['bankSlipNumberId'];

            if (ENV === 'dev') {
                $db_host = 'localhost';
                $db_name = 'information_schema';
                $db_user = 'root';
                $db_pass = 'geh44fed';
                $dbnamesel='pickyshop_dev';
            } else {
                $db_host = '5.189.159.187';
                $db_name = 'information_schema';
                $db_user = 'root';
                $db_pass = 'fGLyZV4N3vapUo9';
                $dbnamesel='pickyshopfront';
            }
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
                $db_con -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $rest = ' connessione ok <br>';
            } catch (PDOException $e) {
                $rest = $e -> getMessage();
            }



            foreach ($res as $result) {
                $rowNumberDocument = $db_con->prepare('SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES
                                                                 WHERE TABLE_SCHEMA = \''.$dbnamesel.'\' AND TABLE_NAME = \'PaymentBill\';');
                $rowNumberDocument->execute();
                $numberDocument=$rowNumberDocument->fetch(PDO::FETCH_ASSOC);

                $braps = $billRegistryActivePaymentSlipRepo->getEmptyEntity();

                $braps->amount = $result['amountPayment'];
                $braps->numberSlip = $numberDocument['AUTO_INCREMENT'];
                $braps->creationDate = $creationDate;
                $braps->paymentDate = $result['paymentDate'];
                $braps->bankSlipNumberId = $numberPaymentBankSlip;
                $braps->statusId = 6;
                $braps->insert();
                $numberActivePayment = $this->app->dbAdapter->query('SELECT max(id)  as billRegistryActivePaymentSlipId
            FROM BillRegistryActivePaymentSlip',[])->fetchAll()[0]['billRegistryActivePaymentSlipId'];
                $array = explode(',',$result['id']);
                foreach ($array as $values) {
                    $btt = $billRegistryTimeTableRepo->findOneBy(['id' => $values]);
                    $btt->billRegistryActivePaymentSlipId = $numberActivePayment;
                    $btt->update();
                }
                $newNumber= $numberDocument['AUTO_INCREMENT']+1;
                $updateNumberDocument=$db_con->prepare('ALTER TABLE PaymentBill auto_increment='.$newNumber);
                $updateNumberDocument->execute();

            }


        }catch(\Throwable $e){
            \Monkey::app()->applicationLog('CGenerateActivePaymentSlipJob','error', 'Active ',$e,'');
        }

    }

}
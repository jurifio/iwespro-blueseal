<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CBillRegistryGenerateActivePaymentManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 05/03/2020
 * @since 1.0
 */
class CBillRegistryGenerateActivePaymentManageAjaxController extends AAjaxController
{


    public function post()
    {

        $data = $this->app->router->request()->getRequestData();
        $paymentStartDate = new \DateTime($data['paymentStartDate']);
        $paymentEndDate = new \DateTime($data['paymentEndDate']);
        $startDate = $paymentStartDate->format('Y-m-d 00:00:00');
        $endDate = $paymentEndDate->format('Y-m-d 23:59:00');
        $typePayment = $data['typePayment'];
        $isProgrammable = $data['isProgrammable'];
        if ($isProgrammable == "1") {
            try {
                $job = \Monkey::app()->repoFactory->create('Job')->findOneBy(['command' => 'bamboo\blueseal\jobs\CGenerateActivePaymentSlipJob']);
                $job->isActive = 1;
                $job->update();

            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('CBillRegistryActivePaymentSlipManagaAjaxController','error','Active ',$e,'');
                $res = 'errore ' . $e;
            }


        } else {
            try {
                $clientId = isset($data['clientId']) ? $data['clientId'] : "0";
                if ($clientId != '') {
                    $sqlFilter = 'and bri.billRegistryClientId=' . $clientId;
                } else {
                    $sqlFilter = '';
                }
                switch ($typePayment) {
                    case "1":
                        $sqlbankable='';
                        break;
                    case "2":
                        $sqlbankable=' and brtp.isBankable=1 ';
                        break;
                    case "3":
                        $sqlbankable=' and brtp.isBankable=0 ';
                        break;


                }


                $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
                $billRegistryTimeTableRepo = \Monkey::app()->repoFactory->create('BillRegistryTimeTable');
                $billRegistryClientRepo = \Monkey::app()->repoFactory->create('BillRegistryClient');
                $billRegistryTypePaymentRepo = \Monkey::app()->repoFactory->create('BillRegistryTypePayment');
                $billRegistryActivePaymentSlipRepo = \Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlip');
                $res = $this->app->dbAdapter->query('SELECT   group_concat(btt.id) as id,SUM(btt.amountPayment) AS amountPayment,MAX(btt.dateEstimated) AS paymentDate FROM BillRegistryTimeTable btt 
JOIN BillRegistryInvoice bri ON btt.billRegistryInvoiceId=bri.id left JOIN BillRegistryTypePayment brtp ON bri.billRegistryTypePaymentId=brtp.id 
 where btt.amountPaid =0 and btt.dateEstimated >=\'' . $startDate . '\' and btt.dateEstimated <=\'' . $endDate . '\'
'.$sqlbankable . $sqlFilter . '  group BY bri.billRegistryClientId,date_format(btt.dateEstimated,"%d-%c-%Y"),bri.billRegistryTypePaymentId',[])->fetchAll();
                if ($res == null) {
                    return "non ci sono scadenze utili per la generazione delle distinte";
                }
                $today = new \DateTime();
                $creationDate = $today->format('Y-m-d H:i:s');
                $numberPaymentBankSlip = $this->app->dbAdapter->query("SELECT ifnull(MAX(bankSlipNumberId),0)+1 as bankSlipNumberId
            FROM BillRegistryActivePaymentSlip",[])->fetchAll()[0]['bankSlipNumberId'];

                if (ENV === 'dev') {
                    $db_host = 'localhost';
                    $db_name = 'information_schema';
                    $db_user = 'root';
                    $db_pass = 'geh44fed';
                    $dbnamesel = 'pickyshop_dev';
                } else {
                    $db_host = '5.189.159.187';
                    $db_name = 'information_schema';
                    $db_user = 'root';
                    $db_pass = 'fGLyZV4N3vapUo9';
                    $dbnamesel = 'pickyshopfront';
                }
                try {

                    $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                    $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                    $rest = ' connessione ok <br>';
                } catch (PDOException $e) {
                    $rest = $e->getMessage();
                }

                $rowNumberDocument = $db_con->prepare('SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES
                                                                 WHERE TABLE_SCHEMA = \'' . $dbnamesel . '\' AND TABLE_NAME = \'PaymentBill\';');
                $rowNumberDocument->execute();
                $numberDocument = $rowNumberDocument->fetch(PDO::FETCH_ASSOC);


                foreach ($res as $result) {


                    $braps = $billRegistryActivePaymentSlipRepo->getEmptyEntity();

                    $braps->amount = $result['amountPayment'];
                    $braps->numberSlip = $numberDocument;
                    $braps->creationDate = $creationDate;
                    $braps->paymentDate = $result['paymentDate'];
                    $braps->statusId = 6;
                    $braps->bankSlipNumberId = $numberPaymentBankSlip;
                    $braps->insert();
                    $numberActivePayment = $this->app->dbAdapter->query("SELECT max(id)  as billRegistryActivePaymentSlipId
                FROM BillRegistryActivePaymentSlip",[])->fetchAll()[0]['billRegistryActivePaymentSlipId'];
                    $array = explode(',',$result['id']);
                    foreach ($array as $values) {
                        $btt = $billRegistryTimeTableRepo->findOneBy(['id' => $values]);
                        $btt->billRegistryActivePaymentSlipId = $numberActivePayment;
                        $btt->update();
                    }


                }
                $newNumber = $numberDocument + 1;
                $this->app->dbAdapter->query('ALTER TABLE PaymentBill auto_increment=' . $newNumber);

                $res = 'generazione Eseguita';

            } catch
            (\Throwable $e) {
                \Monkey::app()->applicationLog('CBillRegistryActivePaymentSlipManagaAjaxController','error','Active ',$e,'');
                $res = 'errore ' . $e;
            }
        }
        return $res;

    }

}
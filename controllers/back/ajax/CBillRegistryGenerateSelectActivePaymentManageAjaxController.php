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
 * Class CBillRegistryGenerateSelectActivePaymentManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/04/2020
 * @since 1.0
 */
class CBillRegistryGenerateSelectActivePaymentManageAjaxController extends AAjaxController
{


    public function post()
    {

        $data = $this->app->router->request()->getRequestData();
        $selected = $data['selectedInvoice'];
        $selectedString=implode(",", $selected);
        $selectedInvoice=trim(str_replace('row__','',$selectedString));



                $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
                $billRegistryTimeTableRepo = \Monkey::app()->repoFactory->create('BillRegistryTimeTable');
                $billRegistryClientRepo = \Monkey::app()->repoFactory->create('BillRegistryClient');
                $billRegistryTypePaymentRepo = \Monkey::app()->repoFactory->create('BillRegistryTypePayment');
                $billRegistryActivePaymentSlipRepo = \Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlip');
                try{
                $res = $this->app->dbAdapter->query('SELECT   group_concat(btt.id) as id,SUM(btt.amountPayment) AS amountPayment,MAX(btt.dateEstimated) AS paymentDate FROM BillRegistryTimeTable btt 
JOIN BillRegistryInvoice bri ON btt.billRegistryInvoiceId=bri.id left JOIN BillRegistryTypePayment brtp ON bri.billRegistryTypePaymentId=brtp.id 
 where btt.amountPaid =0 and bri.id in('.$selectedInvoice.')
 group BY bri.billRegistryClientId,date_format(btt.dateEstimated,"%d-%c-%Y"),bri.billRegistryTypePaymentId',[])->fetchAll();
                if ($res == null) {
                    return "non ci sono scadenze utili per la generazione delle distinte";
                }
                $today = new \DateTime();
                $creationDate = $today->format('Y-m-d H:i:s');
                $numberPaymentBankSlip = $this->app->dbAdapter->query("SELECT ifnull(MAX(bankSlipNumberId),0)+1 as bankSlipNumberId
            FROM BillRegistryActivePaymentSlip",[])->fetchAll()[0]['bankSlipNumberId'];

                if (ENV === 'dev') {
                    $db_host = 'localhost';
                    $db_name = 'pickyshop_dev';
                    $db_user = 'root';
                    $db_pass = 'geh44fed';
                    $dbnamesel = 'pickyshop_dev';
                } else {
                    $db_host = '5.189.159.187';
                    $db_name = 'pickyshopfront';
                    $db_user = 'pickyshop4';
                    $db_pass = 'rrtYvg6W!';
                    $dbnamesel = 'pickyshopfront';
                }
                try {

                    $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                    $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                    $rest = ' connessione ok <br>';
                } catch (PDOException $e) {
                    $rest = $e->getMessage();
                }



                foreach ($res as $result) {
                    $stmtNumberDocument = $db_con->prepare('SELECT max(id)+1  as id from PaymentBill');
                    $stmtNumberDocument->execute();
                    $rowNumberDocument = $stmtNumberDocument->fetch(PDO::FETCH_ASSOC);
                    $numberDocumentPassive=$rowNumberDocument['id'];
                    $stmtNumberDocument = $db_con->prepare('SELECT max(numberSlip)+1  as id from BillRegistryActivePaymentSlip');
                    $stmtNumberDocument->execute();
                    $rowNumberDocument = $stmtNumberDocument->fetch(PDO::FETCH_ASSOC);
                    $numberDocumentActive=$rowNumberDocument['id'];
                    if($numberDocumentActive>$numberDocumentPassive){
                        $numberDocument=$numberDocumentActive;
                    }else{
                        $numberDocument=$numberDocumentPassive;
                    }

                    $braps = $billRegistryActivePaymentSlipRepo->getEmptyEntity();

                    $braps->amount = $result['amountPayment'];
                    $braps->numberSlip =  $numberDocument;
                    $braps->creationDate = $creationDate;
                    $braps->paymentDate = $result['paymentDate'];
                    $braps->statusId = 6;
                    $braps->bankSlipNumberId = $numberPaymentBankSlip;
                    $braps->insert();
                    $numberActivePayment = $this->app->dbAdapter->query('SELECT max(id)  as billRegistryActivePaymentSlipId
                FROM BillRegistryActivePaymentSlip',[])->fetchAll()[0]['billRegistryActivePaymentSlipId'];
                    $array = explode(',',$result['id']);
                    foreach ($array as $values) {
                        $btt = $billRegistryTimeTableRepo->findOneBy(['id' => $values]);
                        $btt->billRegistryActivePaymentSlipId = $numberActivePayment;
                        $btt->update();
                    }

                    $newNumber= $numberDocument;
                    $updateNumberDocument=$db_con->prepare('ALTER TABLE PaymentBill AUTO_INCREMENT='.$newNumber);
                    $updateNumberDocument->execute();
                }



                $res = 'generazione Eseguita';

            } catch
            (\Throwable $e) {
                \Monkey::app()->applicationLog('CBillRegistryGenerateActivePaymentSlipManageAjaxController','error','Active ',$e,'');
                $res = 'errore ' . $e;
            }

        return $res;

    }
    public function delete()
    {
        try{
        $res='';
        $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
        $billRegistryTimeTableRepo = \Monkey::app()->repoFactory->create('BillRegistryTimeTable');
        $billRegistryClientRepo = \Monkey::app()->repoFactory->create('BillRegistryClient');
        $billRegistryTypePaymentRepo = \Monkey::app()->repoFactory->create('BillRegistryTypePayment');
        $billRegistryActivePaymentSlipRepo = \Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlip');
        $data = $this->app->router->request()->getRequestData();
        $selected = $data['selectedInvoice'];
        foreach($selected as $is){
            $invoice=$billRegistryInvoiceRepo->findOneBy(['id'=>$is]);
            $amountInvoice=$invoice->grossTotal;
            $invoiceNumber=$invoice->invoiceNumber.'-'.$invoice->invoiceType.'/'.$invoice->invoiceYear;
            $braps=$billRegistryTimeTableRepo->findOneBy(['billRegistryInvoiceId'=>$is]);
            $billRegistryActivePaymentSlipId=$braps->billRegistryActivePaymentSlipId;
            $paymentBillActive=$billRegistryActivePaymentSlipRepo->findOneBy(['id'=>$billRegistryActivePaymentSlipId]);

            if($paymentBillActive) {
                $newAmount=$paymentBillActive->amount-$amountInvoice;
                $paymentBillActive->amount=$newAmount;
                $bt = $billRegistryTimeTableRepo->findBy(['billRegistryInvoiceId' => $is]);
                foreach ($bt as $timePayment) {
                    $timePayment->billRegistryActivePaymentSlipId = null;
                    $timePayment->update();
                }
                return ' la fattura '.$invoiceNumber. ' è stata disassociata';
            }else{
                return 'Non è stata trovata la distinta da disassociare per la fattura '.$invoiceNumber;
            }

        }

        return 'fine lavoro';

        } catch
        (\Throwable $e) {
            \Monkey::app()->applicationLog('CBillRegistryGenerateActivePaymentSlipManageAjaxController','error','Remove Invoice from PaymentSlip ',$e->getMessage(),'');
            $res = 'errore ' . $e;
        }

        return $res;

    }

}
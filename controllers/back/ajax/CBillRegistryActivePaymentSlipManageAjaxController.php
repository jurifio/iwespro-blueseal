<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CBillRegistryActivePaymentSlipManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/03/2020
 * @since 1.0
 */
class CBillRegistryActivePaymentSlipManageAjaxController extends AAjaxController
{
    public function put()
    {
        try {


            $data = $this->app->router->request()->getRequestData();
            $imp=explode('-',$data['paymentBillId']);
            // id distinta passiva
            $paymentBillId = $imp[0];
            // importo distinta passiva
            $paAmount=$imp[1];
            //importo pagato distinta passiva
            $paAmountPaid=$imp[2];
            //importo
            $paRestPaid=$imp[3];
            $recipientId = $data['recipientId'];
            $amountActive=$data['amountActive'];
            $billRegistryActivePaymentSlipId = $data['documentId'];
            $paymentBillRepo = \Monkey::app()->repoFactory->create('PaymentBill');
            $billRegistryPaymentActiveSlipRepo = \Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlip');
            $billRegistryTimeTableRepo = \Monkey::app()->repoFactory->create('BillRegistryTimeTable');
            $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
            $billRegistryClientRepo=\Monkey::app()->repoFactory->create('BillRegistryClient');
            $billRegistryClientAccountRepo=\Monkey::app()->repoFactory->create('BillRegistryClientAccount');
            $shopRepo=\Monkey::app()->repoFactory->create('Shop');
            $brpas = $billRegistryPaymentActiveSlipRepo->findOneBy(['id' => $billRegistryActivePaymentSlipId]);


            $p = $paymentBillRepo->findOneBy(['id' => $paymentBillId]);
            $paymentSlip=$p->id;
            if($paAmountPaid==0){
                $paymentRest=$p->amount;
            }else{
                $paymentRest=$paAmountPaid;
            }
            $tempNote=$p->note;
            // se l'importo  passivo è maggior di zeo allora
            if($p->amountPaid==0){
                $amountPassive=$p->amountPaid;
            }else{
                $amountPassive =$p->amount;

            }
            $amountInvoice = 0;
                $amountPayment=$amountActive;
                if($paAmountPaid==0) {
                    $p->amountPaid = number_format($paAmount - $amountActive,2,'.');
                }else{
                    $p->amountPaid=number_format($paymentRest-$amountActive,2,'.');
                }
                $p->note=$tempNote.'<br>compensazione con distinta attiva '.$brpas->numberSlip;
                $p->update();

            $i = 1;

            $brtt = $billRegistryTimeTableRepo->findBy(['billRegistryActivePaymentSlipId' => $billRegistryActivePaymentSlipId]);
            $invoiceIds=$brtt;
            $client=$billRegistryClientRepo->findOneBy(['id'=>$brtt->billRegistryClientId]);

            $emailCustomer=[$client->emailAddress];
            $companyName=$client->companyName;
            if ($brtt != null) {
                foreach ($brtt as $payment) {

                    $amountActive -= $amountPayment;
                    if ($i == 1) {
                        $bri = $billRegistryInvoiceRepo->findOneBy(['id' => $payment->billRegistryInvoiceId]);
                        $amountInvoice = $bri->grossTotal;
                    }
                    $date = new \DateTime();
                    $datePayment = $date->format('Y-m-d H:i:s');
                    $ratePayment = number_format($payment->amountPayment,2,'.');
                    if ($ratePayment <= $amountPayment) {
                        $payment->amountPaid = $ratePayment;
                        $payment->datePayment = $datePayment;
                        $amountPayment -= $ratePayment;
                        $amountInvoice -= $ratePayment;
                        $payment->update();
                        if ($amountPayment >= $p->amount) {
                            $p->isPaid = 1;
                            $p->note = $tempNote.'<br>compensata con distinta Attiva n. ' . $billRegistryActivePaymentSlipId;
                            $p->update();

                            /** @var CEmailRepo $mailRepo */
                            $mailRepo = \Monkey::app()->repoFactory->create('Email');
                            $mailRepo->newPackagedMail('sendadjustementtocustomer', 'no-reply@iwes.pro', $emailCustomer, [], ['amministrazione@iwes.it'], [
                                'invoiceIds' => $invoiceIds,
                                'slipArray' => $slipArray,
                                'numberSlip' => $numberSlip,
                                'slipTotalAmount' => $slipTotalAmount,
                                'slipFinalDate' =>$slipFinalDate,
                                'dateEstimated'=>$dateEstimated
                            ],'MailGun',$attachment);

                        }
                    } elseif ($ratePayment > $amountPayment) {
                        $payment->amountPaid = number_format($amountPayment,2,'.');
                        $amountPayment -= $amountPayment;
                        $amountInvoice -= $amountPayment;
                        $payment->update();
                        if ($amountPayment >= $p->amount) {
                            $p->isPaid = 1;
                            $p->note = $tempNote.'<br>compensata con distinta Attiva n. ' . $billRegistryActivePaymentSlipId;
                            $p->update();
                        }
                    } elseif ($p->amountPaid <= 0) {
                        $payment->amountPaid = 0;
                        $payment->update();
                        $amountInvoice -= 0;
                        $p->isPaid = 1;
                        $p->note = $tempNote.'<br>compensata con distinta Attiva n. ' . $billRegistryActivePaymentSlipId;
                        $p->update();

                    }
                    $i++;
                }
                if ($amountInvoice <= 0) {
                    $briUpdate = $billRegistryInvoiceRepo->findOneBy(['id' => $payment->billRegistryInvoiceId]);
                    $briUpdate->statusId = 3;
                    $briUpdate->update();
                } elseif ($amountInvoice > 0) {
                    $briUpdate = $billRegistryInvoiceRepo->findOneBy(['id' => $payment->billRegistryInvoiceId]);
                    $briUpdate->statusId = 4;
                    $briUpdate->update();
                }
                if ($paAmountPaid > $amountActive) {
                    $brpas->statusId = 2;
                    $brpas->datePayment = $datePayment;
                } else {
                    $brpas->statusId = 5;
                }
                $brpas->amountRest=number_format($paymentRest,2,'.');
                $brpas->paymentBillId = $paymentBillId;
                $brpas->recipientId = $recipientId;
                $brpas->update();
            }



            return 'Associazione e Aggiornamento Eseguiti con successo';
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CBillRegistryActivePaymentSlip','error','Association PaymentBill',$e,'');
            return $e;

        }
    }

    public function post()
    {
        try {
            $data = $this->app->router->request()->getRequestData();
            $paymentStartDate = new \DateTime($data['paymentStartDate']);
            $paymentEndDate=new \DateTime($data['paymentEndDate']);
            $startDate=$paymentStartDate->format('Y-m-d 00:00:00');
            $endDate=$paymentEndDate->format('Y-m-d 23:59:00');
            $clientId= isset($data['clientId']) ?$data['clientId'] : "0" ;
            if($clientId!='' ){
                $sqlFilter='and bri.billRegistryClientId='.$clientId;
            }else{
                $sqlFilter='';
            }
            $typePaymentId=$data['typePaymentId'];


            $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
            $billRegistryTimeTableRepo = \Monkey::app()->repoFactory->create('BillRegistryTimeTable');
            $billRegistryClientRepo = \Monkey::app()->repoFactory->create('BillRegistryClient');
            $billRegistryTypePaymentRepo = \Monkey::app()->repoFactory->create('BillRegistryTypePayment');
            $billRegistryActivePaymentSlipRepo = \Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlip');
            $res = $this->app->dbAdapter->query('SELECT   group_concat(btt.id) as id,SUM(btt.amountPayment) AS amountPayment,MAX(btt.dateEstimated) AS paymentDate FROM BillRegistryTimeTable btt 
JOIN BillRegistryInvoice bri ON btt.billRegistryInvoiceId=bri.id left JOIN BillRegistryTypePayment brtp ON bri.billRegistryTypePaymentId=brtp.id 
 where btt.amountPaid =0 and btt.dateEstimated >=\''.$startDate.'\' and btt.dateEstimated <=\''.$endDate.'\'
and brtp.codice_modalita_pagamento_fe like\'%'.$typePaymentId.'%\' '.$sqlFilter.'  group BY bri.billRegistryClientId,date_format(btt.dateEstimated,"%d-%c-%Y"),bri.billRegistryTypePaymentId',[])->fetchAll();
            if($res==null){
                return "non ci sono scadenze utili per la generazione delle distinte";
            }
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
                $db_name = 'pickyshopfront';
                $db_user = 'pickyshop4';
                $db_pass = 'rrtYvg6W!';
                $dbnamesel = 'pickyshopfront';
            }
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
                $db_con -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $rest = ' connessione ok <br>';
            } catch (PDOException $e) {
                $rest = $e -> getMessage();
            }

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


            foreach ($res as $result) {


                $braps = $billRegistryActivePaymentSlipRepo->getEmptyEntity();

                $braps->amount = number_format($result['amountPayment'],2,'.');
                $braps->numberSlip = $numberDocument;
                $braps->creationDate = $creationDate;
                $braps->paymentDate = $result['paymentDate'];
                $braps->statusId = 6;
                $braps->bankSlipNumberId=$numberPaymentBankSlip;
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
            $newNumber= $numberDocument;
            $updateNumberDocument=$db_con->prepare('ALTER TABLE PaymentBill auto_increment='.$newNumber);
            $updateNumberDocument->execute();

            $res= 'generazione Eseguita';

        }catch(\Throwable $e){
            \Monkey::app()->applicationLog('CBillRegistryActivePaymentSlipManageAjaxController','error', 'Active ',$e,'');
            $res= 'errore '.$e;
        }
        return $res;

    }

}
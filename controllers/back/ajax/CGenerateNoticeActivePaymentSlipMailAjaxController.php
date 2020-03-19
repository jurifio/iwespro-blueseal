<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\RedPandaOrderLogicException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CBillRegistryContract;
use bamboo\domain\entities\CBillRegistryContractRow;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\core\theming\CMailerHelper;
use Monkey;

/**
 * Class CGenerateCustomerActivePaymentSlipMailAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/03/2020
 * @since 1.0
 */
class CGenerateNoticeActivePaymentSlipMailAjaxController extends AAjaxController
{

    public function post()
    {
        \Monkey::app()->vendorLibraries->load('phpmailer');
        $billRegistryClientRepo = \Monkey::app()->repoFactory->create('BillRegistryClient');
        $billRegistryContractRepo = \Monkey::app()->repoFactory->create('BillRegistryContract');
        $billRegistryContractRowRepo = \Monkey::app()->repoFactory->create('BillRegistryContractRow');
        $billRegistryContractRowDetailRepo = \Monkey::app()->repoFactory->create('BillRegistryContractRowDetail');
        $billRegistryClientAccountRepo = \Monkey::app()->repoFactory->create('BillRegistryClientAccount');
        $billRegistryContractRowPaymentBillRepo = \Monkey::app()->repoFactory->create('BillRegistryContractRowPaymentBill');
        $billRegistryGroupProductRepo = \Monkey::app()->repoFactory->create('BillRegistryGroupProductRepo');
        $billRegistryPriceListRepo = \Monkey::app()->repoFactory->create('BillRegistryPriceList');
        $billRegistryProductRepo = \Monkey::app()->repoFactory->create('BillRegistryProduct');
        $billRegistryProductDetailRepo = \Monkey::app()->repoFactory->create('BillRegistryProductDetail');
        $invoiceRepo = \Monkey::app()->repoFactory->create('Invoice');
        $billRegistryInvoiceRowRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoiceRow');
        $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $billRegistrySocialRepo = \Monkey::app()->repoFactory->create('BillRegistrySocial');
        $campaignRepo = \Monkey::app()->repoFactory->create('Campaign');
        $marketplaceRepo = \Monkey::app()->repoFactory->create('Marketplace');
        $marketplaceAccountRepo = \Monkey::app()->repoFactory->create('MarketplaceAccount');
        $billRegistryTypeTaxesRepo = \Monkey::app()->repoFactory->create('BillRegistryTypeTaxes');
        $billRegistryTypePaymentRepo = \Monkey::app()->repoFactory->create('BillRegistryTypePayment');
        $billRegistryClientBillingInfoRepo = \Monkey::app()->repoFactory->create('BillRegistryClientBillingInfo');
        $billRegistryTimeTableRepo = \Monkey::app()->repoFactory->create('BillRegistryTimeTable');
        $billRegistryActivePaymentSlipRepo = \Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlip');
        $today = new \DateTime();
        $day = (new \DateTime())->format('d');

        try {
            $sql = "select 
                    brc.id as billRegistryClientId,    
                    `braps`.`numberSlip` as `numberSlip`,
                     SUM(`braps`.`amount`) AS `totalAmount`,
                  `braps`.`creationDate` as `creationDate`,
                  `braps`.`paymentDate` as `PaymentDate`,
                  `braps`.`submissionDate` as `submissionDate`,
                  `brtt`.dateEstimated as dateEstimated,  
                  `braps`.`note` as `note`,
                  `brps`.`name` as `statusId`,
                  `brca`.`shopId` as shopId, 
                  `pb`.id as paymentBillId,
                  `brtt`.amountPayment as amountPayment,                
                  `pb`.amount as negativeAmount,  
                  `braps`.`amount` as positiveAmount, 
                   braps.noticeCounter as noticeCounter, 
                  GROUP_CONCAT(brtp.name) AS typePayment,
						GROUP_CONCAT(brtt.description) AS descriptionRow, 
                  GROUP_CONCAT(DISTINCT `braps`.`id`) AS id,
                  count(DISTINCT `bri`.`billRegistryClientId`) AS `transfers`,
                  group_concat(DISTINCT   `brc`.`companyName`) AS `companyName`,
                  group_concat(DISTINCT concat(`bri`.`invoiceNumber`,'-', `bri`.`invoiceType`, '-',`bri`.`invoiceYear`)) AS `invoicesNumber`,
                  group_concat(distinct bri.id) as invoiceId   
                FROM `BillRegistryActivePaymentSlip` `braps`
                 JOIN `BillRegistryTimeTable` `brtt`  on `braps`.`id` = `brtt`.`billRegistryActivePaymentSlipId` 
                LEFT JOIN `BillRegistryInvoice` `bri` on `brtt`.`billRegistryInvoiceId` =`bri`.`id`      
                 LEFT JOIN `BillRegistryClientAccount` `brca` on `bri`.`id`=`brca`.`billRegistryClientId`   
                LEFT JOIN `BillRegistryTypePayment` `brtp` on `bri`.`billRegistryTypePaymentId` = `brtp`.`id`
                LEFT JOIN `BillRegistryClient` `brc` on `bri`.`billRegistryClientId`=`brc`.`id`
                join `BillRegistryActivePaymentSlipStatus` `brps` on `braps`.`statusId`=`brps`.`id`
                LEFT JOIN PaymentBill pb on braps.paymentBillId=pb.id
                LEFT JOIN Document d on pb.id=d.id      
                LEFT JOIN AddressBook a on d.shopRecipientId=a.id where braps.statusId=6 AND brtt.amountPaid=0 AND brtt.dateEstimated <=NOW()
                GROUP BY brc.id
              ";
            $attachmentRoot = $this->app->rootPath() . $this->app->cfg()->fetch('paths', 'tempMail') ;
            //$attachmentRoot = \Monkey::app()->rootPath() . $config['templateFolder'] . '/attachment';
            $slips = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
            $attachment=[];
            foreach ($slips as $slip) {

                $daynotice=new \DateTime($slip['dateEstimated']);
                $dateToStart=$daynotice->modify('+10 day');
                $date1sol=new \DateTime($slip['dateEstimated']);
                $date1sol->modify('+10 day');
                $date2sol=new \DateTime($slip['dateEstimated']);
                $date2sol->modify('+20 day');
                $date3sol=new \DateTime($slip['dateEstimated']);
                $date3sol->modify('+30 day');
                $date4sol=new \DateTime($slip['dateEstimated']);
                $date4sol->modify('+40 day');
                $date5sol=new \DateTime($slip['dateEstimated']);
                $date5sol->modify('+50 day');
                $date6sol=new \DateTime($slip['dateEstimated']);
                $date6sol->modify('+60 day');
                if($today>=$dateToStart) {

                    $slipArray = explode(',',$slip['id']);
                    $invoiceIds = explode(',',$slip['invoiceId']);
                    $numberSlip = $slip['numberSlip'];
                    $slipTotalAmount = 0;
                    $braps = $billRegistryActivePaymentSlipRepo->findBy(['numberSlip' => $numberSlip]);
                    foreach ($braps as $totalslip) {
                        $slipTotalAmount += $totalslip->amount;
                    }
                    $amountPayment = $slip['amountPayment'];
                    $slipFinalDate = $slip['PaymentDate'];
                    $noticeCounter = $slip['noticeCounter'];
                    $dateEstimated = $slip['dateEstimated'];
                    foreach ($invoiceIds as $invoiceId) {
                        $bri = $billRegistryInvoiceRepo->findOneBy(['id' => $invoiceId]);
                        $attachment[] = ['filePath' => $attachmentRoot . '/' . $bri->invoiceNumber . $bri->invoiceType . $bri->invoiceYear . '.html','fileName' => $bri->invoiceNumber . $bri->invoiceType . $bri->invoiceYear . '.html'];
                        $attachmentFile = fopen($attachmentRoot . '/' . $bri->invoiceNumber . $bri->invoiceType . $bri->invoiceYear . '.html','w');
                        fwrite($attachmentFile,$bri->invoiceText);
                        fclose($attachmentFile);
                    }
                    $billRegistryClient = $billRegistryClientRepo->findOneBy(['id' => $slip['billRegistryClientId']]);

                    $to = [$billRegistryClient->emailAdmin];
                    $braps = $billRegistryActivePaymentSlipRepo->findBy(['numberSlip' => $numberSlip]);
                    switch (true) {
                        case ($today>=  $date1sol && $today< $date2sol):
                            $noticeCounter=1;
                            /** @var CEmailRepo $mailRepo */
                            $mailRepo = \Monkey::app()->repoFactory->create('Email');
                            $mailRepo->newPackagedMail('sendnoticesliptocustomer','no-reply@pickyshop.com',$to,['gianluca@iwes.it'],['amministrazione@iwes.it'],['invoiceIds' => $invoiceIds,
                                'slipArray' => $slipArray,
                                'numberSlip' => $numberSlip,
                                'slipTotalAmount' => $slipTotalAmount,
                                'slipFinalDate' => $slipFinalDate,
                                'dateEstimated' => $dateEstimated,
                                'noticeCounter' => $noticeCounter,
                                'amountPayment' => $amountPayment
                            ],'MailGun',$attachment);

                            foreach ($braps as $paymentBill) {
                                $paymentBill->statusId = 3;
                                $paymentBill->noticeCounter = $noticeCounter;
                                $paymentBill->udpate();
                            }
                            break;
                        case ($today>=  $date2sol && $today< $date3sol):
                            $noticeCounter=2;
                            /** @var CEmailRepo $mailRepo */
                            $mailRepo = \Monkey::app()->repoFactory->create('Email');
                            $mailRepo->newPackagedMail('sendnoticesliptocustomer','no-reply@pickyshop.com',$to,['gianluca@iwes.it'],['amministrazione@iwes.it'],['invoiceIds' => $invoiceIds,
                                'slipArray' => $slipArray,
                                'numberSlip' => $numberSlip,
                                'slipTotalAmount' => $slipTotalAmount,
                                'slipFinalDate' => $slipFinalDate,
                                'dateEstimated' => $dateEstimated,
                                'noticeCounter' => $noticeCounter,
                                'amountPayment' => $amountPayment
                            ],'MailGun',$attachment);

                            foreach ($braps as $paymentBill) {
                                $paymentBill->statusId = 3;
                                $paymentBill->noticeCounter = $noticeCounter;
                                $paymentBill->udpate();
                            }
                            break;
                        case ($today>=  $date3sol && $today< $date4sol):
                            $noticeCounter=3;
                            /** @var CEmailRepo $mailRepo */
                            $mailRepo = \Monkey::app()->repoFactory->create('Email');
                            $mailRepo->newPackagedMail('sendnoticesliptocustomer','no-reply@pickyshop.com',$to,['gianluca@iwes.it'],['amministrazione@iwes.it'],['invoiceIds' => $invoiceIds,
                                'slipArray' => $slipArray,
                                'numberSlip' => $numberSlip,
                                'slipTotalAmount' => $slipTotalAmount,
                                'slipFinalDate' => $slipFinalDate,
                                'dateEstimated' => $dateEstimated,
                                'noticeCounter' => $noticeCounter,
                                'amountPayment' => $amountPayment
                            ],'MailGun',$attachment);

                            foreach ($braps as $paymentBill) {
                                $paymentBill->statusId = 3;
                                $paymentBill->noticeCounter = $noticeCounter;
                                $paymentBill->udpate();
                            }
                            break;
                        case ($today>=  $date4sol && $today< $date5sol):
                            $noticeCounter=4;
                            /** @var CEmailRepo $mailRepo */
                            $mailRepo = \Monkey::app()->repoFactory->create('Email');
                            $mailRepo->newPackagedMail('sendnoticesliptocustomer','no-reply@pickyshop.com',$to,['gianluca@iwes.it'],['amministrazione@iwes.it'],['invoiceIds' => $invoiceIds,
                                'slipArray' => $slipArray,
                                'numberSlip' => $numberSlip,
                                'slipTotalAmount' => $slipTotalAmount,
                                'slipFinalDate' => $slipFinalDate,
                                'dateEstimated' => $dateEstimated,
                                'noticeCounter' => $noticeCounter,
                                'amountPayment' => $amountPayment
                            ],'MailGun',$attachment);

                            foreach ($braps as $paymentBill) {
                                $paymentBill->statusId = 3;
                                $paymentBill->noticeCounter = $noticeCounter;
                                $paymentBill->udpate();
                            }
                            break;
                        case ($today>=  $date5sol && $today< $date6sol):
                            $noticeCounter=5;
                            /** @var CEmailRepo $mailRepo */
                            $mailRepo = \Monkey::app()->repoFactory->create('Email');
                            $mailRepo->newPackagedMail('sendlastadvisesliptocustomer','no-reply@pickyshop.com',$to,['gianluca@iwes.it'],['amministrazione@iwes.it'],['invoiceIds' => $invoiceIds,
                                'slipArray' => $slipArray,
                                'numberSlip' => $numberSlip,
                                'slipTotalAmount' => $slipTotalAmount,
                                'slipFinalDate' => $slipFinalDate,
                                'dateEstimated' => $dateEstimated,
                                'noticeCounter' => $noticeCounter,
                                'amountPayment' => $amountPayment
                            ],'MailGun',$attachment);
                            foreach ($braps as $paymentBill) {
                                $paymentBill->statusId = 4;
                                $paymentBill->noticeCounter = $noticeCounter;
                                $paymentBill->udpate();
                            }
                            break;
                        case ($today>=  $date6sol):
                            /** @var CEmailRepo $mailRepo */
                            $mailRepo = \Monkey::app()->repoFactory->create('Email');
                            $mailRepo->newPackagedMail('senddismissservice','no-reply@pickyshop.com',$to,['gianluca@iwes.it'],['amministrazione@iwes.it'],['invoiceIds' => $invoiceIds,
                                'slipArray' => $slipArray,
                                'numberSlip' => $numberSlip,
                                'slipTotalAmount' => $slipTotalAmount,
                                'slipFinalDate' => $slipFinalDate,
                                'dateEstimated' => $dateEstimated,
                                'noticeCounter' => $noticeCounter,
                                'amountPayment' => $amountPayment
                            ],'MailGun',$attachment);
                            foreach ($braps as $paymentBill) {
                                $paymentBill->statusId = 4;
                                $paymentBill->noticeCounter = $noticeCounter;
                                $paymentBill->udpate();
                            }
                            break;
                    }
                }else{
                    continue;
                }
            }




        } catch (\Throwable $e) {

            \Monkey::app()->applicationLog('CGenerateCustomerActvivePaymentSlipMailAjaxControllerJob','error ','Errore generazione invio mail fatture',$e,'');
        }


    }
}
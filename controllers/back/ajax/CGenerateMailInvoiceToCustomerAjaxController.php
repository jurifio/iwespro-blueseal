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
 * Class CGenerateMailInvoiceToCustomerAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 07/05/2020
 * @since 1.0
 */
class CGenerateMailInvoiceToCustomerAjaxController extends AAjaxController
{
    public function get()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $invoiceId = $data['billRegistryInvoiceId'];
        $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
        $billRegistryClientRepo = \Monkey::app()->repoFactory->create('BillRegistryClient');
        $bri = $billRegistryInvoiceRepo->findOneBy(['id' => $invoiceId]);
        $res = $billRegistryClientRepo->findOneBy(['id' => $bri->billRegistryClientId])->emailAdmin;
        return $res;

    }


    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $invoiceId = $data['billRegistryInvoiceId'];
        $email = $data['email'];
        \Monkey::app()->vendorLibraries->load('phpmailer');
        $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');

        try {
            $attachmentRoot = $this->app->rootPath() . $this->app->cfg()->fetch('paths','tempMail');
            //$attachmentRoot = \Monkey::app()->rootPath() . $config['templateFolder'] . '/attachment';
            $i = 0;
            $attachment = [];

            $bri = $billRegistryInvoiceRepo->findOneBy(['id' => $invoiceId]);

            $attachment[] = ['filePath' => $attachmentRoot . '/' . $bri->invoiceNumber . $bri->invoiceType . $bri->invoiceYear . '.html','fileName' => $bri->invoiceNumber . $bri->invoiceType . $bri->invoiceYear . '.html'];
            if (file_exists($attachmentRoot . '/' . $bri->invoiceNumber . $bri->invoiceType . $bri->invoiceYear . '.html')) {
                unlink($attachmentRoot . '/' . $bri->invoiceNumber . $bri->invoiceType . $bri->invoiceYear . '.html');
            }
            $attachmentFile = fopen($attachmentRoot . '/' . $bri->invoiceNumber . $bri->invoiceType . $bri->invoiceYear . '.html','w');
            fwrite($attachmentFile,$bri->invoiceText);
            fclose($attachmentFile);
            $numberInvoice = $bri->invoiceNumber . '/' . $bri->invoiceType;
            $invoiceDate = $bri->invoiceDate;
            $grossTotal = $bri->grossTotal;

            $to = [$email];
            $tobcc = ['gianluca@iwes.it'];
            /** @var CEmailRepo $mailRepo */
            $mailRepo = \Monkey::app()->repoFactory->create('Email');
            $mailRepo->newPackagedMail('sendinvoiceservicetocustomer','no-reply@pickyshop.com',$to,$tobcc,['amministrazione@iwes.it'],['invoiceId' => $invoiceId,
                'numberInvoice' => $numberInvoice,
                'grossTotal' => $grossTotal,
                'invoiceDate' => $invoiceDate
            ],'MailGun',$attachment);

            $bri->statusId = 2;
            $bri->update();
            return 'Fattura inviata con Successo';

        } catch (\Throwable $e) {

            \Monkey::app()->applicationLog('CGenerateMailInvoiceToCustomerAjaxController','error ','Errore generazione invio mail fattura',$e->getMessage(),$e->getCode());
            return $e->getMessage();
        }


    }
}
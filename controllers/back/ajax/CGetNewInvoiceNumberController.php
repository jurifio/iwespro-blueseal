<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\repositories\CDocumentRepo;

/**
 * Class CFriendOrderRecordInvoice
 * @package bamboo\blueseal\controllers\ajax
 */
class CGetNewInvoiceNumberController extends AAjaxController
{
    public function get()
    {
        $date = \Monkey::app()->router->request()->getRequestData('date');
        $rows = \Monkey::app()->router->request()->getRequestData('rows');
        $invoiceTypeId = \Monkey::app()->router->request()->getRequestData('invoiceTypeId');
        $invoiceTypeCode = \Monkey::app()->router->request()->getRequestData('invoiceTypeCode');

        $olR = \Monkey::app()->repoFactory->create('OrderLine');
        $shpR = \Monkey::app()->repoFactory->create('Shop');
        /** @var CDocumentRepo $iR */
        $iR = \Monkey::app()->repoFactory->create('Document');
        try {
            if (!$date) $date = 'now';
            $dateTime = new \DateTime($date);
            $year = $dateTime->format('Y');
            unset($dateTime);
            $olOC = new CObjectCollection();
            foreach($rows as $k => $v) {
                $line = $olR->findOneByStringId($v);
                if (!$line) throw new BambooException('Riga d\'ordine non trovata');
                $olOC->add($line);
            }
            unset($line);
            $shopId = null;
            foreach($olOC as $v) {
                if (null == $shopId) $shopId = $v->shopId;
                elseif ($shopId != $v->shopId) throw new BambooException('Le righe d\'ordine selezionate devono essere associate ad un solo Shop');
            }
            $shp = $shpR->findOne([$shopId]);
            if (!$invoiceTypeId && !$invoiceTypeCode)
                $invoiceType = \Monkey::app()->repoFactory->create('InvoiceType')->findOneBy(['code' => 'fr_invoice_internal']);
            elseif ($invoiceTypeId) $invoiceType = \Monkey::app()->repoFactory->create('InvoiceType')->findOne([$invoiceTypeId]);
            elseif ($invoiceTypeCode) $invoiceType = \Monkey::app()->repoFactory->create('InvoiceType')->findOneBy(['code' => $invoiceTypeCode]);

            $in = $iR->getNewNumber($shp, $invoiceType, $year);
            return $in->invoiceSectional->code . '/' . $in->invoiceNumber;
        } catch(BambooException $e) {
            \Monkey::app()->router->response()->raiseRoutingError();
            return $e->getMessage();
        }
    }
}
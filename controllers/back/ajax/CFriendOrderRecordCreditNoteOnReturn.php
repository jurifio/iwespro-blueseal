<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\utils\price\SPriceToolbox;

/**
 * Class CFriendOrderRecordInvoice
 * @package bamboo\blueseal\controllers\ajax
 */
class CFriendOrderRecordCreditNoteOnReturn extends AAjaxController
{
    public function get()
    {
        $rows = \Monkey::app()->router->request()->getRequestData('rows');

        $olR = \Monkey::app()->repoFactory->create('OrderLine');

        $res = [];
        $res['error'] = false;
        $res['shop'] = 0;
        $res['lines'] = [];
        $res['totalNoVat'] = 0;
        $res['vat'] = 0;
        $res['total'] = 0;
        $res['responseText'] = '';

        $linesWOInvoice = [];
        $billingAddressBookId = false;
        $olArr = [];
        foreach($rows as $v) {
            $ol = $olR->findOneByStringId($v);
            $invoiceLineOC = $ol->invoiceLine;
            if (!$invoiceLineOC->count()) {
                $linesWOInvoice[] = $ol->printId();
            }
            if (false === $billingAddressBookId) $billingAddressBookId = $ol->shop->billingAddressBookId;
            else {
                if ($billingAddressBookId != $ol->shop->billingAddressBookId) {
                    $res['error'] = true;
                    $res['responseText'] =
                        '<p><strong>Attenzione!</strong> I prodotti selezionati devono appartenere tutti allo stesso negozio.</p>';
                    return $res;
                }
            }
            $olArr[] = $ol;
        }

        $res['billingAddressBookId'] = $billingAddressBookId;

        foreach($invoiceLineOC as $v) {
            if ('fr_credit_note_w_file' == $v->document->invoiceType->code) {
                $res['error'] = true;
                $res['responseText'] = '<p><strong>Attenzione!</strong> Una o più righe d\'ordine è già stata registrata in una nota di credito.</p>';
            }
        }

        /*if (count($linesWOInvoice)) {
            $res['error'] = true;
            $res['responseText'] = '<p>Una o più linee ordini selezionate sono senza fattura e non posso essere inseriti in una nota di credito</p><ul><li>' .
                implode('</li><li>', $linesWOInvoice) .
                '</li></ul>';
        }*/

        $vat = \Monkey::app()->repoFactory->create('Configuration')->findOneBy(['name' => 'main vat'])->value;

        foreach ($olArr as $v) {
            $line =[];
            $line['description'] = $olR->getOrderLineDescription($v);
            $line['friendRevenue'] = SPriceToolbox::formatToEur($v->friendRevenue, true);
            $res['lines'][] = $line;
            if (null == $v->friendRevenue) {
                $res['error'] = true;
                $res['message'] = 'Uno o più prodotti selezionati non hanno il Prezzo Friend. Contattaci';
            }
            $res['totalNoVat']+= SPriceToolbox::roundVat($v->friendRevenue);
        }
        $res['total'] =  SPriceToolbox::formatToEur(SPriceToolbox::grossPriceFromNet($res['totalNoVat'], $vat), true);
        $res['vat'] = SPriceToolbox::formatToEur(SPriceToolbox::vatFromNetPrice($res['totalNoVat'], $vat, true), true);
        $res['totalNoVat'] = SPriceToolbox::formatToEur($res['totalNoVat'], true);
        return json_encode($res);
    }

    public function post() {
        $rows = explode(',', \Monkey::app()->router->request()->getRequestData('rows'));
        $date = \Monkey::app()->router->request()->getRequestData('date');
        $total = \Monkey::app()->router->request()->getRequestData('total');
        $shopId =\Monkey::app()->router->request()->getRequestData('shopId');
        $user = \Monkey::app()->getUser();
        /** @var CDocumentRepo $inR */
        $inR = \Monkey::app()->repoFactory->create('Document');

        $res =[];
        $res['error'] = false;
        $res['responseText'] = 'Nota di credito correttamente inserita. Troverai il numero assegnato alle righe ordine interessate.';

        try {
            if (false !== \DateTime::createFromFormat('Y-m-d G:i:s', $date)) throw new BambooInvoiceException('La data fornita non è valida');

            $date = new \DateTime($date);

            $inR->storeFriendCreditNoteOnReturn(
                $user->id,
                $shopId,
                $date,
                null,
                0,
                $rows,
                $total
            );
            return json_encode($res);
        } catch (BambooInvoiceException $e) {
            $res['error'] = true;
            $res['responseText'] = $e->getMessage();
            return json_encode($res);
        } catch (BambooException $e) {
            \Monkey::app()->applicationError('FriendOrderRecordInvoice', 'errore grave inserimento noda di credito', $e->getMessage());
            return $e->getMessage();
        }
    }
}
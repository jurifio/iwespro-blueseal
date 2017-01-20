<?php
namespace bamboo\blueseal\controllers\ajax;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\domain\repositories\CInvoiceNewRepo;
use bamboo\utils\price\SPriceToolbox;

/**
 * Class CFriendOrderRecordInvoice
 * @package bamboo\blueseal\controllers\ajax
 */
class CFriendOrderRecordInvoice extends AAjaxController
{
    public function get()
    {
        $rows = \Monkey::app()->router->request()->getRequestData('rows');

        $olR = \Monkey::app()->repoFactory->create('OrderLine');

        $res = [];
        $res['error'] = false;
        $res['shop'] = 0;
        $res['lines'] = [];
        $res['total'] = 0;
        $res['responseText'] = '';

        $linesWoInvoice = [];
        $shopId = false;
        $olArr = [];
        foreach($rows as $v) {
            $ol = $olR->findOneByStringId($v);
            $invoiceLineOC = $ol->invoiceLine;
            if ($invoiceLineOC->count()) {
                $linesWoInvoice[] = $ol->printId();
            }
            if (false === $shopId) $shopId = $ol->shopId;
            else {
                if ($shopId != $ol->shopId) {
                    $res['error'] = true;
                    $res['responseText'] =
                        '<p><strong>Attenzione!</strong> I prodotti selezionati devono appartenere tutti allo stesso negozio.</p>';
                    return $res;
                }
            }
            $olArr[] = $ol;
        }

        $res['shop'] = $shopId;

        if (count($linesWoInvoice)) {
            $res['error'] = true;
            $res['responseText'] = '<p>Una o più linee ordini selezionate sono già state fatturate</p><ul><li>' .
                implode('</li><li>', $linesWoInvoice) .
                '</li></ul>';
        }

        foreach ($olArr as $v) {
            $line =[];
            $line['description'] = $olR->getOrderLineDescription($v);
            $line['friendRevenue'] = SPriceToolbox::formatToEur($v->friendRevenue, true);
            $res['lines'][] = $line;
            if (null == $v->friendRevenue) {
                $res['error'] = true;
                $res['message'] = 'Uno o più prodotti selezionati non hanno il Prezzo Friend. Contattaci';
            }
            $res['total'] +=$v->friendRevenue;
        }
        $res['total'] =  SPriceToolbox::formatToEur($res['total'], true);
        return json_encode($res);
    }

    public function post() {

        $rows = explode(',', \Monkey::app()->router->request()->getRequestData('rows'));
        $number = \Monkey::app()->router->request()->getRequestData('number');
        $date = \Monkey::app()->router->request()->getRequestData('date');
        $shopId =\Monkey::app()->router->request()->getRequestData('shopId');
        $user = \Monkey::app()->getUser();
        /** @var CInvoiceNewRepo $inR */
        $inR = \Monkey::app()->repoFactory->create('InvoiceNew');



        $res =[];
        $res['error'] = false;
        $res['responseText'] = 'Fattura inserita correttamente. Troverai il numero della fattura assegnato alle righe ordine interessate.';

        try {

            if (!array_key_exists('file', $_FILES)) throw new BambooInvoiceException('Non hai specificato il file riportante la fattura');
            if ('' == $number) throw new BambooInvoiceException('L\'invio della fattura è obbligatorio');
            if (false !== \DateTime::createFromFormat('Y-m-d G:i:s', $date)) throw new BambooInvoiceException('La data fornita non è valida');

            $date = new \DateTime($date);

            $inR->storeFriendInvoiceWithFile(
                $user->id,
                $shopId,
                $date,
                null,
                0,
                $number,
                $rows,
                $_FILES['file']
            );
            return json_encode($res);
        } catch (BambooInvoiceException $e) {
            $res['error'] = true;
            $res['responseText'] = $e->getMessage();
            return json_encode($res);
        } catch (BambooException $e) {
            return $e->getMessage();
        }
    }
}
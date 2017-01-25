<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\domain\repositories\CInvoiceNewRepo;

/**
 * Class CFriendOrderPayInvoices
 * @package bamboo\blueseal\controllers\ajax
 */
class CFriendOrderPayInvoices extends AAjaxController
{
    public function get() {
        $row = \Monkey::app()->router->request()->getRequestData('row');

        $iR = \Monkey::app()->repoFactory->create('InvoiceNew');

        $invoices = $iR->findBySql('SELECT id FROM InvoiceNew WHERE id in (' . implode(',', $row) . ')');

        // different controls are made if is selected a single row or many

        $res = [];
        $res['error'] = false;
        $res['message'] = '';
        //controllo che i dati fin'ora registrati siano corretti
        try {
            return $iR->checkPaymentBillBeforeInsertAndReturnDue($invoices);
        } catch(BambooInvoiceException $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
            return json_encode($res);
        } catch(BambooException $e) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessage();

        }




    }
    /**
     * @transaction
     */
    public function post()
    {
        $res = [];
        $res['error'] = false;

        $row = \Monkey::app()->router->request()->getRequestData('row');
        $date = \Monkey::app()->router->request()->getRequestData('date');
        $amount = \Monkey::app()->router->request()->getRequestData('amount');

        /** @var CInvoiceNewRepo $iR */
        $iR = \Monkey::app()->repoFactory->create('InvoiceNew');
        $invoices = $iR->findBySql('SELECT id FROM InvoiceNew WHERE id in (' . implode(',', $row) . ')');

        $dba = \Monkey::app()->dbAdapter;
        $dba->beginTransaction();
        try {
            $iR->insertPaymentBillAndPayInvoices($invoices, $amount, $date);
            $dba->commit();
            $res['message'] = 'La fattura è stata registrata come pagata';
            return json_encode($res);
        } catch(BambooInvoiceException $e) {
            $dba->rollBack();
            $res['error'] = true;
            $res['message'] = $e->getMessage();
            return json_encode($res);
        } catch(BambooException $e) {
            $dba->rollBack();
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessage();
        }
    }
}
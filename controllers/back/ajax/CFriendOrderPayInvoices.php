<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\domain\repositories\CDocumentRepo;

/**
 * Class CFriendOrderPayInvoices
 * @package bamboo\blueseal\controllers\ajax
 */
class CFriendOrderPayInvoices extends AAjaxController
{
    public function get()
    {
        $row = \Monkey::app()->router->request()->getRequestData('row');

        $iR = \Monkey::app()->repoFactory->create('Document');

        $invoices = $iR->findBySql('SELECT id FROM Document WHERE id IN (' . implode(',', $row) . ')');

        // different controls are made if is selected a single row or many

        $res = [];
        $res['error'] = false;
        $res['message'] = '';
        //controllo che i dati fin'ora registrati siano corretti
        try {
            return $iR->checkPaymentBillBeforeInsertAndReturnDue($invoices);
        } catch (BambooInvoiceException $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
            return json_encode($res);
        } catch (BambooException $e) {
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

        /** @var CDocumentRepo $iR */
        $iR = \Monkey::app()->repoFactory->create('Document');
        $invoices = $iR->findBySql('SELECT id FROM Document WHERE id IN (' . implode(',', $row) . ')');

        $dba = \Monkey::app()->dbAdapter;
        \Monkey::app()->repoFactory->beginTransaction();
        try {
            $iR->insertPaymentBillAndPayInvoices($invoices, $amount, $date);
            \Monkey::app()->repoFactory->commit();
            $res['message'] = 'La distinta è stata registrata correttamente.';
            return json_encode($res);
        } catch (BambooInvoiceException $e) {
            \Monkey::app()->repoFactory->rollback();
            $res['error'] = true;
            $res['message'] = $e->getMessage();
            return json_encode($res);
        } catch (BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessage();
        }
    }

    /**
     * @transaction
     */
    public function put()
    {
        $request = \Monkey::app()->router->request();
        $row = $request->getRequestData('row');
        $act = $request->getRequestData('action');
        $idBill = $request->getRequestData('idBill');

        $iR = \Monkey::app()->repoFactory->create('Document');
        $dba = \Monkey::app()->dbAdapter;
        if ('add' == $act) {
            $invoices = $iR->findBySql('SELECT id FROM Document WHERE id IN (' . implode(',', $row) . ')');
            try {
                \Monkey::app()->repoFactory->beginTransaction();
                $iR->addInvoicesToPaymentBill($invoices, $idBill);
                \Monkey::app()->repoFactory->commit();
                return 'Le fatture selezionate sono state inserite nella distinta specificata';
            } catch (BambooInvoiceException $e) {
                \Monkey::app()->repoFactory->rollback();
                return $e->getMessage();
            } catch(BambooException $e) {
                \Monkey::app()->repoFactory->rollback();
                \Monkey::app()->router->response()->raiseProcessingError();
                return $e->getMessage();
            }
        } elseif ('deleteInvoice' === $act) {

            $res = [];
            $res['error'] = false;
            $res['message'] = 'La fattura è stata scorporata dalla distinta';

            try {
                \Monkey::app()->repoFactory->beginTransaction();
                $invoice = $iR->findOne([$row]);
                $iR->removeInvoiceFromPaymentBill($invoice);
                \Monkey::app()->repoFactory->commit();
                return json_encode($res);
            } catch(BambooInvoiceException $e) {
                \Monkey::app()->repoFactory->rollback();
                $res['error'] = true;
                $res['message'] = $e->getMessage();
                return json_encode($res);
            } catch(BambooException $e) {
                \Monkey::app()->repoFactory->rollback();
                \Monkey::app()->router->response()->raiseProcessingError();
                return $e->getMessage();
            }
        }
    }
}
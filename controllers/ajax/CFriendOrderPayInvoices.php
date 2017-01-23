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
    /**
     * @transaction
     */
    public function post()
    {
        $res = [];
        $res['error'] = false;

        $row = \Monkey::app()->router->request()->getRequestData('row');
        $date = \Monkey::app()->router->request()->getRequestData('date');
        /** @var CInvoiceNewRepo $iR */
        $iR = \Monkey::app()->repoFactory->create('InvoiceNew');
        $invoice = $iR->findOne([$row]);

        if ($invoice->paymentDate) {
            $res['error'] = true;
            $res['message'] = 'Questa fattura è già stata precedentemente segnata come pagata';
        }
        $dba = \Monkey::app()->dbAdapter;
        $dba->beginTransaction();
        try {
            $iR->payFriendInvoice($invoice, $date);
            $dba->commit();
            $res['message'] = 'La fattura è stata registrata come pagata';
            return json_encode($res);
        } catch(BambooInvoiceException $e) {
            $dba->rollBack();
            return $e->getMessage();
        } catch(BambooException $e) {
            $dba->rollBack();
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessage();
        }
    }
}
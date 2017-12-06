<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\exceptions\BambooException;

/**
 * Class COrderAjaxController
 * @package bamboo\blueseal\controllers\ajax
 */
class COrderPay extends AAjaxController
{
    public function post(){
        $orderId = \Monkey::app()->router->request()->getRequestData('orderId');
        $toPay = \Monkey::app()->router->request()->getRequestData('toPay');

        $orderR = \Monkey::app()->repoFactory->create('Order');
        $dba = \Monkey::app()->dbAdapter;
        try {
            if (!$orderId) throw new BambooException('Nessun ordine è stato fornito');
            if (false === $toPay) throw new BambooException('Non so cosa farmene di questo ordine');
            if (!is_array($orderId)) $orderId = [$orderId];
            \Monkey::app()->repoFactory->beginTransaction();
            foreach($orderId as $oId) {
                $oE = $orderR->findOne([$oId]);
                if (!$oE) throw new BambooException('Uno o più degli ordini forniti non esistono. L\'operazione è stata annullata');
                if ($toPay) {
                    $oE->paidAmount = $oE->netTotal;
                    $oE->paymentDate = date('Y-m-d H:i:s');
                    $oE->update();
                } else {
                    $oE->paidAmount = null;
                    $oE->paymentDate = null;
                    $oE->update();
                }
            }
            \Monkey::app()->repoFactory->commit();
            $non = ($toPay) ? '' : 'non ';
            return "L'ordine ora risulta " . $non . "pagato";
        } catch(BambooException $e) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return 'C\'è stato un problemino: ' + $e->getMessage();
        }
    }
}


<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\domain\repositories\CDocumentRepo;

/**
 * Class CFriendOrderRecordInvoice
 * @package bamboo\blueseal\controllers\ajax
 */
class CFriendOrderRecordInvoiceInternal extends AAjaxController
{
    public function post()
    {
        $rows = explode(',', \Monkey::app()->router->request()->getRequestData('rows'));
        $date = \Monkey::app()->router->request()->getRequestData('date');
        $shopId = \Monkey::app()->router->request()->getRequestData('shopId');
        $user = \Monkey::app()->getUser();
        /** @var CDocumentRepo $inR */
        $inR = \Monkey::app()->repoFactory->create('Document');


        $res = [];
        $res['error'] = false;
        $res['responseText'] = 'Fattura inserita correttamente. Troverai il numero della fattura assegnato alle righe ordine interessate.';

        try {

            if (false !== \DateTime::createFromFormat('Y-m-d G:i:s', $date)) throw new BambooInvoiceException('La data fornita non è valida');

            $date = new \DateTime($date);

            $inR->storeFriendInvoiceInternal(
                $user->id,
                $shopId,
                $date,
                null,
                0,
                $rows
            );
            return json_encode($res);
        } catch (BambooInvoiceException $e) {
            $res['error'] = true;
            $res['responseText'] = $e->getMessage();
            return json_encode($res);
        } catch (BambooException $e) {
            \Monkey::app()->applicationError('FriendOrderRecordInvoiceInternal', 'errore grave inserimento fattura con fatturazione interna', $e->getMessage());
            return $e->getMessage();
        }
    }
}
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
class CFriendOrderRecordCreditNoteOnReturnWithFile extends AAjaxController
{

    public function post() {
        $rows = explode(',', \Monkey::app()->router->request()->getRequestData('rows'));
        $number = \Monkey::app()->router->request()->getRequestData('number');
        $date = \Monkey::app()->router->request()->getRequestData('date');
        $total = \Monkey::app()->router->request()->getRequestData('total');
        $billingAddressBookId =\Monkey::app()->router->request()->getRequestData('shopId');
        $user = \Monkey::app()->getUser();
        /** @var CDocumentRepo $inR */
        $inR = \Monkey::app()->repoFactory->create('Document');



        $res =[];
        $res['error'] = false;
        $res['responseText'] = 'Nota di credito correttamente inserita. Troverai il numero della fattura assegnato alle righe ordine interessate.';

        try {

            if (!array_key_exists('file', $_FILES)) throw new BambooInvoiceException('Non hai specificato il file riportante la fattura');
            if ('' == $number) throw new BambooInvoiceException('Il numero della nota di credito è obbligatorio');
            if (false !== \DateTime::createFromFormat('Y-m-d G:i:s', $date)) throw new BambooInvoiceException('La data fornita non è valida');

            $date = new \DateTime($date);

            $inR->storeFriendCreditNoteWithFile(
                $user->id,
                $billingAddressBookId,
                $date,
                null,
                0,
                $number,
                $rows,
                $_FILES['file'],
                $total
            );
            return json_encode($res);
        } catch (BambooInvoiceException $e) {
            $res['error'] = true;
            $res['responseText'] = $e->getMessage();
            return json_encode($res);
        } catch (BambooException $e) {
            \Monkey::app()->applicationError('FriendOrderRecordInvoice', 'errore grave inserimento fattura con file', $e->getMessage());
            return $e->getMessage();
        }
    }
}
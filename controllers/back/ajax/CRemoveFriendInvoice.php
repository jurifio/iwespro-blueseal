<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CInvoiceLine;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\utils\price\SPriceToolbox;

/**
 * Class CRemoveFriendInvoice
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/01/2018
 * @since 1.0
 */
class CRemoveFriendInvoice extends AAjaxController
{

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function delete()
    {

        $invoiceToDelete = 0;
        $data = \Monkey::app()->router->request()->getRequestData();

        $rows = $data['rows'];
        $selected = $data['selectedValue'];

        $selectedValue = ($selected == '1' ? 'tutte le fatture.' : 'tutti i DDT.');


        foreach ($rows as $row){
            $orderLine = explode('-', $row);

            $orderLineId = $orderLine[0];
            $orderLineOrderId = $orderLine[1];

            /** @var COrderLineRepo $orderLineRepo */
            $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');

            /** @var COrderLine $order */
            $orderLine = $orderLineRepo->findOneBy(['id'=>$orderLineId, 'orderId'=>$orderLineOrderId]);

            /** @var CInvoiceLine $invoiceLines */
            $invoiceLines = $orderLine->invoiceLine;

            foreach ($invoiceLines as $invoiceLine){
                /** @var CDocument $document */
                $document = $invoiceLine->document;

                if ($document->invoiceTypeId == $selected){
                    $invoiceToDelete = $document->id;
                } else {
                    continue;
                }
            }


            //Elimino su InvoiceLineHasOrderLine
            $sql_ILH = "DELETE FROM InvoiceLineHasOrderLine
            WHERE invoiceLineInvoiceId = $invoiceToDelete";

            \Monkey::app()->dbAdapter->query($sql_ILH,[]);

            //Elimino su Invoiceline
            $sq_il = "DELETE FROM InvoiceLine
            WHERE invoiceId = $invoiceToDelete";

            \Monkey::app()->dbAdapter->query($sq_il,[]);

            //Elimino su Invoice Bin
            $sq_b = "DELETE FROM InvoiceBin
            WHERE invoiceId = $invoiceToDelete";

            \Monkey::app()->dbAdapter->query($sq_b,[]);

            //Elimino su Document
            $sq_d = "DELETE FROM Document
            WHERE id = $invoiceToDelete";

            \Monkey::app()->dbAdapter->query($sq_d,[]);

            $res = "Abbiamo disassociato ".$selectedValue;
            return $res;
        }
    }
}
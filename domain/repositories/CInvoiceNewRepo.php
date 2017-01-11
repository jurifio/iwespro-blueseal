<?php
namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\entities\CInvoiceType;

/**
 * Class CInvoiceNewRepo
 * @package bamboo\domain\repositories
 */
class CInvoiceNewRepo extends ARepo {

    /**
     * @param $invoiceType
     * @param $userId
     * @param $isShop
     * @param $userOrShopId
     * @param $number
     * @param $date
     * @param $invoiceTypeId
     * @param $paydAmount
     * @param $paymentExpectedDate
     * @param $note
     * @param $createionDate
     *
     * @transaction
     */
    protected function createInvoice(
        $invoiceType,
        $userId,
        $isShop,
        $userOrShopId,
        $number,
        $date,
        $invoiceTypeId,
        $paydAmount,
        $paymentExpectedDate,
        $note,
        $createionDate)
    {
        $inR = \Monkey::app()->repoFactory->create('InvoiceNew');
        $inumR = \Monkey::app()->repoFactory->create('InvoiceSectional');
    }

    /**
     * @param CInvoiceType $invoiceType
     * @param bool $isShop
     * @param int $subjectId
     */
    public function newInvoiceNumberForSectional(CInvoiceType $invoiceType, bool $isShop, int $subjectId) {
        //TODO:
        $dba = \Monkey::app()->dbAdapter;
        $dba->query('SELECT MAX(invoiceNumber)')
    }

    public function recordFriendInvoice($number, $date, $paymentDate, $shopId, array $orderLine) {

    }
}
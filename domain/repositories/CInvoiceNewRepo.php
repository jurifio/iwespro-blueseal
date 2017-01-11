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
        int $userId,
        bool $isShop,
        int $recipientOrEmitterId,
        $number,
        $date,
        $invoiceTypeId,
        $paydAmount,
        $paymentExpectedDate,
        $note,
        $createionDate)
    {

        $dba = \Monkey::app()->dbAdapter;
        $inR = \Monkey::app()->repoFactory->create('InvoiceNew');
        $inSecR = \Monkey::app()->repoFactory->create('InvoiceSectional');

        //find sectional

        $fieldToSearch = (false == $isShop) ? 'userId' : 'shopId';
        $invoiceSectional = $inSecR->findOneBy(
            [
                $fieldToSearch => $recipientOrEmitterId,
                'invoiceTypeId' => $invoiceTypeId,
            ]
        );
        $dba->query()
        $newInvoiceNumber = $invoiceSectional->findOneBy([]);

    }


    /**
     * @param CInvoiceType $invoiceType
     * @param bool $isShop
     * @param int $subjectId
     */
    private function newInvoiceNumberForSectional(CInvoiceSectional $inSec, $year = null) {
        if (null === $year) $year = date_format(new \DateTime(), 'Y');

        $dba = \Monkey::app()->dbAdapter;
        $dba->query('SELECT MAX(invoiceNumber) FROM InvoiceNumber WHERE invoiceSectionalId = ? AND year = ?', [$inSec->id, $year]);
    }

    public function recordFriendInvoice($number, $date, $paymentDate, $shopId, array $orderLine) {

    }
}
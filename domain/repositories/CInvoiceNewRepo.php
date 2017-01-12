<?php
namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CInvoiceType;
use bamboo\domain\entities\CInvoiceSectional;
use bamboo\utils\price\SFileToolbox;

/**
 * Class CInvoiceNewRepo
 * @package bamboo\domain\repositories
 */
class CInvoiceNewRepo extends ARepo {

    /**
     * @param $invoiceTypeId
     * @param int $userId
     * @param bool $isShop
     * @param int $recipientOrEmitterId
     * @param \DateTime $date
     * @param float $paydAmount
     * @param array $invoiceLines
     * @param \DateTime|null $paymentExpectedDate
     * @param string|null $note
     * @param \DateTime|null $creationDate
     * @param string|null $number
     * @param string|null $filePath
     * @throws BambooException
     */
    protected function createInvoice(
        $invoiceTypeId,
        int $userId,
        bool $isShop,
        int $recipientOrEmitterId,
        \DateTime $date,
        float $paydAmount = 0,
        array $invoiceLines = [],
        \DateTime $paymentExpectedDate = null,
        string $note = null,
        \DateTime $creationDate = null,
        string $number = null,
        string $filePath = null
    ){

        if (!count($invoiceLines)) {
            throw new BambooException('Non posso registrare una fattura senza righe');
        }

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
        if ($invoiceSectional) $number = $this->newINvoiceNumberForSectional($invoiceSectional, $date->format('Y'));
        elseif (null === $number) throw new BambooException('The invoice number can\'t be emtpy');

        try {
            $dba->beginTransaction();
            $in = $inR->getEmptyEntity();
            $in->userId = $userId;
            if ($isShop) $in->shopRecipientId = $recipientOrEmitterId;
            else $in->userRceipientId = $recipientOrEmitterId;
            $in->number = $number;
            $in->date = $date->format('Y-m-d');
            $in->invoiceTypeId = $invoiceTypeId;
            $in->paydAmount = $paydAmount;
            $in->paymentExpectedDate = $paymentExpectedDate;
            $in->note = $note;
            if (null === $creationDate) $creationDate = new \DateTime();
            $in->creationDate = $creationDate->format('Y-m-d');
            $insertedId = $in->insert();

            if ($filePath) {
                $ib = \Monkey::app()->repoFactory->create('InvoiceBin')->getEmptyEntity();
                $ib->invoiceId = $insertedId;
                $ib->fileName = SFileToolbox::getFileNameFromPathString($filePath);
                $ib->bin = addslashes(file_get_contents($filePath));
                $ib->insert();
            }

            foreach($invoiceLines as $v) {

            }

            $dba->commit();
        } catch (BambooException $e) {
            $dba->rollback();
            throw $e;
        }
    }


    /**
     * @param CInvoiceType $invoiceType
     * @param bool $isShop
     * @param int $subjectId
     */
    private function newInvoiceNumberForSectional(CInvoiceSectional $inSec, $year = null) {
        if (null === $year) $year = date_format(new \DateTime(), 'Y');

        $dba = \Monkey::app()->dbAdapter;
        return $dba->query(
            'SELECT (MAX(invoiceNumber) + 1) as newInvoiceNumber FROM InvoiceNumber WHERE invoiceSectionalId = ? AND year = ?',
            [$inSec->id, $year]
        )->fetch()['newInvoiceNumber'];
    }

    public function recordFriendBinInvoice($number, $date, $paymentDate, $shopId, array $orderLine) {

    }

    public function addRowToInvoice($description, $invoiceId, $price, $vat)
}
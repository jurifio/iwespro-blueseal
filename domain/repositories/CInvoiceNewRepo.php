<?php
namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CInvoiceSectional;
use bamboo\domain\entities\CInvoiceType;
use bamboo\utils\price\SPriceToolbox;
use bamboo\domain\entities\COrderLine;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CInvoiceNewRepo
 * @package bamboo\domain\repositories
 */
class CInvoiceNewRepo extends ARepo
{
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
    private function createInvoice(
        $invoiceTypeId,
        int $userId,
        bool $isShop,
        int $recipientOrEmitterId,
        \DateTime $date,
        float $totalWithVat,
        float $paidAmount = 0,
        \DateTime $paymentExpectedDate = null,
        string $number = null,
        string $note = null,
        int $carrierId = null,
        \DateTime $creationDate = null
    )
    {
        $inR = \Monkey::app()->repoFactory->create('InvoiceNew');
        $inSecR = \Monkey::app()->repoFactory->create('InvoiceSectional');

        //date control
        if (!$creationDate) $creationDate = new \DateTime();
        $diff = $creationDate->diff($date);
        if ($diff->days && !$diff->invert) throw new BambooInvoiceException('Non si possono emettere fatture con date di emissione antecedenti alla data odierna');
        if ($paymentExpectedDate) {
            $diff = $date->diff($paymentExpectedDate);
            if ($diff->days && $diff->invert) throw new BambooInvoiceException('La data di previsto pagamento non può essere precedente all\'emissione della fattura');
        }

        $year = $date->format('Y');

        //find sectional

        $fieldToSearch = (false == $isShop) ? 'userId' : 'shopId';
        $fieldToSearchInvoice = (false == $isShop) ? 'userAddressRecipientId' : 'shopRecipientId';
        /** @var CInvoiceSectional $invoiceSectional */
        $invoiceSectional = $inSecR->findOneBy(
            [
                $fieldToSearch => $recipientOrEmitterId,
                'invoiceTypeId' => $invoiceTypeId,
            ]
        );
        if ($invoiceSectional) $number = $this->newInvoiceNumberForSectional($invoiceSectional, $date->format('Y'));
        elseif (null === $number) throw new BambooException('The invoice number can\'t be emtpy');
        else {
            $invoiceWithNumber =
                $inR->findOneBy(['number' => $number, $fieldToSearchInvoice => $recipientOrEmitterId, 'year' => $year]);
            if ($invoiceWithNumber)
                throw new BambooInvoiceException('il numero della fattura è già presente nel nostro sistema e non può essere duplicato');
        }
        $in = $inR->getEmptyEntity();
        $in->userId = $userId;
        if ($isShop) $in->shopRecipientId = $recipientOrEmitterId;
        else $in->userRceipientId = $recipientOrEmitterId;
        $in->number = $number;
        $in->date = $date->format('Y-m-d');
        $in->invoiceTypeId = $invoiceTypeId;
        $in->paydAmount = $paidAmount;
        $in->paymentExpectedDate = ($paymentExpectedDate) ? $paymentExpectedDate->format('Y-m-d') : null;
        $in->totalWithVat = $totalWithVat;
        $in->note = $note;
        $in->year = $year;
        $in->creationDate = $creationDate->format('Y-m-d');
        return $in->insert();
    }

    /**
     * @param CInvoiceSectional $inSec
     * @param string|null $year
     * @return mixed
     */
    private function newInvoiceNumberForSectional(CInvoiceSectional $inSec, string $year = null)
    {
        if (null === $year) $year = date_format(new \DateTime(), 'Y');

        $dba = \Monkey::app()->dbAdapter;
        return $dba->query(
            'SELECT (MAX(invoiceNumber) + 1) AS newInvoiceNumber FROM InvoiceNumber WHERE invoiceSectionalId = ? AND year = ?',
            [$inSec->id, $year]
        )->fetch()['newInvoiceNumber'];
    }

    /**
     * @param int $invoiceId
     * @param COrderLine|string $orderLine
     * @param string $description
     * @param float $price
     * @param bool $countainVat
     * @param int|null $vat
     */
    private function addOrderLineToInvoice(
        int $invoiceId,
        $orderLine,
        float $price,
        bool $countainVat,
        int $vat
    )
    {
        $olR = \Monkey::app()->repoFactory->create('OrderLine');
        $description = 'Ft. Fr.: ';
        if (is_string($orderLine)) {
            $orderLine = $olR->findOneByStringId($orderLine);
        }

        $description .= $olR->getOrderLineDescription($orderLine);

        $invoiceLineId = $this->addLineToInvoice($invoiceId, $description, $price, $countainVat, $vat);
        $ilhol = \Monkey::app()->repoFactory->create('InvoiceLineHasOrderLine')->getEmptyEntity();
        $ilhol->invoiceLineId = $invoiceLineId;
        $ilhol->invoiceLineInvoiceId = $invoiceId;
        $ilhol->orderLineId = $orderLine->id;
        $ilhol->orderLineOrderId = $orderLine->orderId;
        $ilhol->insert();

        return $invoiceLineId;
    }

    /**
     * @param int $invoiceId
     * @param string $description
     * @param float $price
     * @param bool $priceContainsVat
     * @param int $vat
     */
    private function addLineToInvoice(
        int $invoiceId,
        string $description,
        float $price,
        bool $priceContainsVat,
        int $vat
    )
    {
        $il = \Monkey::app()->repoFactory->create('InvoiceLine')->getEmptyEntity();
        $il->invoiceId = $invoiceId;
        $il->description = $description;
        $il->vat = $vat;
        if ($priceContainsVat) {
            $il->price = $price;
            $il->priceNoVat = SPriceToolbox::netPriceFromGross($price, $vat);
        } else {
            $il->price = SPriceToolbox::netPriceFromGross($price, $vat);
            $il->priceNoVat = $price;
        }
        $il->vat = $vat;
        return $il->insert();
    }

    /**
     * @param int $userId
     * @param int $shopId
     * @param \DateTime $emissionDate
     * @param \DateTime $paymentExpectedDate
     * @param $paidAmount
     * @param string $number
     * @param string $filePath
     * @param string|null $note
     * @throws BambooException
     * @throws BambooInvoiceException
     */
    public function storeFriendInvoiceWithFile(
        int $userId,
        int $shopId,
        \DateTime $emissionDate,
        $paymentExpectedDate = null,
        $paidAmount,
        string $number,
        array $orderLines,
        $file,
        string $note = null
    )
    {
        $invoiceType = \Monkey::app()->repoFactory->create('InvoiceType')->findOneBy(['code' => 'fr_invoice_orderlines_file']);
        $invoiceTypeId = $invoiceType->id;
        $dba = \Monkey::app()->dbAdapter;

        $olR = \Monkey::app()->repoFactory->create('OrderLine');
        $shpR = \Monkey::app()->repoFactory->create('Shop');
        $addressBook = $shpR->findOne([$shopId])->addressBook;
        $addressBookId = $shpR->findOne([$shopId])->addressBookId;

        try {
            $totalWithVat = 0;
            foreach($orderLines as $k => $v) {
                $orderLines[$k] = $olR->findOneByStringId($v);
                $totalWithVat+= $orderLines[$k]->friendRevenue;
            }
            $vat = $this->getInvoiceVat($invoiceType, $addressBook);
            $totalWithVat = SPriceToolbox::grossPriceFromNet($totalWithVat, $vat);

                $dba->beginTransaction();
            $insertedId = $this->createInvoice(
                $invoiceTypeId,
                $userId,
                1,
                $addressBookId,
                $emissionDate,
                $totalWithVat,
                $paidAmount,
                $paymentExpectedDate,
                $number,
                $note
            );
            foreach($orderLines as $v) {
                $this->addOrderLineToInvoice($insertedId, $v, (float)$v->friendRevenue, false, $vat);
            }

            if ($file) {
                $ib = \Monkey::app()->repoFactory->create('InvoiceBin')->getEmptyEntity();
                $ib->invoiceId = $insertedId;
                $ib->fileName = $file['name'];
                $ib->bin = file_get_contents($file['tmp_name']);
                $ib->insert();
            }
            $dba->commit();
        } catch (BambooInvoiceException $e) {
            $dba->rollBack();
            throw $e;
        } catch (BambooException $e) {
            $dba->rollBack();
            throw $e;
        }
    }

    /**
     * @param CInvoiceType $invoiceType
     * @param CAddressBook|null $addressBook
     * @return mixed
     */
    private function getInvoiceVat(CInvoiceType $invoiceType, CAddressBook $addressBook = null) {
        /** @var CInvoiceType $ */
        if ($invoiceType->isActive) return $addressBook->countryId->vat;
        else return $countryId = \Monkey::app()->repoFactory->create('Configuration')
            ->findOneBy(['name' => 'main vat'])->value;
    }

    /**
     * @param $invoiceId
     * @param null $date
     * @return bool
     */
    public function payFriendInvoice($invoice, $date = null) {
        if (!is_object($invoice)) $invoice = $this->findOne([$invoice]);
        $date = STimeToolbox::AngloFormattedDatetime($date);
        $invoice->paymentDate = $date;
        $invoice->paydAmount = $invoice->totalWithVat;
        $invoice->update();

        $invoiceLines = $invoice->invoiceLine;
        foreach($invoiceLines as $v) {
            /** @var COrderLine $ol */
            $ol = $v->orderLine->getFirst();
            $ol->orderLineFriendPaymentStatusId = 4;
            $ol->orderLineFriendPaymentDate = $date;
            $ol->update();
        }
        return true;
    }
}
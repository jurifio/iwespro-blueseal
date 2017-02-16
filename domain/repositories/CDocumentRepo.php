<?php
namespace bamboo\domain\repositories;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CInvoiceSectional;
use bamboo\domain\entities\CInvoiceType;
use bamboo\utils\price\SPriceToolbox;
use bamboo\domain\entities\COrderLine;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CDocumentRepo
 * @package bamboo\domain\repositories
 */
class CDocumentRepo extends ARepo
{
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
     *
     * @transaction
     */
    public function storeFriendInvoiceWithFile(
        int $userId,
        int $billingAddressBookId,
        \DateTime $emissionDate,
        $paymentExpectedDate = null,
        $paidAmount,
        string $number,
        array $orderLines,
        $file,
        $totalWithVat = null,
        string $note = null
    )
    {
        $invoiceType = \Monkey::app()->repoFactory->create('InvoiceType')->findOneBy(['code' => 'fr_invoice_orderlines_file']);
        $dba = \Monkey::app()->dbAdapter;
        try {
            $dba->beginTransaction();
            $insertedId =$this->storeFriendDocumentWithFile(
                $invoiceType,
                $userId,
                $billingAddressBookId,
                $emissionDate,
                $paymentExpectedDate,
                $paidAmount,
                $number,
                $orderLines,
                $file,
                $totalWithVat,
                $note
            );
            $dba->commit();
            return $insertedId;
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
     * @param int $userId
     * @param int $shopId
     * @param \DateTime $emissionDate
     * @param null $paymentExpectedDate
     * @param $paidAmount
     * @param $number
     * @param array $orderLines
     * @param null $totalWithVat
     * @param string|null $note
     * @return int|mixed
     * @throws \Exception
     */
    public function storeFriendDocumentBasic(
        CInvoiceType $invoiceType,
        int $userId,
        int $shopAddressBookId,
        \DateTime $emissionDate,
        $paymentExpectedDate = null,
        $paidAmount,
        $number,
        array $orderLines,
        $totalWithVat = null,
        string $note = null
    )
    {
        $invoiceTypeId = $invoiceType->id;
        $addressBook = \Monkey::app()->repoFactory->create('AddressBook')->findOne([$shopAddressBookId]);
        $vat = $this->getInvoiceVat($invoiceType, $addressBook);
        $orderLinesOC = new CObjectCollection();
        $olR = \Monkey::app()->repoFactory->create('OrderLine');
        foreach ($orderLines as $v) {
            $line = $olR->findOneByStringId($v);
            if (!$line) throw new BambooInvoiceException('la riga d\'ordine <strong>' . $v . '</strong> non è stata trovata.');
            $orderLinesOC->add($line);
        }
        unset($line);
        if (null == $totalWithVat) {
            $totalWithVat = $this->sumFriendRevenueFromOrders($orderLinesOC, $vat);
        } else {
            $totalWithVat = SPriceToolbox::EurToFloat($totalWithVat);
        }

        $vat = $this->getInvoiceVat($invoiceType, $addressBook);
        if (null === $totalWithVat) {
            $totalWithVat = 0;
            foreach ($orderLines as $v) {
                $totalWithVat += SPriceToolbox::grossPriceFromNet($v->friendRevenue, $vat);
            }
        } else {
            $totalWithVat = str_replace(',', '.', $totalWithVat);
            if (!is_numeric($totalWithVat)) throw new \Exception('Il totale della fattura fornito non è un numero valido. Controllare il campo');
        }

        $insertedId = $this->createInvoice(
            $invoiceTypeId,
            $userId,
            1,
            $shopAddressBookId,
            $emissionDate,
            $totalWithVat,
            $paidAmount,
            $paymentExpectedDate,
            $number,
            $note
        );
        foreach ($orderLinesOC as $v) {
            $this->addOrderLineToInvoice($insertedId, $v, (float)$v->friendRevenue, false, $vat);
        }
        return $insertedId;
    }

    /**
     * restituisce il valore dell'iva passandogli un indirizzo e il tipo fattura
     * @param CInvoiceType $invoiceType
     * @param CAddressBook|null $addressBook
     * @return mixed
     */
    public function getInvoiceVat(CInvoiceType $invoiceType, CAddressBook $addressBook = null)
    {
        /** @var CInvoiceType $ */
        if ($invoiceType->isActive) return $addressBook->country->vat;
        else return \Monkey::app()->repoFactory->create('Configuration')
            ->findOneBy(['name' => 'main vat'])->value;
    }

    /**
     * Calcola il totale delle friend revenue delle righe d'ordine
     * @param CObjectCollection $orderLines
     * @param $vat
     * @return mixed
     */
    public function sumFriendRevenueFromOrders(CObjectCollection $orderLines, $vat, $round = false)
    {
        $totalWithVat = 0;
        foreach ($orderLines as $v) {
            $totalWithVat += SPriceToolbox::roundVat($v->friendRevenue);
        }
        return SPriceToolbox::grossPriceFromNet($totalWithVat, $vat, $round);
    }

    /**
     * @param $invoiceTypeId
     * @param int $userId
     * @param bool $isShop
     * @param int $recipientOrEmitterId
     * @param \DateTime $date
     * @param float $totalWithVat
     * @param float $paidAmount
     * @param \DateTime|null $paymentExpectedDate
     * @param string|null $number
     * @param string|null $note
     * @param \DateTime|null $creationDate
     * @return int|mixed
     * @throws BambooInvoiceException
     */
    private function createInvoice(
        $invoiceTypeId,
        int $userId,
        bool $isShop,
        int $recipientOrEmitterId,
        \DateTime $date,
        float $totalWithVat,
        float $paidAmount = 0.0,
        \DateTime $paymentExpectedDate = null,
        string $number = null,
        string $note = null,
        \DateTime $creationDate = null
    )
    {
        $docR = \Monkey::app()->repoFactory->create('Document');

        //date control
        if (!$creationDate) $creationDate = new \DateTime();
        $diff = $creationDate->diff($date);
        if ($diff->days && !$diff->invert) throw new BambooInvoiceException('Non si possono emettere fatture con data di emissione successiva alla data odierna');
        if ($paymentExpectedDate) {
            $diff = $date->diff($paymentExpectedDate);
            if ($diff->days && $diff->invert) throw new BambooInvoiceException('La data di previsto pagamento non può essere precedente all\'emissione della fattura');
        }

        $year = $date->format('Y');

        //find sectional
        $fieldToSearchInvoice = (false == $isShop) ? 'userAddressRecipientId' : 'shopRecipientId';
        /** @var CInvoiceSectional $invoiceSectional */

        $invoiceWithNumber =
            $docR->findOneBy(['number' => $number, $fieldToSearchInvoice => $recipientOrEmitterId, 'year' => $year]);
        if ($invoiceWithNumber)
            throw new BambooInvoiceException('il numero della fattura è già presente nel nostro sistema e non può essere duplicato. id fattura: ' . $invoiceWithNumber->id);

        $in = $docR->getEmptyEntity();
        $in->userId = $userId;
        if ($isShop) $in->shopRecipientId = $recipientOrEmitterId;
        else $in->userRecipientId = $recipientOrEmitterId;
        $in->number = $number;
        $in->date = $date->format('Y-m-d');
        $in->invoiceTypeId = $invoiceTypeId;
        $in->paydAmount = $paidAmount;
        if ($paymentExpectedDate) {
            $ped = $paymentExpectedDate->format('Y-m-d');
        } else {
            $ped = $date->modify('+1 day')->format('Y-m-d');
        }
        $in->paymentExpectedDate = $ped;
        $in->totalWithVat = $totalWithVat;
        $in->note = ($note) ? $note : '';
        $in->year = $year;
        $in->creationDate = $creationDate->format('Y-m-d H:m:s');
        return $in->insert();
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
            $il->price = SPriceToolbox::roundVat($price);
            $il->priceNoVat = SPriceToolbox::netPriceFromGross($il->price, $vat, true);
        } else {
            $il->priceNoVat = SPriceToolbox::roundVat($price);
            $il->price = SPriceToolbox::grossPriceFromNet($il->priceNoVat, $vat, true);
        }
        $il->vat = $vat;
        return $il->insert();
    }

    /**
     * @param int $userId
     * @param int $shopId
     * @param \DateTime $emissionDate
     * @param null $paymentExpectedDate
     * @param $paidAmount
     * @param array $orderLines
     * @param string|null $note
     * @throws BambooException
     * @throws BambooInvoiceException
     *
     * @transaction
     */
    public function storeFriendInvoiceInternal(
        int $userId,
        int $shopId,
        \DateTime $emissionDate,
        $paymentExpectedDate = null,
        $paidAmount,
        array $orderLines,
        string $note = null
    )
    {
        $invoiceType = \Monkey::app()->repoFactory->create('InvoiceType')->findOneBy(['code' => 'fr_invoice_internal']);
        $dba = \Monkey::app()->dbAdapter;

        $shp = \Monkey::app()->repoFactory->create('Shop')->findOne([$shopId]);

        $newIn = $this->getNewNumber($shp, $invoiceType, $emissionDate->format('Y'));
        $completeNumber = $newIn->invoiceSectional->code . '/' . $newIn->invoiceNumber;
        try {
            $dba->beginTransaction();
            $this->storeFriendDocumentBasic(
                $invoiceType,
                $userId,
                $shopId,
                $emissionDate,
                $paymentExpectedDate,
                $paidAmount,
                $completeNumber,
                $orderLines,
                $totalWithVat = null,
                $note
            );
            $newIn->insert();
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
     * @param $entity
     * @param $invoiceType
     * @param $year
     * @return \bamboo\core\db\pandaorm\entities\AEntity
     * @throws BambooInvoiceException
     */
    public function getNewNumber($entity, $invoiceType, $year)
    {
        if ('Shop' === $entity->getEntityName()) $addressBook = $entity->billingAddressBook;
        elseif ('AddressBook' === $entity->getEntityNam()) $addressBook = $entity;
        if (!$addressBook)
            throw new BambooInvoiceException('Nel sistema non è presente un indirizzo di fatturazione associato a questo Friend');
        if (is_string($invoiceType)) $invoiceType = \Monkey::app()->repoFactory->create('invoiceType')->findOne([$invoiceType]);
        $is = \Monkey::app()->repoFactory->create('InvoiceSectional')->findOneBy(
            ['shopRecipientId' => $addressBook->id, 'invoiceTypeId' => $invoiceType->id]
        );
        if (!$is) throw new BambooInvoiceException('Non ho trovato nessun sezionale per questa fattura');
        $res = \Monkey::app()->dbAdapter->query('SELECT (max(invoiceNumber) + 1) AS `number` FROM `InvoiceNumber` AS `in` JOIN `InvoiceSectional` AS `is` ON `is`.id = `in`.invoiceSectionalId WHERE invoiceSectionalId = ? AND year = ?', [$is->id, $year])->fetch();
        $in = \Monkey::app()->repoFactory->create('InvoiceNumber')->getEmptyEntity();
        if (!$res['number']) {
            $in->invoiceNumber = 1;
        } else {
            $in->invoiceNumber = $res['number'];
        }
        $in->invoiceSectionalId = $is->id;
        return $in;
    }

    public function storeFriendTransportDocWithFile(
        int $userId,
        int $billingAddressBookId,
        \DateTime $emissionDate,
        $paymentExpectedDate = null,
        $paidAmount,
        string $number,
        array $orderLines,
        $file,
        $totalWithVat = null,
        string $note = null
    ){
        $dba = \Monkey::app()->dbAdapter;
        $invoiceType = \Monkey::app()->repoFactory->create('InvoiceType')->findOneBy(['code' => 'fr_trans_doc_w_file']);
        try {
            $insertedId =$this->storeFriendDocumentWithFile(
                $invoiceType,
                $userId,
                $billingAddressBookId,
                $emissionDate,
                $paymentExpectedDate,
                $paidAmount,
                $number,
                $orderLines,
                $file,
                $totalWithVat,
                $note
            );
            $dba->commit();
            return $insertedId;
        } catch (BambooInvoiceException $e) {
            $dba->rollBack();
            throw $e;
        } catch (BambooException $e) {
            $dba->rollBack();
            throw $e;
        }
    }

    public function storeFriendDocumentWithFile(
        CInvoiceType $invoiceType,
        int $userId,
        int $billingAddressBookId,
        \DateTime $emissionDate,
        $paymentExpectedDate = null,
        $paidAmount,
        string $number,
        array $orderLines,
            $file,
            $totalWithVat = null,
            string $note = null
    )
    {
            $insertedId = $this->storeFriendDocumentBasic(
                $invoiceType,
                $userId,
                $billingAddressBookId,
                $emissionDate,
                $paymentExpectedDate,
                $paidAmount,
                $number,
                $orderLines,
                $totalWithVat,
                $note
            );


            if ($file) {
                $ib = \Monkey::app()->repoFactory->create('InvoiceBin')->getEmptyEntity();
                $ib->invoiceId = $insertedId;
                $ib->fileName = $file['name'];
                $ib->bin = file_get_contents($file['tmp_name']);
                $ib->insert();
            }
            return $insertedId;
    }

    public function storeFriendCreditNoteOnReturn(
        int $userId,
        int $billingAddressBookId,
        \DateTime $emissionDate,
        $paymentExpectedDate = null,
        $paidAmount,
        array $orderLines,
        string $note = null
    )
    {
        $invoiceType = \Monkey::app()->repoFactory->create('InvoiceType')->findOneBy(['code' => 'fr_credit_note']);
        $dba = \Monkey::app()->dbAdapter;

        $shp = \Monkey::app()->repoFactory->create('Shop')->findOne([$shopId]);

        $newIn = $this->getNewNumber($shp, $invoiceType, $emissionDate->format('Y'));
        $completeNumber = $newIn->invoiceSectional->code . '/' . $newIn->invoiceNumber;
        try {
            $dba->beginTransaction();
            $this->storeFriendDocumentBasic(
                $invoiceType,
                $userId,
                $billingAddressBookId,
                $emissionDate,
                $paymentExpectedDate,
                $paidAmount,
                $completeNumber,
                $orderLines,
                $totalWithVat = null,
                $note
            );
            $newIn->insert();
            $dba->commit();
        } catch (BambooInvoiceException $e) {
            $dba->rollBack();
            throw $e;
        } catch (BambooException $e) {
            $dba->rollBack();
            throw $e;
        }
    }

    public function storeFriendCreditNoteWithFile(
        int $userId,
        int $billingAddressBookId,
        \DateTime $emissionDate,
        $paymentExpectedDate = null,
        $paidAmount,
        string $number,
        array $orderLines,
        $file,
        $totalWithVat = null,
        string $note = null
    )
    {
        $invoiceType = \Monkey::app()->repoFactory->create('InvoiceType')->findOneBy(['code' => 'fr_credit_note_w_file']);
        $dba = \Monkey::app()->dbAdapter;
        try {
            $dba->beginTransaction();
            $insertedId =$this->storeFriendDocumentWithFile(
                $invoiceType,
                $userId,
                $billingAddressBookId,
                $emissionDate,
                $paymentExpectedDate,
                $paidAmount,
                $number,
                $orderLines,
                $file,
                $totalWithVat,
                $note
            );
            $dba->commit();
            return $insertedId;
        } catch (BambooInvoiceException $e) {
            $dba->rollBack();
            throw $e;
        } catch (BambooException $e) {
            $dba->rollBack();
            throw $e;
        }
    }

    /**
     *
     * @param CObjectCollection $invoices
     * @param null $amount
     * @param null $date
     * @return bool
     * @throws BambooException
     * @throws BambooInvoiceException
     */
    public function insertPaymentBillAndPayInvoices(CObjectCollection $invoices, $amount = null, $date = null)
    {
        $due = $this->checkPaymentBillBeforeInsertAndReturnDue($invoices);
        if (1 < $invoices->count() && $due != $amount) {
            throw new BambooInvoiceException('La cifra dovuta e quella specificata per il pagamento devono coincidere');
        } elseif (1 == $invoices->count() && $due < $amount) {
            throw new BambooException('La cifra dovuta e quella specificata per il pagamento devono coincidere');
        }

        $pbill = \Monkey::app()->repoFactory->create('PaymentBill')->getEmptyEntity();
        $pbill->amount = $amount;
        $pbill->date = $date;
        $billId = $pbill->insert();

        $amount = (1 < $invoices->count()) ? null : $amount;

        $pbhinR = \Monkey::app()->repoFactory->create('PaymentBillHasInvoiceNew');
        foreach ($invoices as $v) {
            $this->payFriendInvoice($v, $amount, $date);
            $pbhin = $pbhinR->getEmptyEntity();
            $pbhin->paymentBillId = $billId;
            $pbhin->invoiceNewId = $v->id;
            $pbhin->insert();
        }
        return true;
    }

    /**
     * @param CObjectCollection $invoices
     * @return int
     * @throws BambooException
     * @throws BambooInvoiceException
     */
    public function checkPaymentBillBeforeInsertAndReturnDue(CObjectCollection $invoices)
    {
        $iCO = $invoices;
        if (!$iCO->count()) throw new BambooException('Nessuna fattura fornita');

        // different controls are made if is selected a single row or many
        $isSingle = (1 < $iCO->count()) ? false : true;
        //controllo che i dati fin'ora registrati siano corretti
        $due = 0;

        foreach ($iCO as $v) {
            $bill = $v->paymentBill;
            $amountBills = 0;
            foreach ($bill as $b) {
                $amountBills += $b->amount;
            }
            if ((float)$v->paydAmount != $amountBills) {
                throw new BambooInvoiceException('Nella fattura ' . $v->number . ' i dati dei pagamenti effettuati non corrispondono al totale registrato. Ricontrollarli prima di procedere a qualsiasi altra operazione');
            }

            $due += $v->totalWithVat - (float)$v->paydAmount;

            if ($isSingle) {
                if (0 >= $due) {
                    throw new BambooInvoiceException('La fattura selezionata risulta già pagata.');
                }
                break;
            }
            if (0 != (float)$v->paydAmount)
                throw new BambooInvoiceException('La fattura ' . $v->number . ', o è già stata saldata o, in caso di saldo parziale, va saldata singolarmente');
        }

        return $due;
    }

    /**
     * Paga la fattura di un Friend e tutte le righe d'ordine associate
     * @param $invoice
     * @param null $amount
     * @param null $date
     * @return bool
     * @throws BambooException
     * @throws BambooInvoiceException
     */
    public function payFriendInvoice($invoice, $amount = null, $date = null)
    {
        if (!is_object($invoice)) $invoice = $this->findOne([$invoice]);
        if (!$invoice) throw new BambooException('Fattura non trovata!');
        if (strtotime($date) < strtotime($invoice->date))
            throw new BambooInvoiceException('La data di pagamento non può essere più vecchia della data di emissione');
        $date = STimeToolbox::AngloFormattedDatetime($date);
        if ($amount) {
            if ($amount + $invoice->paydAmount > $amount)
                throw new BambooInvoiceException(
                    'Fattura id:' . $invoice->id . ' num.: ' . $invoice->number . 'L\'importo complessivamente versato, non può superare il totale della fattura'
                );
            $amount = $invoice->paydAmount + $amount;
        } else {
            $amount = $invoice->totalWithVat;
        }

        //if (null == $invoice->payment) $invoice->payment = 0;
        $invoice->paydAmount = $amount;
        if ($invoice->paydAmount == $invoice->totalWithVat) $invoice->paymentDate = $date;
        $invoice->paydAmount = $invoice->totalWithVat;
        $invoice->update();

        $invoiceLines = $invoice->invoiceLine;

        if ($invoice->paydAmount == $invoice->totalWithVat) {
            foreach ($invoiceLines as $v) {
                /** @var COrderLine $ol */
                $ol = $v->orderLine->getFirst();
                $ol->orderLineFriendPaymentStatusId = 4;
                $ol->orderLineFriendPaymentDate = $date;
                $ol->update();
            }
        }
        return true;
    }

    /**
     * Aggiunge una fattura ad una distinta
     * @param CObjectCollection $invoices
     * @param int $idBill
     * @return bool
     * @throws BambooInvoiceException
     */
    public function addInvoicesToPaymentBill(CObjectCollection $invoices, int $idBill)
    {
        $pbhR = \Monkey::app()->repoFactory->create('PaymentBillHasInvoiceNew');

        $pb = \Monkey::app()->repoFactory->create('PaymentBill')->findOne([$idBill]);
        if (!$pb) throw new BambooInvoiceException('L\'id fornito non è associato a nessuna distinta di pagamento');
        if ($pb->isSubmitted()) {
            throw new BambooInvoiceException('Non puoi togliere una fattura da una distinta già sottomessa');
        }
        $newAmount = 0;

        foreach ($invoices as $v) {
            if (0 < $v->paydAmount || $pb->isSubmitted()) throw new BambooInvoiceException(
                'La fattura con id: <strong>' . $v->id . '</strong> e numero: <strong>' . $v->number . '</strong>' .
                'ha già una distinta associata. L\'operazione è annullata'
            );
        }
        foreach ($invoices as $v) {
            $newAmount += $v->totalWithVat;
            $pbh = $pbhR->getEmptyEntity();
            $pbh->paymentBillId = $idBill;
            $pbh->invoiceNewId = $v->id;
            $pbh->insert();
        }

        $pb->amount += $newAmount;
        $pb->update();
        return true;
    }

    /**
     * Rimuove una fattura dalla distinta a cui è associata
     * @param $invoice
     * @return bool
     * @throws BambooInvoiceException
     */
    public function removeInvoiceFromPaymentBill(CDocument $invoice)
    {
        /** @var CObjectCollection $bills */
        $bills = $invoice->paymentBill;
        $invoicetotal = $invoice->getSignedValueWithVat();
        if (0 == $bills->count()) throw new BambooInvoiceException('Non è associata nessuna distinta a questa fattura');
        foreach ($invoice->paymentBillHasInvoiceNew as $v) {
            if ($v->paymentBill->isSubmitted()) {
                throw new BambooInvoiceException('Non puoi togliere una fattura da una distinta già sottomessa');
            } else {
                $v->delete();
            }
        }

        if (1 < $bills->count()) {
            foreach ($bills as $v) {
                $v->delete();
            }
        } else {
            $bill = $bills->getFirst();
            if (0 === $bill->paymentBillHasInvoiceNew->count()) {
                $bill->delete();
            } else {
                $bill->amount -= $invoicetotal;
                $bill->update();
            }
        }
        return true;
    }

    /**
     * @param null $dueDate
     * @return CObjectCollection
     */
    public function fetchUnboundedExpiringInvoices($dueDate = null)
    {
        $sql = "SELECT * 
                FROM Document d 
                  LEFT JOIN PaymentBillHasInvoiceNew pbhin 
                    ON d.id = pbhin.invoiceNewId 
                WHERE pbhin.paymentBillId IS NULL 
                    AND date(d.paymentExpectedDate) <= date(ifnull(?,current_date)) 
                    ORDER BY d.paymentExpectedDate ASC ";

        return $this->app->repoFactory->create('Document')->findBySql($sql, [STimeToolbox::DbFormattedDate($dueDate)]);
    }
}
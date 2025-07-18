<?php
namespace bamboo\domain\repositories;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CInvoiceBin;
use bamboo\domain\entities\CInvoiceLine;
use bamboo\domain\entities\CInvoiceNumber;
use bamboo\domain\entities\CInvoiceSectional;
use bamboo\domain\entities\CInvoiceType;
use bamboo\domain\entities\CShooting;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CProductBatch;
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
     * @param int $billingAddressBookId
     * @param \DateTime $emissionDate
     * @param null $paymentExpectedDate
     * @param $paidAmount
     * @param string $number
     * @param array $orderLines
     * @param $file
     * @param null $totalWithVat
     * @param string|null $note
     * @return int|mixed
     * @throws BambooException
     * @throws BambooInvoiceException
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
            \Monkey::app()->repoFactory->beginTransaction();
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
            \Monkey::app()->repoFactory->commit();
            return $insertedId;
        } catch (BambooInvoiceException $e) {
            \Monkey::app()->repoFactory->rollback();
            throw $e;
        } catch (BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
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
     * insert a new custom document with file and rows
     *
     * @param int $invoiceTypeId
     * @param int $userId
     * @param int $recipientOrEmitterId
     * @param \DateTime $date
     * @param float $totalWithVat
     * @param \DateTime $paymentExpectedDate
     * @param string $number
     * @param string $note
     * @param array $rows
     * @param $rowsContainVat
     * @return \bamboo\core\db\pandaorm\entities\AEntity|null
     */
    public function storeNewCustomInvoice(
        int $invoiceTypeId,
        int $userId,
        int $recipientOrEmitterId,
        \DateTime $date,
        float $totalWithVat,
        \DateTime $paymentExpectedDate,
        string $number,
        string $note,
        array $rows,
        $rowsContainVat,
        $filename,
        $fileUrl
    ) {

        $invoiceId = $this->createInvoice($invoiceTypeId,$userId,true,$recipientOrEmitterId,$date,$totalWithVat,0,$paymentExpectedDate,
            $number,
            $note);

        foreach ($rows as $row) {
            $invoiceLine = \Monkey::app()->repoFactory->create('InvoiceLine')->getEmptyEntity();
            $invoiceLine->invoiceId = $invoiceId;
            $invoiceLine->priceNoVat = $row['priceNoVat'];
            $invoiceLine->vat = $row['vat'];
            $invoiceLine->description = $row['description'];
            $invoiceLine->insert();
        }

        $this->insertInvoiceBin($invoiceId, $fileUrl,$filename);

        return $this->findOne([$invoiceId]);
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
     * @param bool $isEmptyDocument
     * @return mixed
     * @throws BambooException
     * @throws BambooInvoiceException
     */
    private function createInvoice(
        $invoiceTypeId,
        int $userId,
        bool $isShop,
        $recipientOrEmitterId,
        \DateTime $date,
        float $totalWithVat,
        float $paidAmount = 0.0,
        \DateTime $paymentExpectedDate = null,
        string $number = null,
        string $note = null,
        \DateTime $creationDate = null,
        bool $isEmptyDocument = false
    )
    {
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
            $this->findOneBy(['number' => $number, $fieldToSearchInvoice => $recipientOrEmitterId, 'year' => $year]);
        if ($invoiceWithNumber){
            if(!$isEmptyDocument){
                throw new BambooInvoiceException('il numero della fattura è già presente nel nostro sistema e non può essere duplicato. id fattura: ' . $invoiceWithNumber->id);
            } else {
                return $invoiceWithNumber;
            }

        }


        $in = $this->getEmptyEntity();
        $in->userId = $userId;
        if ($isShop) $in->shopRecipientId = $recipientOrEmitterId;
        else $in->userAddressRecipientId = $recipientOrEmitterId;
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
     * @param $orderLine
     * @param float $price
     * @param bool $countainVat
     * @param int $vat
     * @return int|mixed
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
     * @return int|mixed
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
            \Monkey::app()->repoFactory->beginTransaction();
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
            \Monkey::app()->repoFactory->commit();
        } catch (BambooInvoiceException $e) {
            \Monkey::app()->repoFactory->rollback();
            throw $e;
        } catch (BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
            throw $e;
        }
    }

    /**
     * @param $entity
     * @param $invoiceType
     * @param $year
     * @return CInvoiceNumber
     * @throws BambooInvoiceException
     */
    public function getNewNumber($entity, $invoiceType, $year)
    {
        if ('Shop' === $entity->getEntityName()) $addressBook = $entity->billingAddressBook;
        elseif ('AddressBook' === $entity->getEntityName()) $addressBook = $entity;
        if (!$addressBook)
            throw new BambooInvoiceException('Nel sistema non è presente un indirizzo di fatturazione associato a questo Friend');
        if (is_string($invoiceType)) $invoiceType = \Monkey::app()->repoFactory->create('invoiceType')->findOne([$invoiceType]);
        $is = \Monkey::app()->repoFactory->create('InvoiceSectional')->findOneBy(
            ['shopRecipientId' => $addressBook->id, 'invoiceTypeId' => $invoiceType->id]
        );
        if (!$is) throw new BambooInvoiceException('Non ho trovato nessun sezionale per questa fattura');
        $res = \Monkey::app()->dbAdapter->query('SELECT (max(invoiceNumber) + 1) AS `number` 
                                                        FROM `InvoiceNumber` AS `in` 
                                                          JOIN `InvoiceSectional` AS `is` ON `is`.id = `in`.invoiceSectionalId 
                                                        WHERE invoiceSectionalId = ? AND 
                                                              year = ?', [$is->id, $year])->fetch();
        /** @var CInvoiceNumber $in */
        $in = \Monkey::app()->repoFactory->create('InvoiceNumber')->getEmptyEntity();
        if (!$res['number']) {
            $in->invoiceNumber = 1;
        } else {
            $in->invoiceNumber = $res['number'];
        }
        $in->invoiceSectionalId = $is->id;
        return $in;
    }

    /**
     * @param int $userId
     * @param int $billingAddressBookId
     * @param \DateTime $emissionDate
     * @param null $paymentExpectedDate
     * @param $paidAmount
     * @param string $number
     * @param array $orderLines
     * @param $file
     * @param null $totalWithVat
     * @param string|null $note
     * @return int|mixed
     * @throws BambooException
     * @throws BambooInvoiceException
     */
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
            \Monkey::app()->repoFactory->commit();
            return $insertedId;
        } catch (BambooInvoiceException $e) {
            \Monkey::app()->repoFactory->rollback();
            throw $e;
        } catch (BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
            throw $e;
        }
    }

    /**
     * @param CInvoiceType $invoiceType
     * @param int $userId
     * @param int $billingAddressBookId
     * @param \DateTime $emissionDate
     * @param null $paymentExpectedDate
     * @param $paidAmount
     * @param string $number
     * @param array $orderLines
     * @param $file
     * @param null $totalWithVat
     * @param string|null $note
     * @return int|mixed
     */
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
                $this->insertInvoiceBin($insertedId,$file['tmp_name'],$file['name']);
            }
            return $insertedId;
    }

    /**
     * @param $invoiceId
     * @param $fineUrl
     * @param $fileName
     * @return int
     */
    private function insertInvoiceBin($invoiceId, $fineUrl,$fileName) {
        $invoiceBin = \Monkey::app()->repoFactory->create('InvoiceBin')->getEmptyEntity();
        $invoiceBin->invoiceId = $invoiceId;
        $invoiceBin->fileName = $fileName;
        $invoiceBin->bin = file_get_contents($fineUrl);
        return $invoiceBin->insert();
    }

    /**
     * @param int $userId
     * @param int $billingAddressBookId
     * @param \DateTime $emissionDate
     * @param null $paymentExpectedDate
     * @param $paidAmount
     * @param array $orderLines
     * @param string|null $note
     * @throws BambooException
     * @throws BambooInvoiceException
     * @throws \Exception
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
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
            \Monkey::app()->repoFactory->beginTransaction();
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
            \Monkey::app()->repoFactory->commit();
        } catch (BambooInvoiceException $e) {
            \Monkey::app()->repoFactory->rollback();
            throw $e;
        } catch (BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
            throw $e;
        }
    }

    /**
     * @param int $userId
     * @param int $billingAddressBookId
     * @param \DateTime $emissionDate
     * @param null $paymentExpectedDate
     * @param $paidAmount
     * @param string $number
     * @param array $orderLines
     * @param $file
     * @param null $totalWithVat
     * @param string|null $note
     * @return int|mixed
     * @throws BambooException
     * @throws BambooInvoiceException
     */
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
            \Monkey::app()->repoFactory->beginTransaction();
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
            \Monkey::app()->repoFactory->commit();
            return $insertedId;
        } catch (BambooInvoiceException $e) {
            \Monkey::app()->repoFactory->rollback();
            throw $e;
        } catch (BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
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
        /** @var CDocument $v */
        foreach ($iCO as $v) {
            $bill = $v->paymentBill;
            $amountBills = 0;
            foreach ($bill as $b) {
                $amountBills += $b->amount;
            }
            if ((float)$v->paydAmount != $amountBills) {
                throw new BambooInvoiceException('Nella fattura ' . $v->number . ' i dati dei pagamenti effettuati non corrispondono al totale registrato. Ricontrollarli prima di procedere a qualsiasi altra operazione');
            }

            $due += $v->getSignedValueWithVat() - (float)$v->paydAmount;

            if ($isSingle) {
                if (0 >= $due) {
                    throw new BambooInvoiceException('La fattura selezionata risulta già pagata.');
                }
                break;
            }
            if (0 != (float)$v->paydAmount)
                throw new BambooInvoiceException('La fattura ' . $v->number . ', o è già stata saldata o, in caso di saldo parziale, va saldata singolarmente');
        }

        return round($due,2);
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
                if($ol == null) continue;
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

        foreach ($invoices as $v) {
            if (0 < $v->paydAmount || $pb->isSubmitted()) throw new BambooInvoiceException(
                'La fattura con id: <strong>' . $v->id . '</strong> e numero: <strong>' . $v->number . '</strong>' .
                'ha già una distinta associata. L\'operazione è annullata'
            );
        }
        foreach ($invoices as $v) {
            $pb->amount += $v->getSignedValueWithVat();
            $pbh = $pbhR->getEmptyEntity();
            $pbh->paymentBillId = $idBill;
            $pbh->invoiceNewId = $v->id;
            $pbh->insert();
        }

        $pb->amount = round($pb->amount,2);
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
        $sql = "SELECT d.id 
                FROM Document d 
                  JOIN InvoiceType it on d.invoiceTypeId = it.id
                  LEFT JOIN PaymentBillHasInvoiceNew pbhin 
                    ON d.id = pbhin.invoiceNewId 
                WHERE
                    it.isPayable = 1
                    AND pbhin.paymentBillId IS NULL 
                    AND date(d.paymentExpectedDate) <= date(ifnull(?,current_date)) 
                    ORDER BY d.paymentExpectedDate ASC ";

        return $this->findBySql($sql, [STimeToolbox::DbFormattedDate($dueDate)]);
    }


    public function checkIfExistOneDDTDocument(array $orderLineCollection){
        $check = 0;
        /** @var COrderLine $singleOrderLine */
        foreach ($orderLineCollection as $singleOrderLine){
            $allInvoice = $singleOrderLine->invoiceLine;
            if($allInvoice->count() == '1'){

                //Gira sempre e solo una volta
                /** @var CInvoiceLine $singleInvoice */
                foreach ($allInvoice as $singleInvoice){
                    if ($singleInvoice->document->invoiceTypeId == 6){
                        $check = 1;
                    } else {
                        $check = 0;
                    }
                }

                // Se Esiste una fattura (non ddt) -> errore
                if (!$check) return $check;

            } else if ($allInvoice->count() == '0'){
                $check = 1;
            } else {
                //Se esistono più fatture -> errore
                $check = 0;
                return $check;
            }
        }

        return $check;
    }

    public function findShootingFriendDdt(CShooting $shooting) : string {

        $friendDdt = $shooting->friendDdt;

        /** @var CDocument $doc */
        $doc = $this->findOneBy(['id'=>$friendDdt]);
        if($doc!=null) {

            $docNumber = $doc->number;
        }else{
            $docNumber='';
        }

        return $docNumber;
    }


    /**
     * @param CShooting $shooting
     * @return string
     */
    public function findShootingPickyDdt(CShooting $shooting) {

        $pickyDdt = $shooting->pickyDdt;

        if(!is_null($pickyDdt)){
            /** @var CDocument $doc */
            $doc = $this->findOneBy(['id'=>$pickyDdt]);

            $res = $doc->number;
        } else {
            $res = false;
        }


        return $res;
    }


    /**
     * @param $invoiceTypeId
     * @param $userId
     * @param $date
     * @param $total
     * @param $number
     * @param $file
     * @param $productBatchIds
     * @throws BambooException
     * @throws BambooInvoiceException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function insertInvoiceFromFoison($invoiceTypeId, $userId, $date, $total, $number, $file, $productBatchIds){

        /** @var CUser $user */
        $user = \Monkey::app()->repoFactory->create('User')->findOneBy(['id'=>$userId]);
        $billingAddressId = $user->foison->addressBook->id;
        $date = new \DateTime($date);

        $invoiceId = $this->createInvoice($invoiceTypeId, $userId, false, $billingAddressId, $date, $total, 0, null, $number);

        foreach ($productBatchIds as $pbId){

            /** @var CProductBatch $productBatch */
            $productBatch = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(['id'=>$pbId]);

            $productBatch->documentId = $invoiceId;
            $productBatch->update();
        }

        $this->insertInvoiceBin($invoiceId,$file['tmp_name'],$file['name']);
    }

    /**
     * @param int $shopId
     * @param string $friendDdtNumber
     * @param $sb
     * @return mixed
     * @throws BambooException
     * @throws BambooInvoiceException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function createEmptyDdtDocument(int $shopId, string $friendDdtNumber, $sb){

        /** @var CUser $user */
        $user = \Monkey::app()->getUser();
        $date = date("Y-m-d");
        $dateTime = new \DateTime($date);
        $invoiceId = $this->createInvoice(CInvoiceType::DDT_SHOOTING, $user->id, true, $shopId, $dateTime, 0, 0, null, $friendDdtNumber, null, null, true);


        /** @var CSectionalRepo $sectionalR */
        $sectionalR = \Monkey::app()->repoFactory->create('Sectional');

        $nextCode = $sectionalR->calculateNewSectionalCodeFromShop($sb->shopId, CInvoiceType::DDT_SHOOTING);
        if($friendDdtNumber == $nextCode){
            $sectionalR->createNewSectionalCodeFromShop($sb->shopId, CInvoiceType::DDT_SHOOTING);
        }

        return $invoiceId;
    }


    /**
     * @param $invoiceId
     * @param $fileName
     * @param $bin
     * @return bool
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function insertInvoiceBinWithRowFile($invoiceId, $fileName, $bin){
        /** @var ARepo $invoiceBin */
        $invoiceBin = \Monkey::app()->repoFactory->create('InvoiceBin');

        /** @var CInvoiceBin $ib */
        $iB = $invoiceBin->findOneBy(['invoiceId' =>$invoiceId]);

        if(is_null($iB)){
            $iB = $invoiceBin->getEmptyEntity();
            $iB->invoiceId = $invoiceId;
            $iB->fileName = $fileName;
            $iB->bin = $bin;
            $iB->smartInsert();
        } else {
            $iB->bin = $bin;
            $iB->update();
        }

        return true;

    }


    /**
     * @param string $pickyDdtNumber
     * @param $sb
     * @return mixed
     * @throws BambooException
     * @throws BambooInvoiceException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function createPickyDDTDocument(string $pickyDdtNumber){

        /** @var CUser $user */
        $user = \Monkey::app()->getUser();
        $date = date("Y-m-d");
        $dateTime = new \DateTime($date);
        $invoiceId = $this->createInvoice(CInvoiceType::DDT_SHOOTING, $user->id, true, null, $dateTime, 0, 0, null, $pickyDdtNumber, null, null, true);


        /** @var CSectionalRepo $sectionalR */
        $sectionalR = \Monkey::app()->repoFactory->create('Sectional');

        $nextCode = $sectionalR->calculateNewSectionalCodeFromShop(null, CInvoiceType::DDT_RETURN_SHOOTING);
        if($pickyDdtNumber == $nextCode){
            $sectionalR->createNewSectionalCodeFromShop(null, CInvoiceType::DDT_RETURN_SHOOTING);
        }

        return $invoiceId;
    }
}
<?php
namespace bamboo\domain\repositories;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CInvoiceLine;
use bamboo\domain\entities\CInvoiceNumber;
use bamboo\domain\entities\CInvoiceSectional;
use bamboo\domain\entities\CInvoiceType;
use bamboo\utils\price\SPriceToolbox;
use bamboo\domain\entities\COrderLine;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CContractDetailsRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/03/2018
 * @since 1.0
 */
class CContractDetailsRepo extends ARepo
{
    /**
     * @param $contractId
     * @param $workCategoryId
     * @param $workListPriceId
     * @param $contractDetailName
     * @param $qty
     * @param $note
     * @return bool
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function createNewContractDetail($workPriceListType, $contractId, $workCategoryId, $workListPriceId, $contractDetailName, $qty, $note){

        /** @var CContractDetails $cD */
        $cD = $this->getEmptyEntity();
        $cD->workCategoryId = $workCategoryId;

        if($workPriceListType === 'v') $cD->isVariable = 1;

        if($workPriceListType === 'f') $cD->workPriceListId = $workListPriceId;

        $cD->contractId = $contractId;
        $cD->contractDetailName = $contractDetailName;
        $cD->dailyQty = $qty;
        $cD->note = $note;
        $cD->smartInsert();

            //info x email
        $nameFoison = $cD->contracts->foison->user->getFullName();
        $category = $cD->workCategory->name;
        $contractCod = $cD->id;
        $nameContract = $cD->contractDetailName;
        $workPriceList = $workPriceListType === 'f' ? $cD->workPriceList->name : 'Non definito';
        $workPriceListUnityPrice = $workPriceListType === 'f' ? $cD->workPriceList->price : 'Non definito';
        $dailyQty = $cD->dailyQty;
        $note = $cD->note;
        $url = \Monkey::app()->baseUrl(false) . "/blueseal/work/contratti/".$cD->contractId;
            //----

        /** @var CEmailRepo $mRepo */
        $mRepo = \Monkey::app()->repoFactory->create('Email');

        $from = "gianluca@iwes.it";
        $to = $cD->contracts->foison->email;
        $subject = "Proposta di conto lavoro";
        $body = "
        Preg.mo sig. $nameFoison,<br /><br />
        Elenchiamo di seguito gli accordi presi in relazione alla nostra proposta di c/lavoro:<br />
        Codice contratto: $contractCod<br />
        Nome contratto: $nameContract<br />
        Tipologia contratto: $category<br />
        Listino/prezzo unitario: $workPriceList/$workPriceListUnityPrice<br />
        Quantità giornaliera: $dailyQty
        Note: $note<br /><br />
        La invitiamo a procedere con la conferma selezionando la riga del contratto e confermando attravero
        l'apposito pulsante che trova in alto a sinistra.<br />
        Link di accettazione: $url<br /><br />
        In attesa di un Suo gentile riscontro<br /><br />
        Cordiali saluti,<br /><br />
        Gianluca Cartechini<br />
        Iwes
        ";

        if(ENV == 'prod') $mRepo->newMail($from, [$to], [], [], $subject, $body);

        return true;
    }

    /**
     * @param $contractDetailId
     * @return bool
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function userAcceptDetailContract($contractDetailId){

        /** @var CContractDetails $contractDetail */
        $contractDetail = $this->findOneBy(['id'=>$contractDetailId]);

        if($contractDetail->accepted == 0){
            $contractDetail->accepted = 1;
            $contractDetail->update();

            /** @var CEmailRepo $mailRepo */
            $mailRepo = \Monkey::app()->repoFactory->create('Email');
            $name = $contractDetail->contracts->foison->user->getFullName();

            $to = $contractDetail->contracts->foison->email;
            $body = "Gentilissimo sig. $name,<br />
                Le confermiamo l'avvenuta accettazione del contratto.<br /><br />
                Cordiali saluti;<br /><br />
                Gianluca Cartechini<br />
                Iwes";

            if(ENV == 'prod') $mailRepo->newMail('gianluca@iwes.it', [$to], [], ['gianluca@iwes.it'], 'Conferma di accettazione contratto', $body);

            return true;
        } else {
            return false;
        }

    }

    public function contractDetailIsAccepted($contractDetail){

        if (is_numeric($contractDetail)){
            $cD = $this->findOneBy(['id'=>$contractDetail]);
        }

        if($cD->accepted == 0){
            return false;
        } else {
            return true;
        }
    }
}
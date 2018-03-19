<?php
namespace bamboo\domain\repositories;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\domain\entities\CAddressBook;
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
     * @return bool
     */
    public function createNewContractDetail($contractId, $workCategoryId, $workListPriceId, $contractDetailName){
        $cD = $this->getEmptyEntity();
        $cD->workCategoryId = $workCategoryId;
        $cD->workPriceListId = $workListPriceId;
        $cD->contractId = $contractId;
        $cD->contractDetailName = $contractDetailName;
        $cD->smartInsert();

        return true;
    }
}
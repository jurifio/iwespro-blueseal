<?php
namespace bamboo\domain\repositories;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CContracts;
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
 * Class CContractsRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/03/2018
 * @since 1.0
 */
class CContractsRepo extends ARepo
{
    /**
     * @param CFoison $foison
     * @param $name
     * @param $description
     * @return \bamboo\core\db\pandaorm\entities\AEntity|CContracts|bool
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function createNewContract(CFoison $foison, $name, $description) {

        try{
            \Monkey::app()->repoFactory->beginTransaction();
            /** @var CContracts $contract */
            $contract = $this->getEmptyEntity();
            $contract->foisonId = $foison->id;
            $contract->name = $name;
            $contract->description = $description;
            $contract->smartInsert();
            \Monkey::app()->repoFactory->commit();
        } catch (\Throwable $e){
            \Monkey::app()->repoFactory->rollback();
            return false;
        }

        return $contract;
    }
}
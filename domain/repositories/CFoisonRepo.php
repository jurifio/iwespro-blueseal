<?php
namespace bamboo\domain\repositories;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CInvoiceLine;
use bamboo\domain\entities\CInvoiceNumber;
use bamboo\domain\entities\CInvoiceSectional;
use bamboo\domain\entities\CInvoiceType;
use bamboo\utils\price\SPriceToolbox;
use bamboo\domain\entities\COrderLine;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CFoisonRepo
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
class CFoisonRepo extends ARepo
{
    /**
     * @param $name
     * @param $surname
     * @param $email
     * @param $iban
     * @param $userId
     * @return bool
     */
    public function assignUser($name, $surname, $email, $iban, $userId){
        $faison = $this->getEmptyEntity();
        $faison->name = $name;
        $faison->surname = $surname;
        $faison->email = $email;
        $faison->iban = $iban;
        $faison->userId = $userId;
        $faison->smartInsert();

        return true;

    }
}
<?php
namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;

/**
 * Class CProductRepo
 * @package bamboo\domain\repositories
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/11/2014
 * @since 1.0
 */
class CInvoiceNewRepo extends ARepo {

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

    public function recordFriendInvoice($number, $date, $paymentDate, $shopId, array $orderLine) {

    }
}
<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\RedPandaOrderLogicException;
use bamboo\domain\entities\CAddressBook;
use bamboo\controllers\api\Helper\DateTime;


/**
 * Class CBillRegistryInvoiceRowManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/04/2020
 * @since 1.0
 */
class CBillRegistryInvoiceRowManageAjaxController extends AAjaxController
{

    public function post()
    {

    }

    public function put()
    {

    }

    public function delete()
    {
        $invoiceRow = [];
        $data = $this->app->router->request()->getRequestData();
        $rowInvoiceId = $data['id'];
        $brir = \Monkey::app()->repoFactory->create('BillRegistryInvoiceRow')->findOneBy(['id' => $rowInvoiceId]);
        $invoiceRow[] = ['netTotalRow' => $brir->netPriceRow,'vatRow' => $brir->vatRow,'discountRowAmount' => $brir->discountRow,'grossTotalRow' => $brir->grossTotalRow];
        $brir->delete();
        return json_encode($invoiceRow);

    }

}
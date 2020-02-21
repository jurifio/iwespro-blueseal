<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\RedPandaOrderLogicException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CBillRegistryContract;
use bamboo\domain\entities\CBillRegistryContractRow;

/**
 * Class CSelectBillRegistryTypeTaxesToRowInvoiceAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 21/02/2020
 * @since 1.0
 */



class CSelectBillRegistryTypeTaxesToRowInvoiceAjaxController extends AAjaxController
{

    public function post()
    {




    }

    public function get()
    {
        $data = $this->app->router->request()->getRequestData();
        $id = $data['id'];

        $brt =Monkey::app()->repoFactory->create('BillRegistryTypeTaxes')->findOneBy(['id' => $detailRow->billRegistryTypeTaxesId]);
        $perc=$brt->perc;




return $perc;

}

public
function put()
{


}

public
function delete()
{

}
}

<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\RedPandaOrderLogicException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CBillRegistryContract;
use bamboo\domain\entities\CBillRegistryContractRow;


/**
 * Class CBillRegistryContractRowDetailManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/02/2020
 * @since 1.0
 */
class CBillRegistryContractRowDetailManageAjaxController extends AAjaxController
{

    public function post()
    {



    }

    public function get()
    {
        $data = $this->app->router->request()->getRequestData();
        $id = $data['id'];
        $billRegistryClientId=$data['billRegistryClientId'];

        $contractRowDetail = [];
        /* @var CBillRegistryContractRowDetail $brc */
        $brcrd = \Monkey::app()->repoFactory->create('BillRegistryContractRowDetail')->findOneBy(['billRegistryContractRowId' => $id]);
        foreach ($brcrd as $detailRow) {
            $brp = \Monkey::app()->repoFactory->create('BillRegistryProduct')->findOneBy(['id' => $detailRow->billRegistryProductId]);
            $bpl= \Monkey::app()->repoFactory->create('BillRegistryPriceList')->findOneBy(['billRegistryProductId'=>$detailRow->billRegistryProductId,'billRegistryClientId'=>$billRegistryClientId,'isActive'=>1]);
            $brt= \Monkey::app()->repoFactory->create('BillRegistryTaxes')->findOneBy(['id' => $detailRow->billRegistryTypeTaxeId]);
            $contractRowDetail[]=['billRegistryProductId'=>$detailRow->id,'codeProduct'=>$brp->codeProduct,'nameProduct'=>$brp->nameProduct,'um'=>$detailRow->um,'price'=>$bpl->price,'qty'=>$detailRow->qty,'taxes'=>$brt->description];
            }


return json_encode($contractRowDetail);

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
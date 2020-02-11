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
        $data = $this->app->router->request()->getRequestData();
        $billRegistryProductId = $data['productBillRegistryProductId'];
        $um=$data['um'];
        $qty=$data['qty'];
        $price=$data['price'];
        $billRegistryContractRowId=$data['billRegistryContractRowId'];
        $billRegistryTypeTaxesId=$data['productBillRegistryTypeTaxesId'];
        $billRegistryClientId=$data['billRegistryClientId'];
        $billRegistryProductContractRowDetailRepo=\Monkey::app()->repoFactory->create('BillRegistryProductContractRowDetail');
        $billRegistryPriceListRepo=\Monkey::app()->repoFactory->create('BillRegistryPriceList');
        try {
            $brplFind = $billRegistryPriceListRepo->findOneBy(['billRegistryProductId' => $billRegistryProductId,'billRegistryClientId' => $billRegistryClientId,'isActive' => 1]);
            if ($brlFind != null) {
                $brplFind->isActive = 0;
                $brplFind->update();
            }
            $brpl = $billRegistryPriceListRepo->getEmptyEntity();
            $brpl->billRegistryProductId = $billRegistryProductId;
            $brpl->billRegistryClientId = $billRegistryClientId;
            $brpl->price = $price;
            $brpl->isActive = 1;
            $brpl->insert();
            $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryPriceList ',[])->fetchAll();
            foreach ($res as $result) {
                $lastId = $result['id'];
            }
            $brcrd = $billRegistryProductContractRowDetailRepo->getEmptyEntity();
            $brcrd->billRegistryContractRowId = $billRegistryContractRowId;
            $brcrd->billRegistryProductId = $billRegistryProductId;
            $brcrd->um = $um;
            $brcrd->billRegistryPriceListId = $lastId;
            $brcrd->qty = $qty;
            $brcrd->billRegistryTypeTaxesId = $billRegistryTypeTaxesId;
            $brcrd->insert();
            return '1';
        }catch (\Throwable $e){
            \Monkey::app()->applicationLog('CBillRegistryContractRowDetailManageAjaxController','Error','Error insert product in detail Row',$e,'');
            return '0';
        }



    }

    public function get()
    {
        $data = $this->app->router->request()->getRequestData();
        $id = $data['id'];
        $billRegistryClientId=$data['billRegistryClientId'];

        $contractRowDetail = [];
        $brcrd = \Monkey::app()->repoFactory->create('BillRegistryContractRowDetail')->findBy(['billRegistryContractRowId' => $id]);
        foreach ($brcrd as $detailRow) {
            $brp = \Monkey::app()->repoFactory->create('BillRegistryProduct')->findOneBy(['id' => $detailRow->billRegistryProductId]);
            $bpl= \Monkey::app()->repoFactory->create('BillRegistryPriceList')->findOneBy(['billRegistryProductId'=>$detailRow->billRegistryProductId,'billRegistryClientId'=>$billRegistryClientId,'isActive'=>1]);
            $brt= \Monkey::app()->repoFactory->create('BillRegistryTypeTaxes')->findOneBy(['id' => $detailRow->billRegistryTypeTaxesId]);
            $contractRowDetail[]=['billRegistryContractRowDetailId'=>$detailRow->id,'codeProduct'=>$brp->codeProduct,'nameProduct'=>$brp->nameProduct,'um'=>$detailRow->um,'price'=>$bpl->price,'qty'=>$detailRow->qty,'taxes'=>$brt->description];
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
<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\RedPandaOrderLogicException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CBillRegistryContract;
use bamboo\domain\entities\CBillRegistryContractRow;

/**
 * Class CSelectBillRegistryProductToRowInvoiceAjaxController
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
class CSelectBillRegistryProductToRowInvoiceAjaxController extends AAjaxController
{

    public function post()
    {




    }

    public function get()
    {
        $data = $this->app->router->request()->getRequestData();
        $id = $data['id'];

        $productDetail = [];
        $detailRow = \Monkey::app()->repoFactory->create('BillRegistryProduct')->findOneBy(['id' => $id]);
        $brt =\Monkey::app()->repoFactory->create('BillRegistryTypeTaxes')->findOneBy(['id' => $detailRow->billRegistryTypeTaxesId]);
        $perc=$brt->perc;
            $description=$detailRow->description.'</br>';
            $brp = \Monkey::app()->repoFactory->create('BillRegistryProductDetail')->findBy(['billRegistryProductId' => $detailRow->id]);

            foreach($brp as $descDetail){
                $description.=$descDetail->detailDescription.'</br>';
            }
        $productDetail[]=['id'=>$detailRow->id,'nameProduct'=>$detailRow->nameProduct,'um'=>$detailRow->um,'price'=>$detailRow->price,'taxes'=>$detailRow->billRegistryTypeTaxesId,'description'=>$description,'perc'=>$perc];



return json_encode($productDetail);

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

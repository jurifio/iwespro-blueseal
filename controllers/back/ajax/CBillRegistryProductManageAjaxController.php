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
class CBillRegistryProductManageAjaxController extends AAjaxController
{

    public function post()
    {



    }

    public function get()
    {
        $data = $this->app->router->request()->getRequestData();
        $billRegistryProductId = $data['id'];
        $billRegistryClientId =$data['billRegistryClientId'];


        $ProductRowDetail = [];
        $brp = \Monkey::app()->repoFactory->create('BillRegistryProduct')->findOneBy(['billRegistryGroupProductId' => $billRegistryProductId]);


            $bpl= \Monkey::app()->repoFactory->create('BillRegistryPriceList')->findOneBy(['billRegistryProductId'=>$product->id,'billRegistryClientId'=>$billRegistryClientId,'isActive'=>1]);
            if($bpl!=null){
                $price=$bpl->price;
            }else{
                $price=$product->price;
            }
            $brt= \Monkey::app()->repoFactory->create('BillRegistryTypeTaxes')->findOneBy(['id' => $brp->billRegistryTypeTaxesId]);
            $contractRowDetail[]=['productId'=>$brp->id,'codeProduct'=>$brp->codeProduct,'nameProduct'=>$brp->nameProduct,'um'=>$product->um,'price'=>$price,'idTaxes'=>$brp->billRegistryTypeTaxesId,'taxesDescription'=>$brt->description];


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
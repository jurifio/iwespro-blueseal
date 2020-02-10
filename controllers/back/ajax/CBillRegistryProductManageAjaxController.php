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

        $data = $this->app->router->request()->getRequestData();
        $billRegistryProductRepo=\Monkey::app()->repoFactory->create('BillRegistryProduct');
        if ($_GET['codeProduct'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> codice Prodotto non inserito non inserita</i>';
        } else {
            $codeProduct = $_GET['codeProduct'];
        }
        if ($_GET['nameProduct'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">nome Prodotto  non inserito</i>';
        } else {
            $nameProduct = $_GET['nameProduct'];
        }
        if ($_GET['um'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Unità di misura  non inserita</i>';
        } else {
            $um = $_GET['um'];
        }

        if ($_GET['logoFile'] == '') {
            $logoFile = '';
        } else {
            $logoFile = $_GET['logoFile'];
        }
        if ($_GET['cost'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Prezzo di Acquisto   non inserito</i>';
        } else {
            $cost = $_GET['cost'];
        }
        if ($_GET['price'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Prezzo di Vendita  non inserito</i>';
        } else {
            $price = $_GET['price'];
        }
        if ($_GET['billRegistryGroupProductId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Gruppo Prodotto Non Selezionata</i>';
        } else {
            $billRegistryGroupProductId = $_GET['billRegistryGroupProductId'];
        }
        if ($_GET['billRegistryTypeTaxesId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Aliquota Iva non Selezionata</i>';
        } else {
            $billRegistryTypeTaxesId = $_GET['billRegistryTypeTaxesId'];
        }

        if ($_GET['productList'] == '') {
           $productList='';
        } else {
            $productList = $_GET['productList'];
        }
try {
    $brpInsert = $billRegistryProductRepo->getEmptyEntity();
    $brpInsert->codeProduct = $codeProduct;
    $brpInsert->nameProduct = $nameProduct;
    $brpInsert->um = $um;
    if ($logoFile != null) {
        $brpInsert->image = $logoFile;
    }
    $brpInsert->price = $price;
    $brpInsert->cost = $cost;
    $brpInsert->billRegistryGroupProductId = $billRegistryGroupProductId;
    $brpInsert->billRegistryTypeTaxesId = $billRegistryTypeTaxesId;
    $brpInsert->insert();
    $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryProduct ',[])->fetchAll();
    foreach ($res as $result) {
        $productId = $result['id'];
    }
    if($productList!=null){
        $productDescriptions=explode(',',$productList);
    }
    foreach($productDescriptions as $description){
        $billRegistryProductDetail=\Monkey::app()->repoFactory->create('BillRegistryProductDetail')->getEmptyEntity();
        $billRegistryProductDetail->billRegistryProductId=$productId;
        $billRegistryProductDetail->detailDescription=$description;
        $billRegistryProductDetail->insert();

    }

         \Monkey::app()->applicationLog('CBillRegistryProductManageAjaxController','Error','insert Product',$productId.'-'.$codeProduct,'');
return 'Inserimento eseguito con Successo ';

}catch(\Throwable $e){
    \Monkey::app()->applicationLog('CBillRegistryProductManageAjaxController','Error','insert Product',$e,'');
    return 'Errore inserimento  '.$e;
}



    }

    public function get()
    {
        $data = $this->app->router->request()->getRequestData();
        $billRegistryProductId = $data['id'];
        $billRegistryClientId =$data['billRegistryClientId'];


        $ProductRowDetail = [];
        $brp = \Monkey::app()->repoFactory->create('BillRegistryProduct')->findOneBy(['billRegistryGroupProductId' => $billRegistryProductId]);


            $bpl= \Monkey::app()->repoFactory->create('BillRegistryPriceList')->findOneBy(['billRegistryProductId'=>$brp->id,'billRegistryClientId'=>$billRegistryClientId,'isActive'=>1]);
            if($bpl!=null){
                $price=$bpl->price;
            }else{
                $price=$product->price;
            }
            $brt= \Monkey::app()->repoFactory->create('BillRegistryTypeTaxes')->findOneBy(['id' => $brp->billRegistryTypeTaxesId]);
            $contractRowDetail[]=['productId'=>$brp->id,'codeProduct'=>$brp->codeProduct,'nameProduct'=>$brp->nameProduct,'um'=>$brp->um,'price'=>$price,'idTaxes'=>$brp->billRegistryTypeTaxesId,'taxesDescription'=>$brt->description];


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
<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\RedPandaOrderLogicException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CBillRegistryContract;
use bamboo\domain\entities\CBillRegistryContractRow;


/**
 * Class CBillRegistryGroupProductManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 07/03/2020
 * @since 1.0
 */
class CBillRegistryGroupProductManageAjaxController extends AAjaxController
{

    public function post()
    {

        $data = $this->app->router->request()->getRequestData();
        $billRegistryGroupProductRepo=\Monkey::app()->repoFactory->create('BillRegistryGroupProduct');
        if ($data['codeProduct'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> codice Prodotto non inserito non inserita</i>';
        } else {
            $codeProduct = $data['codeProduct'];
        }
        if ($data['nameProduct'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">nome Prodotto  non inserito</i>';
        } else {
            $nameProduct = $data['nameProduct'];
        }
        if ($data['um'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Unità di misura  non inserita</i>';
        } else {
            $um = $data['um'];
        }
        if ($data['description'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Descrizione</i>';
        } else {
            $description = $data['description'];
        }


        if ($data['cost'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Prezzo di Acquisto   non inserito</i>';
        } else {
            $cost = $data['cost'];
        }
        if ($data['price'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Prezzo di Vendita  non inserito</i>';
        } else {
            $price = $data['price'];
        }

        if ($data['billRegistryTypeTaxesId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Aliquota Iva non Selezionata</i>';
        } else {
            $billRegistryTypeTaxesId = $data['billRegistryTypeTaxesId'];
        }
        if ($data['billRegistryCategoryProductId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Categoria non Selezionata</i>';
        } else {
            $billRegistryCategoryProductId = $data['billRegistryCategoryProductId'];
        }


try {
    $brpFindCodeGroupProduct=$billRegistryGroupProductRepo->findOneBy(['codeProduct'=>$codeProduct]);
        if($brpFindCodeGroupProduct!=null){
            return 'Codice Prodotto esistente ';
    }

    $brpInsert = $billRegistryGroupProductRepo->getEmptyEntity();
    $brpInsert->codeProduct = $codeProduct;
    $brpInsert->name = $nameProduct;
    $brpInsert->um = $um;
    $brpInsert->description=$description;
    $brpInsert->price = $price;
    $brpInsert->cost = $cost;
    $brpInsert->billRegistryCategoryProductId = $billRegistryCategoryProductId;
    $brpInsert->billRegistryTypeTaxesId = $billRegistryTypeTaxesId;
    $brpInsert->insert();


         \Monkey::app()->applicationLog('CBillRegistryGroupProductManageAjaxController','Error','insert Product',$nameProduct.'-'.$codeProduct,'');
return 'Inserimento eseguito con Successo ';

}catch(\Throwable $e){
    \Monkey::app()->applicationLog('CBillRegistryGroupProductManageAjaxController','Error','insert Product',$e,'');
    return 'Errore inserimento  '.$e;
}



    }

    public function get()
    {


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
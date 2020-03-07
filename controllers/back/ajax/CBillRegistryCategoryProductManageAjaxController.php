<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\RedPandaOrderLogicException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CBillRegistryContract;
use bamboo\domain\entities\CBillRegistryContractRow;


/**
 * Class CBillRegistryCategoryProductManageAjaxController
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
class CBillRegistryCategoryProductManageAjaxController extends AAjaxController
{

    public function post()
    {

        $data = $this->app->router->request()->getRequestData();
        $billRegistryCategoryProductRepo=\Monkey::app()->repoFactory->create('BillRegistryCategoryProduct');
        if ($data['nameCategory'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Nome Categoria non inserita</i>';
        } else {
            $nameCategory = $data['nameCategory'];
        }
        if ($data['descriptionCategory'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Descrizione Categoria non inserita</i>';
        } else {
            $descriptionCategory = $data['descriptionCategory'];
        }



try {
    $brpFindCodeCategoryProduct=$billRegistryCategoryProductRepo->findOneBy(['name'=>$nameCategory]);
        if($brpFindCodeCategoryProduct!=null){
            return 'Categoria  Prodotto esistente ';
    }

    $brpInsert = $billRegistryCategoryProductRepo->getEmptyEntity();
    $brpInsert->name = $nameCategory;
    $brpInsert->description=$descriptionCategory;
    $brpInsert->insert();


         \Monkey::app()->applicationLog('CBillRegistryCategoryProductManageAjaxController','Error','insert Category',$nameCategory,'');
return 'Inserimento eseguito con Successo ';

}catch(\Throwable $e){
    \Monkey::app()->applicationLog('CBillRegistryCategoryProductManageAjaxController','Error','insert Category',$e,'');
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
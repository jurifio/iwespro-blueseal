<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CShop;


/**
 * Class CUserList
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/07/2016
 * @since 1.0
 */
class CShopHasCounterManage extends AAjaxController
{
    public function get()
    {

    }

    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $id = $data["counterId"];
        $receiptCounter = $data["receiptCounter"];
        $invoiceCounter = $data["invoiceCounter"];
        $invoiceExtraUeCounter = $data["invoiceExtraUeCounter"];
        try {
            $shopHasCounter=\Monkey::app()->repoFactory->create('ShopHasCounter')->findOneBy(['id'=>$id]);
            $shopHasCounter->receiptCounter=$receiptCounter;
            $shopHasCounter->invoiceCounter=$invoiceCounter;
            $shopHasCounter->invoiceExtraUeCounter=$invoiceExtraUeCounter;
            $shopHasCounter->update();
            $res= 'Progressivi Aggiornati Correttamente';
        } catch (\Throwable $e) {
           \Monkey::app()->applicationLog('CShopHasCounterManage','error','Modify number document',$e,'');
           $res='C\'e stato un problema Consulta la sezione Application Log  ';
            throw $e;
        }

        return $res;
    }
}
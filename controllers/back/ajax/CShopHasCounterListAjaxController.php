<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CShop;
use bamboo\domain\entities\CShopHasCounter;

use bamboo\domain\entities\CAddressBook;

/**
 *
 */
class CShopHasCounterListAjaxController extends AAjaxController
{
    public function get()
    {

        $sql = "SELECT 
                        shc.id ,
                        s.title ,
                        s.receipt,
                        shc.receiptCounter   ,
                         s.invoiceUe ,
                        shc.invoiceCounter ,
                         s.invoiceExtraUe ,
                        shc.invoiceExtraUeCounter  ,
                        shc.invoiceYear 
                         FROM  ShopHasCounter  shc  
                             JOIN   Shop s  on  shc.shopId = s.id 
                        WHERE  s.hasEcommerce  = 1";
        $datatable = new CDataTables($sql,['id'],$_GET,true);
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');

        $shopHasCounters = \Monkey::app()->repoFactory->create('ShopHasCounter')->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('ShopHasCounter')->em()->findCountBySql($datatable->getQuery(true),$datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('ShopHasCounter')->em()->findCountBySql($datatable->getQuery('full'),$datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];
        /** @var CShop $shop */
        foreach ($shopHasCounters as $shopHasCounter) {
                $row = [];
                $row['DT_RowId'] = $shopHasCounter->printId();
                $row['id'] =  $shopHasCounter->printId();
                $shopFind = $shopRepo->findOneBy(['id' => $shopHasCounter->shopId]);
                if($shopFind->id==44){
                    $shopTitle=$shopFind->title.'-'.'Iwes';
                }else{
                    $shopTitle=$shopFind->title;
                }
                $row['title'] = $shopTitle;
                $row['receipt'] = $shopFind->receipt;
                $row['receiptCounter'] = $shopHasCounter->receiptCounter;
                $row['invoiceUe'] = $shopFind->invoiceUe;
                $row['invoiceCounter'] = $shopHasCounter->invoiceCounter;
                $row['invoiceExtraUe'] = $shopFind->invoiceExtraUe;
                $row['invoiceExtraUeCounter'] = $shopHasCounter->invoiceExtraUeCounter;
                $row['invoiceYear'] = $shopHasCounter->invoiceYear;

                $response['data'][] = $row;

        }
        return json_encode($response);
    }
}
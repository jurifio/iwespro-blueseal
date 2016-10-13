<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\domain\entities\CProductSku;

/**
 * Class COrderListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */

class COrderByUserListAjaxController extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

    /**
     * @param $action
     * @return mixed
*/
    public function createAction($action)
    {
        $this->app->setLang(new CLang(1,'it'));
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";
        $this->urls['page'] = $this->urls['base']."prodotti";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else{
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->products = $this->app->entityManagerFactory->create('Order');

        return $this->{$action}();
    }

    public function get()
    {
        /** @var $em CEntityManager * */
        $em = $this->app->entityManagerFactory->create('Shop');
        $shops = $em->findAll("limit 999", "");

        $datatable = new CDataTables('vBluesealOrdersByUsersList', ['id'], $_GET);
	    $datatable->addCondition('statusCode',['ORD_CANCEL'],true);
	    $datatable->addSearchColumn('orderLineStatus');
	    $datatable->addSearchColumn('shop');
	    $datatable->addSearchColumn('product');
	    $datatable->addSearchColumn('productBrand');
	    $datatable->addSearchColumn('email');

        $orders = $this->app->repoFactory->create('Order')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->products->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totlalCount = $this->em->products->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $orderStatuses = $this->app->repoFactory->create('OrderStatus')->findAll();
        $colorStatus = [];
        foreach($orderStatuses as $orderStatus){
            $colorStatus[$orderStatus->code] = $orderStatus->color;
        }

        $orderLineStatuses = $this->app->repoFactory->create('OrderLineStatus')->findAll();
	    $plainLineStatuses = [];
        $colorLineStatus = [];
	    foreach($orderLineStatuses as $orderLineStatus){
			$plainLineStatuses[$orderLineStatus->code] = $orderLineStatus->title;
            $colorLineStatus[$orderLineStatus->code] = $orderLineStatus->colore;
	    }

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totlalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $opera = $blueseal."ordini/aggiungi?order=";
        $i = 0;
        foreach ($orders as $val) {

	        /** ciclo le righe */
	        $response['aaData'][$i]["content"] = "";
	        $alert = false;
	        /*foreach ($val->orderLine as $line) {
		        try {

			        $sku = igbinary_unserialize($line->frozenProduct);
			        $sku->setEntityManager($this->app->entityManagerFactory->create('ProductSku'));

			        $code = $sku->shop->name . ' ' . $sku->printPublicSku(). " (".$sku->product->productBrand->name.")";
			        if($line->orderLineStatus->notify === 1) $alert = true;
		        } catch (\Throwable $e) {
			        $code = 'non trovato';
		        }
		        $response['aaData'][$i]["content"] .= "<span style='color:" . $colorLineStatus[$line->status] . "'>" . $code . " - "  . $plainLineStatuses[$line->status] ."</span>";
		        $response['aaData'][$i]["content"] .= "<br/>";
	        }*/

	        $orderDate = date("D d-m-y H:i", strtotime($val->orderDate));
            $paidAmount = isset($val->paidAmount) ? $val->paidAmount : 0;
            if ($val->lastUpdate != null) {
                $timestamp = time() - strtotime($val->lastUpdate);
                $day = date("z", $timestamp);
                $h = date("H", $timestamp);
                $m = date("i", $timestamp);
                $since = $day . ' giorni ' . $h . ":" . $m . " fa";
            }
            $response['aaData'][$i]["id"] = '<a href="'.$opera.$val->id.'" >'.$val->id.'</a>';
	        if($alert) $response['aaData'][$i]["id"].=" <i style=\"color:red\"class=\"fa fa-exclamation-triangle\"></i>";

            $response['aaData'][$i]['email'] = $val->user->email;
            \Monkey::dump($val->shipmentAddress);
            $response['aaData'][$i]['city'] = ($sa = $val->shipmentAddressId) ?
                $val->shipmentAddress->city : '-';
            $response['aaData'][$i]['country'] = ($sa = $val->shipmentAddressId) ?
                $val->shipmentAddress->country->name : '-';
            $response['aaData'][$i]['orderStatus'] = $val->orderStatus->title;

            $brands = [];
            $shops = [];
            $friendsRev = 0;
            foreach($val->orderLine as $v) {
                $product = $v->productSku->product;
                $brands[] = $product->productBrand->name;
                $shops[] = $v->shop->name;
                $friendsRev += $v->friendRevenue;
            }
            $response['aaData'][$i]['brand'] = implode(', ', $brands);
            $response['aaData'][$i]['shop'] = implode(', ', $shops);

            $response['aaData'][$i]['margine'] = $val->netTotal - $friendsRev;
            $response['aaData'][$i]["lastUpdate"] = isset($since) ? $since : "Mai";
            $response['aaData'][$i]["user"] = '<span>'.$val->userDetails->name . " " . $val->userDetails->surname . '</span>';
            if(isset($val->rbacRole) && count($val->rbacRole)>0){
                $response['aaData'][$i]["user"] .= ' <i class="fa fa-diamond"></i>';
            }

            $response['aaData'][$i]["status"] = "<span style='color:" . $colorStatus[$val->status] . "'>" . $val->orderStatus->orderStatusTranslation->getFirst()->title . "</span>";
            $response['aaData'][$i]["total"] = $val->netTotal;
            $response['aaData'][$i]["payment"] = $val->orderPaymentMethod->name;
            $i++;
        }
        return json_encode($response);
    }

    public function post()
    {
        throw new \Exception();
    }

    public function delete()
    {
        throw new \Exception();
    }

    public function orderBy(){
        $dtOrderingColumns = $_GET['order'];
        $dbOrderingColumns = [
            ['column'=>'o.id'],
            ['column'=>'o.creationDate'],
            ['column'=>'o.lastUpdate']
        ];
        $dbOrderingDefault = [
            ['column'=>'o.creationDate','dir'=>'desc']
        ];

        $sqlOrder = " ORDER BY ";
        foreach ($dtOrderingColumns as $column) {
            if (isset($dbOrderingColumns[$column['column']]) && $dbOrderingColumns[$column['column']]['column'] !== null) {
                $sqlOrder .= $dbOrderingColumns[$column['column']]['column']." ".$column['dir'].", ";
            }
        }
        if (substr($sqlOrder,-1,2) != ', ') {
            foreach($dbOrderingDefault as $column) {
                $sqlOrder .= $column['column'].' '.$column['dir'].', ';
            }
        }
        return rtrim($sqlOrder,', ');
    }
}
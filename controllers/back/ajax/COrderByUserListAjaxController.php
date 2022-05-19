<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\domain\entities\CProductSku;

/**
 * Class COrderListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
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

        $sql =
'select 
  concat(`o`.`id`) AS `id`,
  concat(`ud`.`name`,\' \',`ud`.`surname`) AS `user`,
  `ud`.`name` AS `name`,`ud`.`surname` AS `surname`,
  `u`.`email` AS `email`,
  `ua`.`city` AS `city`,
  `c`.`name` AS `country`,
  `o`.`status` AS `statusCode`,
  concat(`o`.`orderDate`) AS `orderDate`,
  group_concat(`ol`.`productId`,\'-\',`ol`.`productVariantId` separator \',\') AS `product`,
  group_concat(`s`.`title` separator \',\') AS `shop`,
  `os`.`title` AS `orderStatus`,
  group_concat(`pb`.`name` separator \',\') AS `productBrand`,
  `opm`.`name` AS `paymentMethod`,
  `o`.`lastUpdate` as `lastUpdate`
  from
  ((((((((((((`Order` `o` 
  join `User` `u`)
  join `UserAddress` `ua` on(((`ua`.`id` = `o`.`billingAddressId`) or (`ua`.`id` = `o`.`shipmentAddressId`)))) 
  join `Country` `c` on((`ua`.`countryId` = `c`.`id`))) 
  join `UserDetails` `ud`)
  join `OrderPaymentMethod` `opm`) 
  join `OrderStatus` `os`)
  join `OrderStatusTranslation` `oshl`) 
  join `OrderLine` `ol`)
  join `Shop` `s`)
  join `OrderLineStatus` `ols`) 
  join `Product` `p`)
  join `ProductBrand` `pb`) 
  where ((`o`.`userId` = `u`.`id`) and (`ud`.`userId` = `u`.`id`) and (`o`.`orderPaymentMethodId` = `opm`.`id`) and (`o`.`status` = `os`.`code`) and (`o`.`status` like \'ORD%\') and (`oshl`.`orderStatusId` = `os`.`id`) and (`ol`.`orderId` = `o`.`id`) and (`s`.`id` = `ol`.`shopId`) and (`ol`.`productId` = `p`.`id`) and (`ol`.`productVariantId` = `p`.`productVariantId`) and (`p`.`productBrandId` = `pb`.`id`) and (`ol`.`status` = convert(`ols`.`code` using utf8)) and (`o`.`shipmentAddressId` is not null)) group by `o`.`id`';

        $datatable = new CDataTables($sql, ['id'], $_GET, true);
	    $datatable->addCondition('statusCode',['ORD_CANCEL'],true);
	    $datatable->addSearchColumn('orderLineStatus');
	    $datatable->addSearchColumn('shop');
	    $datatable->addSearchColumn('product');
	    $datatable->addSearchColumn('productBrand');
	    $datatable->addSearchColumn('email');

        $orders = \Monkey::app()->repoFactory->create('Order')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->products->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totlalCount = $this->em->products->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $orderStatuses = \Monkey::app()->repoFactory->create('OrderStatus')->findAll();
        $colorStatus = [];
        foreach($orderStatuses as $orderStatus){
            $colorStatus[$orderStatus->code] = $orderStatus->color;
        }

        $orderLineStatuses = \Monkey::app()->repoFactory->create('OrderLineStatus')->findAll();
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
	        $response['data'][$i]["content"] = "";
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
            $response['data'][$i]["id"] = '<a href="'.$opera.$val->id.'" >'.$val->id.'</a>';
	        if($alert) $response['aaData'][$i]["id"].=" <i style=\"color:red\"class=\"fa fa-exclamation-triangle\"></i>";

            $response['data'][$i]['email'] = $val->user->email;
            \Monkey::dump($val->shipmentAddress);
            $response['data'][$i]['city'] = ($sa = $val->shipmentAddressId) ?
                $val->shipmentAddress->city : '-';
            $response['data'][$i]['country'] = ($sa = $val->shipmentAddressId) ?
                $val->shipmentAddress->country->name : '-';
            $response['data'][$i]['orderStatus'] = $val->orderStatus->title;

            $brands = [];
            $shops = [];
            $friendsRev = 0;
            foreach($val->orderLine as $v) {
                $product = $v->productSku->product;
                $brands[] = $product->productBrand->name;
                $shops[] = $v->shop->name;
                $friendsRev += $v->friendRevenue;
            }
            $response['data'][$i]['brand'] = implode(', ', $brands);
            $response['data'][$i]['shop'] = implode(', ', $shops);

            $response['data'][$i]['margine'] = $val->netTotal - $friendsRev;
            $response['data'][$i]["lastUpdate"] = isset($since) ? $since : "Mai";
            $response['data'][$i]["data"] = $val->lastUpdate;
            $response['data'][$i]["user"] = '<span>'.$val->user->userDetails->name . " " . $val->user->userDetails->surname . '</span>';
            if(isset($val->rbacRole) && count($val->rbacRole)>0){
                $response['data'][$i]["user"] .= ' <i class="fa fa-diamond"></i>';
            }

            $response['data'][$i]["status"] = "<span style='color:" . $colorStatus[$val->status] . "'>" . $val->orderStatus->orderStatusTranslation->getFirst()->title . "</span>";
            $response['data'][$i]["total"] = $val->netTotal;
            $response['data'][$i]["payment"] = $val->orderPaymentMethod->name;
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
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
class CFriendOrderListAjaxController extends AAjaxController
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
        $user = $this->app->getUser();
        $allShops = $user->hasPermission('allShops');
        // Se non è allshop devono essere visualizzate solo le linee relative allo shop e solo a un certo punto di avanzamento

        $datatable = new CDataTables('vBluesealFriendOrderList',['id', 'orderId'],$_GET);
	    $datatable->addCondition(
	        'orderLineStatusCode',
            ['ORD_CANCEL', 'ORD_ARCH', 'CRT', 'CRT_MRG'],
            true
        );
        if (!$allShops) {
            $shops = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser($user);
            $datatable->addCondition('shopId', $shops);
        }
            $datatable->addCondition('orderLineStatusCode',
                ['ORD_PENDING', 'ORD_WAIT', 'ORD_LAB', 'ORD_FRND_SNDING', 'ORD_ERR_SEND'],
                true
            );
        //var_dump($datatable->getQuery());
        //die();
        $orderLines = $this->app->repoFactory->create('OrderLine')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->products->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totlalCount = $this->em->products->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $orderStatuses = $this->app->repoFactory->create('OrderStatus')->findAll();
        $colorStatus = [];
        foreach($orderStatuses as $orderStatus){
            $colorStatus[$orderStatus->code] = $orderStatus->color;
        }

        $orderLineStatuses = $this->app->repoFactory->create('OrderLineStatus')->findAll();
	    $plainLineStatuses = [];
        $colorLineStatuses = [];
	    foreach($orderLineStatuses as $orderLineStatus){
			$plainLineStatuses[$orderLineStatus->code] = $orderLineStatus->title;
            $colorLineStatuses[$orderLineStatus->code] = $orderLineStatus->colore;
	    }

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totlalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $opera = $blueseal."ordini/aggiungi?order=";
        $i = 0;

        foreach ($orderLines as $v) {
	        /** ciclo le righe */
            $response['data'][$i]['id'] = $v->id;
            $response['data'][$i]['orderCode'] = $v->orderId . '-' . $v->id;
            $response['data'][$i]['line_id'] = $v->printId();
            $response['data'][$i]['orderId'] = $v->orderId;
            $response['data'][$i]['code'] = $v->product->id . "-" . $v->product->productVariantId;
            $response['data'][$i]['size'] = $v->productSize->name;
            $img = strpos($v->product->dummyPicture, 'amazonaws') ? $v->product->dummyPicture : $this->urls['dummy']."/".$v->product->dummyPicture;
            if($v->product->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
            else $imgs = "";
            $response['data'][$i]['dummyPicture'] = '<img width="50" src="'.$img.'" />' . $imgs . '<br />';
            $statusCode = $v->orderLineStatus->code;
            $lineStatus = '<span style="color:' . $colorLineStatuses[$statusCode] . '" ">' .
                $plainLineStatuses[$statusCode] .
                '</span>';
            $statusSelect = '<div class="selectOlStatus">{line}</div>';

            $response['data'][$i]['orderLineStatusTitle'] = $lineStatus;

            $invoiceLine = $v->invoiceLine->getFirst();
            $response['data'][$i]['invoiceNumber'] = ($invoiceLine) ? $invoiceLine->invoice->number : '-';
            $response['data'][$i]['invoicePaymentDate'] = ($invoiceLine) ? $invoiceLine->invoice->paymentDate : '-';
            $response['data'][$i]['invoiceExpectedPaymentDate'] = ($invoiceLine) ? $invoiceLine->invoice->expectedPaymentDate : '-';
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
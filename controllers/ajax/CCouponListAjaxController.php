<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CCouponListAjaxController
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
class CCouponListAjaxController extends AAjaxController
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
        $this->urls['page'] = $this->urls['base']."coupon";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else{
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->coupons = $this->app->entityManagerFactory->create('Coupon');

        return $this->{$action}();
    }

    public function get()
    {
        $editCouponLink = $this->urls['base']."coupon/modifica";
        $editOrderLink = $this->urls['base']."ordini/aggiungi";
        $datatable = new CDataTables('vBluesealCouponList',['id'],$_GET);

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $coupons = $this->app->repoFactory->create('Coupon')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->coupons->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->coupons->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $this->app->router->request()->getRequestData('draw');
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;
        foreach($coupons as $coupon) {

            $issued = new \DateTime($coupon->issueDate);
            $valid = new \DateTime($coupon->validThru);
            $user = (!is_null ($coupon->user) && !is_null($coupon->user->userDetails)) ? $coupon->user->userDetails->name.' '.$coupon->user->userDetails->surname : null;
            $order = (!is_null ($coupon->order) && !is_null ($coupon->order->couponId)) ? $coupon->order->couponId : null;

            $response['data'][$i]["DT_RowId"] = 'row__'.$coupon->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['code'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$editCouponLink.'/'.$coupon->id.'" style="font-family:consolas">'.$coupon->code.'</a>';
            $response['data'][$i]['couponType'] = $coupon->couponType->name;
            $response['data'][$i]['issueDate'] = $issued->format('d-m-Y');
            $response['data'][$i]['validThru'] = $valid->format('d-m-Y');
            $response['data'][$i]['amount'] = ($coupon->amountType == 'P') ? $coupon->amount.'%' : $coupon->amount.' &euro;';
            $response['data'][$i]['validForCartTotal'] = $coupon->couponType->validForCartTotal.' &euro;';
            $response['data'][$i]['utente'] = $user;
            $response['data'][$i]['orderId'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$editOrderLink.'?order='.$order.'">'.$order.'</a>';
            $response['data'][$i]['valid'] = ($coupon->valid == 1) ? 'valido' : 'non valido';
            $i++;
        }

        return json_encode($response);
    }
}
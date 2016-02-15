<?php
namespace bamboo\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CCouponTypeListAjaxController
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
class CCouponTypeListAjaxController extends AAjaxController
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
        $this->urls['page'] = $this->urls['base']."tipocoupon";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        if ($this->app->getUser()->hasRole('ownerEmployee')) {

        } else if($this->app->getUser()->hasRole('friendEmployee')){
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->coupons = $this->app->entityManagerFactory->create('CouponType');

        return $this->{$action}();
    }

    public function get()
    {
        $editCouponTypeLink = $this->urls['base']."tipocoupon/modifica";
        $datatable = new CDataTables('vBluesealCouponTypeList',['id'],$_GET);

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $coupons = $this->app->repoFactory->create('CouponType')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->coupons->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->coupons->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $this->app->router->request()->getRequestData('draw');
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;
        foreach($coupons as $coupon) {

            $valid = new \DateInterval($coupon->validity);
            if (($anni = ($valid->format('%y'))) != 0) {
               $periodo = ($anni > 1) ? $anni . " anni" : $anni . " anno";
            } elseif ($mesi = ($valid->format('%m')) != 0) {
                $periodo = ($mesi > 1) ? $mesi . " mesi" : $mesi . " mese";
            } else {
                $periodo = ($valid->format('%d') > 1) ? $valid->format('%d') . " giorni" : $valid->format('%d') . " giorno";
            }

            $response['data'][$i]["DT_RowId"] = 'row__'.$coupon->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['name'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$editCouponTypeLink.'/'.$coupon->id.'" >'.$coupon->name.'</a>';
            $response['data'][$i]['validity'] = $periodo;
            $response['data'][$i]['amount'] = ($coupon->amountType == 'F') ? $coupon->amount.' &euro;' : $coupon->amount.'%';
            $response['data'][$i]['validForCartTotal'] = $coupon->validForCartTotal.' &euro;';
            $i++;
        }

        echo json_encode($response);
    }
}
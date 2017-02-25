<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CCouponEventListAjaxController
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
class CCouponEventListAjaxController extends AAjaxController
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
        $this->urls['page'] = $this->urls['base']."eventocoupon";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else{
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->coupons = $this->app->entityManagerFactory->create('CouponEvent');

        return $this->{$action}();
    }

    public function get()
    {
        $editCouponEventLink = $this->urls['base']."eventocoupon/modifica";
        $sql = "SELECT
  `CouponEvent`.`id`          AS `id`,
  `CouponEvent`.`name`        AS `name`,
  `CouponEvent`.`description` AS `description`,
  `CouponEvent`.`click`       AS `click`,
  `CouponEvent`.`startDate`   AS `startDate`,
  `CouponEvent`.`endDate`     AS `endDate`,
  `CouponType`.`name`         AS `couponType`
FROM (`CouponEvent`
  JOIN `CouponType` ON ((`CouponEvent`.`couponTypeId` = `CouponType`.`id`)))";
        $datatable = new CDataTables($sql,['id'],$_GET,true);

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $coupons = $this->app->repoFactory->create('CouponEvent')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->coupons->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->coupons->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $this->app->router->request()->getRequestData('draw');
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;
        foreach($coupons as $coupon) {
            $start = new \DateTime($coupon->startDate);
            $end = new \DateTime($coupon->endDate);

            $response['data'][$i]["DT_RowId"] = 'row__'.$coupon->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['name'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$editCouponEventLink.'/'.$coupon->id.'" >'.$coupon->name.'</a>';
            $response['data'][$i]['description'] = $coupon->description;
            $response['data'][$i]['couponType'] = $coupon->couponType->name;
            $response['data'][$i]['startDate'] = $start->format('d-m-Y');
            $response['data'][$i]['endDate'] = $end->format('d-m-Y');
            $i++;
        }

        return json_encode($response);
    }
}
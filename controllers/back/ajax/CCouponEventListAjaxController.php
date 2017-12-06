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

    public function get()
    {
        $editCouponEventLink = "/blueseal/eventocoupon/modifica";
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
        $repo = \Monkey::app()->repoFactory->create('CouponEvent');
        $datatable->doAllTheThings(true);
        foreach($datatable->getResponseSetData() as $key=>$row) {
            $coupon = $repo->findOneBy($row);
            $start = new \DateTime($coupon->startDate);
            $end = new \DateTime($coupon->endDate);

            $row["DT_RowId"] = 'row__'.$coupon->id;
            $row["DT_RowClass"] = 'colore';
            $row['name'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$editCouponEventLink.'/'.$coupon->id.'" >'.$coupon->name.'</a>';
            $row['description'] = $coupon->description;
            $row['couponType'] = $coupon->couponType->name;
            $row['startDate'] = $start->format('d-m-Y');
            $row['endDate'] = $end->format('d-m-Y');
            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}
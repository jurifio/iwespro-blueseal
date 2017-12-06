<?php
namespace bamboo\controllers\back\ajax;

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
    public function get()
    {
        $editCouponTypeLink = "/blueseal/tipocoupon/modifica";

        $sql = "SELECT
                  `ct`.`id`                AS `id`,
                  `ct`.`name`              AS `name`,
                  `ct`.`amount`            AS `amount`,
                  `ct`.`amountType`        AS `amountType`,
                  `ct`.`validity`          AS `validity`,
                  `ct`.`validForCartTotal` AS `validForCartTotal`,
                  if(`ct`.`hasFreeShipping` = 1, 'sisì','no') as hasFreeShipping,
                  if(`ct`.`hasFreeReturn` = 1, 'sisì','no') as hasFreeReturn,
                  ifnull(group_concat(distinct t.slug),'') as tags
                FROM `CouponType` ct
                  LEFT JOIN (
                    CouponTypeHasTag ctht JOIN Tag t on ctht.tagId = t.id
                  ) on ct.id = ctht.couponTypeId
                GROUP BY ct.id";
        $datatable = new CDataTables($sql,['id'],$_GET,true);

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $datatable->doAllTheThings(true);
        $couponRepo = \Monkey::app()->repoFactory->create('CouponType');

        foreach($datatable->getResponseSetData() as $key=>$row) {
            $coupon = $couponRepo->findOne($row);

            $valid = new \DateInterval($coupon->validity);
            if (($anni = ($valid->format('%y'))) != 0) {
               $periodo = ($anni > 1) ? $anni . " anni" : $anni . " anno";
            } elseif ($mesi = ($valid->format('%m')) != 0) {
                $periodo = ($mesi > 1) ? $mesi . " mesi" : $mesi . " mese";
            } else {
                $periodo = ($valid->format('%d') > 1) ? $valid->format('%d') . " giorni" : $valid->format('%d') . " giorno";
            }

            $row["DT_RowId"] = 'row__'.$coupon->id;
            $row["DT_RowClass"] = 'colore';
            $row['name'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$editCouponTypeLink.'/'.$coupon->id.'" >'.$coupon->name.'</a>';
            $row['validity'] = $periodo;
            $row['amount'] = ($coupon->amountType == 'F') ? $coupon->amount.' &euro;' : $coupon->amount.'%';
            $row['validForCartTotal'] = $coupon->validForCartTotal.' &euro;';
            $row['tags'] = implode('<br />',explode(',',$row['tags']));

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}
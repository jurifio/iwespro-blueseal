<?php
namespace bamboo\controllers\back\ajax;

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

    public function get()
    {
        $editCouponLink = "/blueseal/coupon/modifica";
        $editOrderLink = "/blueseal/ordini/aggiungi";
        $sql = "
                SELECT
                  `Coupon`.`id`                                              AS `id`,
                  `Coupon`.`couponTypeId`                                    AS `couponTypeId`,
                  `Coupon`.`code`                                            AS `code`,
                  `Coupon`.`issueDate`                                       AS `issueDate`,
                  `Coupon`.`validThru`                                       AS `validThru`,
                  `Coupon`.`amount`                                          AS `amount`,
                  `Coupon`.`userId`                                          AS `userId`,
                  `Coupon`.`valid`                                           AS `valid`,
                  `CouponType`.`name`                                        AS `couponType`,
                  `CouponType`.`amountType`                                  AS `amountType`,
                  `CouponType`.`validForCartTotal`                           AS `validForCartTotal`,
                  if(UserDetails.userId is null, '', concat(`UserDetails`.`name`, ' ', `UserDetails`.`surname`)) AS `utente`,
                  ifnull(`Order`.`id`,'')                                               AS `orderId`,
                  ifnull(CouponEvent.name,'') as couponEvent
                FROM (((`Coupon`
                  JOIN `CouponType` ON ((`Coupon`.`couponTypeId` = `CouponType`.`id`)))
                   LEFT JOIN CouponEvent on (Coupon.couponEventId = CouponEvent.id)
                  LEFT JOIN `UserDetails` ON ((`UserDetails`.`userId` = `Coupon`.`userId`))) 
                  LEFT JOIN `Order` ON ((`Order`.`couponId` = `Coupon`.`id`)))";
        $datatable = new CDataTables($sql,['id'],$_GET, true);

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $datatable->doAllTheThings(true);
        $repo = $this->app->repoFactory->create('Coupon');
        foreach($datatable->getResponseSetData() as $key=>$row) {

            $coupon = $repo->findOneBy($row);

            $issued = new \DateTime($coupon->issueDate);
            $valid = new \DateTime($coupon->validThru);
            $user = (!is_null ($coupon->user) && !is_null($coupon->user->userDetails)) ? $coupon->user->userDetails->name.' '.$coupon->user->userDetails->surname : null;
            $order = $coupon->order;

            $row["DT_RowId"] = 'row__'.$coupon->id;
            $row["DT_RowClass"] = 'colore';
            $row['code'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$editCouponLink.'/'.$coupon->id.'" style="font-family:consolas">'.$coupon->code.'</a>';
            $row['couponType'] = $coupon->couponType->name;
            $row['issueDate'] = $issued->format('d-m-Y');
            $row['validThru'] = $valid->format('d-m-Y');
            $row['amount'] = ($coupon->amountType == 'P') ? $coupon->amount.'%' : $coupon->amount.' &euro;';
            $row['validForCartTotal'] = $coupon->couponType->validForCartTotal.' &euro;';
            $row['utente'] = $user ?? "";
            $row['orderId'] = $order ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$editOrderLink.'?order='.$order->id.'">'.$order->id.'</a>' : '';
            $row['valid'] = ($coupon->valid == 1) ? 'valido' : 'non valido';

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}
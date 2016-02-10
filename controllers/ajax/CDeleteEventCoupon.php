<?php
namespace bamboo\blueseal\controllers\ajax;

use Aws\CloudFront\Exception\Exception;

/**
 * Class CDeleteEventCoupon
 * @package redpanda\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CDeleteEventCoupon extends AAjaxController
{
    /**
     * @return string
     */
    public function get()
    {
        $em = $this->app->entityManagerFactory->create('CouponEvent');

        $id =[];
        foreach ($this->app->router->request()->getRequestData() as $coupon) {
            $id []= $coupon;
        }

        $conditions = ['id' => $id];
        $coupons = $em->findBy($conditions);

        $html = "<table><thead><tr><th>Nome</th><th>Descrizione</th><th>Tipo</th></tr></thead><tbody>";
        foreach ($coupons as $coupon) {
            if (is_null ($coupon->click) || ($coupon->click == 0)) {
                $html .= "<tr><td>" . $coupon->name . "</td><td>" . $coupon->description . "</td><td>" . $coupon->couponType->name . "</td></tr>";
            }
            else {
                $html .= "<tr><td>" . $coupon->name . "</td><td>" . $coupon->description . "</td><td>" . $coupon->couponType->name . "</td></tr>";
                $html .= "<tr><td>Evento già utilizzato. Non può essere eliminato!</td></tr>";
            }
        }
        $html .= "</tbody></table>";

        return json_encode(
            [
                'status' => 'ok',
                'bodyMessage' => $html,
                'okButtonLabel' => 'Elimina',
                'cancelButtonLabel' => 'Annulla'
            ]
        );
    }

    /**
     * @return string
     */
    public function delete()
    {
        $couponRepo = $this->app->repoFactory->create('CouponEvent');

        $id = [];
        foreach ($this->app->router->request()->getRequestData() as $couponId) {
            $id[] = $couponId;
        }

        $conditions = ['id' => $id];
        $coupons = $couponRepo->findBy($conditions,'LIMIT 0,999','ORDER BY id');

        $deletedCoupons['ok'] = [];
        $deletedCoupons['ko'] = [];

        foreach ($coupons as $coupon) {
            if (is_null ($coupon->click) || ($coupon->click == 0)) {
                try {
                    $couponRepo->delete($coupon);
                    $deletedCoupons['ok'][] = $coupon;
                } catch (\Exception $e) {
                    $deletedCoupons['ko'][] = $coupon;
                }
            }
            else {
                $deletedCoupons['ko'][] = $coupon;
            }
        }

        $html = "<table><thead><tr><th>Nome</th><th>Descrizione</th><th>Tipo</th></tr></thead><tbody>";

        foreach ($deletedCoupons['ok'] as $deletedCoupon) {
            $html .= "<tr><td>" . $deletedCoupon->name . "</td><td>" . $deletedCoupon->description . "</td><td>" . $coupon->couponType->name . "</td></tr>";
            $html .= "<td>Eliminato</td></tr>";
        }
        foreach ($deletedCoupons['ko'] as $deletedCoupon) {
            $html .= "<tr><td>" . $deletedCoupon->name . "</td><td>" . $deletedCoupon->description . "</td><td>" . $coupon->couponType->name . "</td></tr>";
            $html .= "<td>Non eliminato</td></tr>";
        }

        $html .= "</tbody></table>";

        return json_encode(
            [
                'status' => 'ok',
                'bodyMessage' => $html,
                'okButtonLabel' => 'Ok',
                'cancelButtonLabel' => null
            ]
        );
    }
}
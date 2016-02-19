<?php
namespace bamboo\blueseal\controllers\ajax;

use Aws\CloudFront\Exception\Exception;

/**
 * Class CDeleteCoupon
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
class CDeleteCoupon extends AAjaxController
{
    /**
     * @return string
     */
    public function get()
    {
        $em = $this->app->entityManagerFactory->create('Coupon');

        $id =[];
        foreach ($this->app->router->request()->getRequestData('id') as $coupon) {
            $id []= $coupon;
        }

        $conditions = ['id' => $id];
        $coupons = $em->findBy($conditions);

        $html = "<table><thead><tr><th>Codice</th><th>Valore</th><th>Tipo</th></tr></thead><tbody>";
        foreach ($coupons as $coupon) {
            $amType = ($coupon->amountType == 'F') ? '&euro;' : '%';
            $html .= "<tr><td>" . $coupon->code . "</td><td>" . $coupon->amount . "</td><td>" . $amType . "</td></tr>";
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
        $couponRepo = $this->app->repoFactory->create('Coupon');

        $id = [];
        foreach ($this->app->router->request()->getRequestData() as $couponId) {
            $id[] = $couponId;
        }

        $conditions = ['id' => $id];
        $coupons = $couponRepo->findBy($conditions,'LIMIT 0,999','ORDER BY id');

        $deletedCoupons['ok'] = [];
        $deletedCoupons['ko'] = [];

        foreach ($coupons as $coupon) {
            try {
                $couponRepo->delete($coupon);
                $deletedCoupons['ok'][] = $coupon;
            } catch (\Exception $e) {
                $deletedCoupons['ko'][] = $coupon;
            }
        }

        $html = "<table><thead><tr><th>Codice</th><th>Valore</th><th>Tipo</th></tr></thead><tbody>";

        foreach ($deletedCoupons['ok'] as $deletedCoupon) {
            $amType = ($deletedCoupon->amountType == 'F') ? '&euro;' : '%';
            $html .= "<tr><td>" . $deletedCoupon->code . "</td><td>" . $deletedCoupon->amount . "</td><td>" . $amType . "</td></tr>";
            $html .= "<td>Eliminato</td></tr>";
        }
        foreach ($deletedCoupons['ko'] as $deletedCoupon) {
            $amType = ($deletedCoupon->amountType == 'F') ? '&euro;' : '%';
            $html .= "<tr><td>" . $deletedCoupon->code . "</td><td>" . $deletedCoupon->amount . "</td><td>" . $amType . "</td></tr>";
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
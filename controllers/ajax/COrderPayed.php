<?php
namespace bamboo\blueseal\controllers\ajax;

use Aws\CloudFront\Exception\Exception;

/**
 * Class COrderPayed
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
class COrderPayed extends AAjaxController
{
    /**
     * @return string
     */
    public function get()
    {
        $id = $this->app->router->request()->getRequestData();
        $order = $this->app->repoFactory->create('Order')->findOneBy(['id'=>$id]);

        $html = "Importo pagato :";
        $html .= '<input type="text" class="form-control" name="Payed" value=' . number_format($order->paidAmount, 2) . ">";

        return json_encode(
            [
                'status' => 'ok',
                'bodyMessage' => $html,
                'okButtonLabel' => 'Modifica pagamento',
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
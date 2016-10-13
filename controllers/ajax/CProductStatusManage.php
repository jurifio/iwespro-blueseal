<?php
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CProductListAjaxController
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
class CProductStatusManage extends AAjaxController
{
    public function post() {
        $code = \Monkey::app()->router->request()->getRequestData('code');
        $statusId = \Monkey::app()->router->request()->getRequestData('status');

        $product = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($code);
        if (!$product) return json_encode(['status' => 'ko', 'message' => "OOPS! Non trovo il prodotto di cui si sta tentando di aggiornare lo status"]);
        try {
            $product->productStatusId = $statusId;
            $product->update();
            return json_encode(['status' => 'ok', 'message' => 'Stato aggiornato: ' . $product->productStatus->name]);
        } catch(\Throwable $e){
            return json_encode(['status' => 'ko', 'message' => $e->getMessage()]);
        }
    }
}
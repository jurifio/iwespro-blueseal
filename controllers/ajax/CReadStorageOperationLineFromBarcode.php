<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

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
class CReadStorageOperationLineFromBarcode extends AAjaxController
{


    public function get()
    {
        $shopId = $this->app->router->request()->getRequestData('shop');
        if(!in_array($shopId,$this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser())) {
            $this->app->router->response()->raiseProcessingError();
            return 'Shop non autorizzato!';
        } else {
            $sku = $this->app->repoFactory->create('ProductSku')->findOneBy(['barcode'=>$this->app->router->request()->getRequestData('barcode')]);
            if(is_null($sku)) {
                $this->app->router->response()->raiseProcessingError();
                return 'Barcode non trovato';
            } elseif($sku->shopId != $shopId) {
                $this->app->router->response()->raiseProcessingError();
                return 'Il barcode non appartiene allo shop inserito';
            } else {
                $res = [];
                $res['id'] = $sku->printId();
                $res['barcode'] = $sku->barcode();
                $res['description'] = $sku->printId();

                return json_encode($res);
            }
        }

    }
}
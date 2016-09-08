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
class CStorageOperationFastInsertBarcode extends AAjaxController
{
    /**
     * @return string
     */
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
                $res['barcode'] = $sku->barcode;
                $res['description'] = $sku->printId();

                return json_encode($res);
            }
        }
    }

    public function post() {
        $shopId = $this->app->router->request()->getRequestData('shop');
        if(!in_array($shopId,$this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser())) {
            $this->app->router->response()->raiseProcessingError();
            return 'Shop non autorizzato!';
        }
        $this->app->dbAdapter->beginTransaction();
        try {
            $storehouseOperation = $this->app->repoFactory->create('StorehouseOperation')->getEmptyEntity();
            $storehouseOperation->shopId = $shopId;
            $storehouseOperation->storehouseId = $this->app->repoFactory->create('Storehouse')->findOneBy(['shopId'=>$shopId])->id;
            $storehouseOperation->storehouseOperationCauseId = $this->app->router->request()->getRequestData('cause');
            $storehouseOperation->userId = $this->app->getUser()->id;
            $storehouseOperation->notes = 'Fast Movement';
            $storehouseOperation->operationDate = $this->app->router->request()->getRequestData('date');
            $storehouseOperation->id = $storehouseOperation->insert();

            foreach ($this->app->router->request()->getRequestData('rows') as $row) {
                $storehouseOperationLine = $this->app->repoFactory->create('StorehouseOperationLine')->getEmptyEntity();
                $storehouseOperationLine->storehouseOperationId = $storehouseOperation->id;
                $storehouseOperationLine->storehouseId = $storehouseOperation->storehouseId;
                $storehouseOperationLine->shopId = $storehouseOperation->shopId;

                $productSku = $this->app->repoFactory->create('ProductSku')->findOneByStringId($row['id']);

                $storehouseOperationLine->productId = $productSku->productId;
                $storehouseOperationLine->productVariantId = $productSku->productVariantId;
                $storehouseOperationLine->shopId = $shopId;
                $storehouseOperationLine->productSizeId = $productSku->productSizeId;
                $qty = $row['qty'];

                switch($storehouseOperation->storehouseOperationCause->sign) {
                    case 1: $qty = abs($qty);
                        break;
                    case 0: $qty = (-1 * abs($qty));
                        break;
                }

                $storehouseOperationLine->qty = $qty;
                $productSku->stockQty += $qty;

                $storehouseOperationLine->insert();
                $productSku->update();

                $this->app->dbAdapter->commit();
            }
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
            throw $e;
        }
        return 'ok';
    }
}
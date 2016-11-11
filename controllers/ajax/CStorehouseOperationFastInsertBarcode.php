<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\exceptions\BambooException;
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
class CStorehouseOperationFastInsertBarcode extends AAjaxController
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
                $res['description'] = $sku->product->printId() . " / " . $sku->product->productBrand->name ." / " . $sku->product->printCpf(). " / " . $sku->productSize->name;

                return json_encode($res);
            }
        }
    }

    public function post() {
        $shopId = \Monkey::app()->router->request()->getRequestData('shop');
        if(!in_array($shopId, \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser())) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return 'Shop non autorizzato!';
        }

        $movementCauseId = \Monkey::app()->router->request()->getRequestData('cause');
        $socE = \Monkey::app()->repoFactory->create('StorehouseOperationCause')->findOne([$movementCauseId]);
        $multiplier = ($socE->getMultiplier());

        $soR = \Monkey::app()->repoFactory->create('StorehouseOperation');

        $skusToMove = [];
        foreach (\Monkey::app()->router->request()->getRequestData('rows') as $row) {
            $single = [];
            list($single['id'], $single['productVariantId'], $single['productSizeId'], $shopId) = explode('-', $row['id']);
            $single['qtMove'] = $row['qty'] * $multiplier;
            $skusToMove[] = $single;
        }

        $shop = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $shopId]);

        $soR->registerOperation($skusToMove, $shop, $movementCauseId, $storehouse = null, $notes = null);

        /*try {
            $storehouseOperation = $this->app->repoFactory->create('StorehouseOperation')->getEmptyEntity();
            $storehouseOperation->shopId = $shopId;
            $storehouse = $this->app->repoFactory->create('Storehouse')->findOneBy(['shopId'=>$shopId]);
            $storehouseOperation->storehouseId = $storehouse->id;
            $storehouseOperation->storehouseOperationCauseId = $this->app->router->request()->getRequestData('cause');
            $storehouseOperation->userId = $this->app->getUser()->id;
            $storehouseOperation->notes = 'Fast Movement';
            $storehouseOperation->operationDate = $this->app->router->request()->getRequestData('date');
            $storehouseOperation->id = $storehouseOperation->insert();



            foreach ($this->app->router->request()->getRequestData('rows') as $row) {
                list($id, $productVariantId, $productSizeId, $shopfromBarcode) = explode('-', $row['id']);
                $solRepo->createMovementLine($id, $productVariantId, $productSizeId, $shopfromBarcode, $row['qty'] * $signMultiplier, $storehouseOperation->id, $storehouse->id);
            }
            $this->app->dbAdapter->commit();
        } catch (\Throwable $e) {
            $this->app->dbAdapter->rollBack();
            throw $e;
        }*/
        return 'ok';
    }
}
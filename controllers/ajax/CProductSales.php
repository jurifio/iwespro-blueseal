<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\core\events\EGenericEvent;
use bamboo\core\exceptions\BambooException;

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
class CProductSales extends AAjaxController
{
    public function get()
    {

    }

    public function post()
    {
        $get = $this->app->router->request()->getRequestData();
        $action = '';
        if (array_key_exists('action', $get)) $action = $get['action'];

        switch ($action) {
            case 'assign':
                $res = $this->assign($get['rows'], $get['percentage']);
                break;
            case 'set':
                $res = $this->set($get['rows'], $get['isSale']);
                break;
            default:
                $res = "Nessuna azione è stata selezionata";
        }
        return $res;
    }

    private function assign($rows, $percent = 0)
    {
        foreach ($rows as $v) {
            $this->app->dbAdapter->beginTransaction();
            try {
	            $product = $this->app->repoFactory->create('Product')->findOne(['id' => $v['id'], 'productVariantId' => $v['productVariantId']]);
	            foreach($product->productSku as $productSku ) {
                    $productSku->salePrice = floor($productSku->price / 100 * (100 - $percent ));
                    $productSku->update();
	            }
                $this->app->eventManager->triggerEvent('product.stock.change',['productKeys'=>$product->printId()]);
                //$sql = "UPDATE ProductSku SET salePrice = FLOOR(price / 100 * (100 - ? )) WHERE productId = ? AND productVariantId = ? ";
                //$res = $this->app->dbAdapter->query($sql, [$percent, $v['id'], $v['productVariantId']]);
            } catch (\Throwable $e) {
                $this->app->dbAdapter->rollback();
                return "Non riesco ad avviare le promozioni le promozioni dai prodotti selezionati:<br />" . $e->getMessage();
            }
            $this->app->dbAdapter->commit();
        }

	    //$this->app->cacheService->getCache('entities')->flush();
        return "Promozioni aggiunte e aggiornate!";
    }

    private function set($rows, $isSale)
    {
        foreach ($rows as $v) {
            $product = $this->app->repoFactory->create('Product')->findOne(['id' => $v['id'], 'productVariantId' => $v['productVariantId']]);
            foreach ($product->productSku as $productSku) {
                if((bool)$productSku->isOnSale != (bool)$isSale) {
                    if (!$isSale || 0 != $productSku->salePrice) {
                        try {
                            $productSku->isOnSale = $isSale;
                            $productSku->update();
                        } catch (\Throwable $e) {
                            return "OOPS! Non riesco a impostare le promozioni:<br />" . $e->getMessage();
                        }
                    }
                }
            }
            $this->app->eventManager->triggerEvent('product.stock.change',['productKeys'=>$product->printId()]);
        }
	    //$this->app->cacheService->getCache('entities')->flush();
        return "Le promozioni sono state impostate correttamente.";
    }
}
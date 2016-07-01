<?php
namespace bamboo\blueseal\controllers\ajax;

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
	            /*
	            $prod = $this->app->repoFactory->create('Product')->findOne([$v]);
	            foreach($prod->productSku as $sku ) {
		            $sku->salePrice = floor($sku->pice / 100 * (100 - $percent ));
		            $sku->update();
	            }*/
                $sql = "UPDATE ProductSku SET salePrice = FLOOR(price / 100 * (100 - ? )) WHERE productId = ? AND productVariantId = ? ";
                $res = $this->app->dbAdapter->query($sql, [$percent, $v['id'], $v['productVariantId']]);
	            $this->app->cacheService->getCache('entity')->flush();
            } catch (\Exception $e) {
                $this->app->dbAdapter->rollback();
                return "Non riesco ad avviare le promozioni le promozioni dai prodotti selezionati:<br />" . $e->getMessage();
            }
        }
        $this->app->dbAdapter->commit();
	    $this->app->cacheService->getCache('entity')->flush();
        return "Promozioni aggiunte e aggiornate!";
    }

    private function set($rows, $isSale)
    {
        foreach ($rows as $v) {
                        $psku = $this->app->repoFactory->create('ProductSku')->findOneBy(['productId' => $v['id'], 'productVariantId' => $v['productVariantId']]);
            if (0 != $psku->salePrice) {
                $sql = "UPDATE ProductSku SET isOnsale = ? WHERE productId = ? AND productVariantId = ?";
                try {
                    $res = $this->app->dbAdapter->query($sql, [$isSale, $v['id'], $v['productVariantId']]);
                } catch (\Exception $e) {
                    return "OOPS! Non riesco a impostare le promozioni:<br />" . $e->getMessage();
                }
            }
        }
	    $this->app->cacheService->getCache('entity')->flush();
        return "Le promozioni sono state impostate correttamente.";
    }
}
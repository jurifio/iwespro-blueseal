<?php
namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CMarketplaceHasProductAssociate;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
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
            \Monkey::app()->repoFactory->beginTransaction();
            try {
                /** @var CProduct $product */
                $product = \Monkey::app()->repoFactory->create('Product')->findOne(['id' => $v['id'], 'productVariantId' => $v['productVariantId']]);
                $phpR = \Monkey::app()->repoFactory->create('MarketplaceHasProductAssociate')->findOneBy(['productId' => $v['id'], 'productVariantId' => $v['productVariantId']]);

                if ($phpR!=null) {
                        $phpR->statusPublished = 2;
                        $phpR->update();
                }
                foreach ($product->shopHasProduct as $shopHasProduct) {
                    $shopHasProduct->salePrice = floor($shopHasProduct->price / 100 * (100 - $percent));
                    $shopHasProduct->update();


                }

                foreach ($product->productSku as $productSku) {
                    $productSku->salePrice = floor($productSku->price / 100 * (100 - $percent));
                    $productSku->update();
                }

                foreach ($product->productPublicSku as $singleProductPublicSku){
                    $singleProductPublicSku->salePrice = floor($singleProductPublicSku->price / 100 * (100 - $percent));
                    $singleProductPublicSku->update();
                }

                $this->app->eventManager->triggerEvent('product.price.change', ['productId' => $product->printId()]);
            } catch (\Throwable $e) {
                \Monkey::app()->repoFactory->rollback();
                return "Non riesco ad avviare le promozioni le promozioni dai prodotti selezionati:<br />" . $e->getMessage();
            }
            \Monkey::app()->repoFactory->commit();
        }

        return "Promozioni aggiunte e aggiornate!";
    }

    private function set($rows, $isSale)
    {
        foreach ($rows as $v) {
            $product = \Monkey::app()->repoFactory->create('Product')->findOne(['id' => $v['id'], 'productVariantId' => $v['productVariantId']]);
            /** @var CPrestashopHasProductRepo $phpR */
            $phpR = \Monkey::app()->repoFactory->create('MarketplaceHasProductAssociate')->findOneBy(['productId' => $v['id'], 'productVariantId' => $v['productVariantId']]);
              if ($phpR!=null) {
                  try {
                      $phpR->statusPublished = 2;
                      $phpR->update();
                  } catch (\Throwable $e) {
                      if ($isSale == 1) {
                          return "OOPS! Non riesco a impostare il prodotto in saldo e  l' aggiornamento per la  modifica perchè non è presente nei prodotti  di esportazione per il marketplace :<br/>!" . $e->getMessage();
                      } else {
                          return "OOPS! Non riesco a impostare il prodotto con prezzo pieno e  l' aggiornamento per la  modifica perchè non è presente nei prodotti  di esportazione per il marketplace :<br/>!" . $e->getMessage();
                      }
                  }
              }
            try {
                $product->isOnSale = $isSale;
                $product->update();
            } catch (\Throwable $e) {
                return "OOPS! Non riesco a impostare le promozioni:<br />" . $e->getMessage();
            }
            $this->app->eventManager->triggerEvent('product.price.change', ['productId' => $product->printId()]);
        }
        return "Le promozioni sono state impostate correttamente.";
    }
}
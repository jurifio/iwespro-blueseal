<?php
namespace bamboo\controllers\back\ajax;
use bamboo\domain\repositories\CMarketplaceHasProductAssociateRepo;

/**
 * Class CMarketplaceProductPrestashopManageController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 27/09/2018
 * @since 1.0
 */
class CMarketplaceProductPrestashopManageController extends AAjaxController
{
    public function get()
    {
        $response = [];
        $sql="select mphs.id as id, mphs.shopId as shopId, 
                     mphs.marketPlaceId as marketPlaceId,
                     mphs.prestashopId as prestashopId,
                     `m`.`name` as marketPlaceName,
                      s.`name` as shopName from MarketplaceHasShop mphs join Marketplace m on mphs.marketPlaceId = m.id
                     join Shop s on mphs.shopId=s.id";
       //$marketPlaces= \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        foreach ( $marketPlaces= \Monkey::app()->dbAdapter->query($sql, [])->fetchAll() as $marketPlace) {

            $response[] = ['id' => $marketPlace['id'],'shopId' => $marketPlace['shopId'], 'name' => $marketPlace['marketPlaceName'], 'marketplaceId' => $marketPlace['marketPlaceId'], 'shopname'=>$marketPlace['shopName'],'prestashopId'=>$marketPlace['prestashopId']];
        }

        return json_encode($response);
    }

    public function post()
    {
        $marketplaceHasShop = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOne([$this->app->router->request()->getRequestData('account')]);
        $shopId=$marketplaceHasShop->shopId;
        $marketPlaceId=$marketplaceHasShop->marketplaceId;
        $prestashopId=$marketplaceHasShop->prestashopId;
        $marketplaceHasShopId=$marketplaceHasShop->id;
        $amount = $this->app->router->request()->getRequestData('amount');
        $typeRetouchPrice = $this->app->router->request()->getRequestData('typeRetouchPrice');

        $i = 0;
        $rows = $this->app->router->request()->getRequestData('rows');
        if ($rows == 'all') {
            $query = "SELECT DISTINCT concat(product,'-', variant) AS code
                      FROM vProductSortingView v 
                      WHERE (product, variant) NOT IN (
                        SELECT DISTINCT m.productId, m.productVariantId 
                        FROM MarketPlaceHasProductAssociate m 
                        WHERE m.marketplaceId = ? AND m.shopId = ? )";
            $rows = $this->app->dbAdapter->query($query, [$marketplaceHasShop->marketplaceId, $marketplaceHasShop->id])->fetchAll(\PDO::FETCH_COLUMN, 0);
        }
        /** @var CMarketplaceHasProductAssociateRepo $marketplaceHasProductAssociateRepo */
        $marketplaceHasProductAssociateRepo = \Monkey::app()->repoFactory->create('MarketplaceHasProductAssociate');
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        \Monkey::app()->repoFactory->beginTransaction();
        foreach ($rows as $row) {
            try {
                $ids = [];

                set_time_limit(6);
                $product = $productRepo->findOneByStringId($row);
                $marketplaceHasProductAssociate = $marketplaceHasProductAssociateRepo->addProductToMarketPlacePrestaShop($product,  $shopId, $marketPlaceId,$prestashopId,$typeRetouchPrice,$amount,$marketplaceHasShopId);
                $i++;
                \Monkey::app()->repoFactory->commit();
            } catch
            (\Throwable $e) {
                \Monkey::app()->repoFactory->rollback();
                throw $e;
            }
        }
        return $i;
    }

    public function put()
    {
        //RETRY
        $i = 0;
        foreach ($this->app->router->request()->getRequestData('rows') as $row) {
            $product = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($row);
            $this->app->eventManager->triggerEvent('product.marketplace.change', ['productId' => $product->printId()]);
        }
        return $i;
    }

    /**
     * @return int
     */
    public function delete()
    {
        $count = 0;
        /** @var CMarketplaceAccountHasProductRepo $repo */
        $repo = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct');
        foreach ($this->app->router->request()->getRequestData('ids') as $mId) {
            if ($repo->deleteProductFromMarketplaceAccount($mId)) $count++;
        }
        return $count;
    }
}
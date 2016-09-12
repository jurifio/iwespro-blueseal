<?php
namespace bamboo\blueseal\controllers\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CProductPhoto;

/**
 * Class CCheckProductsToBePublished
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
class CProductPhotoDownload extends AAjaxController
{
    public function get()
    {
        $shopsId = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
        $contoProdotti = 0;
        $contoSoldi = 0;
        $listaProdotti = [];
        foreach ($this->app->router->request()->getRequestData('rows') as $productId) {
            $product = $this->app->repoFactory->create('Product')->findOneByStringId($productId);
            if($product->productPhoto->count()<1) continue;
            foreach ($product->shopHasProduct as $shopHasProduct) {
                if(!in_array($shopHasProduct->shopId,$shopsId)) continue;
                if(is_null($shopHasProduct->photoDownloadDate)) {
                    $contoSoldi += $shopHasProduct->shop->config['photoCost'];
                    $contoProdotti++;
                    $listaProdotti[$shopHasProduct->printId()] = ['id'=>$product->printId(),'shopHasProductId'>=$shopHasProduct->printId(),'shop'=>$shopHasProduct->shop->title,'cost'=>$shopHasProduct->shop->config['photoCost']];
                } else {
                    $listaProdotti[$shopHasProduct->printId()] = ['id'=>$product->printId(),'shopHasProductId'>=$shopHasProduct->printId(),'shop'=>$shopHasProduct->shop->title,'cost'=>0];
                    $contoProdotti++;
                }
            }
        }
        return json_encode(['costo'=>$contoSoldi,'productList'=>$listaProdotti,'conto'=>$contoProdotti]);
    }


    public function post()
    {
        $toDownload = [];
        foreach ($this->app->router->request()->getRequestData('rows') as $productId) {
            $shopHasProduct = $this->app->repoFactory->create('ShopHasProduct')->findOneByStringId($productId);
            $shopHasProduct->photoDownloadDate = (new \DateTime())->format('');
            $shopHasProduct->update();

            $toDownload[$shopHasProduct->product->printId()] = $shopHasProduct->product;
        }

        foreach ($toDownload as $product) {
            foreach ($product->productPhoto as $productPhoto) {
                if($productPhoto->size != CProductPhoto::SIZE_BIG) continue;

            }
        }
    }

    /**
     * @param $url
     * @return string
     */
    public function downloadUrl($url) {
        return "data/www";
    }
}
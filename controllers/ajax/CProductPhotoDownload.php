<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\traits\TMySQLTimestamp;
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
    use TMySQLTimestamp;

    public function get()
    {
        $shopsId = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
        $contoProdotti = 0;
        $contoSoldi = 0;
        $listaProdotti = [];
        foreach ($this->app->router->request()->getRequestData('rows') as $productId) {
            $product = $this->app->repoFactory->create('Product')->findOneByStringId($productId);
            if ($product->productPhoto->count() < 1) continue;
            foreach ($product->shopHasProduct as $shopHasProduct) {
                if (!in_array($shopHasProduct->shopId, $shopsId)) continue;
                if (is_null($shopHasProduct->productPhotoDownloadTime)) {
                    try {
                        $singleCost = $shopHasProduct->shop->config['photoCost'];
                    } catch (\Throwable $e) {
                        $singleCost = 3;
                    }
                    $contoSoldi += $singleCost;
                    $contoProdotti++;
                    $listaProdotti[$shopHasProduct->printId()] = ['id' => $product->printId(), 'shopHasProductId' >= $shopHasProduct->printId(), 'shop' => $shopHasProduct->shop->title, 'cost' => $singleCost];
                } else {
                    $listaProdotti[$shopHasProduct->printId()] = ['id' => $product->printId(), 'shopHasProductId' >= $shopHasProduct->printId(), 'shop' => $shopHasProduct->shop->title, 'cost' => 0];
                    $contoProdotti++;
                }
            }
        }
        return json_encode(['costo' => $contoSoldi, 'productList' => $listaProdotti, 'conto' => $contoProdotti]);
    }


    public function post()
    {
        $toDownload = [];
        $allShop = $this->app->getUser()->hasPermission('allShops');
        foreach ($this->app->router->request()->getRequestData('rows') as $productId) {
            $shopHasProduct = $this->app->repoFactory->create('ShopHasProduct')->findOneByStringId($productId);
            $toDownload[$shopHasProduct->product->printId()] = $shopHasProduct->product;

            if (!$allShop) {
                $shopHasProduct->productPhotoDownloadTime = $this->time();
                $shopHasProduct->update();
            }
        }

        $local = $this->app->rootPath() . "/temp";
        $remote = $this->app->cfg()->fetch("general", "product-photo-host");
        $zipName = time() . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($local . '/' . $zipName, \ZipArchive::CREATE) !== TRUE) {
            throw  new \Exception('Ops. problemi');
        }
        $files = [];
        foreach ($toDownload as $product) {
            foreach ($product->productPhoto as $productPhoto) {
                if ($productPhoto->size != CProductPhoto::SIZE_BIG) continue;
                $localName = $local . '/' . $productPhoto->name;
                if (in_array($localName, $files)) continue;
                $files[] = $localName;
                $zip->addFromString($productPhoto->name, file_get_contents($remote . $product->productBrand->slug . '/' . urlencode($productPhoto->name), 'r'));
            }
        }
        if ($zip->close()) {
            /** @var \PharData $compressed */
            return json_encode(['file' => $zipName, 'size' => number_format(filesize($local . '/' . $zipName) / 1048576, 2) . ' MB']);
        } else {
            return ' cazzo';
        }
    }
}
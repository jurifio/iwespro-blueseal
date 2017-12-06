<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\utils\slugify\CSlugify;
use bamboo\domain\repositories\CProductDetailRepo;

/**
 * Class CDetailRawReplace
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CDetailRawReplace extends AAjaxController
{
    /**
     * @return bool
     * @throws \Exception
     */
    public function put()
    {
	    $productsIds = $this->app->router->request()->getRequestData('productsId');
        $productDetailsRaw = $this->app->router->request()->getRequestData()['newDetails'];

        /** @var CProductDetailRepo $detailRepo */
        $detailRepo = \Monkey::app()->repoFactory->create('ProductDetail');
        \Monkey::app()->repoFactory->beginTransaction();
        try {
            $productDetails = [];
            foreach(explode("\n",$productDetailsRaw) as $productDetail) {
                $productDetails[] = $detailRepo->fetchOrInsert(trim($productDetail));
            }

            $productRepo = \Monkey::app()->repoFactory->create('Product');
            $productSheetActualRepo = \Monkey::app()->repoFactory->create('ProductSheetActual');

            foreach ($productsIds as $productId) {
                $product = $productRepo->findOneByStringId($productId);
                foreach ($product->productSheetActual as $productSheetActual) {
                    $productSheetActual->delete();
                }

                $product->productSheetPrototypeId = 33;
                $product->update();
                $product->productSheetPrototype->productDetailLabel->getFirst();
                foreach ($productDetails as $productDetail) {
                    $productDetailLabel = $product->productSheetPrototype->productDetailLabel->current();
                    $productSheetActual = $productSheetActualRepo->getEmptyEntity();

                    $productSheetActual->productId = $product->id;
                    $productSheetActual->productVariantId = $product->productVariantId;
                    $productSheetActual->productDetailLabelId = $productDetailLabel->id;
                    $productSheetActual->productDetailId = $productDetail->id;
                    $productSheetActual->insert();

                    $product->productSheetPrototype->productDetailLabel->next();
                }
            }
            \Monkey::app()->repoFactory->commit();
            return count($productsIds);
        } catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();
            throw $e;
        }

    }
}
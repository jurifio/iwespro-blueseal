<?php
namespace bamboo\blueseal\controllers\ajax;
use bamboo\core\utils\slugify\CSlugify;

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
	    $productIds = $this->app->router->request()->getRequestData('productsId');
        $productDetails = $this->app->router->request()->getRequestData()['newDetails'];

        $ids = [];
        $this->app->dbAdapter->beginTransaction();
        foreach(explode("\n",$productDetails) as $productDetail) {
            
        }

        foreach ($data as $key => $val) {
	        if($val == $productDetailId) continue;
            if($val == $productDetailName) continue;
            $ids[] = $val;
        }

	    $productDetailPrimary = $this->app->repoFactory->create("ProductDetail")->findOneBy(['id' => $productDetailId]);
	    $productDetailPrimary->productDetailTranslation->getFirst()->name = $productDetailName;
        $slug = new CSlugify();
        $productDetailPrimary->slug = $slug->slugify($productDetailName);
	    $productDetailPrimary->productDetailTranslation->getFirst()->update();

        $em = $this->app->entityManagerFactory->create('ProductSheetActual');
        try {

            $modelRepo = $this->app->repoFactory->create('ProductSheetModelActual');
            foreach($ids as $id) {
                if ($id != $productDetailId) {
                    $models = $modelRepo->findBy(['productDetailId' => $id]);
                    foreach($models as $m) {
                        $m->delete();
                    }
                }
            }

            foreach ($ids as $id) {
                $productSheets = $em->findBy(['productDetailId' => $id]);

                foreach ($productSheets as $productSheet) {
                    $productSheet->delete();
                    $productSheet->productDetailId = $productDetailId;
                    $productSheet->insert();
                }
	            $productDetail = $this->app->repoFactory->create("ProductDetail",false)->findOneBy(['id' => $id]);

	            foreach ($productDetail->productDetailTranslation as $detailTranslation) {
					$detailTranslation->delete();
	            }
	            $productDetail->delete();
            }
            $this->app->dbAdapter->commit();
            return true;
        } catch (\Throwable $e){
            $this->app->dbAdapter->rollBack();
	        throw $e;
        }
    }
}
<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\exceptions\BambooException;
use bamboo\core\utils\slugify\CSlugify;
use bamboo\ecommerce\views\VBase;

/**
 * Class CGetDataSheet
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CEditVariantDescription extends AAjaxController
{
    public function post()
    {
        $prestashopHasProductRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
        $codes = \Monkey::app()->router->request()->getRequestData('codes');
        $colorNameManufacturer = \Monkey::app()->router->request()->getRequestData('colorNameManufacturer');
        $groupId = \Monkey::app()->router->request()->getRequestData('groupId');
        $pvR = \Monkey::app()->repoFactory->create('ProductVariant');
        $dba = \Monkey::app()->dbAdapter;
        \Monkey::app()->repoFactory->beginTransaction();

        if (!count($codes)) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return 'uno o più dati necessari per procedere non sono stati recapitati';
        }
        try {
            if ($colorNameManufacturer) {
                foreach ($codes as $c) {
                    $exploded = explode('-', $c);
                    $pvE = $pvR->findOne([$exploded[1]]);
                    $pvE->description = $colorNameManufacturer;
                    $pvE->update();
                }
            }

            if ($groupId) {
                foreach ($codes as $c) {
                    $product = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($c);
                    $productId=$product->id;
                    $productVariantId=$product->productVariantId;

                    $product->productColorGroupId = $groupId;
                    $product->update();
                    $prestashopHasProduct=$prestashopHasProductRepo->findOneBy(
                        [
                            'productId' => $productId,
                            'productVariantId' => $productVariantId
                        ]);
                    if($prestashopHasProduct!==null){
                        $prestashopHasProduct->status=2;
                        $prestashopHasProduct->update();
                    }

                }
            }
            \Monkey::app()->repoFactory->commit();
            return 'I prodotti sono stati aggiornati correttamente';
        } catch (BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
            \Monkey::app()->router->response()->raiseProcessingError();
            return 'C\'è stato un problema:<br />' . $e->getMessage();
        }
    }
}
<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\exceptions\BambooException;
use bamboo\core\utils\slugify\CSlugify;
use bamboo\ecommerce\views\VBase;

/**
 * Class CGetDataSheet
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
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
        $codes = \Monkey::app()->router->request()->getRequestData('codes');
        $colorNameManufacturer = \Monkey::app()->router->request()->getRequestData('colorNameManufacturer');
        $groupId = \Monkey::app()->router->request()->getRequestData('groupId');
        $pvR = \Monkey::app()->repoFactory->create('ProductVariant');
        $dba = \Monkey::app()->dbAdapter;
        $dba->beginTransaction();

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
                    $product->productColorGroupId = $groupId;
                    $product->update();
                }
            }
            $dba->commit();
            return 'I prodotti sono stati aggiornati correttamente';
        } catch (BambooException $e) {
            $dba->rollBack();
            \Monkey::app()->router->response()->raiseProcessingError();
            return 'C\'è stato un problema:<br />' . $e->getMessage();
        }
    }
}
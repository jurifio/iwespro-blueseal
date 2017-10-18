<?php
namespace bamboo\controllers\back\ajax;


/**
 * Class CProductSizeGroupManage
 * @package bamboo\controllers\back\ajax
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
class CProductSizeGroupManage extends AAjaxController
{
    /**
     * @return string
     */
    public function put()
    {
        $productSizeGroupHasProdctSizeRepo = \Monkey::app()->repoFactory->create('ProductSizeGroupHasProductSize');
        $productSizeGroupId = \Monkey::app()->router->request()->getRequestData('productSizeGroupId');
        $productSizeId = \Monkey::app()->router->request()->getRequestData('productSizeId');
        $position = \Monkey::app()->router->request()->getRequestData('position');

        $productSizeGroupHasProductSize = $productSizeGroupHasProdctSizeRepo->findOneBy([
            'productSizeGroupId' => $productSizeGroupId,
            'productSizeId' => $productSizeId,
            'position' => $position
        ]);

        if(!$productSizeGroupHasProductSize) {
            $productSizeGroupHasProductSize = $productSizeGroupHasProdctSizeRepo->getEmptyEntity();
            $productSizeGroupHasProductSize->productSizeGroupId = $productSizeGroupId;
            $productSizeGroupHasProductSize->productSizeId = $productSizeId;
            $productSizeGroupHasProductSize->position = $position;

            $productSizeGroupHasProductSize->insert();
        } else {

        }

        return true;
    }
}
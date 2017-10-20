<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductSizeGroupHasProductSize;


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
        \Monkey::app()->router->response()->setContentType('application/json');
        try {
            $productSizeGroupHasProdctSizeRepo = \Monkey::app()->repoFactory->create('ProductSizeGroupHasProductSize');
            $productSizeGroupId = \Monkey::app()->router->request()->getRequestData('productSizeGroupId');
            $productSizeId = \Monkey::app()->router->request()->getRequestData('productSizeId');
            $position = \Monkey::app()->router->request()->getRequestData('position');

            /** @var CProductSizeGroupHasProductSize $productSizeGroupHasProductSize */
            $productSizeGroupHasProductSize = $productSizeGroupHasProdctSizeRepo->findOneBy([
                'productSizeGroupId' => $productSizeGroupId,
                'position' => $position
            ]);

            if (!$productSizeGroupHasProductSize) {
                $productSizeGroupHasProductSize = $productSizeGroupHasProdctSizeRepo->getEmptyEntity();
                $productSizeGroupHasProductSize->productSizeGroupId = $productSizeGroupId;
                $productSizeGroupHasProductSize->productSizeId = $productSizeId;
                $productSizeGroupHasProductSize->position = $position;

                $productSizeGroupHasProductSize->insert();
            } else {
                if (!$productSizeGroupHasProductSize->isProductSizeCorrespondenceDeletable())
                    throw new BambooException('Corrispondenza non eliminabile', [], -1);

                $productSizeGroupHasProductSize->delete();

                $productSizeGroupHasProductSize->productSizeId = $productSizeId;
                $productSizeGroupHasProductSize->insert();
            }
        } catch (\Throwable $e) {
            \Monkey::app()->router->response()->raiseProcessingError();
            $res = ['message' => $e->getMessage()];
            if ($e->getCode() == -1) {
                $res['products'] = $productSizeGroupHasProductSize->getProductCorrespondences();
            } else {
                $res['trace'] = $e->getTrace();
            }
            return json_encode($res);
        }

        return json_encode(true);
    }


}
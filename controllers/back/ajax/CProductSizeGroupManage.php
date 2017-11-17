<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeGroupHasProductSize;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use function MongoDB\BSON\toJSON;


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
    public function post()
    {
        try {
            $data = \Monkey::app()->router->request()->getRequestData();
            $productSizeGroup = \Monkey::app()->repoFactory->create('ProductSizeGroup')->getEmptyEntity();
            $productSizeMacroGroup = \Monkey::app()->repoFactory->create('ProductSizeMacroGroup')->findOneBy(['name'=>$data['macroName']]);
            $productSizeGroup->productSizeMacroGroupId = $productSizeMacroGroup->id;
            $productSizeGroup->locale = $data['locale'];
            $productSizeGroup->name = $data['name'];
            //$productSizeGroup->publicName = $data['publicName'];

            return json_encode([
                    'id' => $productSizeGroup->insert()
                ]);
        } catch (\Throwable $e) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return json_encode([
                'message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ]);
        }
    }

    public function delete()
    {
        $data = \Monkey::app()->router->request()->getRequestData('productSizeGroupId');
        /** @var CProductSizeGroup $productSizeGroup */
        $productSizeGroup = \Monkey::app()->repoFactory->create('ProductSizeGroup')->findOneBy(['id'=>$data]);

        if(!$productSizeGroup->product->isEmpty()) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return json_encode([
                'products'=> $productSizeGroup->product
            ]);
        }

        foreach ($productSizeGroup->productSizeGroupHasProductSize as $productSizeGroupHasProductSize) {
            $productSizeGroupHasProductSize->delete();
        }

        $productSizeGroup->delete();
        return json_encode(true);
    }

    public function put() {
        $productSizeGroupId = \Monkey::app()->router->request()->getRequestData('productSizeGroupId');
        $productSizeGroup = \Monkey::app()->repoFactory->create('ProductSizeGroup')->findOneByStringId($productSizeGroupId);
        $datas = \Monkey::app()->router->request()->getRequestData();
        foreach (\Monkey::app()->router->request()->getRequestData() as $key => $data) {
            if($key == 'productSizeGroupId' || $key == 'id') continue;
            $productSizeGroup->{$key} = $data;
        }
        $productSizeGroup->update();
        return true;
    }
}
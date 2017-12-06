<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeGroupHasProductSize;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use function MongoDB\BSON\toJSON;


/**
 * Class CProductSizeGroupRowManage
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
class CProductSizeGroupRowManage extends AAjaxController
{
    /**
     * @return string
     */
    public function put()
    {
        \Monkey::app()->router->response()->setContentType('application/json');
        try {
            $fromRow = \Monkey::app()->router->request()->getRequestData('rowNum');
            $versus = \Monkey::app()->router->request()->getRequestData('versus');
            $macroGroupName = \Monkey::app()->router->request()->getRequestData('macroName');
            /** @var CProductSizeMacroGroup $productSizeMacroGroup */
            $productSizeMacroGroup = \Monkey::app()->repoFactory->create('ProductSizeMacroGroup')->findOneBy(['name'=>$macroGroupName]);
            if($versus == 'up') $toRow = $fromRow - 1;
            elseif($versus == 'down') $toRow = $fromRow + 1;
            else $toRow = $fromRow;

            /** @var CProductSizeGroupRepo $productSizeGroupRepo */
            $productSizeGroupRepo = \Monkey::app()->repoFactory->create('ProductSizeGroup');
            return json_encode($productSizeGroupRepo->moveSizesPosition($productSizeMacroGroup, $fromRow, $toRow));

        } catch (\Throwable $e) {
            \Monkey::app()->router->response()->raiseProcessingError();
            $res = ['message' => $e->getMessage()];
            $res['trace'] = $e->getTrace();

            return json_encode($res);
        }
    }

    /**
     * @return string
     */
    public function delete()
    {
        try {
            \Monkey::app()->router->response()->setContentType('application/json');

            $macroName = \Monkey::app()->router->request()->getRequestData('macroName');
            $rowNum = \Monkey::app()->router->request()->getRequestData('rowNum');
            $shift = \Monkey::app()->router->request()->getRequestData('versus');

            /** @var CProductSizeGroupRepo $productSizeGroupRepo */
            $productSizeGroupRepo = \Monkey::app()->repoFactory->create('ProductSizeGroup');
            $productSizeMacroGroup = \Monkey::app()->repoFactory->create('ProductSizeMacroGroup')->findOneBy(['name'=>$macroName]);
            $res = $productSizeGroupRepo->deleteGroupPosition($productSizeMacroGroup,$rowNum,$shift);
            if(is_array($res)) {
                \Monkey::app()->router->response()->raiseProcessingError();
            }

            return json_encode($res);
        } catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();
            \Monkey::app()->router->response()->raiseProcessingError();
            return json_encode([
                'message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ]);
        }
    }
}
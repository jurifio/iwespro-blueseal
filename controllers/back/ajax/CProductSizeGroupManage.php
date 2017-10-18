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
        $productSizeGroupId = \Monkey::app()->router->request()->getRequestData();
        $data = \Monkey::app()->router->request()->getRequestData();
        $data = \Monkey::app()->router->request()->getRequestData();
        return json_encode($data);
    }
}
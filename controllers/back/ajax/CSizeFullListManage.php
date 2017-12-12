<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductSize;


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
class CSizeFullListManage extends AAjaxController
{
    /**
     * @return int
     * @throws BambooException
     * @throws \Exception
     */
    public function post()
    {
        try {
            $data = \Monkey::app()->router->request()->getRequestData();
            /** @var CProductSize $productSizeRepo */
            $productSizeRepo = \Monkey::app()->repoFactory->create('ProductSize')->getEmptyEntity();
            $productSizeRepo->slug = $data['slug'];
            $productSizeRepo->name = $data['name'];
            $productSizeRepo->smartInsert();
            return true;
        } catch (\Throwable $e) {
        }
    }

}
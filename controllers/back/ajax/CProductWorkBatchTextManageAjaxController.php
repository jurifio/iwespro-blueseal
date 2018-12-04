<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\utils\slugify\CSlugify;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchTextManage;
use bamboo\domain\entities\CProductSize;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductBatchTextManageRepo;
use bamboo\domain\repositories\CProductSizeRepo;


/**
 * Class CProductWorkBatchTextManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/12/2018
 * @since 1.0
 */
class CProductWorkBatchTextManageAjaxController extends AAjaxController
{
    /**
     * @return string
     */
    public function post()
    {
        $productBatchId = \Monkey::app()->router->request()->getRequestData('productBatchId');
        $theme = \Monkey::app()->router->request()->getRequestData('theme');
        $description = \Monkey::app()->router->request()->getRequestData('description');

        if(empty($productBatchId)) return 'Non hai inserito nessun lotto';

        /** @var CProductBatchRepo $productBatchRepo */
        $productBatchRepo = \Monkey::app()->repoFactory->create('ProductBatch');

        /** @var CProductBatch $productBatch */
        $productBatch = $productBatchRepo->findOneBy(['id'=>$productBatchId]);

        if($productBatch->workCategoryId != 5 &&
            $productBatch->workCategoryId != 6 &&
            $productBatch->workCategoryId != 7 &&
            $productBatch->workCategoryId != 8 &&
            $productBatch->workCategoryId != 9) return 'Il lotto che stai cercando di assegnare non fa parte della categoria di lavorazione corretta';

        /** @var CProductBatchTextManageRepo $productBatchTextManageRepo */
        $productBatchTextManageRepo = \Monkey::app()->repoFactory->create('ProductBatchTextManage');
        $productBatchTextManageRepo->insertNewProductBatchTextManage($productBatch, $theme, $description);

        return 'Lotto completato con successo';
    }

}
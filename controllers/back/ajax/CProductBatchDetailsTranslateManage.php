<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\repositories\CProductBatchHasProductDetailRepo;

/**
 * Class CProductBatchDetailsTranslateManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 07/01/2019
 * @since 1.0
 */
class CProductBatchDetailsTranslateManage extends AAjaxController
{
    /**
     * @return string
     */
    public function post()
    {
        $pbId = \Monkey::app()->router->request()->getRequestData('productBatchId');
        $langId = \Monkey::app()->router->request()->getRequestData('langId');
        $prIds = \Monkey::app()->router->request()->getRequestData('pIds');

        /** @var CProductBatchHasProductDetailRepo $pbdhpdRepo */
        $pbdhpdRepo = \Monkey::app()->repoFactory->create('ProductBatchHasProductDetail');

        //Insert details on 'ProductBathHasProductDetail' table
        $res = $pbdhpdRepo->insertNewDetails($pbId, $prIds, $langId);

        if($res) return 'Dettagli da tradurre insieriti correttamente nel lotto';

        return 'Errore durante l\'inserimento';
    }

}
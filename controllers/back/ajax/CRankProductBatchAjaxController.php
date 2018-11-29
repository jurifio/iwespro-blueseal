<?php

namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CProductBatch;


/**
 * Class CRankProductBatchAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 31/08/2018
 * @since 1.0
 */
class CRankProductBatchAjaxController extends AAjaxController
{

    public function put()
    {
        $pb = \Monkey::app()->router->request()->getRequestData('pb');
        $rank = \Monkey::app()->router->request()->getRequestData('ranking');

        /** @var CProductBatch $prBt */
        $prBt = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(["id"=>$pb]);
        $prBt->operatorRankIwes = $rank;
        $prBt->update();

        $prBt->contractDetails->contracts->foison->totalRank(true);


        return "ORI inserito con successo";
    }


}
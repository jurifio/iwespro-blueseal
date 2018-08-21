<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CWorkPriceList;
use bamboo\domain\repositories\CWorkCategoryRepo;
use bamboo\domain\repositories\CWorkPriceListRepo;


/**
 * Class CWorkPriceListManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/06/2018
 * @since 1.0
 */
class CWorkPriceListManage extends AAjaxController
{
    /**
     * @return bool|string
     */
    public function post()
    {

        $data = \Monkey::app()->router->request()->getRequestData();

        $cat = $data['cat'];
        $wcps = $data['wcp'];

        /** @var CWorkPriceListRepo $workPriceListRepo */
        $workPriceListRepo = \Monkey::app()->repoFactory->create('WorkPriceList');
        if($workPriceListRepo->insertNewPrice($wcps, $cat, 1)) return 'Listino inserito con successo';

        return false;

    }

}
<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CWorkPriceList;
use bamboo\domain\repositories\CWorkCategoryRepo;


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
     * @return string
     */
    public function post()
    {

        $data = \Monkey::app()->router->request()->getRequestData();

        $cat = $data['cat'];
        $wcps = $data['wcp'];

        /** @var CRepo $workPriceListRepo */
        $workPriceListRepo = \Monkey::app()->repoFactory->create('WorkPriceList');

        foreach ($wcps as $wcp) {
            /** @var CWorkPriceList $newWcp */
            $newWcp = $workPriceListRepo->getEmptyEntity();
            $newWcp->name = $wcp['name'];
            $newWcp->price = $wcp['price'];
            $newWcp->start_date = $wcp['start'];
            $newWcp->end_date = $wcp['end'];
            $newWcp->active = 1;
            $newWcp->workCategoryId = $cat;
            $newWcp->smartInsert();
        }

        return 'Listino inserito con successo';


    }

}
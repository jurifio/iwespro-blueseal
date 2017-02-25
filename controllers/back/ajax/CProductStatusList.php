<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CCheckProductsToBePublished
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CProductStatusList extends AAjaxController
{
    public function get() {
        $res = $this->app->dbAdapter->select('ProductStatus', [])->fetchAll();
        $statuses = [];
        foreach($res as $k => $v) {
            if(13 == $v['id']) continue;
            $statuses[] = $v;
        }
        return json_encode($res);
    }
}
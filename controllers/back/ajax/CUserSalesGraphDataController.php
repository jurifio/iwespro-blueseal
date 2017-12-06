<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\intl\CLang;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\widget\VBase;

/**
 * Class CUserSellRecapController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 2016/04/08
 * @since 1.0
 */
class CUserSalesGraphDataController extends AAjaxController
{
	public function get()
	{
	    $shops = \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
        $date = date("Y-m-d H:i:s", strtotime('begin of the year', time()));
		$x = \Monkey::app()->repoFactory->create('Order')->statisticsPoints($shops,$date);
        $res['key'] = "vendita per giorno";
        foreach ($x as $point) {
            $pointData = [];
            $pointData[] = $point['dataOrdine'];
            $pointData[] = $point['value'];
            $res['values'][] = $pointData;
        }

        return json_encode($res);
	}
}
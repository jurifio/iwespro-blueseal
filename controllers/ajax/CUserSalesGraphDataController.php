<?php
namespace bamboo\blueseal\controllers\ajax;

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
	    $shops = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
        $date = date("Y-m-d H:i:s", strtotime('last monday midnight', time()));
		$x = $this->app->repoFactory->create('Order')->statisticsPoints($shops,$date);
        $res = [];
        foreach ($x as $point) {
            $pointData = [];
            $pointData[] = (new \DateTime($point['orderDate']))->getTimestamp();
            $pointData[] = $point['value'];
            $res[] = $pointData;
        }
        return json_encode($res);
	}
}
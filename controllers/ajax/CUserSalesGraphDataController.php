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
	protected $urls = [];
	protected $authorizedShops = [];
	protected $em;

	public function get()
	{
	    $sql = "os.id > 2 AND os.id < 11 AND
                                ((ols.id > 2 AND ols.id < 11) OR ols.id = 17 ) AND
							  	o.orderDate is not null";
		$this->app->repoFactory->create('Order');
	}
}
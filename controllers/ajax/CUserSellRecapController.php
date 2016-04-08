<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

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
class CUserSellRecapController extends AAjaxController
{
	protected $urls = [];
	protected $authorizedShops = [];
	protected $em;

	/**
	 * @param $action
	 * @return mixed
	 */
	public function createAction($action)
	{
		$this->app->setLang(new CLang(1, 'it'));
		$this->urls['base'] = $this->app->baseUrl(false) . "/blueseal/";
		$this->urls['page'] = $this->urls['base'] . "prodotti";
		$this->urls['dummy'] = $this->app->cfg()->fetch('paths', 'dummyUrl');

		$this->em = new \stdClass();
		$this->em->products = $this->app->entityManagerFactory->create('Product');

		return $this->{$action}();
	}

	public function get()
	{
		$okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

		if($this->app->router->request()->getRequestData('day')) {
			$groupBy = "group BY YEAR(orderDate), DAYOFYEAR(orderDate)";
		} else if($this->app->router->request()->getRequestData('week')) {
			$groupBy = "group BY YEAR(orderDate), WEEKOFYEAR(orderDate)";
		} else if($this->app->router->request()->getRequestData('month')) {
			$groupBy = "group BY YEAR(orderDate), MONTH(orderDate)";
		} else if($this->app->router->request()->getRequestData('year')) {
			$groupBy = "group BY YEAR(orderDate) ";
		} else if($this->app->router->request()->getRequestData('list')) {
			$groupBy = "group BY orderId,id";
		}
		$valueToSelect = "";
		if ($this->app->getUser()->hasRole('ownerEmployee')) {
			$valueToSelect = "iwes";
			$shopsWhere = "";
		} else if($this->app->getUser()->hasRole('friendEmployee')){
			$valueToSelect = "friend";
			$authorizedShops = [];
			foreach($this->app->getUser()->shop as $val) {
				$authorizedShops[] = $val['id'];
			}
			$shopsWhere = " AND ol.shopId in (".implode(',',$authorizedShops)." ";

		}

		$sql = "SELECT ol.orderId, ol.id, 
						ifnull(sum(friendRevenue),0) AS friend, 
						ifnull(sum(netPrice),0) AS costumer, 
						ifnull(sum(netPrice) - sum(friendRevenue),0) AS iwes,
						o.orderDate,
						YEAR(orderDate) as year,
						MONTH(orderDate) as month,
						WEEKOFYEAR(orderDate) as week,
						DAYOFYEAR(orderDate) as day
						FROM 
						`Order` o,
						OrderLine ol, 
						OrderLineStatus ols 
						WHERE 	o.id = ol.orderId AND 
								ol.status = ols.code AND
								o.status not like 'ORD_CANCEL' and
							  	ols.phase >= 5 AND 
							  	o.orderDate is not null ".$shopsWhere.$groupBy." order by orderDate desc";

		$data = $this->app->dbAdapter->query($sql,[])->fetchAll();

		$trend = 0;
		if(isset($groupBy) && !$this->app->router->request()->getRequestData('list')) {
			$trend = 100*$data[0][$valueToSelect] / $data[1][$valueToSelect];
		}
		$response = ['trend'=>$trend,'value'=>$valueToSelect];

		return json_encode($response);
	}
}
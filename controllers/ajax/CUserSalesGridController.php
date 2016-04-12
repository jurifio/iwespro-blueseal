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
class CUserSalesGridController extends AAjaxController
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

		$view = new VBase(array());
		$view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/widgets/sales_box.php');

		if($this->app->router->request()->getRequestData('period') == 'list') {
			$title = 'Vendite Recenti';
			$periodProgress = 0;
			$view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/widgets/sales_grid.php');
			$groupBy = "group BY orderId,id LIMIT 30 ";
		} else {
			$title = '';
			$periodProgress = 0;
			$groupBy = "";
		}

		//$completed = (($current - $start) / ($end - $start)) * 100;

		if ($this->app->getUser()->hasRole('manager')) {
			$valueToSelect = "iwes";
			$shopsWhere = "";
		} else{
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
		if(isset($groupBy) && $this->app->router->request()->getRequestData('period') != 'list') {
			$trend = 100 * $data[0][$valueToSelect] / $data[1][$valueToSelect];
			if($data[0][$valueToSelect] - $data[1][$valueToSelect] < 0) {
				$trend=$trend*-1;
			}
		}
		$response = ['trend'=>$trend,'value'=>$valueToSelect];

		return $view->render([
			'app'=>new CRestrictedAccessWidgetHelper($this->app),
			'trend'=>$trend,
			'value'=>$data[0][$valueToSelect],
			'periodProgress'=>$periodProgress,
			'title'=>$title,
			'class'=>$this->app->router->request()->getRequestData('class') ? $this->app->router->request()->getRequestData('class') : "bg-primary"
		]);
	}
}
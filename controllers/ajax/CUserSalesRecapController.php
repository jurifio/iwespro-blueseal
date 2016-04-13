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
class CUserSalesRecapController extends AAjaxController
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
		$view = new VBase(array());
		$view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/widgets/sales_box.php');

		if($this->app->router->request()->getRequestData('period') == 'day') {
			$title = 'Vendite Giornaliere';
			$periodProgress = (gettimeofday(true) - strtotime('00:00') )/(60*60*24)*100;
			$groupBy = "group BY YEAR(orderDate), DAYOFYEAR(orderDate)";
		} else if($this->app->router->request()->getRequestData('period') == 'week') {
			$title = 'Vendite Settimanali';
			$periodProgress = (time()- strtotime('Last Monday')) / (60*60*24*7) * 100;
			$groupBy = "group BY YEAR(orderDate), WEEKOFYEAR(orderDate)";
		} else if($this->app->router->request()->getRequestData('period') == 'month') {
			$title = 'Vendite Mensili';
			$periodProgress = (time() - strtotime('First day of this Month')) / (60*60*24*cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'))) * 100;
			$groupBy = "group BY YEAR(orderDate), MONTH(orderDate)";
		} else if($this->app->router->request()->getRequestData('period') == 'year') {
			$title = 'Vendite Annuali';
			$periodProgress = (time() - strtotime('first day of January')) / (60*60*24*365)*100;
			$groupBy = "group BY YEAR(orderDate) ";
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
				$authorizedShops[] = $val->id;
			}
			$shopsWhere = " AND ol.shopId in (".implode(',',$authorizedShops).") ";
		}

		$sql = "SELECT ol.orderId, ol.id, 
						ifnull(sum(friendRevenue),0) AS friend, 
						ifnull(sum(netPrice),0) AS costumer, 
						ifnull(sum(netPrice) - sum(friendRevenue) - sum(ol.vat),0) AS iwes,
						o.orderDate,
						YEAR(orderDate) as year,
						MONTH(orderDate) as month,
						WEEKOFYEAR(orderDate) as week,
						DAYOFYEAR(orderDate) as day
						FROM 
						`Order` o,
						OrderStatus os,
						OrderLine ol, 
						OrderLineStatus ols 
						WHERE 	o.id = ol.orderId AND 
								o.status =  os.code AND
								ol.status = ols.code AND
								os.order > 2 AND os.id != 13 AND
						    	ols.phase >= 5 AND ols.id != 15 AND
							  	o.orderDate is not null ".$shopsWhere.$groupBy." order by orderDate desc";

		$data = $this->app->dbAdapter->query($sql,[])->fetchAll();

		$trend = 0;
		if(isset($groupBy) && $this->app->router->request()->getRequestData('period') != 'list') {
			$trend = 100 * $data[0][$valueToSelect] / $data[1][$valueToSelect];
			if($data[0][$valueToSelect] - $data[1][$valueToSelect] < 0) {
				$trend=$trend*-1;
			}
		}

		return $view->render([
			'app'=>new CRestrictedAccessWidgetHelper($this->app),
			'trend'=>$trend,
			'value'=>$data[0][$valueToSelect],
			'periodProgress'=>$periodProgress,
			'title'=>$title,
			'class'=>$this->app->router->request()->getRequestData('class') ? $this->app->router->request()->getRequestData('class') : "bg-white"
		]);
	}
}
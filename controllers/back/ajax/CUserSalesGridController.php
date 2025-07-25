<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\intl\CLang;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\widget\VBase;

/**
 * Class CUserSellRecapController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
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

		$limit = $this->app->router->request()->getRequestData('limit') ? $this->app->router->request()->getRequestData('limit') : 100;

		$view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/widgets/sales_grid.php');
		$groupBy = "";

		if ($this->app->getUser()->hasRole('manager')) {
			$valueToSelect = "iwes";
			$shopsWhere = "";
			$title = $this->app->cfg()->fetch("general","name");
		} else{
			$valueToSelect = "friend";
			$authorizedShops = [];
			$authorizedShopsNames = [];
			foreach($this->app->getUser()->shop as $val) {
				$authorizedShops[] = $val->id;
				$authorizedShopsNames[] = $val->name;
			}
			$shopsWhere = " AND ol.shopId in (".implode(',',$authorizedShops).") ";
			$title = implode(',',$authorizedShopsNames);
		}

		$sql = "SELECT ol.orderId, ol.id
				FROM 
					`Order` o,
					OrderStatus os,
					OrderLine ol, 
					OrderLineStatus ols 
					WHERE 	o.id = ol.orderId AND
					 		o.status = os.code AND
							ol.status = ols.code AND
							os.order BETWEEN 2 AND 6 and
						    ols.phase BETWEEN 5 AND 11 AND 
						    o.orderDate is not null ".$shopsWhere." group BY orderId,id order by o.orderDate desc LIMIT ".$limit;

		$data = \Monkey::app()->repoFactory->create('OrderLine')->em()->findBySql($sql,[]);
		$sum = 0;
		foreach ($data as $key=>$val){
			if($valueToSelect == "iwes") {
				$val->show = $val->netPrice - $val->friendRevenue - $val->vat;
			} else {
				$val->show = $val->friendRevenue;
			}
			$sum += $val->show;
		}


		return $view->render([
			'app'=>new CRestrictedAccessWidgetHelper($this->app),
			'orders'=>$data,
			'limit'=>$limit,
			'sum'=>$sum,
			'title'=>$title
		]);
	}
}
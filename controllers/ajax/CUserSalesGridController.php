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

		$limit = $this->app->router->request()->getRequestData('limit') ? $this->app->router->request()->getRequestData('limit') : 30;

		$view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/widgets/sales_grid.php');
		$groupBy = "group BY orderId,id";

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
			$shopsWhere = " AND ol.shopId in (".implode(',',$authorizedShops)." ";
			$title = implode(',',$authorizedShopsNames);
		}

		$sql = "SELECT ol.orderId, ol.id
				FROM 
					`Order` o,
					OrderLine ol, 
					OrderLineStatus ols 
					WHERE 	o.id = ol.orderId AND 
							ol.status = ols.code AND
							o.status not like 'ORD_CANCEL' and
						    ols.phase >= 5 AND ols.phase <= 12 AND
						    o.orderDate is not null ".$shopsWhere.$groupBy." order by o.orderDate desc LIMIT ".$limit;

		$data = $this->app->repoFactory->create('OrderLine')->em()->findBySql($sql,[]);
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
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
		$title = "boh";
		$view = new VBase(array());
		$view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/widgets/sales_box.php');

		$orders = $this->app->repoFactory->create("Order");

		$get = $this->app->router->request()->getRequestData();
		//$completed = (($current - $start) / ($end - $start)) * 100;

        if ($this->app->getUser()->hasPermission('allShops')) {
            $valueToSelect = "iwes";
        } else {
            $valueToSelect = "friend";
		}

        $authorizedShops = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
        $shopsWhere = $authorizedShops;
		//recupero i dati dal db
		$res = [];
		$res['current'] = $orders->statisticsByDate($shopsWhere, $get['period'], $get['period']);

		$res['last'] = $orders->statisticsByDate($shopsWhere, $get['period'], $get['period'], -1);
		
		$data = [];
		foreach($res as $k => $v) {
			if (!array_key_exists(0, $v)) {
				$data[$k]['margin'] = 0;
				$data[$k]['customer'] = 0;
			} else {
				$data[$k] = $res[$k][0];
				$data[$k]['margin'] = $data[$k][$valueToSelect];
			}
		}

		$trend = 0;
		if($get['period'] != 'list') {
			$trend = ($data['last']['margin']) ? 100 * $data['current']['margin'] / $data['last']['margin'] : 0;

			if($data['current']['margin'] - $data['last']['margin'] < 0) {
				$trend=$trend*-1;
			}
		}

		//title
		switch ($get['period']){
			case "year":
				$title = "Anno";
				$timeStartMask = strtotime("first day of this year midnight");
				$timeEndMasks = strtotime("last day of this year midnight");
				break;
			case "month":
				$title = "Mese";
				$timeStartMask = strtotime("first day of this month midnight");
				$timeEndMasks = strtotime("last day of this month midnight");

				break;
			case "week":
				$title = "Settimana";
				$timeStartMask = strtotime("last monday midnight");
				$timeEndMasks = strtotime("next monday midnight");

				break;
			case "day":
				$title = "Giorno";
				$timeStartMask = strtotime("midnight");
				$timeEndMasks = strtotime("tomorrow midnight");

				break;
			case "hour":
				$title = "Ora";
				$timeStartMask = strToTime("Y-m-d H:00:00");
				$timeEndMasks = strToTime("Y-m-d H:00:00");
				break;
		}

		$periodProgress = (time() - $timeStartMask) / ( ($timeEndMasks - $timeStartMask) / 100 );

		return $view->render([
			'app'=>new CRestrictedAccessWidgetHelper($this->app),
			'trend'=>$trend,
			'value'=>$data,
			'periodProgress'=>$periodProgress,
			'title'=>$title,
			'class'=>$this->app->router->request()->getRequestData('class') ? $this->app->router->request()->getRequestData('class') : "bg-white"
		]);
	}
}
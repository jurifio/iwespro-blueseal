<?php

namespace bamboo\blueseal\controllers;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;


/**
 * Class CImportProductController
 * @package bamboo\back\controllers
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, 13/04/2016
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CImportProductFileController extends ARestrictedAccessRootController
{
	protected $fallBack = "blueseal";
	protected $pageSlug = "product_import_file";

	public function get(){
		$view = new VBase(array());
		$view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/product_import_file.php');

		if($this->app->getUser()->hasPermission('allShops')) {
			$shops = $this->app->repoFactory->create('Shop')->findAll();
		} else {
			$shops = $this->app->getUser()->shop;
		}

		return $view->render([
			'app' => new CRestrictedAccessWidgetHelper($this->app),
			'shops'=> $shops,
			'page' => $this->page,
			'sidebar' => $this->sidebar->build()
		]);
	}
	public function post()
	{
		$files = $this->app->router->request()->getFiles();
		if(isset($files['importerFile'])) {
			$f = fopen($files['importerFile']['tmp_name'],'r');
			$i =0;
			for(;fgets($f) !== false;$i++);
			if($i < $this->app->router->request()->getRequestData('csvRows')) {
				$this->app->router->response()->raiseProcessingError();
				return json_encode(['reason'=>'rows','rows'=>$i,'input'=>$this->app->router->request()->getRequestData('csvRows')]);
			}
			//CHECK IF IS CSV
			$separators = [",",";","|","\t"];
			$ok = 0;
			foreach ($separators as $separator) {
				try {
					rewind($f);
					while($values = fgetcsv($f,null,$separator)) {
						if(count($values) == 1) {
							$ok = -1;
							break;
						} else if(count($values) < 3 ) {
							$ok = 0;
							$columns = count($values);
							break;
						} else {
							$ok = 1;
						}
					}
					if($ok == 1) break;
				} catch(\Exception $e) {
					$ok = -1;
					$a = $e;
				}
			}
			if($ok == -1) {
				$this->app->router->response()->raiseProcessingError();
				return json_encode(['reason'=>'csv','file'=>$i,'input'=>$this->app->router->request()->getRequestData('csvRows')]);
			} else if ($ok == 0) {
				$this->app->router->response()->raiseProcessingError();
				return json_encode(['reason'=>'columns','file'=>$i,'input'=>$this->app->router->request()->getRequestData('csvRows')]);
			}

			$shop = $this->app->router->request()->getRequestData('shopId');
			$shop = $this->app->repoFactory->create('shop')->findOne([$shop]);
			$path = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'productSync') . '/' . $shop->name.'/import/';
			$name = $this->app->router->request()->getRequestData('action').'_'
						.$shop->id.'_'
						.date('YmdHis').'_'
						.$this->app->router->request()->getRequestData('csvRows').'.csv';
			if (!rename($files['importerFile']['tmp_name'], $path . $name)) throw new \Exception();

			return ['ok'=>'done'];
		}
		throw new \Exception();
	}
}
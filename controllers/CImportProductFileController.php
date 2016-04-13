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
			$shops = $this->app->repoFactory->create('Shop')->fetchAll();
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
	public function post(){
		return "asdaasd";
	}
}
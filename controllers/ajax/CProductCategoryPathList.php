<?php


namespace bamboo\back\controllers\ajax;
use bamboo\blueseal\controllers\ajax\AAjaxController;


/**
 * Class CProductCategoryPathList
 * @package bamboo\back\controllers\ajax
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, 13/07/2016
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CProductCategoryPathList extends AAjaxController
{
	public function get() {
		$all = [];
		foreach ($this->app->dbAdapter->select('ProductCateogry',[])->findAll() as $category) {
			$all[$category['id']] = $this->app->categoryManager->getCategoryFullPath($category['id']);
		}
		return json_encode($all);
	}
}
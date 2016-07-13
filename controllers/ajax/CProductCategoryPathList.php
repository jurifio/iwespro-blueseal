<?php


namespace bamboo\blueseal\controllers\ajax;


/**
 * Class CProductCategoryPathList
 * @package bamboo\back\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CProductCategoryPathList extends AAjaxController
{
	public function get() {
		$all = [];
		foreach ($this->app->dbAdapter->select('ProductCategory',[])->fetchAll() as $category) {
			$one['id'] = $category['id'];
			$one['value'] = $this->app->categoryManager->categories()->getStringPath($category['id'],' ');
			$all[] = $one;
		}
		return json_encode($all);
	}
}
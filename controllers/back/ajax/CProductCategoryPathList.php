<?php


namespace bamboo\controllers\back\ajax;


/**
 * Class CProductCategoryPathList
 * @package bamboo\back\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
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
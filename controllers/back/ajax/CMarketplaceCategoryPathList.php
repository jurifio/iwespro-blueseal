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
class CMarketplaceCategoryPathList extends AAjaxController
{
	public function get() {
		$all = [];
		foreach (\Monkey::app()->repoFactory->create('MarketplaceAccountCategory')->findBy(['isRelevant'=>1]) as $category) {
			$one['id'] = $category->getHashKey('md5');
			$one['value'] = $category->name." -- ".$category->path;
			$all[$category->marketplaceAccountId.'-'.$category->marketplaceId][] = $one;
            unset($category);
		}
		return json_encode($all);
	}
}
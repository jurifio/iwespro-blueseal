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
class CMarketplaceCategoryPathList extends AAjaxController
{
	public function get() {
		$all = [];
		foreach (\Monkey::app()->repoFactory->create('MarketplaceAccountCategory')->findBy(['isRelevant'=>1]) as $category) {
			$one['id'] = $category->getHashKey('md5');
			$one['value'] = $category->name;
			$all[$category->marketplaceAccount->printId()][] = $one;
		}
		return json_encode($all);
	}
}
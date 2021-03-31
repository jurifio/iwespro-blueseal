<?php


namespace bamboo\controllers\back\ajax;


/**
 * Class CProductSizePathList
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 30/03/2021
 * @since 1.0
 */
class CProductSizePathList extends AAjaxController
{
	public function get() {
		$all = [];
		$psghpsRepo=\Monkey::app()->repoFactory->create('ProductSizeGroupHasProductSize');
		$productSizeGroupRepo=\Monkey::app()->repoFactory->create('ProductSizeGroup');
		foreach ($this->app->dbAdapter->select('ProductSize',[])->fetchAll() as $size) {
			$one['id'] = $size['id'];
			$productSizeGroupFind=$psghpsRepo->findOneBy(['productSizeId'=>$size['id']]);
			if($productSizeGroupFind) {
                $productSizeGroup = $productSizeGroupRepo->findOneBy(['id' => $productSizeGroupFind->productSizeGroupId]);
                $one['value'] = $size['slug'] . '/' . $productSizeGroup->locale . '-' . $productSizeGroup->name;
                $all[] = $one;
			}else{
			    continue;
            }

		}
		return json_encode($all);
	}
}
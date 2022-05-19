<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;


/**
 * Class CMarketplaceCategoryAssignController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 12/07/2016
 * @since 1.0
 */
class CMarketplaceSizeAssignController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "marketplace_size_assign";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/marketplace_size_assign.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

	public function put()
	{
		$sample = \Monkey::app()->repoFactory->create('MarketplaceAccountSize')->getEmptyEntity();
		$sample->readId($this->app->router->request()->getRequestData("id"));
		$one = \Monkey::app()->repoFactory->create('MarketplaceAccountSize')->findOneBy($sample->getIds());
		$this->app->dbAdapter->delete('ProductSizeHasMarketplaceAccountSize',
			[   'marketplaceId'=>$one->marketplaceId,
				'marketplaceAccountId'=>$one->marketplaceAccountId,
				'marketplaceSizeId'=>$one->marketplaceSizeId]);
		foreach ($this->app->router->request()->getRequestData("value") as $value) {
			$this->app->dbAdapter->insert('ProductSizeHasMarketplaceAccountSize',[   'marketplaceId'=>$one->marketplaceId,
			                                  'marketplaceAccountId'=>$one->marketplaceAccountId,
			                                  'marketplaceSizeId'=>$one->marketplaceSizeId,
                                              'productSizeId'=>$value]);
		}
	}

	/**
	 * @return int
	 */
	public function delete() {
		$i = 0;
		foreach($this->app->router->request()->getRequestData('ids') as $id) {
			$sample = \Monkey::app()->repoFactory->create('MarketplaceAccountSize')->getEmptyEntity();
			$sample->readId($id);
			$one = \Monkey::app()->repoFactory->create('MarketplaceAccountSize')->findOneBy($sample->getIds());
			$one->isRelevant = 0;
			if($one->update()>0) $i++;
		}
		return $i;
	}
}
<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;


/**
 * Class CMarketplaceCategoryAssignController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 12/07/2016
 * @since 1.0
 */
class CMarketplaceCategoryAssignController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "marketplace_category_assign";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/marketplace_category_assign.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

	public function put()
	{
		$sample = $this->app->repoFactory->create('MarketplaceAccountCategory')->getEmptyEntity();
		$sample->readIds($this->app->router->request()->getRequestData("id"));
		$one = $this->app->repoFactory->create('MarketplaceAccountCategory')->findOneBy($sample->getIds());
		$this->app->dbAdapter->delete('ProductCategoryHasMarketplaceAccountCategory',
			[   'marketplaceId'=>$one->marketplaceId,
				'marketplaceAccountId'=>$one->marketplaceAccountId,
				'marketplaceAccountCategoryId'=>$one->marketplaceCategoryId]);
		foreach ($this->app->router->request()->getRequestData("value") as $value) {
			$this->app->dbAdapter->insert('ProductCategoryHasMarketplaceAccountCategory',[   'marketplaceId'=>$one->marketplaceId,
			                                  'marketplaceAccountId'=>$one->marketplaceAccountId,
			                                  'marketplaceAccountCategoryId'=>$one->marketplaceCategoryId,'productCategoryId'=>$value]);
		}
	}

	/**
	 * @return int
	 */
	public function delete() {
		$i = 0;
		foreach($this->app->router->request()->getRequestData('ids') as $id) {
			$sample = $this->app->repoFactory->create('MarketplaceAccountCategory')->getEmptyEntity();
			$sample->readIds($id);
			$one = $this->app->repoFactory->create('MarketplaceAccountCategory')->findOneBy($sample->getIds());
			$one->isRelevant = 0;
			if($one->update()>0) $i++;
		}
		return $i;
	}
}
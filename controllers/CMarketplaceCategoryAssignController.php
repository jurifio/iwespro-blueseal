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
		$data = explode('-',$this->app->router->request()->getRequestData("id"));
		$one = $this->app->repoFactory->create('MarketplaceCategoryLookup')->findOneBy(['marketplaceId'=>$data[0],'marketplaceCategoryId'=>$data[1]]);
		$one->productCategoryId = $this->app->router->request()->getRequestData("value");
		$one->update();
	}

	/**
	 * @return int
	 */
	public function delete() {
		$i = 0;
		foreach($this->app->router->request()->getRequestData('ids') as $id) {
			$data = explode('-',$id);
			$one = $this->app->repoFactory->create('MarketplaceCategoryLookup')->findOneBy(['marketplaceId'=>$data[0],'marketplaceCategoryId'=>$data[1]]);
			\BlueSeal::dump($one);
			$one->isRelevant = 0;
			if($one->update()>0) $i++;
		}
		return $i;
	}
}
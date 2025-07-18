<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\intl\CLang;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\widget\VBase;

/**
 * Class CUserSellRecapController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 2016/04/08
 * @since 1.0
 */
class CCatalogGetTemplateController extends AAjaxController
{

	public function get()
	{
		$view = new VBase(array());
        $allShops = $this->app->getUser()->hasPermission('allShops');
        $shops = false;
        if ($allShops) {
            $shops = $this->rfc('Shop')->findAll();
        }
		$view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/widgets/catalog_form.php');

        $causes = \Monkey::app()->repoFactory->create('StorehouseOperationCause')->findBy(['available' => 1])->toArray();
		return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'causes' => $causes,
            'allShops' => $allShops,
            'shops' => $shops
        ]);
	}
}
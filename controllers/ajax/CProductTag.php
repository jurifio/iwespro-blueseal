<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\widget\VBase;

/**
 * Class CProducTag
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/04/2016
 * @since 1.0
 */
class CProductTag extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

    public function get()
    {
	    $view = new VBase(array());
	    $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/widgets/productTagSelection.php');

        $tags = $this->app->repoFactory->create('Tag')->findAll('Tag');

	    return $view->render([
		    'app'=>new CRestrictedAccessWidgetHelper($this->app),
		    'tags'=>$tags
	    ]);
    }

	public function post()
	{
		$a = $this->app->router->request()->getRequestData();
		return $a;
	}
}
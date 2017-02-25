<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\widget\VBase;

/**
 * Class CProductPriority
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
class CProductPriority extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

	/**
	 * @return string
	 */
    public function get()
    {
	    $priorities = $this->app->repoFactory->create('SortingPriority')->findAll();
	    $output = [];
	    foreach ($priorities as $priority) {
			$output[] = ['id'=>$priority->id,'priority'=>'('.$priority->id.') '.$priority->priority];
	    }
		return json_encode($output);
    }

	/**
	 * @return int
	 */
    public function put()
    {
	    $sample = $this->app->repoFactory->create('Product')->getEmptyEntity();
	    $i = 0;
	    foreach ($this->app->router->request()->getRequestData('rows') as $row) {
	    	$sample->readId($row);
		    $product = $this->app->repoFactory->create('Product')->findOneBy($sample->getIds());
		    $product->sortingPriorityId = $this->app->router->request()->getRequestData('priority');
		    if($product->update() > 0) $i++;
	    }
	    return $i;
    }
}
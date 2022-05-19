<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\widget\VBase;

/**
 * Class CProducTagExclusive
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/04/2016
 * @since 1.0
 */
class CProductTagExclusive extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

    public function get()
    {
	    $view = new VBase(array());
	    $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/widgets/productTagExclusiveSelection.php');

        $tags = \Monkey::app()->repoFactory->create('TagExclusive')->findAll('TagExclusive');

	    $deleteTags = [];
	    foreach ($this->app->router->request()->getRequestData('rows') as $key => $val ) {
			$pro = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($val);
            foreach ($pro->tagExclusive as $tag) {
                if (!array_key_exists($tag->id, $deleteTags)) $deleteTags[$tag->id] = $tag;
            }
		    /*if(empty($deleteTags)) {
				foreach ($pro->tag as $tag) {
					$deleteTags[$tag->id] = $tag;
				}
			} else {
			    foreach ($deleteTags as $id => $tag) {
                    if (!array_key_exists($id))
				    //if(!$pro->tag->findOneByKey('id',$tag->id)) unset($deleteTags[$id]);
			    }
		    }*/
	    }

	    return $view->render([
		    'app'=>new CRestrictedAccessWidgetHelper($this->app),
		    'allTags'=>$tags,
		    'deleteTags'=>$deleteTags
	    ]);
    }

	/**
	 *
	 */
	public function post()
	{
		if($this->app->router->request()->getRequestData('rows')) {
			foreach ($this->app->router->request()->getRequestData('rows') as $row) {
				$pKeys = explode('-',$row);
				if($this->app->router->request()->getRequestData('tags')) {
					foreach ($this->app->router->request()->getRequestData('tags') as $tags) {
						$this->app->dbAdapter->insert('ProductHasTagExclusive',['productId'=>$pKeys[0],'productVariantId'=>$pKeys[1],'tagExclusiveId'=>$tags],false,true);
					}
				}
			}
		}
	}

	/**
	 *
	 */
	public function put()
	{
		if($this->app->router->request()->getRequestData('rows')) {
			foreach ($this->app->router->request()->getRequestData('rows') as $row) {
				$pKeys = explode('-',$row);
				if($this->app->router->request()->getRequestData('tags')) {
					foreach ($this->app->router->request()->getRequestData('tags') as $tags) {
						$this->app->dbAdapter->delete('ProductHasTagExclusive',['productId'=>$pKeys[0],'productVariantId'=>$pKeys[1],'tagExclusiveId'=>$tags],'AND',true);
					}
				}
			}
		}
	}
}